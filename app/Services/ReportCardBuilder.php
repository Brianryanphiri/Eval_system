<?php

namespace App\Services;

use App\Models\GradingScale;
use App\Models\Student;
use App\Models\SubjectTermResult;
use App\Models\Term;
use App\Models\TermResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportCardBuilder
{
    public const FULL_MARKS = 100;
    public const PASS_MARK = 50;

    public static function build(Student $student, ?Term $term = null): array
    {
        $school = $student->school;

        if ($term === null) {
            $term = $school->currentAcademicYear()?->currentTerm();
        }

        $academicYear = $term?->academicYear;

        $subjectResults = $term
            ? SubjectTermResult::where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->with('subject')
                ->get()
                ->sortBy(fn($r) => $r->subject->name ?? '')
                ->values()
            : collect();

        $termResult = $term
            ? TermResult::where('student_id', $student->id)->where('term_id', $term->id)->first()
            : null;

        $scales = GradingScale::where('school_id', $school->id)->orderByDesc('min_score')->get();
        $remarkByLetter = $scales->pluck('interpretation', 'letter');

        $breakdown = ReportCardBreakdownService::forStudent($student, $term);

        $rows = [];
        $sum = 0.0;

        foreach ($subjectResults as $i => $r) {
            $final = (float) $r->final_percentage;
            $storedGrade = $r->grade_letter ?: self::fallbackGrade($final);
            $storedPassed = $r->passed !== null ? (bool) $r->passed : $final >= self::PASS_MARK;

            $bd = $breakdown[$r->subject_id] ?? null;
            $ca = $bd['ca'] ?? null;
            $exam = $bd['exam'] ?? null;

            $bothSidesPresent = $ca !== null && $exam !== null;
            $caOnly = $ca !== null && $exam === null;

            $rows[] = [
                'no' => $i + 1,
                'subject' => $r->subject->name ?? 'Unknown subject',
                'full_marks' => self::FULL_MARKS,
                'pass_marks' => self::PASS_MARK,
                'ca' => $ca,
                'exam' => $exam,
                'ca_out_of' => $bd['ca_out_of'] ?? null,
                'exam_out_of' => $bd['exam_out_of'] ?? null,
                'show_breakdown' => $bothSidesPresent,
                'total' => $bothSidesPresent ? (int) round($final) : null,
                'grade' => $bothSidesPresent ? $storedGrade : null,
                'remark' => $bothSidesPresent ? $remarkByLetter->get($storedGrade, '') : null,
                'passed' => $bothSidesPresent ? $storedPassed : null,
                'pending' => $caOnly,
            ];

            if ($bothSidesPresent) {
                $sum += $final;
            }
        }

        $count = count($rows);
        $finalisedCount = count(array_filter($rows, fn($r) => $r['show_breakdown']));

        $overall = ($termResult && $termResult->overall_percentage !== null)
            ? (float) $termResult->overall_percentage
            : ($finalisedCount > 0 ? round($sum / $finalisedCount, 1) : 0);

        $className = null;
        if ($termResult && $termResult->class_id) {
            $className = DB::table('classes')->where('id', $termResult->class_id)->value('name');
        }
        $className = $className ?: ($student->currentClassroom()?->name ?? '-');

        $positionText = ($termResult && $termResult->position)
            ? $termResult->position . ($termResult->class_size ? ' / ' . $termResult->class_size : '')
            : '-';

        $attendance = null;
        if ($term) {
            $row = DB::table('attendance_summaries')
                ->where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->first();

            if ($row) {
                $attended = (int) $row->days_present + (int) $row->days_late;
                $attendance = [
                    'attended' => $attended,
                    'total' => $attended + (int) $row->days_absent + (int) $row->days_excused,
                    'percentage' => $row->percentage !== null ? (float) $row->percentage : null,
                ];
            } elseif ($termResult && $termResult->attendance_percentage !== null) {
                $attendance = [
                    'attended' => null,
                    'total' => null,
                    'percentage' => (float) $termResult->attendance_percentage,
                ];
            }
        }

        $teacherComment = null;
        $headComment = null;
        if ($termResult) {
            $teacherComment = DB::table('report_comments')
                ->where('term_result_id', $termResult->id)
                ->where('type', 'class_teacher')
                ->orderByDesc('id')
                ->value('comment');

            $headComment = DB::table('report_comments')
                ->where('term_result_id', $termResult->id)
                ->where('type', 'head_teacher')
                ->orderByDesc('id')
                ->value('comment');
        }

        $gradingKey = $scales->map(function ($s) {
            $range = $s->max_score >= 100
                ? "{$s->min_score}% and above"
                : "{$s->min_score}% to {$s->max_score}%";

            return [
                'letter' => $s->letter,
                'range' => $range,
                'label' => $s->interpretation,
            ];
        })->all();

        $address = '';
        if (method_exists($school, 'fullAddress')) {
            $address = trim((string) $school->fullAddress());
        }
        if ($address === '') {
            $address = trim((string) ($school->address ?? ''));
        }

        $promotionStatus = strtoupper(str_replace('_', ' ', $termResult?->promotion_status ?? 'pending'));

        return [
            'school' => $school,
            'studentModel' => $student,
            'guardians' => $student->guardians,
            'academicYear' => $academicYear?->label ?? now()->year,
            'term' => $term?->name ?? 'Current Term',
            'overall' => $overall,
            'achievementLevel' => $termResult?->achievement_level ?? 'Not yet assessed',
            'promotionStatus' => $promotionStatus,
            'teacherComment' => $teacherComment ?: $termResult?->getAttribute('teacher_comment'),
            'headComment' => $headComment ?: $termResult?->getAttribute('head_comment'),

            'student' => (object) [
                'id' => $student->id,
                'name' => $student->full_name,
                'student_number' => $student->student_number,
                'class' => $className,
                'position' => $positionText,
            ],

            'rows' => $rows,
            'grandTotal' => (int) round($sum),
            'grandOutOf' => $finalisedCount * self::FULL_MARKS,
            'attendance' => $attendance,
            'gradingKey' => $gradingKey,
            'address' => $address,
            'primary' => self::hex($school->primary_color ?? null, '#0F0F0F'),
            'accent' => self::hex($school->accent_color ?? null, '#7AC142'),
            'files' => [
                'logo' => self::publicFile($school->logo_path ?? null),
                'signature' => self::publicFile($school->principal_signature_path ?? null),
                'photo' => self::publicFile($student->photo_path ?? null),
            ],
        ];
    }

    private static function publicFile(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $full = Storage::disk('public')->path($path);

        return is_file($full) ? $full : null;
    }

    private static function hex(?string $value, string $fallback): string
    {
        return ($value && preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) ? $value : $fallback;
    }

    private static function fallbackGrade(float $score): string
    {
        return match (true) {
            $score >= 80 => 'A',
            $score >= 70 => 'B',
            $score >= 60 => 'C',
            $score >= 50 => 'D',
            default => 'F',
        };
    }
}