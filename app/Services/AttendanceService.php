<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Ensure a session exists for a given class + date + period.
     * Returns the session, creating it if needed.
     */
    public function getOrCreateSession(
        int $schoolId,
        int $classId,
        int $termId,
        string $date,
        ?string $period,
        ?int $takenBy = null
    ): AttendanceSession {
        $period = $period ?: 'full-day';

        $session = AttendanceSession::firstOrCreate(
            [
                'school_id' => $schoolId,
                'class_id' => $classId,
                'date' => $date,
                'period' => $period,
            ],
            [
                'term_id' => $termId,
                'taken_by' => $takenBy,
            ]
        );

        // Ensure every active student has a record — default to "present"
        // so the teacher only has to change the exceptions.
        $students = Student::where('school_id', $schoolId)
            ->whereHas('enrollments', function ($q) use ($classId, $termId) {
                $q->where('class_id', $classId)
                    ->where('academic_year_id', function ($sub) use ($termId) {
                        $sub->select('academic_year_id')->from('terms')->where('id', $termId)->limit(1);
                    })
                    ->where('status', 'active');
            })
            ->get();

        foreach ($students as $student) {
            AttendanceRecord::firstOrCreate(
                [
                    'session_id' => $session->id,
                    'student_id' => $student->id,
                ],
                [
                    'school_id' => $schoolId,
                    'status' => 'present',
                ]
            );
        }

        return $session->fresh(['records']);
    }

    /**
     * Save attendance records for a session.
     * $records is an array: [student_id => ['status' => 'present', 'reason' => '...']]
     */
    public function saveSession(AttendanceSession $session, array $records, ?int $markedBy = null): int
    {
        $count = 0;

        DB::transaction(function () use ($session, $records, $markedBy, &$count) {
            foreach ($records as $studentId => $data) {
                $studentId = (int) $studentId;
                $status = $data['status'] ?? 'present';
                if (!in_array($status, ['present', 'absent', 'late', 'excused', 'sick'], true)) {
                    $status = 'present';
                }

                AttendanceRecord::updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'school_id' => $session->school_id,
                        'status' => $status,
                        'minutes_late' => $data['minutes_late'] ?? null,
                        'reason' => $data['reason'] ?? null,
                    ]
                );

                $count++;
            }

            $session->update(['taken_by' => $markedBy ?? $session->taken_by]);
        });

        // Refresh summaries for this term
        $this->recomputeSummariesForTerm($session->term_id, $session->school_id);

        return $count;
    }

    /**
     * Compute per-student attendance summaries for a term.
     * Writes into the attendance_summaries table (which exists).
     */
    public function recomputeSummariesForTerm(int $termId, int $schoolId): int
    {
        // Get all sessions in this term for this school
        $sessions = AttendanceSession::where('school_id', $schoolId)
            ->where('term_id', $termId)
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $sessionIds = $sessions->pluck('id');

        // Get all records across those sessions
        $records = AttendanceRecord::whereIn('session_id', $sessionIds)->get();

        // Group by student
        $byStudent = $records->groupBy('student_id');

        $count = 0;
        foreach ($byStudent as $studentId => $studentRecords) {
            $total = $studentRecords->count();
            if ($total === 0) {
                continue;
            }

            $present = $studentRecords->where('status', 'present')->count();
            $absent = $studentRecords->where('status', 'absent')->count();
            $late = $studentRecords->where('status', 'late')->count();
            $excused = $studentRecords->where('status', 'excused')->count();
            $sick = $studentRecords->where('status', 'sick')->count();

            // Present + late count toward attendance; excused does not.
            $countedPresent = $present + $late;
            $percentage = $total > 0
                ? round(($countedPresent / $total) * 100, 2)
                : 0;

            DB::table('attendance_summaries')->updateOrInsert(
                [
                    'school_id' => $schoolId,
                    'student_id' => $studentId,
                    'term_id' => $termId,
                ],
                [
                    'days_present' => $present,
                    'days_absent' => $absent,
                    'days_late' => $late,
                    'days_excused' => $excused + $sick,
                    'percentage' => $percentage,
                    'computed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $count++;
        }

        return $count;
    }

    /**
     * Get attendance summary for one student in one term.
     */
    public function getStudentSummary(int $studentId, int $termId): ?object
    {
        return DB::table('attendance_summaries')
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->first();
    }

    /**
     * Get attendance rate per class for a term.
     * Returns: [class_id => percentage, ...]
     */
    public function getClassAttendanceRates(int $schoolId, int $termId): array
    {
        $sessions = AttendanceSession::where('school_id', $schoolId)
            ->where('term_id', $termId)
            ->get();

        if ($sessions->isEmpty()) {
            return [];
        }

        $sessionIds = $sessions->pluck('id');

        $rows = AttendanceRecord::whereIn('session_id', $sessionIds)
            ->join('attendance_sessions', 'attendance_sessions.id', '=', 'attendance_records.session_id')
            ->select(
                'attendance_sessions.class_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN attendance_records.status IN ('present', 'late') THEN 1 ELSE 0 END) as present_count")
            )
            ->groupBy('attendance_sessions.class_id')
            ->get();

        $rates = [];
        foreach ($rows as $row) {
            $rates[$row->class_id] = $row->total > 0
                ? round(($row->present_count / $row->total) * 100, 1)
                : 0;
        }

        return $rates;
    }

    /**
     * Get a student's recent attendance records (last N sessions).
     */
    public function getStudentRecentRecords(int $studentId, int $limit = 30): array
    {
        return AttendanceRecord::with('session.classroom')
            ->where('student_id', $studentId)
            ->orderByDesc(
                AttendanceSession::select('date')
                    ->whereColumn('attendance_sessions.id', 'attendance_records.session_id')
                    ->limit(1)
            )
            ->limit($limit)
            ->get()
            ->map(function ($r) {
                return [
                    'date' => $r->session?->date,
                    'class' => $r->session?->classroom?->name,
                    'status' => $r->status,
                    'status_label' => $r->status_label,
                    'status_color' => $r->status_color,
                    'reason' => $r->reason,
                ];
            })
            ->toArray();
    }
}