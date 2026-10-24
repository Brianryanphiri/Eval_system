<?php

namespace App\Http\Controllers;

use App\Mail\GuardianProgressUpdate;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Intervention;
use App\Models\Student;
use App\Services\StudentNarrativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StudentProfileController extends Controller
{
    public function show(Student $student, StudentNarrativeService $narrativeService)
    {
        abort_unless($student->school_id === auth()->user()->school_id, 403);

        $currentTerm = $student->school->currentAcademicYear()?->currentTerm();

        // ---------- Subject results ----------
        $subjectResults = $currentTerm
            ? $student->subjectTermResults()->where('term_id', $currentTerm->id)->with('subject')->get()
            : collect();

        $subjects = $subjectResults->map(fn($r) => [
            $r->subject->name,
            $r->final_percentage,
            ($r->change_from_previous >= 0 ? '+' : '') . $r->change_from_previous,
            $r->trendDirection(),
        ])->all();

        $termResult = $currentTerm
            ? $student->termResults()->where('term_id', $currentTerm->id)->first()
            : null;

        [$strengths, $weaknesses] = $currentTerm
            ? $narrativeService->strengthsAndWeaknesses($student, $currentTerm)
            : [[], []];

        $learningReport = $currentTerm
            ? $narrativeService->learningReport($student, $termResult, $subjectResults)
            : "{$student->full_name} has not been assigned to a current term yet.";

        // ---------- Header stats object ----------
        $studentData = (object) [
            'id' => $student->id,
            'student_number' => $student->student_number,
            'name' => $student->full_name,
            'class' => $student->currentClassroom()?->name ?? '—',
            'average' => $termResult?->overall_percentage ?? 0,
            'position' => ($termResult && \App\Services\PeerComparison::showRank())
                ? "{$termResult->position}/{$termResult->class_size}"
                : null,
            'attendance' => $termResult?->attendance_percentage ?? 0,
            'photo_url' => $student->photoUrl(),
            'achievement_level' => $termResult?->achievement_level ?? 'Not yet assessed',
        ];

        // ---------- Risk detection ----------
        $risk = $this->computeRisk($student, $currentTerm, $subjectResults);

        // ---------- Intervention history ----------
        $interventions = Intervention::where('student_id', $student->id)
            ->with('subject')
            ->latest('start_date')
            ->limit(6)
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'area' => $i->area,
                'type' => str_replace('_', ' ', $i->intervention_type),
                'status' => $i->status,
                'priority' => $i->priority,
                'started' => $i->start_date?->format('d M') ?? '—',
                'review' => $i->review_date?->format('d M') ?? '—',
                'score_note' => $i->score_after !== null
                    ? "{$i->score_before}% to {$i->score_after}%"
                    : "Target {$i->score_target}%",
            ])->all();

        // ---------- Guardians ----------
        $guardians = $student->guardians->map(fn($g) => [
            'id' => $g->id,
            'name' => $g->name,
            'email' => $g->email,
            'relationship' => $g->pivot->relationship ?? 'Guardian',
            'is_primary' => (bool) $g->pivot->is_primary_contact,
            'last_contact' => $g->pivot->last_contacted_at
                ? \Carbon\Carbon::parse($g->pivot->last_contacted_at)->diffForHumans()
                : 'Never contacted',
        ])->all();

        // ---------- Missing assessments ----------
        $missingAssessments = $this->missingAssessments($student, $currentTerm);

        return view('students.profile', [
            'student' => $studentData,
            'studentModel' => $student,
            'subjects' => $subjects,
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'learningReport' => $learningReport,
            'risk' => $risk,
            'interventions' => $interventions,
            'guardians' => $guardians,
            'missingAssessments' => $missingAssessments,
            'currentTerm' => $currentTerm,
        ]);
    }

    /**
     * Compute live risk level + reasons from real data.
     */
    private function computeRisk(Student $student, $currentTerm, $subjectResults): array
    {
        $reasons = [];

        foreach ($subjectResults as $r) {
            if ($r->final_percentage < 50) {
                $reasons[] = "{$r->subject->name} is below 50% ({$r->final_percentage}%)";
            }
        }

        $recent = AssessmentResult::where('student_id', $student->id)
            ->latest('id')->limit(6)->pluck('percentage')->all();

        $declines = 0;
        for ($i = 0; $i < count($recent) - 1; $i++) {
            if ($recent[$i] < $recent[$i + 1]) {
                $declines++;
            }
        }
        if ($declines >= 2) {
            $reasons[] = "Declining across last {$declines} assessments";
        }

        $missing = $this->missingAssessments($student, $currentTerm);
        if ($missing->isNotEmpty()) {
            $reasons[] = "{$missing->count()} assessment(s) not marked";
        }

        $level = count($reasons) >= 2 ? 'at_risk' : (count($reasons) === 1 ? 'watch' : 'ok');

        return ['level' => $level, 'reasons' => $reasons];
    }

    /**
     * Expected vs. recorded assessments for the current term.
     */
    private function missingAssessments(Student $student, $currentTerm)
    {
        if (!$currentTerm) {
            return collect();
        }

        $classroom = $student->currentClassroom();
        if (!$classroom) {
            return collect();
        }

        $expectedIds = Assessment::where('class_id', $classroom->id)
            ->where('term_id', $currentTerm->id)
            ->pluck('id');

        $markedIds = AssessmentResult::where('student_id', $student->id)
            ->whereIn('assessment_id', $expectedIds)
            ->pluck('assessment_id');

        return Assessment::whereIn('id', $expectedIds->diff($markedIds))
            ->with('subject')
            ->get();
    }

    /**
     * Send a progress-update email to a specific guardian.
     */
    public function emailGuardian(Request $request, Student $student)
    {
        abort_unless($student->school_id === auth()->user()->school_id, 403);

        $data = $request->validate([
            'guardian_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:2000',
        ]);

        $guardian = $student->guardians()->where('users.id', $data['guardian_id'])->firstOrFail();

        Mail::to($guardian->email)->queue(
            new GuardianProgressUpdate($student, $data['message'] ?? null)
        );

        $student->guardians()->updateExistingPivot($guardian->id, [
            'last_contacted_at' => now(),
        ]);

        return back()->with('success', "Progress update sent to {$guardian->name}.");
    }
}