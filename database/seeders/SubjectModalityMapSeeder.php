<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\SubjectModalityMap;
use Illuminate\Database\Seeder;

class SubjectModalityMapSeeder extends Seeder
{
    public function run(): void
    {
        // The transcript's own subject → modality examples, applied per school.
        // Adjust these per school as the school sees fit.
        $defaults = [
            'Mathematics' => ['visual', 'practical'],
            'English' => ['audio', null],
            'Chichewa' => ['audio', null],
            'Biology' => ['visual', 'practical'],
            'Physical Science' => ['practical', 'visual'],
            'Geography' => ['visual', 'audio'],
            'History' => ['audio', 'visual'],
            'Agriculture' => ['practical', 'visual'],
        ];

        Subject::all()->each(function (Subject $subject) use ($defaults) {
            $modalities = $defaults[$subject->name] ?? null;
            if (!$modalities) {
                return; // unknown subject — school can tag it manually later
            }

            SubjectModalityMap::updateOrCreate(
                ['school_id' => $subject->school_id, 'subject_id' => $subject->id],
                ['primary_modality' => $modalities[0], 'secondary_modality' => $modalities[1]]
            );
        });
    }
}