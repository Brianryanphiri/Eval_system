<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Collection;
use League\Csv\Reader;

class StudentCsvImporter
{
    /**
     * Expected CSV columns (header row required):
     * first_name, last_name, date_of_birth, gender, class_name
     *
     * date_of_birth format: YYYY-MM-DD (optional — can be blank)
     * gender: male / female / other (optional — can be blank)
     * class_name: must match an existing Classroom.name for this school exactly
     */
    public function import(School $school, string $filePath, StudentNumberGenerator $numberGenerator): array
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $classCache = Classroom::where('school_id', $school->id)->pluck('id', 'name');
        $currentYear = $school->currentAcademicYear();

        $imported = [];
        $errors = [];
        $rowNumber = 1; // header is row 1, data starts at row 2

        foreach ($csv->getRecords() as $record) {
            $rowNumber++;

            $firstName = trim($record['first_name'] ?? '');
            $lastName = trim($record['last_name'] ?? '');
            $className = trim($record['class_name'] ?? '');

            if (! $firstName || ! $lastName) {
                $errors[] = "Row {$rowNumber}: missing first_name or last_name — skipped.";
                continue;
            }

            $classId = $classCache->get($className);
            if (! $classId) {
                $errors[] = "Row {$rowNumber}: class \"{$className}\" not found for this school — skipped.";
                continue;
            }

            $gender = strtolower(trim($record['gender'] ?? ''));
            if ($gender && ! in_array($gender, ['male', 'female', 'other'])) {
                $gender = null; // ignore invalid values rather than failing the whole row
            }

            $dob = trim($record['date_of_birth'] ?? '');
            $dob = $dob && strtotime($dob) ? date('Y-m-d', strtotime($dob)) : null;

            $student = Student::create([
                'school_id' => $school->id,
                'student_number' => $numberGenerator->next($school),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => $dob,
                'gender' => $gender ?: null,
                'status' => 'active',
            ]);

            if ($currentYear) {
                $student->enrollments()->create([
                    'class_id' => $classId,
                    'academic_year_id' => $currentYear->id,
                    'status' => 'active',
                ]);
            }

            $imported[] = $student;
        }

        return [
            'imported_count' => count($imported),
            'errors' => $errors,
        ];
    }
}