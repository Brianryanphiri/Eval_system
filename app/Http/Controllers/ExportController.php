<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\SubjectTermResult;
use App\Models\Term;
use App\Models\TermResult;
use App\Services\ReportCardBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportController extends Controller
{
    /**
     * Export center — landing page with all export options.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        return view('reports.export', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
        ]);
    }

    /**
     * Export every student's marks for a class + term as CSV.
     */
    public function resultsCsv(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $classId = $request->query('class_id');
        $termId = $request->query('term');

        if (!$classId || !$termId) {
            return back()->with('error', 'Select a class and term first.');
        }

        $class = Classroom::where('school_id', $school->id)->findOrFail($classId);
        $term = Term::findOrFail($termId);

        $students = Student::where('school_id', $school->id)
            ->whereHas('enrollments', function ($q) use ($class, $term) {
                $q->where('class_id', $class->id)
                    ->where('academic_year_id', $term->academic_year_id)
                    ->where('status', 'active');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $studentIds = $students->pluck('id');

        $results = SubjectTermResult::where('term_id', $termId)
            ->whereIn('student_id', $studentIds)
            ->with('subject')
            ->get();

        $subjects = $results->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $byStudent = $results->groupBy('student_id');

        $termResults = TermResult::where('term_id', $termId)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        $filename = 'results-'
            . str_replace(' ', '-', strtolower($class->name))
            . '-' . str_replace(' ', '-', strtolower($term->name ?? 'term'))
            . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($students, $subjects, $byStudent, $termResults, $class, $term, $school) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Title block
            fputcsv($handle, [$school->name ?? 'School']);
            fputcsv($handle, ['Class Results — ' . $class->name]);
            fputcsv($handle, ['Term: ' . ($term->name ?? '')]);
            fputcsv($handle, ['Generated: ' . now()->format('d M Y H:i')]);
            fputcsv($handle, []);

            // Header row
            $header = ['Position', 'Student Number', 'Student Name'];
            foreach ($subjects as $subject) {
                $header[] = $subject->name . ' (%)';
                $header[] = $subject->name . ' (Grade)';
            }
            $header[] = 'Total';
            $header[] = 'Average';
            $header[] = 'Promotion';
            fputcsv($handle, $header);

            // Build rows
            $rows = [];
            foreach ($students as $student) {
                $studentResults = $byStudent->get($student->id, collect());
                $tr = $termResults->get($student->id);

                $line = [
                    $tr->position ?? '',
                    $student->student_number,
                    $student->full_name,
                ];

                $total = 0;
                $count = 0;

                foreach ($subjects as $subject) {
                    $r = $studentResults->firstWhere('subject_id', $subject->id);
                    $line[] = $r ? round($r->final_percentage, 1) : '';
                    $line[] = $r ? $r->grade_letter : '';
                    if ($r) {
                        $total += $r->final_percentage;
                        $count++;
                    }
                }

                $line[] = round($total, 1);
                $line[] = $count > 0 ? round($total / $count, 1) . '%' : '';
                $line[] = $tr->promotion_status ?? '';

                $rows[] = ['sort' => $tr->position ?? PHP_INT_MAX, 'line' => $line];
            }

            usort($rows, fn($a, $b) => $a['sort'] <=> $b['sort']);

            foreach ($rows as $row) {
                fputcsv($handle, $row['line']);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Bulk download all report cards for a class + term as a ZIP of PDFs.
     */
    public function reportCardsZip(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $classId = $request->query('class_id');
        $termId = $request->query('term');

        if (!$classId || !$termId) {
            return back()->with('error', 'Select a class and term first.');
        }

        $class = Classroom::where('school_id', $school->id)->findOrFail($classId);
        $term = Term::findOrFail($termId);

        $students = Student::where('school_id', $school->id)
            ->whereHas('enrollments', function ($q) use ($class, $term) {
                $q->where('class_id', $class->id)
                    ->where('academic_year_id', $term->academic_year_id)
                    ->where('status', 'active');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students in this class.');
        }

        // Build each report card PDF into a temp file
        $zipName = 'report-cards-'
            . str_replace(' ', '-', strtolower($class->name))
            . '-' . str_replace(' ', '-', strtolower($term->name ?? 'term'))
            . '-' . now()->format('Y-m-d') . '.zip';

        $tempZipPath = storage_path('app/temp/' . $zipName);

        // Ensure the temp directory exists
        if (!is_dir(dirname($tempZipPath))) {
            mkdir(dirname($tempZipPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create ZIP archive.');
        }

        $added = 0;
        foreach ($students as $student) {
            try {
                $data = ReportCardBuilder::build($student, $term);
                $pdf = Pdf::loadView('reports.report-card-pdf', $data)
                    ->setPaper('A4', 'portrait');

                $pdfContent = $pdf->output();

                $filename = 'report-card-'
                    . str_replace(' ', '-', strtolower($student->full_name))
                    . '-' . $student->student_number . '.pdf';

                $zip->addFromString($filename, $pdfContent);
                $added++;
            } catch (\Throwable $e) {
                // Skip students whose report card can't be generated, don't fail the whole ZIP
                continue;
            }
        }

        $zip->close();

        if ($added === 0) {
            @unlink($tempZipPath);
            return back()->with('error', 'No report cards could be generated.');
        }

        return response()->download($tempZipPath, $zipName)->deleteFileAfterSend(true);
    }

    /**
     * Export every student's full results for the whole school as XLSX.
     */
    public function fullResultsXlsx(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $termId = $request->query('term');
        if (!$termId) {
            return back()->with('error', 'Select a term first.');
        }

        $term = Term::findOrFail($termId);

        // Every student in this school
        $students = Student::where('school_id', $school->id)
            ->whereHas('enrollments', function ($q) use ($term) {
                $q->where('academic_year_id', $term->academic_year_id)
                    ->where('status', 'active');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $studentIds = $students->pluck('id');

        // Every subject result for those students in this term
        $results = SubjectTermResult::where('term_id', $termId)
            ->whereIn('student_id', $studentIds)
            ->with('subject')
            ->get();

        $subjects = $results->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $byStudent = $results->groupBy('student_id');

        // Class lookup per student
        $classLookup = \DB::table('enrollments')
            ->join('classes', 'classes.id', '=', 'enrollments.class_id')
            ->where('enrollments.academic_year_id', $term->academic_year_id)
            ->where('enrollments.status', 'active')
            ->whereIn('enrollments.student_id', $studentIds)
            ->select('enrollments.student_id', 'classes.name as class_name')
            ->get()
            ->keyBy('student_id');

        $termResults = TermResult::where('term_id', $termId)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        // ---------- Build spreadsheet ----------
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Full Results');

        // Columns: # + Student Number + Student Name + Class + subjects (score + grade) + Total + Average + Position + Promotion
        $totalCols = 4 + ($subjects->count() * 2) + 4;

        // Header row 1 — Title
        $sheet->setCellValue('A1', $school->name ?? 'School');
        $sheet->mergeCells('A1:' . $this->columnLetter($totalCols) . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0F0F0F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->setCellValue('A2', 'Full Results — ' . ($term->name ?? 'Term') . ' · ' . ($term->academicYear?->label ?? ''));
        $sheet->mergeCells('A2:' . $this->columnLetter($totalCols) . '2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '5E9A31']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $sheet->setCellValue('A3', 'Generated: ' . now()->format('d M Y H:i'));
        $sheet->mergeCells('A3:' . $this->columnLetter($totalCols) . '3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header row (row 5)
        $headerRow = 5;
        $col = 1;
        $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Position');
        $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Student Number');
        $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Student Name');
        $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Class');
        foreach ($subjects as $subject) {
            $sheet->setCellValue($this->columnLetter($col++) . $headerRow, $subject->name);
            $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Grade');
        }
        $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Total');
        $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Average');
        $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Promotion');
        $sheet->setCellValue($this->columnLetter($col++) . $headerRow, 'Achievement');

        $headerRange = 'A' . $headerRow . ':' . $this->columnLetter($totalCols) . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5E9A31'],
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '5E9A31'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(24);

        // Data rows — sorted by class then position
        $rowData = [];
        foreach ($students as $student) {
            $studentResults = $byStudent->get($student->id, collect());
            $tr = $termResults->get($student->id);

            $line = [
                $tr->position ?? '',
                $student->student_number,
                $student->full_name,
                $classLookup->get($student->id)?->class_name ?? '',
            ];

            $total = 0;
            $count = 0;
            foreach ($subjects as $subject) {
                $r = $studentResults->firstWhere('subject_id', $subject->id);
                $line[] = $r ? round($r->final_percentage, 1) : '';
                $line[] = $r ? $r->grade_letter : '';
                if ($r) {
                    $total += $r->final_percentage;
                    $count++;
                }
            }
            $line[] = round($total, 1);
            $line[] = $count > 0 ? round($total / $count, 1) : '';
            $line[] = $tr->promotion_status ?? '';
            $line[] = $tr->achievement_level ?? '';

            $rowData[] = [
                'class' => $classLookup->get($student->id)?->class_name ?? 'ZZZ',
                'position' => $tr->position ?? PHP_INT_MAX,
                'line' => $line,
            ];
        }

        usort($rowData, function ($a, $b) {
            $c = strcmp($a['class'], $b['class']);
            if ($c !== 0)
                return $c;
            return $a['position'] <=> $b['position'];
        });

        $rowNum = $headerRow + 1;
        foreach ($rowData as $rd) {
            $col = 1;
            foreach ($rd['line'] as $value) {
                $sheet->setCellValue($this->columnLetter($col++) . $rowNum, $value);
            }
            $rowNum++;
        }

        $lastDataRow = $rowNum - 1;

        // Style data range
        $dataRange = 'A' . ($headerRow + 1) . ':' . $this->columnLetter($totalCols) . $lastDataRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Left-align names
        $sheet->getStyle('C' . ($headerRow + 1) . ':C' . $lastDataRow)
            ->getAlignment()->setHorizontal('left');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(14);
        $letter = 5;
        foreach ($subjects as $subject) {
            $sheet->getColumnDimension($this->columnLetter($letter))->setWidth(12);
            $letter++;
            $sheet->getColumnDimension($this->columnLetter($letter))->setWidth(8);
            $letter++;
        }
        $sheet->getColumnDimension($this->columnLetter($letter))->setWidth(10);
        $sheet->getColumnDimension($this->columnLetter($letter + 1))->setWidth(10);
        $sheet->getColumnDimension($this->columnLetter($letter + 2))->setWidth(14);
        $sheet->getColumnDimension($this->columnLetter($letter + 3))->setWidth(16);

        // Freeze header row
        $sheet->freezePane('E' . ($headerRow + 1));

        // Auto-filter
        $sheet->setAutoFilter($headerRange);

        // Print setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        // ---------- Save ----------
        $filename = 'full-results-'
            . str_replace(' ', '-', strtolower($term->name ?? 'term'))
            . '-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // ---------- Helpers ----------

    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = (int) (($index - $mod) / 26);
        }
        return $letter;
    }
}