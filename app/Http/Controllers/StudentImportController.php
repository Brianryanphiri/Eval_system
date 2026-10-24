<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentImportController extends Controller
{
    /**
     * Show the import form.
     */
    public function create()
    {
        return view('students.import');
    }

    /**
     * Download a professional .xlsx template with instructions and classes.
     */
    public function template()
    {
        $schoolId = auth()->user()->school_id;

        $classNames = Classroom::where('school_id', $schoolId)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        if (empty($classNames)) {
            $classNames = ['Form 1A', 'Form 1B', 'Form 2A'];
        }

        $sampleClass = $classNames[0] ?? 'Form 1A';

        $spreadsheet = new Spreadsheet();

        // ============================================================
        // SHEET 1 — Students
        // ============================================================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Students');

        $headers = ['first_name', 'last_name', 'class_name', 'date_of_birth', 'gender'];
        $sheet->fromArray($headers, null, 'A1');

        // Style the header row
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5E9A31'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '5E9A31'],
                ],
            ],
        ]);

        // Sample rows
        $samples = [
            ['John', 'Banda', $sampleClass, '2012-03-14', 'Male'],
            ['Grace', 'Phiri', $sampleClass, '2012-06-22', 'Female'],
            ['Kelvin', 'Chirwa', $sampleClass, '2012-01-09', 'Male'],
        ];
        $sheet->fromArray($samples, null, 'A2');

        // Italic grey style for samples
        $sheet->getStyle('A2:E' . (1 + count($samples)))->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '9CA3AF'],
                'size' => 11,
            ],
        ]);

        // Force date column as TEXT so Excel doesn't reformat
        $sheet->getStyle('D1:D1000')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_TEXT);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(14);

        // Row heights
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Freeze header
        $sheet->freezePane('A2');

        // Add filter
        $sheet->setAutoFilter('A1:E1');

        // ============================================================
        // SHEET 2 — Instructions
        // ============================================================
        $instructions = $spreadsheet->createSheet();
        $instructions->setTitle('Instructions');

        $instructions->setCellValue('A1', 'How to use this template');
        $instructions->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0F0F0F']],
        ]);
        $instructions->getRowDimension(1)->setRowHeight(28);

        $steps = [
            ['1.', 'Fill in the "Students" sheet with one student per row.'],
            ['2.', 'Do not change the header row (row 1).'],
            ['3.', 'Delete the italic sample rows before uploading.'],
            ['4.', 'first_name and last_name are required.'],
            ['5.', 'class_name must exactly match one of the classes listed in the "Classes" sheet.'],
            ['6.', 'date_of_birth must be in YYYY-MM-DD format, e.g. 2012-03-14.'],
            ['7.', 'gender must be Male or Female.'],
            ['8.', 'Save the file, then upload it using the Import Students page.'],
            ['9.', 'Rows with missing or invalid data will be skipped and reported back to you.'],
        ];

        $rowIndex = 3;
        foreach ($steps as [$num, $text]) {
            $instructions->setCellValue('A' . $rowIndex, $num);
            $instructions->setCellValue('B' . $rowIndex, $text);
            $instructions->getStyle('A' . $rowIndex)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '5E9A31']],
            ]);
            $rowIndex++;
        }

        $instructions->getColumnDimension('A')->setWidth(6);
        $instructions->getColumnDimension('B')->setWidth(90);

        // ============================================================
        // SHEET 3 — Available Classes
        // ============================================================
        $classesSheet = $spreadsheet->createSheet();
        $classesSheet->setTitle('Classes');

        $classesSheet->setCellValue('A1', 'Available Classes');
        $classesSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5E9A31'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $classesSheet->getColumnDimension('A')->setWidth(30);
        $classesSheet->getRowDimension(1)->setRowHeight(26);

        $rowIndex = 2;
        foreach ($classNames as $name) {
            $classesSheet->setCellValue('A' . $rowIndex, $name);
            $rowIndex++;
        }

        // ============================================================
        // Stream
        // ============================================================
        $filename = 'students-import-template-' . now()->format('Y-m-d') . '.xlsx';
        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Process the uploaded file (.csv or .xlsx) and import students.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120'],
        ]);

        $schoolId = auth()->user()->school_id;
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());

        // Read the uploaded file into a header + rows array
        try {
            if (in_array($ext, ['xlsx', 'xls'], true)) {
                $rows = $this->readSpreadsheet($file->getRealPath());
            } else {
                $rows = $this->readCsv($file->getRealPath());
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not read the uploaded file: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return back()->with('error', 'The uploaded file is empty.');
        }

        // First row = headers
        $header = array_shift($rows);
        $header = array_map(fn($h) => strtolower(trim((string) $h)), $header);

        // Required columns
        $required = ['first_name', 'last_name', 'class_name'];
        $missing = array_diff($required, $header);
        if (!empty($missing)) {
            return back()->with('error', 'Missing required column(s): ' . implode(', ', $missing));
        }

        $col = array_flip($header);

        // Preload class lookup (case-insensitive)
        $classes = Classroom::where('school_id', $schoolId)
            ->get()
            ->keyBy(fn($c) => strtolower(trim($c->name)));

        $imported = 0;
        $errors = [];
        $rowNumber = 1; // header row

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $rowNumber++;

                // Skip fully empty rows
                if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                    continue;
                }

                // Skip comment rows (legacy CSV template)
                $firstCell = trim((string) ($row[0] ?? ''));
                if ($firstCell !== '' && str_starts_with($firstCell, '#')) {
                    continue;
                }

                // Pull values by column name
                $firstName = trim((string) ($row[$col['first_name']] ?? ''));
                $lastName = trim((string) ($row[$col['last_name']] ?? ''));
                $className = trim((string) ($row[$col['class_name']] ?? ''));
                $dob = isset($col['date_of_birth']) ? trim((string) ($row[$col['date_of_birth']] ?? '')) : '';
                $gender = isset($col['gender']) ? trim((string) ($row[$col['gender']] ?? '')) : '';

                // Required fields
                if ($firstName === '' || $lastName === '' || $className === '') {
                    $errors[] = "Row {$rowNumber}: first_name, last_name, and class_name are all required.";
                    continue;
                }

                // Match class
                $classroom = $classes->get(strtolower($className));
                if (!$classroom) {
                    $errors[] = "Row {$rowNumber}: class \"{$className}\" does not exist.";
                    continue;
                }

                // Validate date_of_birth (allow both YYYY-MM-DD and Excel serial numbers)
                if ($dob !== '') {
                    // Excel might hand us a serial number if the cell was formatted as a date
                    if (is_numeric($dob) && (float) $dob > 0 && (float) $dob < 100000) {
                        try {
                            $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $dob)
                                ->format('Y-m-d');
                        } catch (\Throwable $e) {
                            $errors[] = "Row {$rowNumber}: date_of_birth could not be interpreted.";
                            continue;
                        }
                    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
                        // Try parsing other common formats
                        $ts = strtotime($dob);
                        if ($ts !== false) {
                            $dob = date('Y-m-d', $ts);
                        } else {
                            $errors[] = "Row {$rowNumber}: date_of_birth must be in YYYY-MM-DD format.";
                            continue;
                        }
                    }
                }

                // Validate gender
                if ($gender !== '' && !in_array(strtolower($gender), ['male', 'female'], true)) {
                    $errors[] = "Row {$rowNumber}: gender must be Male or Female.";
                    continue;
                }

                // Duplicate check
                $exists = Student::where('school_id', $schoolId)
                    ->where('classroom_id', $classroom->id)
                    ->whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                    ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                    ->exists();

                if ($exists) {
                    $errors[] = "Row {$rowNumber}: {$firstName} {$lastName} already exists in {$classroom->name}.";
                    continue;
                }

                // Create
                Student::create([
                    'school_id' => $schoolId,
                    'classroom_id' => $classroom->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'date_of_birth' => $dob !== '' ? $dob : null,
                    'gender' => $gender !== '' ? strtolower($gender) : null,
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        // Response
        $message = "{$imported} " . str('student')->plural($imported) . ' imported successfully.';

        if (!empty($errors)) {
            return redirect()
                ->route('students.import')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }

        return redirect()
            ->route('students.index')
            ->with('success', $message);
    }

    // ============================================================
    // Helpers
    // ============================================================

    /**
     * Read a CSV file into a 2D array.
     */
    protected function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Could not open CSV file.');
        }

        // Skip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            rewind($handle);
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Read an .xlsx / .xls file into a 2D array using PhpSpreadsheet.
     */
    protected function readSpreadsheet(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        // Use the "Students" sheet if it exists, otherwise the first sheet
        $sheet = $spreadsheet->getSheetByName('Students') ?? $spreadsheet->getSheet(0);

        $rows = $sheet->toArray(null, true, true, false);

        // Clean up: strip fully empty trailing rows
        $rows = array_values(array_filter($rows, function ($row) {
            return count(array_filter($row, fn($v) => trim((string) $v) !== '')) > 0;
        }));

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $rows;
    }
}