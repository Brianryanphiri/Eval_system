<?php

namespace App\Services;

use App\Domain\Modality;
use App\Models\Student;
use App\Models\SubjectModalityMap;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ModalityClassifier
{
    public const MIN_DATA_POINTS = 5;
    public const MIN_COHORT_SIZE = 10;
    public const MIN_SIGNAL = 0.5;
    public const MIN_FIT = 0.3;
    public const AMBIGUITY_MARGIN = 0.10;

    private const PRIMARY_WEIGHT = 1.0;
    private const SECONDARY_WEIGHT = 0.5;

    private array $mapCache = [];
    private array $cohortCache = [];

    public function profile(Student $student, int $termId): array
    {
        $maps = $this->mapsFor((int) $student->school_id);
        $grade = $student->currentClassroom()?->grade_level;
        $cohort = $this->cohortStats((int) $student->school_id, $termId, $grade);

        $results = $student->subjectTermResults()
            ->where('term_id', $termId)
            ->get();

        $zScores = [];
        $usedMaps = [];

        foreach ($results as $result) {
            $map = $maps->get($result->subject_id);
            $stat = $cohort[$result->subject_id] ?? null;

            if (!$map || !$stat || $stat['sd'] <= 0.0) {
                continue;
            }

            $zScores[] = ((float) $result->final_percentage - $stat['mean']) / $stat['sd'];
            $usedMaps[] = $map;
        }

        $fit = array_fill_keys(Modality::all(), 0.0);
        $signal = 0.0;

        if (count($zScores) >= 2) {
            $signal = $this->stddev($zScores);

            foreach (Modality::all() as $modality) {
                $pattern = array_map(
                    fn($map) => $this->patternWeight($map, $modality),
                    $usedMaps
                );
                $fit[$modality] = round($this->correlation($zScores, $pattern), 4);
            }
        }

        return $fit + [
            'signal' => round($signal, 4),
            'data_points' => count($zScores),
        ];
    }

    public function isAmbiguous(array $profile): bool
    {
        if (($profile['signal'] ?? 0.0) < self::MIN_SIGNAL) {
            return true;
        }

        $fits = [];
        foreach (Modality::all() as $m) {
            $fits[] = (float) ($profile[$m] ?? 0.0);
        }
        rsort($fits);

        if ($fits[0] < self::MIN_FIT) {
            return true;
        }

        return count($fits) >= 2 && ($fits[0] - $fits[1]) < self::AMBIGUITY_MARGIN;
    }

    public function topModality(array $profile): ?array
    {
        $best = null;
        foreach (Modality::all() as $m) {
            $score = (float) ($profile[$m] ?? 0.0);
            if ($best === null || $score > $best['score']) {
                $best = ['modality' => $m, 'score' => $score];
            }
        }
        return $best;
    }

    private function patternWeight(SubjectModalityMap $map, string $modality): float
    {
        if ($map->primary_modality === $modality) {
            return self::PRIMARY_WEIGHT;
        }
        if ($map->secondary_modality === $modality) {
            return self::SECONDARY_WEIGHT;
        }
        return 0.0;
    }

    private function mapsFor(int $schoolId): Collection
    {
        return $this->mapCache[$schoolId] ??= SubjectModalityMap::where('school_id', $schoolId)
            ->get()
            ->keyBy('subject_id');
    }

    private function cohortStats(int $schoolId, int $termId, ?string $grade): array
    {
        $key = $schoolId . '|' . $termId . '|' . ($grade ?? '*');

        if (isset($this->cohortCache[$key])) {
            return $this->cohortCache[$key];
        }

        $query = DB::table('subject_term_results as r')
            ->join('enrollments as e', 'e.student_id', '=', 'r.student_id')
            ->join('academic_years as y', 'y.id', '=', 'e.academic_year_id')
            ->join('classes as c', 'c.id', '=', 'e.class_id')
            ->where('r.term_id', $termId)
            ->where('c.school_id', $schoolId)
            ->where('y.is_current', 1)
            ->where('e.status', 'active');

        if ($grade !== null) {
            $query->where('c.grade_level', $grade);
        }

        $rows = $query
            ->groupBy('r.subject_id')
            ->selectRaw(
                'r.subject_id as subject_id, COUNT(*) as n, '
                . 'AVG(r.final_percentage) as mean, '
                . 'AVG(r.final_percentage * r.final_percentage) as mean_sq'
            )
            ->get();

        $stats = [];
        foreach ($rows as $row) {
            if ((int) $row->n < self::MIN_COHORT_SIZE) {
                continue;
            }
            $mean = (float) $row->mean;
            $variance = max(0.0, (float) $row->mean_sq - $mean * $mean);
            $stats[(int) $row->subject_id] = ['mean' => $mean, 'sd' => sqrt($variance)];
        }

        return $this->cohortCache[$key] = $stats;
    }

    private function stddev(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }
        $mean = array_sum($values) / $n;
        $variance = array_sum(array_map(fn($v) => ($v - $mean) ** 2, $values)) / $n;
        return sqrt($variance);
    }

    private function correlation(array $x, array $y): float
    {
        $n = count($x);
        if ($n < 2 || $n !== count($y)) {
            return 0.0;
        }

        $mx = array_sum($x) / $n;
        $my = array_sum($y) / $n;

        $sxy = $sxx = $syy = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $dx = $x[$i] - $mx;
            $dy = $y[$i] - $my;
            $sxy += $dx * $dy;
            $sxx += $dx * $dx;
            $syy += $dy * $dy;
        }

        if ($sxx <= 0.0 || $syy <= 0.0) {
            return 0.0;
        }

        return $sxy / sqrt($sxx * $syy);
    }
}