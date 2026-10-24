<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\PromotionRule;
use App\Models\PromotionRuleCondition;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTermResult;
use App\Models\Term;
use App\Services\PromotionEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionEngineTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAndTerm(): array
    {
        $school = School::create(['name' => 'Test School', 'institution_type' => 'secondary', 'country' => 'Malawi']);
        $year = AcademicYear::create(['school_id' => $school->id, 'label' => '2026', 'is_current' => true]);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Term 1', 'sequence' => 1, 'is_current' => true]);

        return compact('school', 'term');
    }

    /** @test */
    public function overall_average_rule_promotes_a_student_who_meets_the_threshold()
    {
        ['school' => $school, 'term' => $term] = $this->makeSchoolAndTerm();

        PromotionRule::create(['school_id' => $school->id, 'rule_type' => 'overall_average', 'min_overall_average' => 50]);

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-1', 'first_name' => 'Pass', 'last_name' => 'Student', 'status' => 'active']);
        $subject = Subject::create(['school_id' => $school->id, 'name' => 'Maths', 'is_compulsory' => true]);

        $subjectResults = collect([
            SubjectTermResult::create([
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'term_id' => $term->id,
                'final_percentage' => 65,
                'passed' => true,
            ]),
        ]);

        [$status, $reason] = (new PromotionEngine())->evaluate($student, $term, $subjectResults);

        $this->assertEquals('promoted', $status);
    }

    /** @test */
    public function overall_average_rule_marks_not_promoted_when_far_below_threshold()
    {
        ['school' => $school, 'term' => $term] = $this->makeSchoolAndTerm();

        PromotionRule::create(['school_id' => $school->id, 'rule_type' => 'overall_average', 'min_overall_average' => 50]);

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-2', 'first_name' => 'Fail', 'last_name' => 'Student', 'status' => 'active']);
        $subject = Subject::create(['school_id' => $school->id, 'name' => 'Maths', 'is_compulsory' => true]);

        $subjectResults = collect([
            SubjectTermResult::create([
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'term_id' => $term->id,
                'final_percentage' => 30,
                'passed' => false,
            ]),
        ]);

        [$status, $reason] = (new PromotionEngine())->evaluate($student, $term, $subjectResults);

        $this->assertEquals('not_promoted', $status);
        $this->assertStringContainsString('30', $reason);
    }

    /** @test */
    public function overall_average_rule_marks_conditional_when_close_to_threshold()
    {
        ['school' => $school, 'term' => $term] = $this->makeSchoolAndTerm();

        PromotionRule::create(['school_id' => $school->id, 'rule_type' => 'overall_average', 'min_overall_average' => 50]);

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-3', 'first_name' => 'Close', 'last_name' => 'Student', 'status' => 'active']);
        $subject = Subject::create(['school_id' => $school->id, 'name' => 'Maths', 'is_compulsory' => true]);

        $subjectResults = collect([
            SubjectTermResult::create([
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'term_id' => $term->id,
                'final_percentage' => 47,
                'passed' => false, // within 5 points of 50 threshold
            ]),
        ]);

        [$status, $reason] = (new PromotionEngine())->evaluate($student, $term, $subjectResults);

        $this->assertEquals('conditional', $status);
    }

    /** @test */
    public function all_compulsory_rule_fails_when_a_compulsory_subject_is_not_passed()
    {
        ['school' => $school, 'term' => $term] = $this->makeSchoolAndTerm();

        PromotionRule::create(['school_id' => $school->id, 'rule_type' => 'all_compulsory']);

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-4', 'first_name' => 'Mixed', 'last_name' => 'Student', 'status' => 'active']);
        $compulsorySubject = Subject::create(['school_id' => $school->id, 'name' => 'Maths', 'is_compulsory' => true]);
        $electiveSubject = Subject::create(['school_id' => $school->id, 'name' => 'Art', 'is_compulsory' => false]);

        $subjectResults = collect([
            SubjectTermResult::create(['student_id' => $student->id, 'subject_id' => $compulsorySubject->id, 'term_id' => $term->id, 'final_percentage' => 30, 'passed' => false]),
            SubjectTermResult::create(['student_id' => $student->id, 'subject_id' => $electiveSubject->id, 'term_id' => $term->id, 'final_percentage' => 90, 'passed' => true]),
        ]);

        [$status, $reason] = (new PromotionEngine())->evaluate($student, $term, $subjectResults);

        $this->assertEquals('not_promoted', $status);
        $this->assertStringContainsString('Maths', $reason);
    }

    /** @test */
    public function subject_specific_condition_blocks_promotion_even_with_good_average()
    {
        ['school' => $school, 'term' => $term] = $this->makeSchoolAndTerm();

        $rule = PromotionRule::create(['school_id' => $school->id, 'rule_type' => 'overall_average', 'min_overall_average' => 50]);

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-5', 'first_name' => 'Edge', 'last_name' => 'Case', 'status' => 'active']);
        $maths = Subject::create(['school_id' => $school->id, 'name' => 'Mathematics', 'is_compulsory' => true]);
        $english = Subject::create(['school_id' => $school->id, 'name' => 'English', 'is_compulsory' => true]);

        PromotionRuleCondition::create(['promotion_rule_id' => $rule->id, 'subject_id' => $maths->id, 'operator' => '>=', 'value' => 40]);

        // Average is 65% (well above the 50% threshold) but Maths itself is only 25%
        $subjectResults = collect([
            SubjectTermResult::create(['student_id' => $student->id, 'subject_id' => $maths->id, 'term_id' => $term->id, 'final_percentage' => 25, 'passed' => false]),
            SubjectTermResult::create(['student_id' => $student->id, 'subject_id' => $english->id, 'term_id' => $term->id, 'final_percentage' => 100, 'passed' => true]),
        ]);

        [$status, $reason] = (new PromotionEngine())->evaluate($student, $term, $subjectResults);

        $this->assertEquals('not_promoted', $status);
        $this->assertStringContainsString('Mathematics', $reason);
    }

    /** @test */
    public function board_decision_rule_returns_pending_rather_than_guessing()
    {
        ['school' => $school, 'term' => $term] = $this->makeSchoolAndTerm();

        PromotionRule::create(['school_id' => $school->id, 'rule_type' => 'board_decision']);

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-6', 'first_name' => 'Board', 'last_name' => 'Case', 'status' => 'active']);

        [$status, $reason] = (new PromotionEngine())->evaluate($student, $term, collect());

        $this->assertEquals('pending', $status);
    }

    /** @test */
    public function it_returns_pending_when_no_promotion_rule_is_configured()
    {
        ['school' => $school, 'term' => $term] = $this->makeSchoolAndTerm();

        $student = Student::create(['school_id' => $school->id, 'student_number' => 'ST-7', 'first_name' => 'No', 'last_name' => 'Rule', 'status' => 'active']);

        [$status, $reason] = (new PromotionEngine())->evaluate($student, $term, collect());

        $this->assertEquals('pending', $status);
    }
}