<?php

namespace App\Http\Controllers;

use App\Mail\ReportCardEmail;
use App\Models\Student;
use App\Services\ReportCardBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Reports landing page (Reports hub).
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Broadsheet (students × subjects grid) with sort, term, delta, averages.
     */
    public function broadsheet(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = \App\Models\Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $previousTerm = null;
        if ($currentTerm) {
            $previousTerm = $allTerms
                ->filter(fn($t) => $t->id !== $currentTerm->id)
                ->first(function ($t) use ($currentTerm) {
                    if ($t->academic_year_id < $currentTerm->academic_year_id)
                        return true;
                    if (
                        $t->academic_year_id == $currentTerm->academic_year_id
                        && $t->sequence < $currentTerm->sequence
                    )
                        return true;
                    return false;
                });
        }

        $classes = \App\Models\Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id') ?? $classes->first()?->id;
        $selectedClass = $selectedClassId
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        $rows = [];
        $subjects = collect();
        $classAverages = [];
        $gradeSummary = [];

        if ($selectedClass && $currentTerm && $currentYear) {
            $students = \App\Models\Student::where('school_id', $school->id)
                ->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                    $q->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();

            $studentIds = $students->pluck('id');

            $results = \App\Models\SubjectTermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->with('subject')
                ->get();

            $previousResults = $previousTerm
                ? \App\Models\SubjectTermResult::where('term_id', $previousTerm->id)
                    ->whereIn('student_id', $studentIds)
                    ->get()
                    ->groupBy('student_id')
                : collect();

            $byStudent = $results->groupBy('student_id');

            $subjects = $results->pluck('subject')
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->values();

            foreach ($subjects as $subject) {
                $marks = $results
                    ->where('subject_id', $subject->id)
                    ->pluck('final_percentage');
                $classAverages[$subject->id] = $marks->count() > 0
                    ? round($marks->avg(), 1)
                    : null;
            }

            $termResults = \App\Models\TermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $studentResults = $byStudent->get($student->id, collect());
                $prevStudentResults = $previousResults->get($student->id, collect());

                $subjectsData = [];
                $total = 0;
                $count = 0;
                $failedCount = 0;

                foreach ($subjects as $subject) {
                    $r = $studentResults->firstWhere('subject_id', $subject->id);
                    $p = $prevStudentResults->firstWhere('subject_id', $subject->id);

                    $delta = ($r && $p)
                        ? round($r->final_percentage - $p->final_percentage, 1)
                        : null;

                    $subjectsData[$subject->id] = $r ? [
                        'percentage' => $r->final_percentage,
                        'grade' => $r->grade_letter,
                        'passed' => $r->passed,
                        'delta' => $delta,
                        'above_avg' => $classAverages[$subject->id] !== null
                            ? ($r->final_percentage >= $classAverages[$subject->id])
                            : null,
                    ] : null;

                    if ($r) {
                        $total += $r->final_percentage;
                        $count++;
                        if (!$r->passed)
                            $failedCount++;
                    }
                }

                $average = $count > 0 ? round($total / $count, 1) : 0;
                $tr = $termResults->get($student->id);

                $rows[] = [
                    'student' => $student,
                    'subjects' => $subjectsData,
                    'total' => $total,
                    'average' => $average,
                    'position' => $tr->position ?? null,
                    'class_size' => $tr->class_size ?? null,
                    'failed_count' => $failedCount,
                ];
            }

            foreach ($subjects as $subject) {
                $grades = $results
                    ->where('subject_id', $subject->id)
                    ->pluck('grade_letter');
                $gradeSummary[$subject->id] = [
                    'A' => $grades->where('A')->count(),
                    'B' => $grades->where('B')->count(),
                    'C' => $grades->where('C')->count(),
                    'D' => $grades->where('D')->count(),
                    'E' => $grades->where('E')->count(),
                    'F' => $grades->where('F')->count(),
                ];
            }
        }

        $sort = $request->query('sort', 'position');
        $sortDir = $request->query('dir', 'asc');

        switch ($sort) {
            case 'name':
                usort($rows, fn($a, $b) => strcmp($a['student']->last_name, $b['student']->last_name));
                break;
            case 'total':
                usort($rows, fn($a, $b) => $b['total'] <=> $a['total']);
                break;
            case 'average':
                usort($rows, fn($a, $b) => $b['average'] <=> $a['average']);
                break;
            default:
                if (is_numeric($sort) && $subjects->contains('id', (int) $sort)) {
                    $subjectId = (int) $sort;
                    usort($rows, function ($a, $b) use ($subjectId) {
                        $av = $a['subjects'][$subjectId]['percentage'] ?? -1;
                        $bv = $b['subjects'][$subjectId]['percentage'] ?? -1;
                        return $bv <=> $av;
                    });
                } else {
                    usort($rows, function ($a, $b) {
                        $ap = $a['position'] ?? PHP_INT_MAX;
                        $bp = $b['position'] ?? PHP_INT_MAX;
                        return $ap <=> $bp;
                    });
                }
                break;
        }

        return view('reports.broadsheet', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'previousTerm' => $previousTerm,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'subjects' => $subjects,
            'rows' => $rows,
            'classAverages' => $classAverages,
            'gradeSummary' => $gradeSummary,
            'sort' => $sort,
            'sortDir' => $sortDir,
        ]);
    }

    /**
     * Export the broadsheet as a styled .xlsx file.
     */
    public function broadsheetExport(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $currentYear = \App\Models\AcademicYear::where('school_id', $school->id)
            ->where('is_current', true)
            ->first();

        $termId = $request->query('term');
        $currentTerm = $termId
            ? \App\Models\Term::find($termId)
            : ($currentYear ? \App\Models\Term::where('academic_year_id', $currentYear->id)->where('is_current', true)->first() : null);

        $selectedClassId = $request->query('class_id');
        $selectedClass = $selectedClassId ? \App\Models\Classroom::find($selectedClassId) : null;

        if (!$selectedClass || !$currentTerm || !$currentYear) {
            return back()->with('error', 'Select a class and term first.');
        }

        $students = \App\Models\Student::where('school_id', $school->id)
            ->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                $q->where('class_id', $selectedClass->id)
                    ->where('academic_year_id', $currentYear->id)
                    ->where('status', 'active');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $studentIds = $students->pluck('id');

        $results = \App\Models\SubjectTermResult::where('term_id', $currentTerm->id)
            ->whereIn('student_id', $studentIds)
            ->with('subject')
            ->get();

        $subjects = $results->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $byStudent = $results->groupBy('student_id');

        $termResults = \App\Models\TermResult::where('term_id', $currentTerm->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        $rows = [];
        $subjectTotals = [];
        foreach ($subjects as $subject) {
            $subjectTotals[$subject->id] = ['sum' => 0, 'count' => 0];
        }

        foreach ($students as $student) {
            $studentResults = $byStudent->get($student->id, collect());
            $marks = [];
            $total = 0;
            $count = 0;

            foreach ($subjects as $subject) {
                $r = $studentResults->firstWhere('subject_id', $subject->id);
                $value = $r ? round($r->final_percentage) : null;
                $marks[$subject->id] = $value;
                if ($r) {
                    $total += $r->final_percentage;
                    $count++;
                    $subjectTotals[$subject->id]['sum'] += $r->final_percentage;
                    $subjectTotals[$subject->id]['count']++;
                }
            }

            $rows[] = [
                'student' => $student,
                'marks' => $marks,
                'total' => $total,
                'average' => $count > 0 ? round($total / $count, 1) : 0,
                'position' => $termResults->get($student->id)?->position,
            ];
        }

        usort($rows, fn($a, $b) => $b['average'] <=> $a['average']);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($selectedClass->name, 0, 31));

        $lastColIndex = 2 + $subjects->count() + 2;
        $lastColLetter = $this->columnLetter($lastColIndex);

        $sheet->setCellValue('A1', $school->name ?? 'School');
        $sheet->mergeCells('A1:' . $lastColLetter . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0F0F0F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->setCellValue('A2', 'Broadsheet — ' . $selectedClass->name);
        $sheet->mergeCells('A2:' . $lastColLetter . '2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '5E9A31']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $sheet->setCellValue('A3', ($currentTerm->name ?? 'Term') . ' · ' . ($currentYear->label ?? '') . ' · Generated ' . now()->format('d M Y H:i'));
        $sheet->mergeCells('A3:' . $lastColLetter . '3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $headerRow = 5;
        $colIdx = 1;

        $sheet->setCellValue($this->columnLetter($colIdx++) . $headerRow, 'Position');
        $sheet->setCellValue($this->columnLetter($colIdx++) . $headerRow, 'Student');
        foreach ($subjects as $subject) {
            $sheet->setCellValue($this->columnLetter($colIdx++) . $headerRow, $subject->name);
        }
        $sheet->setCellValue($this->columnLetter($colIdx++) . $headerRow, 'Total');
        $sheet->setCellValue($this->columnLetter($colIdx++) . $headerRow, 'Average');

        $headerRange = 'A' . $headerRow . ':' . $lastColLetter . $headerRow;

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

        $rowNum = $headerRow + 1;
        foreach ($rows as $row) {
            $colIdx = 1;
            $sheet->setCellValue($this->columnLetter($colIdx++) . $rowNum, $row['position']);
            $sheet->setCellValue($this->columnLetter($colIdx++) . $rowNum, $row['student']->full_name);

            foreach ($subjects as $subject) {
                $value = $row['marks'][$subject->id];
                $sheet->setCellValue($this->columnLetter($colIdx++) . $rowNum, $value ?? '—');
            }

            $sheet->setCellValue($this->columnLetter($colIdx++) . $rowNum, round($row['total']));
            $sheet->setCellValue($this->columnLetter($colIdx++) . $rowNum, $row['average'] . '%');

            $rowNum++;
        }

        $lastDataRow = $rowNum - 1;

        $dataRange = 'A' . ($headerRow + 1) . ':' . $lastColLetter . $lastDataRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->getStyle('B' . ($headerRow + 1) . ':B' . $lastDataRow)
            ->getAlignment()->setHorizontal('left');

        for ($i = $headerRow + 1; $i <= $lastDataRow; $i++) {
            $pos = $sheet->getCell('A' . $i)->getValue();
            if ($pos <= 3 && $pos > 0) {
                $sheet->getStyle('A' . $i)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '5E9A31']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E9F5DD'],
                    ],
                ]);
            }
        }

        $avgRow = $lastDataRow + 1;
        $colIdx = 1;
        $sheet->setCellValue($this->columnLetter($colIdx++) . $avgRow, '');
        $sheet->setCellValue($this->columnLetter($colIdx++) . $avgRow, 'Class Average');
        foreach ($subjects as $subject) {
            $s = $subjectTotals[$subject->id];
            $avg = $s['count'] > 0 ? round($s['sum'] / $s['count'], 1) . '%' : '—';
            $sheet->setCellValue($this->columnLetter($colIdx++) . $avgRow, $avg);
        }
        $sheet->setCellValue($this->columnLetter($colIdx++) . $avgRow, '');
        $sheet->setCellValue($this->columnLetter($colIdx++) . $avgRow, '');

        $sheet->getStyle('A' . $avgRow . ':' . $lastColLetter . $avgRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F0F0F']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5F6F8'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '5E9A31'],
                ],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getStyle('B' . $avgRow)->getAlignment()->setHorizontal('left');

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(28);
        $letter = 3;
        foreach ($subjects as $subject) {
            $sheet->getColumnDimension($this->columnLetter($letter))->setWidth(14);
            $letter++;
        }
        $sheet->getColumnDimension($this->columnLetter($letter))->setWidth(10);
        $sheet->getColumnDimension($this->columnLetter($letter + 1))->setWidth(12);

        $sheet->freezePane('C' . ($headerRow + 1));

        $sheet->setAutoFilter($headerRange);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);

        $filename = 'broadsheet-'
            . str_replace(' ', '-', strtolower($selectedClass->name))
            . '-' . str_replace(' ', '-', strtolower($currentTerm->name ?? 'term'))
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

    /**
     * Student rankings — sorted by position for the selected term.
     */
    public function rankings(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = \App\Models\Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $classes = \App\Models\Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id') ?? $classes->first()?->id;
        $selectedClass = $selectedClassId
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        $rows = [];

        if ($selectedClass && $currentTerm && $currentYear) {
            $students = \App\Models\Student::where('school_id', $school->id)
                ->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                    $q->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();

            $studentIds = $students->pluck('id');

            $termResults = \App\Models\TermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->keyBy('student_id');

            $subjectCounts = \App\Models\SubjectTermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->select(
                    'student_id',
                    \DB::raw('COUNT(*) as cnt'),
                    \DB::raw('SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_cnt')
                )
                ->groupBy('student_id')
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $tr = $termResults->get($student->id);
                $sc = $subjectCounts->get($student->id);

                $rows[] = [
                    'student' => $student,
                    'position' => $tr->position ?? null,
                    'class_size' => $tr->class_size ?? null,
                    'average' => $tr->overall_percentage ?? 0,
                    'achievement_level' => $tr->achievement_level ?? '—',
                    'promotion_status' => $tr->promotion_status ?? 'pending',
                    'subjects_taken' => $sc->cnt ?? 0,
                    'subjects_passed' => $sc->passed_cnt ?? 0,
                ];
            }

            usort($rows, function ($a, $b) {
                $ap = $a['position'] ?? PHP_INT_MAX;
                $bp = $b['position'] ?? PHP_INT_MAX;
                return $ap <=> $bp;
            });
        }

        return view('reports.rankings', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'rows' => $rows,
        ]);
    }

    /**
     * Merit list — top N students per class (or school-wide).
     */
    public function merit(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = \App\Models\Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $classes = \App\Models\Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id', 'all');
        $selectedClass = $selectedClassId !== 'all'
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        $limit = (int) $request->query('limit', 10);
        if (!in_array($limit, [5, 10, 20, 50])) {
            $limit = 10;
        }

        $rows = [];

        if ($currentTerm && $currentYear) {
            $studentsQuery = \App\Models\Student::where('school_id', $school->id);
            if ($selectedClass) {
                $studentsQuery->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                    $q->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            } else {
                $studentsQuery->whereHas('enrollments', function ($q) use ($currentYear) {
                    $q->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            }

            $studentIds = $studentsQuery->pluck('id');

            $termResults = \App\Models\TermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->with('student')
                ->get();

            $subjectCounts = \App\Models\SubjectTermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->select(
                    'student_id',
                    \DB::raw('COUNT(*) as cnt'),
                    \DB::raw('SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_cnt')
                )
                ->groupBy('student_id')
                ->get()
                ->keyBy('student_id');

            $classLookup = \DB::table('enrollments')
                ->join('classes', 'classes.id', '=', 'enrollments.class_id')
                ->where('enrollments.academic_year_id', $currentYear->id)
                ->where('enrollments.status', 'active')
                ->whereIn('enrollments.student_id', $studentIds)
                ->select('enrollments.student_id', 'classes.name as class_name')
                ->get()
                ->keyBy('student_id');

            $all = [];
            foreach ($termResults as $tr) {
                $student = $tr->student;
                if (!$student)
                    continue;

                $sc = $subjectCounts->get($student->id);

                $all[] = [
                    'student' => $student,
                    'class_name' => $classLookup->get($student->id)?->class_name ?? '—',
                    'position' => $tr->position,
                    'average' => $tr->overall_percentage ?? 0,
                    'achievement_level' => $tr->achievement_level ?? '—',
                    'subjects_taken' => $sc->cnt ?? 0,
                    'subjects_passed' => $sc->passed_cnt ?? 0,
                    'class_size' => $tr->class_size ?? null,
                ];
            }

            usort($all, fn($a, $b) => $b['average'] <=> $a['average']);

            $rows = array_slice($all, 0, $limit);
        }

        return view('reports.merit', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'rows' => $rows,
            'limit' => $limit,
        ]);
    }

    /**
     * Pass list — students who passed, grouped by overall grade band.
     */
    public function passList(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = \App\Models\Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $classes = \App\Models\Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id', 'all');
        $selectedClass = $selectedClassId !== 'all'
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        $passThreshold = (float) $request->query('pass', 50);
        if ($passThreshold < 0 || $passThreshold > 100) {
            $passThreshold = 50;
        }

        $bands = [
            'A' => ['min' => 80, 'label' => 'Distinction', 'color' => 'a'],
            'B' => ['min' => 70, 'label' => 'Very Good', 'color' => 'b'],
            'C' => ['min' => 60, 'label' => 'Good', 'color' => 'c'],
            'D' => ['min' => 50, 'label' => 'Pass', 'color' => 'd'],
            'E' => ['min' => 40, 'label' => 'Weak Pass', 'color' => 'e'],
            'F' => ['min' => 0, 'label' => 'Fail', 'color' => 'f'],
        ];

        $students = collect();
        $grouped = collect();
        $summary = [
            'total' => 0,
            'passed' => 0,
            'failed' => 0,
            'pass_rate' => 0,
        ];

        if ($currentTerm && $currentYear) {
            $studentsQuery = \App\Models\Student::where('school_id', $school->id);
            if ($selectedClass) {
                $studentsQuery->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                    $q->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            } else {
                $studentsQuery->whereHas('enrollments', function ($q) use ($currentYear) {
                    $q->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            }

            $studentIds = $studentsQuery->pluck('id');

            $termResults = \App\Models\TermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->with('student')
                ->get()
                ->keyBy('student_id');

            $classLookup = \DB::table('enrollments')
                ->join('classes', 'classes.id', '=', 'enrollments.class_id')
                ->where('enrollments.academic_year_id', $currentYear->id)
                ->where('enrollments.status', 'active')
                ->whereIn('enrollments.student_id', $studentIds)
                ->select('enrollments.student_id', 'classes.name as class_name')
                ->get()
                ->keyBy('student_id');

            $subjectCounts = \App\Models\SubjectTermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->select(
                    'student_id',
                    \DB::raw('COUNT(*) as cnt'),
                    \DB::raw('SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_cnt')
                )
                ->groupBy('student_id')
                ->get()
                ->keyBy('student_id');

            foreach ($termResults as $tr) {
                $student = $tr->student;
                if (!$student)
                    continue;

                $overall = $tr->overall_percentage ?? 0;
                $letter = strtoupper($tr->grade_letter ?? $this->defaultGrade($overall));
                $passed = $overall >= $passThreshold;

                $sc = $subjectCounts->get($student->id);

                $row = [
                    'student' => $student,
                    'class_name' => $classLookup->get($student->id)?->class_name ?? '—',
                    'average' => $overall,
                    'grade' => $letter,
                    'position' => $tr->position,
                    'class_size' => $tr->class_size,
                    'promotion_status' => $tr->promotion_status,
                    'achievement_level' => $tr->achievement_level,
                    'subjects_taken' => $sc->cnt ?? 0,
                    'subjects_passed' => $sc->passed_cnt ?? 0,
                    'passed' => $passed,
                ];

                $students->push($row);
                $summary['total']++;
                if ($passed) {
                    $summary['passed']++;
                } else {
                    $summary['failed']++;
                }
            }

            $grouped = $students
                ->groupBy('grade')
                ->sortKeys();

            $grouped = $grouped->map(function ($group) {
                return $group->sortByDesc('average')->values();
            });

            $summary['pass_rate'] = $summary['total'] > 0
                ? round(($summary['passed'] / $summary['total']) * 100, 1)
                : 0;
        }

        return view('reports.pass-list', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'grouped' => $grouped,
            'bands' => $bands,
            'summary' => $summary,
            'passThreshold' => $passThreshold,
        ]);
    }

    /**
     * Grade analysis — A/B/C/D/F distribution per subject per class.
     */
    public function gradeAnalysis(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = \App\Models\Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $classes = \App\Models\Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id', 'all');
        $selectedClass = $selectedClassId !== 'all'
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        $subjects = collect();
        $subjectDistribution = [];
        $subjectTotals = [];
        $subjectAverages = [];
        $subjectPassRates = [];
        $overallDistribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0];
        $grandTotal = 0;

        if ($currentTerm && $currentYear) {
            $studentsQuery = \App\Models\Student::where('school_id', $school->id);

            if ($selectedClass) {
                $studentsQuery->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                    $q->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            } else {
                $studentsQuery->whereHas('enrollments', function ($q) use ($currentYear) {
                    $q->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            }

            $studentIds = $studentsQuery->pluck('id');

            $results = \App\Models\SubjectTermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->with('subject')
                ->get();

            $subjects = $results->pluck('subject')
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->values();

            $bySubject = $results->groupBy('subject_id');

            foreach ($subjects as $subject) {
                $subjectResults = $bySubject->get($subject->id, collect());

                $dist = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0];
                $sum = 0;
                $passed = 0;
                $count = 0;

                foreach ($subjectResults as $r) {
                    $grade = strtoupper($r->grade_letter ?? '');
                    if (array_key_exists($grade, $dist)) {
                        $dist[$grade]++;
                    }
                    $sum += $r->final_percentage;
                    if ($r->passed)
                        $passed++;
                    $count++;
                }

                $subjectDistribution[$subject->id] = $dist;
                $subjectTotals[$subject->id] = $count;
                $subjectAverages[$subject->id] = $count > 0 ? round($sum / $count, 1) : 0;
                $subjectPassRates[$subject->id] = $count > 0 ? round(($passed / $count) * 100, 1) : 0;

                foreach ($dist as $letter => $n) {
                    $overallDistribution[$letter] += $n;
                }
                $grandTotal += $count;
            }
        }

        $overallPercentages = [];
        foreach ($overallDistribution as $letter => $n) {
            $overallPercentages[$letter] = $grandTotal > 0 ? round(($n / $grandTotal) * 100, 1) : 0;
        }

        return view('reports.grade-analysis', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'subjects' => $subjects,
            'subjectDistribution' => $subjectDistribution,
            'subjectTotals' => $subjectTotals,
            'subjectAverages' => $subjectAverages,
            'subjectPassRates' => $subjectPassRates,
            'overallDistribution' => $overallDistribution,
            'overallPercentages' => $overallPercentages,
            'grandTotal' => $grandTotal,
        ]);
    }

    /**
     * Subject performance — mean, pass rate, and delta vs previous term.
     */
    public function subjectPerformance(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = \App\Models\Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $previousTerm = null;
        if ($currentTerm) {
            $previousTerm = $allTerms
                ->filter(fn($t) => $t->id !== $currentTerm->id)
                ->first(function ($t) use ($currentTerm) {
                    if ($t->academic_year_id < $currentTerm->academic_year_id)
                        return true;
                    if (
                        $t->academic_year_id == $currentTerm->academic_year_id
                        && $t->sequence < $currentTerm->sequence
                    )
                        return true;
                    return false;
                });
        }

        $classes = \App\Models\Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id', 'all');
        $selectedClass = $selectedClassId !== 'all'
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        $rows = [];

        if ($currentTerm && $currentYear) {
            $studentsQuery = \App\Models\Student::where('school_id', $school->id);
            if ($selectedClass) {
                $studentsQuery->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                    $q->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            } else {
                $studentsQuery->whereHas('enrollments', function ($q) use ($currentYear) {
                    $q->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            }

            $studentIds = $studentsQuery->pluck('id');

            $results = \App\Models\SubjectTermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->with('subject')
                ->get();

            $previousResults = $previousTerm
                ? \App\Models\SubjectTermResult::where('term_id', $previousTerm->id)
                    ->whereIn('student_id', $studentIds)
                    ->get()
                : collect();

            $bySubject = $results->groupBy('subject_id');
            $prevBySubject = $previousResults->groupBy('subject_id');

            $subjects = $results->pluck('subject')
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->values();

            foreach ($subjects as $subject) {
                $subjectResults = $bySubject->get($subject->id, collect());
                $prevSubjectResults = $prevBySubject->get($subject->id, collect());

                $count = $subjectResults->count();
                if ($count === 0)
                    continue;

                $scores = $subjectResults->pluck('final_percentage')->toArray();
                $mean = round(array_sum($scores) / $count, 1);
                $min = round(min($scores), 1);
                $max = round(max($scores), 1);

                sort($scores);
                $middle = (int) floor($count / 2);
                $median = $count % 2 === 0
                    ? round(($scores[$middle - 1] + $scores[$middle]) / 2, 1)
                    : round($scores[$middle], 1);

                $passed = $subjectResults->where('passed', true)->count();
                $passRate = $count > 0 ? round(($passed / $count) * 100, 1) : 0;

                $grades = $subjectResults->pluck('grade_letter');
                $dist = [
                    'A' => $grades->where('A')->count(),
                    'B' => $grades->where('B')->count(),
                    'C' => $grades->where('C')->count(),
                    'D' => $grades->where('D')->count(),
                    'E' => $grades->where('E')->count(),
                    'F' => $grades->where('F')->count(),
                ];

                $prevCount = $prevSubjectResults->count();
                $prevMean = $prevCount > 0
                    ? round($prevSubjectResults->avg('final_percentage'), 1)
                    : null;

                $delta = $prevMean !== null ? round($mean - $prevMean, 1) : null;

                $rows[] = [
                    'subject' => $subject,
                    'count' => $count,
                    'mean' => $mean,
                    'median' => $median,
                    'min' => $min,
                    'max' => $max,
                    'pass_rate' => $passRate,
                    'distribution' => $dist,
                    'prev_mean' => $prevMean,
                    'delta' => $delta,
                ];
            }

            usort($rows, fn($a, $b) => $a['mean'] <=> $b['mean']);
        }

        $schoolStats = null;
        if (!empty($rows)) {
            $allMeans = array_column($rows, 'mean');
            $allPassRates = array_column($rows, 'pass_rate');
            $schoolStats = [
                'subject_count' => count($rows),
                'best_subject' => collect($rows)->sortByDesc('mean')->first()['subject']->name,
                'worst_subject' => collect($rows)->sortBy('mean')->first()['subject']->name,
                'overall_mean' => round(array_sum($allMeans) / count($allMeans), 1),
                'overall_pass_rate' => round(array_sum($allPassRates) / count($allPassRates), 1),
            ];
        }

        return view('reports.subject-performance', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'previousTerm' => $previousTerm,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'rows' => $rows,
            'schoolStats' => $schoolStats,
        ]);
    }

    /**
     * Statistics — mean, median, stddev, distribution, top/bottom performers.
     */
    public function statistics(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        $allTerms = \App\Models\Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderByDesc('sequence')
            ->get();

        $currentTermId = $request->query('term') ?? optional($allTerms->first())->id;
        $currentTerm = $allTerms->firstWhere('id', (int) $currentTermId);
        $currentYear = $currentTerm?->academicYear;

        $classes = \App\Models\Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id', 'all');
        $selectedClass = $selectedClassId !== 'all'
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        $overall = [
            'count' => 0,
            'mean' => 0,
            'median' => 0,
            'stddev' => 0,
            'min' => 0,
            'max' => 0,
            'q1' => 0,
            'q3' => 0,
            'pass_rate' => 0,
        ];

        $bands = [];
        $top = [];
        $bottom = [];
        $subjectStats = [];

        if ($currentTerm && $currentYear) {
            $studentsQuery = \App\Models\Student::where('school_id', $school->id);
            if ($selectedClass) {
                $studentsQuery->whereHas('enrollments', function ($q) use ($selectedClass, $currentYear) {
                    $q->where('class_id', $selectedClass->id)
                        ->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            } else {
                $studentsQuery->whereHas('enrollments', function ($q) use ($currentYear) {
                    $q->where('academic_year_id', $currentYear->id)
                        ->where('status', 'active');
                });
            }

            $studentIds = $studentsQuery->pluck('id');

            $termResults = \App\Models\TermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->with('student')
                ->get();

            $scores = $termResults->pluck('overall_percentage')->filter(fn($v) => $v !== null)->toArray();

            if (!empty($scores)) {
                sort($scores);
                $count = count($scores);
                $sum = array_sum($scores);
                $mean = $sum / $count;

                $mid = (int) floor($count / 2);
                $median = $count % 2 === 0
                    ? ($scores[$mid - 1] + $scores[$mid]) / 2
                    : $scores[$mid];

                $variance = array_sum(array_map(fn($v) => pow($v - $mean, 2), $scores)) / $count;
                $stddev = sqrt($variance);

                $q1Pos = (int) floor($count * 0.25);
                $q3Pos = (int) floor($count * 0.75);
                $q1 = $scores[$q1Pos] ?? 0;
                $q3 = $scores[$q3Pos] ?? 0;

                $passed = count(array_filter($scores, fn($v) => $v >= 50));

                $overall = [
                    'count' => $count,
                    'mean' => round($mean, 1),
                    'median' => round($median, 1),
                    'stddev' => round($stddev, 1),
                    'min' => round(min($scores), 1),
                    'max' => round(max($scores), 1),
                    'q1' => round($q1, 1),
                    'q3' => round($q3, 1),
                    'pass_rate' => round(($passed / $count) * 100, 1),
                ];
            }

            $bandDefs = [
                ['label' => '90–100', 'min' => 90, 'max' => 100, 'color' => '#16A34A'],
                ['label' => '80–89', 'min' => 80, 'max' => 89, 'color' => '#22C55E'],
                ['label' => '70–79', 'min' => 70, 'max' => 79, 'color' => '#2563EB'],
                ['label' => '60–69', 'min' => 60, 'max' => 69, 'color' => '#7C3AED'],
                ['label' => '50–59', 'min' => 50, 'max' => 59, 'color' => '#D97706'],
                ['label' => '40–49', 'min' => 40, 'max' => 49, 'color' => '#EA580C'],
                ['label' => '0–39', 'min' => 0, 'max' => 39, 'color' => '#DC2626'],
            ];

            foreach ($bandDefs as $band) {
                $n = count(array_filter($scores, fn($v) => $v >= $band['min'] && $v <= $band['max']));
                $bands[] = [
                    'label' => $band['label'],
                    'count' => $n,
                    'pct' => !empty($scores) ? round(($n / count($scores)) * 100, 1) : 0,
                    'color' => $band['color'],
                ];
            }

            $sorted = $termResults->sortByDesc('overall_percentage')->values();
            $top = $sorted->take(5)->all();
            $bottom = $sorted->reverse()->take(5)->values()->all();

            $subjectResults = \App\Models\SubjectTermResult::where('term_id', $currentTerm->id)
                ->whereIn('student_id', $studentIds)
                ->with('subject')
                ->get();

            $bySubject = $subjectResults->groupBy('subject_id');

            foreach ($bySubject as $subjectId => $results) {
                $subject = $results->first()->subject;
                if (!$subject)
                    continue;

                $subScores = $results->pluck('final_percentage')->toArray();
                if (empty($subScores))
                    continue;

                sort($subScores);
                $c = count($subScores);
                $m = array_sum($subScores) / $c;

                $midIdx = (int) floor($c / 2);
                $med = $c % 2 === 0
                    ? ($subScores[$midIdx - 1] + $subScores[$midIdx]) / 2
                    : $subScores[$midIdx];

                $v = array_sum(array_map(fn($x) => pow($x - $m, 2), $subScores)) / $c;
                $sd = sqrt($v);

                $passCount = count(array_filter($subScores, fn($x) => $x >= 50));

                $subjectStats[] = [
                    'subject' => $subject->name,
                    'count' => $c,
                    'mean' => round($m, 1),
                    'median' => round($med, 1),
                    'stddev' => round($sd, 1),
                    'min' => round(min($subScores), 1),
                    'max' => round(max($subScores), 1),
                    'pass_rate' => round(($passCount / $c) * 100, 1),
                ];
            }

            usort($subjectStats, fn($a, $b) => $a['mean'] <=> $b['mean']);
        }

        return view('reports.statistics', [
            'allTerms' => $allTerms,
            'currentTerm' => $currentTerm,
            'currentTermId' => $currentTerm?->id,
            'currentYear' => $currentYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'overall' => $overall,
            'bands' => $bands,
            'top' => $top,
            'bottom' => $bottom,
            'subjectStats' => $subjectStats,
        ]);
    }

    /**
     * Export center (bulk PDF/Excel/CSV).
     */
    public function export()
    {
        return view('reports.export');
    }

    /**
     * Render the report card view (default browser view).
     */
    public function reportCard(Student $student)
    {
        abort_unless($student->school_id === auth()->user()->school_id, 403);

        $data = ReportCardBuilder::build($student);

        return view('reports.report-card', $data);
    }

    /**
     * Stream the report card as a PDF download.
     */
    public function downloadPdf(Student $student)
    {
        abort_unless($student->school_id === auth()->user()->school_id, 403);

        $data = ReportCardBuilder::build($student);

        $pdf = Pdf::loadView('reports.report-card-pdf', $data)
            ->setPaper('A4', 'portrait');

        $filename = 'report-card-' . str_replace(' ', '-', strtolower($student->full_name))
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Email the report card PDF to one or all guardians of the student.
     */
    public function emailReportCard(Request $request, Student $student)
    {
        abort_unless($student->school_id === auth()->user()->school_id, 403);

        if (!\App\Services\Notifications::enabled('report_card_sent')) {
            return back()->withErrors([
                'guardian' => 'Report card emails are disabled for this school. Enable them under Settings → Notifications.',
            ]);
        }

        $data = $request->validate([
            'guardian_id' => 'nullable|exists:users,id',
            'message' => 'nullable|string|max:2000',
        ]);

        $guardians = $data['guardian_id']
            ? $student->guardians()->where('users.id', $data['guardian_id'])->get()
            : $student->guardians;

        if ($guardians->isEmpty()) {
            return back()->withErrors(['guardian' => 'No guardians linked to this student.']);
        }

        // Render the PDF once and save it to disk. Binary bytes cannot survive
        // JSON encoding of the queued mailable, so we pass a path instead.
        $pdfPath = $this->saveReportCardPdfToDisk($student);

        foreach ($guardians as $g) {
            Mail::to($g->email)->queue(
                new ReportCardEmail($student, $pdfPath, $data['message'] ?? null)
            );

            $student->guardians()->updateExistingPivot($g->id, [
                'last_contacted_at' => now(),
            ]);
        }

        $count = $guardians->count();
        return back()->with('success', "Report card emailed to {$count} guardian(s).");
    }

    /**
     * Render the report card PDF and write it to storage. Returns the absolute
     * path so the queued mailable can read it later.
     */
    private function saveReportCardPdfToDisk(Student $student): string
    {
        $data = ReportCardBuilder::build($student);

        $binary = Pdf::loadView('reports.report-card-pdf', $data)
            ->setPaper('A4', 'portrait')
            ->output();

        $dir = storage_path('app/report-card-exports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'report-card-' . $student->id . '-' . uniqid() . '.pdf';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        file_put_contents($path, $binary);

        return $path;
    }

    private function defaultGrade(float $score): string
    {
        return match (true) {
            $score >= 80 => 'A',
            $score >= 70 => 'B',
            $score >= 60 => 'C',
            $score >= 50 => 'D',
            default => 'F',
        };
    }

    /**
     * Helper: convert 1-based column index to letter (1 => A, 27 => AA).
     */
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