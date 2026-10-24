<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class ReportCardBreakdownService
{
    public static function forStudent(Student $student, ?Term $term): array
    {
        if (!$term) {
            return [];
        }

        $weights = DB::table('assessment_weights')
            ->join('assessment_types', 'assessment_types.id', '=', 'assessment_weights.assessment_type_id')
            ->where('assessment_weights.school_id', $student->school_id)
            ->whereNull('assessment_weights.subject_id')
            ->select('assessment_types.id as type_id', 'assessment_types.key', 'assessment_weights.weight_percentage')
            ->get()
            ->keyBy('type_id');

        if ($weights->isEmpty()) {
            return [];
        }

        $subjectIds = DB::table('subject_term_results')
            ->where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->pluck('subject_id')
            ->all();

        if (empty($subjectIds)) {
            return [];
        }

        $rows = DB::table('assessment_results')
            ->join('assessments', 'assessments.id', '=', 'assessment_results.assessment_id')
            ->where('assessment_results.student_id', $student->id)
            ->where('assessments.term_id', $term->id)
            ->whereIn('assessments.subject_id', $subjectIds)
            ->select(
                'assessments.subject_id',
                'assessments.assessment_type_id',
                'assessments.total_marks',
                'assessment_results.total_obtained'
            )
            ->get();

        $buckets = [];

        foreach ($rows as $r) {
            $type = $weights->get($r->assessment_type_id);

            if (!$type) {
                continue;
            }

            $bucketKey = $type->key === 'end_term' ? 'exam' : 'ca';

            $buckets[$r->subject_id][$bucketKey]['obtained'] =
                ($buckets[$r->subject_id][$bucketKey]['obtained'] ?? 0) + (float) $r->total_obtained;
            $buckets[$r->subject_id][$bucketKey]['total'] =
                ($buckets[$r->subject_id][$bucketKey]['total'] ?? 0) + (int) $r->total_marks;
        }

        $out = [];

        foreach ($buckets as $subjectId => $sides) {
            $ca = $sides['ca'] ?? null;
            $exam = $sides['exam'] ?? null;

            $out[$subjectId] = [
                'ca' => ($ca && $ca['total'] > 0)
                    ? round(($ca['obtained'] / $ca['total']) * 100, 1)
                    : null,
                'ca_out_of' => $ca['total'] ?? null,
                'exam' => ($exam && $exam['total'] > 0)
                    ? round(($exam['obtained'] / $exam['total']) * 100, 1)
                    : null,
                'exam_out_of' => $exam['total'] ?? null,
            ];
        }

        return $out;
    }
}