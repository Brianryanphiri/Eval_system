<?php

namespace App\Http\Controllers;

use App\Models\AssessmentType;
use App\Models\AssessmentWeight;
use App\Models\GradingScale;
use App\Models\PromotionRule;
use App\Models\PromotionRuleCondition;
use App\Models\School;
use App\Models\Subject;
use App\Services\DefaultRolePermissionsService;
use Illuminate\Http\Request;

class SetupWizardController extends Controller
{
    public function step1()
    {
        return view('setup.step1');
    }

    public function storeStep1(Request $request)
    {
        $data = $request->validate([
            'institution_type' => 'required|in:primary,secondary,international,college',
            'academic_year' => 'required|string',
            'term_structure' => 'required|in:3_term,2_semester,4_quarter,custom',
        ]);

        $school = $request->user()->school;
        $school->update([
            'institution_type' => $data['institution_type'],
            'term_structure' => $data['term_structure'],
            'setup_step' => 2,
        ]);

        $school->academicYears()->firstOrCreate(
            ['label' => $data['academic_year']],
            ['is_current' => true]
        );

        return redirect()->route('setup.step2');
    }

    public function step2()
    {
        return view('setup.step2');
    }

    public function storeStep2(Request $request)
    {
        $data = $request->validate([
            'assessment_types' => 'required|array|min:1',
            'assessment_types.*' => 'string',
        ]);

        $school = $request->user()->school;

        $labels = [
            'continuous_assessment' => 'Continuous Assessment',
            'class_tests' => 'Class Tests',
            'assignments' => 'Assignments',
            'projects' => 'Projects',
            'mid_term' => 'Mid-Term Examinations',
            'end_term' => 'End-of-Term Examinations',
            'practical' => 'Practical Assessments',
            'oral' => 'Oral Assessments',
            'coursework' => 'Coursework',
            'final_exam' => 'Final Examinations',
            'competency' => 'Competency-Based Assessment',
        ];

        foreach ($data['assessment_types'] as $key) {
            AssessmentType::firstOrCreate(
                ['school_id' => $school->id, 'key' => $key],
                ['name' => $labels[$key] ?? ucfirst($key)]
            );
        }

        $school->update(['setup_step' => 3]);

        return redirect()->route('setup.step3');
    }

    public function step3(Request $request)
    {
        $selectedTypes = $request->user()->school->assessmentTypes()
            ->pluck('name', 'key');

        return view('setup.step3', ['selectedTypes' => $selectedTypes]);
    }

    public function storeStep3(Request $request)
    {
        $data = $request->validate([
            'weights' => 'required|array',
            'weights.*' => 'required|numeric|min:0|max:100',
        ]);

        if (array_sum($data['weights']) != 100) {
            return back()->withErrors(['weights' => 'Weights must add up to exactly 100%.']);
        }

        $school = $request->user()->school;

        foreach ($data['weights'] as $typeKey => $weight) {
            $type = AssessmentType::where('school_id', $school->id)->where('key', $typeKey)->first();
            if (!$type)
                continue;

            AssessmentWeight::updateOrCreate(
                ['school_id' => $school->id, 'assessment_type_id' => $type->id, 'subject_id' => null],
                ['weight_percentage' => $weight]
            );
        }

        $school->update(['setup_step' => 4]);

        return redirect()->route('setup.step4');
    }

    public function step4()
    {
        return view('setup.step4');
    }

    public function storeStep4(Request $request)
    {
        $data = $request->validate([
            'grades' => 'required|array|min:1',
            'grades.*.letter' => 'required|string',
            'grades.*.min' => 'required|integer|min:0|max:100',
            'grades.*.max' => 'required|integer|min:0|max:100',
            'grades.*.interpretation' => 'required|string',
        ]);

        $school = $request->user()->school;
        $school->gradingScales()->delete(); // replace scale wholesale rather than merge

        foreach ($data['grades'] as $grade) {
            GradingScale::create([
                'school_id' => $school->id,
                'letter' => $grade['letter'],
                'min_score' => $grade['min'],
                'max_score' => $grade['max'],
                'interpretation' => $grade['interpretation'],
            ]);
        }

        $school->update(['setup_step' => 5]);

        return redirect()->route('setup.step5');
    }

    public function step5()
    {
        return view('setup.step5');
    }

    public function storeStep5(Request $request, DefaultRolePermissionsService $rolePermissions)
    {
        $data = $request->validate([
            'promotion_rule' => 'required|in:overall_average,all_compulsory,min_passed,credit_based,board_decision,custom',
            'min_overall_average' => 'nullable|integer|min:0|max:100',
            'max_failed_subjects' => 'nullable|integer|min:0',
            'subject_conditions' => 'nullable|array',
            'subject_conditions.*.subject' => 'nullable|string',
            'subject_conditions.*.operator' => 'nullable|in:>=,>',
            'subject_conditions.*.value' => 'nullable|integer|min:0|max:100',
        ]);

        $school = $request->user()->school;

        $rule = PromotionRule::create([
            'school_id' => $school->id,
            'rule_type' => $data['promotion_rule'],
            'min_overall_average' => $data['min_overall_average'] ?? null,
            'max_failed_subjects' => $data['max_failed_subjects'] ?? null,
        ]);

        foreach ($data['subject_conditions'] ?? [] as $condition) {
            if (empty($condition['subject']) || empty($condition['value'])) {
                continue;
            }

            $subject = Subject::firstOrCreate(
                ['school_id' => $school->id, 'name' => $condition['subject']],
                ['is_compulsory' => true]
            );

            PromotionRuleCondition::create([
                'promotion_rule_id' => $rule->id,
                'subject_id' => $subject->id,
                'operator' => $condition['operator'] ?? '>=',
                'value' => $condition['value'],
            ]);
        }

        // Seed this school's default role -> permission grants now that setup is complete
        $rolePermissions->seedForSchool($school);

        $school->update(['setup_step' => 5, 'setup_completed' => true]);

        return redirect()->route('dashboard')->with('success', 'School setup complete! Welcome to Pro Tech Solutions.');
    }
}