<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Services\TopicMasteryAnalyzer;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index(Request $request, TopicMasteryAnalyzer $analyzer)
    {
        $teacher = $request->user();

        $classroomIds = Classroom::where('school_id', $teacher->school_id)
            ->whereHas('subjects', fn($q) => $q->where('class_subject.teacher_id', $teacher->id))
            ->orWhere('homeroom_teacher_id', $teacher->id)
            ->pluck('id');

        $classroom = Classroom::whereIn('id', $classroomIds)->first();

        if (!$classroom) {
            return view('teacher.dashboard', [
                'insightTopics' => [],
                'needsAttention' => [],
                'excelling' => [],
                'insight' => null,
            ]);
        }

        $currentTerm = $teacher->school->currentAcademicYear()?->currentTerm();

        $students = $classroom->currentStudents()->with('termResults')->get();

        $needsAttention = $students
            ->filter(fn($s) => ($s->termResults->where('term_id', $currentTerm?->id)->first()?->overall_percentage ?? 100) < 50)
            ->map(fn($s) => [$s->full_name, 'Below pass threshold this term'])
            ->values()->all();

        $excelling = $students
            ->filter(fn($s) => ($s->termResults->where('term_id', $currentTerm?->id)->first()?->overall_percentage ?? 0) >= 80)
            ->map(fn($s) => $s->full_name)
            ->values()->all();

        $insightTopics = [];
        $insight = null;

        if ($currentTerm) {
            $topicMastery = $analyzer->topicMasteryForClassroom($classroom, $currentTerm);
            $insightTopics = $topicMastery->map(fn($score, $topic) => [$topic, $score])->values()->all();

            $classAverage = $students->avg(fn($s) => $s->termResults->where('term_id', $currentTerm->id)->first()?->overall_percentage ?? 0);
            $insight = $analyzer->lowestMasteryInsight($topicMastery, round($classAverage, 1));
        }

        return view('teacher.dashboard', compact('insightTopics', 'needsAttention', 'excelling', 'insight'));
    }
}