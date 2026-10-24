<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\Question;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkingLockTest extends TestCase
{
    use RefreshDatabase;

    private function setupAssessment(string $status = 'draft'): array
    {
        $school = School::create(['name' => 'S', 'institution_type' => 'secondary', 'country' => 'Malawi', 'setup_completed' => true, 'setup_step' => 5]);
        $year = AcademicYear::create(['school_id' => $school->id, 'label' => '2026', 'is_current' => true]);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Term 1', 'sequence' => 1, 'is_current' => true]);
        $teacher = User::create(['school_id' => $school->id, 'name' => 'T', 'email' => 't@t.test', 'role' => 'teacher', 'password' => bcrypt('x')]);
        $classroom = Classroom::create(['school_id' => $school->id, 'name' => 'Form 1A']);
        $subject = Subject::create(['school_id' => $school->id, 'name' => 'Maths', 'is_compulsory' => true]);
        $type = AssessmentType::create(['school_id' => $school->id, 'key' => 'end_term', 'name' => 'End Term']);

        $assessment = Assessment::create([
            'school_id' => $school->id,
            'term_id' => $term->id,
            'class_id' => $classroom->id,
            'subject_id' => $subject->id,
            'assessment_type_id' => $type->id,
            'created_by' => $teacher->id,
            'name' => 'Exam',
            'category' => 'summative',
            'total_marks' => 100,
            'duration_minutes' => 60,
            'status' => $status,
        ]);

        $question = Question::create(['assessment_id' => $assessment->id, 'question_number' => 1, 'question_text' => 'Q1', 'max_marks' => 100]);
        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-1', 'first_name' => 'John', 'last_name' => 'Banda', 'status' => 'active']);
        $student->enrollments()->create(['class_id' => $classroom->id, 'academic_year_id' => $year->id, 'status' => 'active']);

        return compact('school', 'teacher', 'assessment', 'question', 'student');
    }

    /** @test */
    public function marks_can_be_saved_on_an_unlocked_assessment()
    {
        ['teacher' => $teacher, 'assessment' => $assessment, 'question' => $question, 'student' => $student] = $this->setupAssessment('draft');

        $response = $this->actingAs($teacher)->post(route('marking.store', $assessment), [
            'marks' => [$student->id => [$question->question_number => 85]],
        ]);

        $response->assertRedirect(route('assessments.show', $assessment));
        $this->assertDatabaseHas('student_marks', ['student_id' => $student->id, 'question_id' => $question->id, 'marks_obtained' => 85]);
        $this->assertDatabaseHas('assessment_results', ['student_id' => $student->id, 'assessment_id' => $assessment->id, 'percentage' => 85.00]);
    }

    /** @test */
    public function marks_cannot_be_edited_on_a_locked_assessment()
    {
        ['teacher' => $teacher, 'assessment' => $assessment, 'question' => $question, 'student' => $student] = $this->setupAssessment('locked');

        $response = $this->actingAs($teacher)->post(route('marking.store', $assessment), [
            'marks' => [$student->id => [$question->question_number => 85]],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('student_marks', ['student_id' => $student->id, 'question_id' => $question->id]);
    }

    /** @test */
    public function locking_an_assessment_sets_status_and_timestamp()
    {
        ['teacher' => $teacher, 'assessment' => $assessment] = $this->setupAssessment('draft');

        $response = $this->actingAs($teacher)->post(route('marking.lock', $assessment));

        $response->assertRedirect();
        $assessment->refresh();
        $this->assertEquals('locked', $assessment->status);
        $this->assertNotNull($assessment->locked_at);
    }
}