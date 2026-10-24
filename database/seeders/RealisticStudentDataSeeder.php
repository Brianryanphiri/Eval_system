<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\DiagnosticResult;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectModalityMap;
use App\Models\SubjectTermResult;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Replaces students 400–1299 (school 3) with realistic, modality-varied
 * synthetic data so the PlacementEngine can demonstrate real behaviour.
 *
 * Preserves students 200–399 (real data) and students 1–6 (school 1).
 */
class RealisticStudentDataSeeder extends Seeder
{
    private const SCHOOL_ID = 3;
    private const TERM_ID = 2;
    private const SEED_TAG = 'synthetic_v2';
    private const ID_FROM = 400;
    private const ID_TO = 1299;
    private const NEW_COUNT = 900;

    private const NAMES = [
        'Chikondi',
        'Thandiwe',
        'Mphatso',
        'Chimwemwe',
        'Takondwa',
        'Yamikani',
        'Madalitso',
        'Tiwonge',
        'Pemphero',
        'Chisomo',
        'Alinafe',
        'Watipaso',
        'Tadala',
        'Chifundo',
        'Mwayi',
        'Ruth',
        'Grace',
        'Ephraim',
        'Loveness',
        'Faith',
        'Tawonga',
        'Kondwani',
        'Wongani',
        'Lumbani',
        'Tiyamike',
        'Chimango',
        'Mtendere',
        'Dalitso',
        'Sangwani',
        'Khumbo',
        'Pilirani',
        'Tamandani',
        'Zikomo',
        'Blessings',
        'Gift',
        'Precious',
        'Praise',
        'Hope',
    ];

    private const SURNAMES = [
        'Banda',
        'Phiri',
        'Chirwa',
        'Mvula',
        'Gondwe',
        'Tembo',
        'Zulu',
        'Kachale',
        'Kamanga',
        'Nyasulu',
        'Mhango',
        'Kalua',
        'Chirambo',
        'Msiska',
        'Nkhoma',
        'Nyondo',
        'Mponda',
        'Kanyenda',
        'Kalinde',
        'Nyirenda',
        'Munthali',
        'Chilenga',
        'Mkandawire',
        'Kaunda',
        'Chikwanha',
        'Kumwenda',
        'Mhone',
        'Mganga',
        'Chavula',
        'Khoswe',
    ];

    public function run(): void
    {
        // 1. Delete previous synthetic data for this school
        $deleted = Student::where('school_id', self::SCHOOL_ID)
            ->where('seed_source', self::SEED_TAG)
            ->delete();
        $this->command->info("Deleted {$deleted} previous synthetic students.");

        // 2. Load reference data
        $subjects = Subject::where('school_id', self::SCHOOL_ID)->get()->keyBy('name');
        $modality = SubjectModalityMap::where('school_id', self::SCHOOL_ID)
            ->get()->keyBy('subject_id');

        $classes = Classroom::where('school_id', self::SCHOOL_ID)->get();

        if ($subjects->isEmpty() || $classes->isEmpty()) {
            $this->command->error('Seed subjects and classes for school 3 first.');
            return;
        }

        // 3. Create 900 new students with modality-varied scores
        $year = now()->year;
        $batchSize = 100;
        $created = 0;
        $currentStudentId = self::ID_FROM;

        for ($i = 0; $i < self::NEW_COUNT; $i++) {
            $profile = $this->pickProfile($i);         // audio / visual / practical / balanced
            $name = self::NAMES[array_rand(self::NAMES)];
            $surname = self::SURNAMES[array_rand(self::SURNAMES)];

            $student = Student::create([
                'id' => $currentStudentId,
                'school_id' => self::SCHOOL_ID,
                'student_number' => sprintf('DEM-SYN-%05d', $i + 1),
                'first_name' => $name,
                'last_name' => $surname,
                'date_of_birth' => Carbon::create(2009, 1, 1)->addDays($i),
                'gender' => $i % 2 === 0 ? 'male' : 'female',
                'status' => 'active',
                'seed_source' => self::SEED_TAG,
                'clinical_referral_flag' => false,
            ]);

            // Assign a class (round-robin across the 20 Form 1-4 classes)
            $class = $classes[$i % $classes->count()];
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => 3,
                'status' => 'active',
            ]);

            // Generate subject scores according to the profile
            $this->assignScores($student, $profile, $subjects, $modality);
            $created++;
            $currentStudentId++;
        }

        // 4. Reset auto-increment on students so future inserts don't collide
        \DB::statement(
            'ALTER TABLE students AUTO_INCREMENT = ' . ($currentStudentId + 1)
        );

        $this->command->info("Created {$created} realistic synthetic students.");
        $this->command->info('Profiles generated: 300 audio-strong, 300 visual-strong, 300 practical-strong.');
    }

    /** @return string 'audio' | 'visual' | 'practical' | 'balanced' */
    private function pickProfile(int $i): string
    {
        // 33% audio, 33% visual, 33% practical, plus a small balanced tail
        $bucket = $i % 10;
        if ($bucket === 0)
            return 'balanced';
        if ($bucket <= 3)
            return 'audio';
        if ($bucket <= 6)
            return 'visual';
        return 'practical';
    }

    /**
     * Generate scores per subject so that the student clearly leans
     * toward their target modality, with subject-level noise.
     */
    private function assignScores(
        Student $student,
        string $profile,
        $subjects,
        $modality
    ): void {
        // Base score varies from 40–85 per student
        $base = mt_rand(40, 85);

        foreach ($subjects as $subject) {
            $modMap = $modality->get($subject->id);
            if (!$modMap) {
                continue;
            }

            $score = $this->scoreFor($base, $profile, $modMap->primary_modality, $modMap->secondary_modality);

            // Keep within 0–100
            $score = max(0, min(100, $score));

            SubjectTermResult::create([
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'term_id' => self::TERM_ID,
                'final_percentage' => round($score, 2),
                'grade_letter' => $this->gradeFor($score),
                'achievement_level' => $this->achievementFor($score),
                'passed' => $score >= 40 ? 1 : 0,
            ]);
        }
    }

    /**
     * A student's score in a subject depends on how well the subject's
     * modality matches the student's profile.
     *   - match (primary):    base + 15 ± 8
     *   - match (secondary):  base + 5  ± 8
     *   - no match:           base - 20 ± 8
     */
    private function scoreFor(int $base, string $profile, string $primary, ?string $secondary): int
    {
        if ($profile === 'balanced') {
            return $base + mt_rand(-5, 5);
        }

        if ($primary === $profile) {
            return $base + 15 + mt_rand(-8, 8);
        }
        if ($secondary === $profile) {
            return $base + 5 + mt_rand(-8, 8);
        }
        return $base - 20 + mt_rand(-8, 8);
    }

    private function gradeFor(float $score): string
    {
        return match (true) {
            $score >= 80 => 'A',
            $score >= 70 => 'B',
            $score >= 60 => 'C',
            $score >= 50 => 'D',
            $score >= 40 => 'E',
            default => 'F',
        };
    }

    private function achievementFor(float $score): string
    {
        return match (true) {
            $score >= 80 => 'Exceptional',
            $score >= 70 => 'Advanced',
            $score >= 60 => 'Proficient',
            $score >= 50 => 'Developing',
            default => 'Beginning',
        };
    }
}