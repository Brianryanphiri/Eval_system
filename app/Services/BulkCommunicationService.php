<?php

namespace App\Services;

use App\Mail\BulkCommunicationEmail;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\School;
use App\Models\Term;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BulkCommunicationService
{
    public function resolveRecipients(School $school, string $group, array $params): \Illuminate\Support\Collection
    {
        if ($group === 'guardian') {
            $guardian = User::where('id', $params['guardian_id'] ?? null)
                ->where('school_id', $school->id)
                ->first();

            if (!$guardian) {
                return collect();
            }

            $children = DB::table('student_guardian')
                ->join('students', 'students.id', '=', 'student_guardian.student_id')
                ->join('enrollments', 'enrollments.student_id', '=', 'students.id')
                ->where('student_guardian.guardian_id', $guardian->id)
                ->where('students.school_id', $school->id)
                ->where('enrollments.status', 'active')
                ->select('students.first_name', 'students.last_name', 'enrollments.class_id')
                ->get();

            $classNames = Classroom::where('school_id', $school->id)->pluck('name', 'id');

            $names = $children
                ->map(fn($c) => trim("{$c->first_name} {$c->last_name}"))
                ->filter()
                ->values()
                ->all();

            $childClasses = $children
                ->pluck('class_id')
                ->filter()
                ->unique()
                ->map(fn($id) => $classNames[$id] ?? null)
                ->filter()
                ->values()
                ->all();

            return collect([
                (object) [
                    'id' => $guardian->id,
                    'name' => $guardian->name,
                    'email' => $guardian->email,
                    'student_name' => $names[0] ?? null,
                    'children' => implode(', ', $names),
                    'class_name' => implode(', ', $childClasses),
                ],
            ]);
        }

        $studentIds = collect();

        if ($group === 'class') {
            $studentIds = $this->studentIdsInClass($school->id, (int) ($params['class_id'] ?? 0));
        } elseif ($group === 'year') {
            $classIds = Classroom::where('school_id', $school->id)
                ->where('grade_level', $params['grade_level'] ?? '')
                ->pluck('id');
            $studentIds = $this->studentIdsInClasses($school->id, $classIds);
        } elseif ($group === 'school') {
            $studentIds = $this->allActiveStudentIds($school->id);
        }

        if ($studentIds->isEmpty()) {
            return collect();
        }

        $rows = DB::table('student_guardian')
            ->join('users', 'users.id', '=', 'student_guardian.guardian_id')
            ->join('students', 'students.id', '=', 'student_guardian.student_id')
            ->leftJoin('enrollments', function ($j) {
                $j->on('enrollments.student_id', '=', 'students.id')
                    ->where('enrollments.status', '=', 'active');
            })
            ->whereIn('student_guardian.student_id', $studentIds)
            ->where('users.school_id', $school->id)
            ->whereNotNull('users.email')
            ->where('users.email', '!=', '')
            ->select(
                'users.id as guardian_id',
                'users.name as guardian_name',
                'users.email as guardian_email',
                'students.first_name',
                'students.last_name',
                'enrollments.class_id'
            )
            ->get();

        $classNames = Classroom::where('school_id', $school->id)->pluck('name', 'id');

        return $rows
            ->groupBy('guardian_id')
            ->map(function ($group) use ($classNames) {
                $first = $group->first();

                $names = $group
                    ->map(fn($r) => trim("{$r->first_name} {$r->last_name}"))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $classes = $group
                    ->pluck('class_id')
                    ->filter()
                    ->unique()
                    ->map(fn($id) => $classNames[$id] ?? null)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return (object) [
                    'id' => $first->guardian_id,
                    'name' => $first->guardian_name,
                    'email' => $first->guardian_email,
                    'student_name' => $names[0] ?? null,
                    'children' => implode(', ', $names),
                    'class_name' => implode(', ', $classes),
                ];
            })
            ->values();
    }

    public function substitute(string $text, School $school, $recipient, ?Term $term = null): string
    {
        $replacements = [
            '{{guardian_name}}' => $recipient->name ?? '',
            '{{school_name}}' => $school->name ?? '',
            '{{student_name}}' => $recipient->student_name ?? '',
            '{{children}}' => $recipient->children ?? ($recipient->student_name ?? ''),
            '{{class_name}}' => $recipient->class_name ?? '',
            '{{term}}' => $term?->name ?? '',
            '{{academic_year}}' => $term?->academicYear->label ?? (string) now()->year,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    public function sendToRecipients(School $school, $recipients, string $subjectTemplate, string $bodyTemplate, ?Term $term = null): int
    {
        $count = 0;

        foreach ($recipients as $recipient) {
            $subjectLine = $this->substitute($subjectTemplate, $school, $recipient, $term);
            $bodyText = $this->substitute($bodyTemplate, $school, $recipient, $term);

            Mail::to($recipient->email)->queue(new BulkCommunicationEmail(
                school: $school,
                recipientName: $recipient->name,
                subjectLine: $subjectLine,
                bodyText: $bodyText,
            ));

            DB::table('email_log')->insert([
                'school_id' => $school->id,
                'recipient' => $recipient->email,
                'subject' => $subjectLine,
                'body_preview' => mb_substr($bodyText, 0, 250),
                'template_key' => 'bulk_communication',
                'status' => 'queued',
                'reference_type' => 'bulk_communication',
                'reference_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $count++;
        }

        return $count;
    }

    private function studentIdsInClass(int $schoolId, int $classId)
    {
        if (!$classId) {
            return collect();
        }

        $currentYearId = AcademicYear::where('school_id', $schoolId)
            ->where('is_current', true)
            ->value('id');

        if (!$currentYearId) {
            return collect();
        }

        return DB::table('enrollments')
            ->where('class_id', $classId)
            ->where('academic_year_id', $currentYearId)
            ->where('status', 'active')
            ->pluck('student_id');
    }

    private function studentIdsInClasses(int $schoolId, $classIds)
    {
        if ($classIds->isEmpty()) {
            return collect();
        }

        $currentYearId = AcademicYear::where('school_id', $schoolId)
            ->where('is_current', true)
            ->value('id');

        if (!$currentYearId) {
            return collect();
        }

        return DB::table('enrollments')
            ->whereIn('class_id', $classIds)
            ->where('academic_year_id', $currentYearId)
            ->where('status', 'active')
            ->pluck('student_id');
    }

    private function allActiveStudentIds(int $schoolId)
    {
        return DB::table('enrollments')
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->where('students.school_id', $schoolId)
            ->where('enrollments.status', 'active')
            ->pluck('enrollments.student_id');
    }
}