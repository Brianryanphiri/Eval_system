<?php

namespace App\Services;

use App\Domain\CorrectiveAction;
use App\Domain\Modality;
use App\Domain\ReasonCode;
use App\Domain\StreamResolver;
use App\Models\PlacementRecommendation;
use App\Models\Student;
use App\Models\StudentModalityProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PlacementEngine
{
    public const ATTENDANCE_THRESHOLD = 60.0;

    public function __construct(private ModalityClassifier $classifier)
    {
    }

    public function evaluate(Student $student, int $termId): PlacementRecommendation
    {
        $profile = $this->classifier->profile($student, $termId);
        $this->persistProfile($student, $termId, $profile);

        $attendance = $this->attendancePercentage($student, $termId);
        $rawStream = StreamResolver::fromClassroom($student->currentClassroom());
        $currentStream = $rawStream !== null
            ? StreamResolver::toPlacementClass($rawStream)
            : null;

        $reasons = [];
        $actions = [];
        $recommend = null;

        if ($student->clinical_referral_flag) {
            $reasons[] = ReasonCode::CLINICAL_REFERRAL;
            $actions[] = CorrectiveAction::CLINICAL_REFERRAL;
        } elseif ($profile['data_points'] < ModalityClassifier::MIN_DATA_POINTS) {
            $reasons[] = ReasonCode::INSUFFICIENT_DATA;
            $actions[] = CorrectiveAction::GATHER_MORE_DATA;
        } elseif ($attendance < self::ATTENDANCE_THRESHOLD) {
            $reasons[] = ReasonCode::LOW_ATTENDANCE;
            $actions[] = CorrectiveAction::ATTENDANCE_INTERVENTION;
        } elseif ($this->classifier->isAmbiguous($profile)) {
            $reasons[] = ReasonCode::AMBIGUOUS_PROFILE;
            $actions[] = CorrectiveAction::MULTIMODAL_SUPPORT;
        } else {
            $top = $this->classifier->topModality($profile);
            $modality = Modality::from($top['modality']);
            $recommend = Modality::classFor($modality);

            if ($currentStream === $recommend) {
                $reasons[] = ReasonCode::CORRECTLY_PLACED;
                $actions[] = CorrectiveAction::NO_ACTION;
            } else {
                $reasons[] = ReasonCode::MISPLACED;
                $actions[] = CorrectiveAction::RECLASSIFY;
            }
        }

        $existing = PlacementRecommendation::where('student_id', $student->id)
            ->where('term_id', $termId)
            ->first();

        if ($existing && $existing->status === 'applied') {
            return $existing;
        }

        return PlacementRecommendation::updateOrCreate(
            [
                'student_id' => $student->id,
                'term_id' => $termId,
            ],
            [
                'school_id' => $student->school_id,
                'current_class' => $currentStream,
                'recommended_class' => $recommend,
                'reason_codes' => array_values(array_unique($reasons)),
                'corrective_actions' => array_values(array_unique($actions)),
                'status' => 'pending',
            ]
        );
    }

    private function persistProfile(Student $student, int $termId, array $profile): void
    {
        $top = $this->classifier->topModality($profile);

        StudentModalityProfile::updateOrCreate(
            ['student_id' => $student->id, 'term_id' => $termId],
            [
                'school_id' => $student->school_id,
                'audio_score' => $profile['audio'],
                'visual_score' => $profile['visual'],
                'practical_score' => $profile['practical'],
                'top_modality' => $top['modality'] ?? null,
                'is_ambiguous' => $this->classifier->isAmbiguous($profile),
                'data_points' => $profile['data_points'],
                'computed_at' => Carbon::now(),
            ]
        );
    }

    private function attendancePercentage(Student $student, int $termId): float
    {
        $row = DB::table('attendance_summaries')
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->first();

        return $row ? (float) $row->percentage : 100.0;
    }
}