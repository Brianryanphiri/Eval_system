<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\AssessmentWeight;
use App\Models\Classroom;
use App\Models\GradingScale;
use App\Models\Question;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentMark;
use App\Models\Subject;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\AssessmentCalculationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentCalculationEngineTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolWithTermAndClass(): array
    {
        $school = School::create(['name' => 'Test School', 'institution_type' => 'secondary', 'country' => 'Malawi']);
        $year = AcademicYear::create(['school_id' => $school->id, 'label' => '2026', 'is_current' => true]);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Term 1', 'sequence' => 1, 'is_current' => true]);
        $teacher = User::create(['school_id' => $school->id, 'name' => 'T', 'email' => 't@t.test', 'role' => 'teacher', 'password' => bcrypt('x')]);
        $classroom = Classroom::create(['school_id' => $school->id, 'name' => 'Form 1A']);

        foreach ([['A', 80, 100, 'Excellent'], ['B', 70, 79, 'Very Good'], ['F', 0, 39, 'Fail']] as [$l, $mi, $ma, $i]) {
            GradingScale::create(['school_id' => $school->id, 'letter' => $l, 'min_score' => $mi, 'max_score' => $ma, 'interpretation' => $i]);
        }

        return compact('school', 'year', 'term', 'teacher', 'classroom');
    }

    /** @test */
    public function it_sums_question_marks_into_a_percentage_and_correct_grade()
    {
        ['school' => $school, 'term' => $term, 'teacher' => $teacher, 'classroom' => $classroom] = $this->makeSchoolWithTermAndClass();

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
            'pass_mark' => 40,
            'status' => 'locked',
        ]);

        $q1 = Question::create(['assessment_id' => $assessment->id, 'question_number' => 1, 'question_text' => 'Q1', 'max_marks' => 60]);
        $q2 = Question::create(['assessment_id' => $assessment->id, 'question_number' => 2, 'question_text' => 'Q2', 'max_marks' => 40]);

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-1', 'first_name' => 'John', 'last_name' => 'Banda', 'status' => 'active']);

        StudentMark::create(['student_id' => $student->id, 'question_id' => $q1->id, 'marks_obtained' => 50]);
        StudentMark::create(['student_id' => $student->id, 'question_id' => $q2->id, 'marks_obtained' => 20]);
        // Total: 70/100 = 70% -> Grade B

        $result = (new AssessmentCalculationEngine())->calculateAssessmentResult($student, $assessment);

        $this->assertEquals(70, $result->total_obtained);
        $this->assertEquals(70.00, $result->percentage);
        $this->assertEquals('B', $result->grade_letter);
        $this->assertTrue($result->passed); // 70% >= 40% pass mark
    }

    /** @test */
    public function it_fails_a_student_below_the_pass_mark()
    {
        ['school' => $school, 'term' => $term, 'teacher' => $teacher, 'classroom' => $classroom] = $this->makeSchoolWithTermAndClass();

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
            'pass_mark' => 40,
            'status' => 'locked',
        ]);

        $q1 = Question::create(['assessment_id' => $assessment->id, 'question_number' => 1, 'question_text' => 'Q1', 'max_marks' => 100]);
        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-2', 'first_name' => 'Weak', 'last_name' => 'Student', 'status' => 'active']);

        StudentMark::create(['student_id' => $student->id, 'question_id' => $q1->id, 'marks_obtained' => 30]); // 30% < 40% pass mark

        $result = (new AssessmentCalculationEngine())->calculateAssessmentResult($student, $assessment);

        $this->assertEquals(30.00, $result->percentage);
        $this->assertFalse($result->passed);
        $this->assertEquals('F', $result->grade_letter);
    }

    /** @test */
    public function it_blends_multiple_assessments_by_weight_matching_the_docs_example()
    {
        // Section 12 example: Assignment 18/20 (20%) + Midterm 55/100 (30%) + Final 72/100 (50%) = 70.5%
        ['school' => $school, 'term' => $term, 'teacher' => $teacher, 'classroom' => $classroom] = $this->makeSchoolWithTermAndClass();

        $subject = Subject::create(['school_id' => $school->id, 'name' => 'Maths', 'is_compulsory' => true]);

        $assignmentType = AssessmentType::create(['school_id' => $school->id, 'key' => 'assignments', 'name' => 'Assignments']);
        $midtermType = AssessmentType::create(['school_id' => $school->id, 'key' => 'mid_term', 'name' => 'Mid-Term']);
        $finalType = AssessmentType::create(['school_id' => $school->id, 'key' => 'final_exam', 'name' => 'Final']);

        AssessmentWeight::create(['school_id' => $school->id, 'assessment_type_id' => $assignmentType->id, 'weight_percentage' => 20]);
        AssessmentWeight::create(['school_id' => $school->id, 'assessment_type_id' => $midtermType->id, 'weight_percentage' => 30]);
        AssessmentWeight::create(['school_id' => $school->id, 'assessment_type_id' => $finalType->id, 'weight_percentage' => 50]);

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-3', 'first_name' => 'Blend', 'last_name' => 'Test', 'status' => 'active']);

        $engine = new AssessmentCalculationEngine();

        foreach ([
            [$assignmentType, 20, 18],
            [$midtermType, 100, 55],
            [$finalType, 100, 72],
        ] as [$type, $total, $obtained]) {
            $assessment = Assessment::create([
                'school_id' => $school->id,
                'term_id' => $term->id,
                'class_id' => $classroom->id,
                'subject_id' => $subject->id,
                'assessment_type_id' => $type->id,
                'created_by' => $teacher->id,
                'name' => $type->name,
                'category' => 'summative',
                'total_marks' => $total,
                'duration_minutes' => 60,
                'status' => 'locked',
            ]);
            $q = Question::create(['assessment_id' => $assessment->id, 'question_number' => 1, 'question_text' => 'Q', 'max_marks' => $total]);
            StudentMark::create(['student_id' => $student->id, 'question_id' => $q->id, 'marks_obtained' => $obtained]);
            $engine->calculateAssessmentResult($student, $assessment);
        }

        $result = $engine->calculateSubjectTermResult($student, $subject, $term);

        $this->assertEquals(70.5, $result->final_percentage);
    }

    /** @test */
    public function it_handles_a_student_with_no_marks_at_all_without_erroring()
    {
        ['school' => $school, 'term' => $term, 'teacher' => $teacher, 'classroom' => $classroom] = $this->makeSchoolWithTermAndClass();

        $subject = Subject::create(['school_id' => $school->id, 'name' => 'Maths', 'is_compulsory' => true]);
        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-4', 'first_name' => 'No', 'last_name' => 'Marks', 'status' => 'active']);

        $result = (new AssessmentCalculationEngine())->calculateSubjectTermResult($student, $subject, $term);

        $this->assertEquals(0, $result->final_percentage);
        $this->assertFalse($result->passed);
    }
}