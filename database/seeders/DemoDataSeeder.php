<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
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
use App\Models\TermResult;
use App\Models\User;
use App\Services\AssessmentCalculationEngine;
use App\Services\DefaultRolePermissionsService;
use App\Services\StudentNumberGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- School + admin ----------
        $school = School::create([
            'name' => 'Kamuzu Academy',
            'institution_type' => 'secondary',
            'country' => 'Malawi',
            'address' => 'Lilongwe, Malawi',
            'term_structure' => '3_term',
            'setup_completed' => true,
            'setup_step' => 5,
        ]);

        $admin = User::create([
            'school_id' => $school->id,
            'name' => 'Ryan Banda',
            'email' => 'admin@protech.test',
            'role' => 'school_admin',
            'phone' => '+265 999 000 000',
            'password' => Hash::make('password'),
        ]);

        $teacher = User::create([
            'school_id' => $school->id,
            'name' => 'Mr. Phiri',
            'email' => 'teacher@protech.test',
            'role' => 'teacher',
            'phone' => '+265 999 000 001',
            'password' => Hash::make('password'),
        ]);

        $englishTeacher = User::create([
            'school_id' => $school->id,
            'name' => 'Mrs. Zulu',
            'email' => 'teacher2@protech.test',
            'role' => 'teacher',
            'phone' => '+265 999 000 002',
            'password' => Hash::make('password'),
        ]);

        // ---------- Academic year + term ----------
        $year = AcademicYear::create([
            'school_id' => $school->id,
            'label' => '2026',
            'is_current' => true,
        ]);
        $term = Term::create([
            'academic_year_id' => $year->id,
            'name' => 'Term 2',
            'sequence' => 2,
            'is_current' => true,
        ]);

        // ---------- Classes ----------
        $classroom = Classroom::create([
            'school_id' => $school->id,
            'name' => 'Form 2A',
            'grade_level' => 'Form 2',
            'homeroom_teacher_id' => $teacher->id,
        ]);

        // ---------- Subjects ----------
        $math = Subject::create(['school_id' => $school->id, 'name' => 'Mathematics', 'code' => 'MATH101', 'is_compulsory' => true]);
        $eng = Subject::create(['school_id' => $school->id, 'name' => 'English', 'code' => 'ENG101', 'is_compulsory' => true]);
        $classroom->subjects()->attach([$math->id => ['teacher_id' => $teacher->id], $eng->id => ['teacher_id' => $englishTeacher->id]]);

        // ---------- Assessment types + weights ----------
        $ca = AssessmentType::create(['school_id' => $school->id, 'key' => 'continuous_assessment', 'name' => 'Continuous Assessment']);
        $exam = AssessmentType::create(['school_id' => $school->id, 'key' => 'end_term', 'name' => 'End-of-Term Examination']);

        AssessmentWeight::create(['school_id' => $school->id, 'assessment_type_id' => $ca->id, 'weight_percentage' => 30]);
        AssessmentWeight::create(['school_id' => $school->id, 'assessment_type_id' => $exam->id, 'weight_percentage' => 70]);

        // ---------- Grading scale ----------
        foreach ([
            ['A', 80, 100, 'Excellent'],
            ['B', 70, 79, 'Very Good'],
            ['C', 60, 69, 'Good'],
            ['D', 50, 59, 'Satisfactory'],
            ['E', 40, 49, 'Needs Improvement'],
            ['F', 0, 39, 'Unsatisfactory'],
        ] as [$letter, $min, $max, $interp]) {
            GradingScale::create([
                'school_id' => $school->id,
                'letter' => $letter,
                'min_score' => $min,
                'max_score' => $max,
                'interpretation' => $interp,
            ]);
        }

        // ---------- Students ----------
        $numberGenerator = app(StudentNumberGenerator::class);
        $names = [
            ['John', 'Banda'],
            ['Grace', 'Phiri'],
            ['Chikondi', 'Mvula'],
            ['Blessings', 'Nyirenda'],
            ['Mercy', 'Chirwa'],
        ];

        $students = collect();
        foreach ($names as [$first, $last]) {
            $student = Student::create([
                'school_id' => $school->id,
                'student_number' => $numberGenerator->next($school),
                'first_name' => $first,
                'last_name' => $last,
                'status' => 'active',
            ]);
            $student->enrollments()->create([
                'class_id' => $classroom->id,
                'academic_year_id' => $year->id,
                'status' => 'active',
            ]);
            $students->push($student);
        }

        // ---------- Parent account, linked to John Banda (first student) ----------
        $parent = User::create([
            'school_id' => $school->id,
            'name' => 'Mrs. Banda',
            'email' => 'parent@protech.test',
            'role' => 'parent',
            'phone' => '+265 999 000 010',
            'password' => Hash::make('password'),
        ]);
        $students->first()->guardians()->attach($parent->id, ['relationship' => 'Mother', 'is_primary_contact' => true]);

        // ---------- Student portal account, linked to John Banda too ----------
        $studentUser = User::create([
            'school_id' => $school->id,
            'name' => 'John Banda',
            'email' => 'student@protech.test',
            'role' => 'student',
            'phone' => null,
            'password' => Hash::make('password'),
        ]);
        $students->first()->update(['user_id' => $studentUser->id]);

        // ---------- Mathematics: one locked, marked exam ----------
        $mathAssessment = $this->createLockedExam(
            $school,
            $term,
            $classroom,
            $math,
            $exam,
            $teacher,
            name: 'Mid-Term Examination',
            questions: [
                ['topic' => 'Algebra', 'difficulty' => 'easy', 'cognitive_level' => 'apply', 'max' => 50],
                ['topic' => 'Probability', 'difficulty' => 'hard', 'cognitive_level' => 'analyse', 'max' => 50],
            ],
        );
        $mathScores = [
            [45, 25],
            [40, 20],
            [35, 15],
            [15, 10],
            [48, 28],
        ]; // [Q1, Q2] per student, out of [50, 50]

        // ---------- English: one locked, marked exam ----------
        $engAssessment = $this->createLockedExam(
            $school,
            $term,
            $classroom,
            $eng,
            $exam,
            $englishTeacher,
            name: 'Mid-Term Examination',
            questions: [
                ['topic' => 'Comprehension', 'difficulty' => 'medium', 'cognitive_level' => 'understand', 'max' => 50],
                ['topic' => 'Essay Writing', 'difficulty' => 'medium', 'cognitive_level' => 'create', 'max' => 50],
            ],
        );
        $engScores = [
            [40, 38],
            [44, 42],
            [30, 28],
            [35, 30],
            [46, 44],
        ];

        $engine = app(AssessmentCalculationEngine::class);
        $mathQuestions = $mathAssessment->questions()->orderBy('question_number')->get();
        $engQuestions = $engAssessment->questions()->orderBy('question_number')->get();

        foreach ($students as $i => $student) {
            StudentMark::create(['student_id' => $student->id, 'question_id' => $mathQuestions[0]->id, 'marks_obtained' => $mathScores[$i][0], 'marked_by' => $teacher->id]);
            StudentMark::create(['student_id' => $student->id, 'question_id' => $mathQuestions[1]->id, 'marks_obtained' => $mathScores[$i][1], 'marked_by' => $teacher->id]);
            $engine->calculateAssessmentResult($student, $mathAssessment);
            $engine->calculateSubjectTermResult($student, $math, $term);

            StudentMark::create(['student_id' => $student->id, 'question_id' => $engQuestions[0]->id, 'marks_obtained' => $engScores[$i][0], 'marked_by' => $englishTeacher->id]);
            StudentMark::create(['student_id' => $student->id, 'question_id' => $engQuestions[1]->id, 'marks_obtained' => $engScores[$i][1], 'marked_by' => $englishTeacher->id]);
            $engine->calculateAssessmentResult($student, $engAssessment);
            $engine->calculateSubjectTermResult($student, $eng, $term);
        }

        // ---------- Term results (overall average, position, promotion) ----------
        $termAverages = $students->mapWithKeys(function ($student) use ($term) {
            $avg = $student->subjectTermResults()->where('term_id', $term->id)->avg('final_percentage');
            return [$student->id => round($avg ?? 0, 2)];
        });

        $ranked = $termAverages->sortDesc()->keys()->values(); // best to worst

        foreach ($students as $student) {
            $overall = $termAverages->get($student->id);
            $position = $ranked->search($student->id) + 1;

            TermResult::create([
                'student_id' => $student->id,
                'term_id' => $term->id,
                'class_id' => $classroom->id,
                'overall_percentage' => $overall,
                'achievement_level' => match (true) {
                    $overall >= 90 => 'Exceptional',
                    $overall >= 80 => 'Advanced',
                    $overall >= 70 => 'Proficient',
                    $overall >= 50 => 'Developing',
                    default => 'Beginning',
                },
                'position' => $position,
                'class_size' => $students->count(),
                'attendance_percentage' => rand(85, 98),
                'promotion_status' => $overall >= 50 ? 'promoted' : 'not_promoted',
                'promotion_reason' => $overall >= 50 ? 'Overall average meets promotion threshold.' : 'Overall average below the required 50%.',
            ]);
        }

        // ---------- Role permissions ----------
        app(DefaultRolePermissionsService::class)->seedForSchool($school);

        $this->command->info('Demo data seeded successfully.');
        $this->command->line('Admin:   admin@protech.test / password');
        $this->command->line('Teacher: teacher@protech.test / password');
        $this->command->line('Parent:  parent@protech.test / password');
        $this->command->line('Student: student@protech.test / password');
    }

    /** Helper: creates a locked assessment with N questions, avoids repeating this block per subject */
    private function createLockedExam($school, $term, $classroom, $subject, $assessmentType, $createdBy, string $name, array $questions): Assessment
    {
        $totalMarks = array_sum(array_column($questions, 'max'));

        $assessment = Assessment::create([
            'school_id' => $school->id,
            'term_id' => $term->id,
            'class_id' => $classroom->id,
            'subject_id' => $subject->id,
            'assessment_type_id' => $assessmentType->id,
            'created_by' => $createdBy->id,
            'name' => $name,
            'category' => 'summative',
            'total_marks' => $totalMarks,
            'duration_minutes' => 120,
            'pass_mark' => 40,
            'contribution_percentage' => 70,
            'status' => 'locked',
            'locked_at' => now(),
        ]);

        foreach ($questions as $i => $q) {
            Question::create([
                'assessment_id' => $assessment->id,
                'question_number' => $i + 1,
                'question_text' => "Sample {$q['topic']} question " . ($i + 1),
                'topic' => $q['topic'],
                'difficulty' => $q['difficulty'],
                'cognitive_level' => $q['cognitive_level'],
                'question_type' => 'short_answer',
                'max_marks' => $q['max'],
            ]);
        }

        return $assessment;
    }
}