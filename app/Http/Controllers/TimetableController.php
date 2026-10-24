<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\ClassTimetable;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TimetablePeriod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TimetableController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $school = $user->school;

        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        if ($user->role === 'teacher') {
            $assignedClassIds = DB::table('class_subject')
                ->where('teacher_id', $user->id)
                ->pluck('class_id')
                ->unique()
                ->all();

            $classes = $classes->whereIn('id', $assignedClassIds)->values();
        }

        return view('timetable.index', [
            'classes' => $classes,
        ]);
    }

    public function show(Classroom $classroom, Request $request)
    {
        $data = $this->buildGridData($classroom, $request);

        return view('timetable.show', array_merge($data, [
            'canEdit' => $this->canEdit(auth()->user(), $classroom),
            'conflicts' => $this->detectConflicts(
                $classroom->school_id,
                $data['selectedTerm']?->id,
                $classroom->id
            ),
            'defaultMessage' => $this->buildDefaultEmailMessage($classroom, $data['selectedTerm']),
        ]));
    }

    /**
     * Show a single teacher's week across all classes.
     */
    public function teacher(?User $teacher = null, Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        abort_unless($school !== null, 403);

        $adminRoles = ['super_admin', 'school_admin', 'academic_director', 'head_of_department'];

        if (in_array($user->role, $adminRoles, true)) {
            if (!$teacher || $teacher->school_id !== $school->id) {
                $teacher = User::where('school_id', $school->id)
                    ->whereIn('role', ['teacher', 'head_of_department', 'academic_director'])
                    ->orderBy('name')
                    ->first();
            }
        } else {
            abort_unless($user->role === 'teacher', 403);
            $teacher = $user;
        }

        abort_unless($teacher !== null, 404);

        $teachers = in_array($user->role, $adminRoles, true)
            ? User::where('school_id', $school->id)
                ->whereIn('role', ['teacher', 'head_of_department', 'academic_director'])
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect([$user->only(['id', 'name'])]);

        $data = $this->buildTeacherGridData($school, $teacher, $request);

        return view('timetable.teacher', array_merge($data, [
            'teachers' => $teachers,
            'isAdmin' => in_array($user->role, $adminRoles, true),
        ]));
    }

    /**
     * Download a teacher's timetable as a PDF.
     */
    public function teacherPdf(?User $teacher = null, Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        abort_unless($school !== null, 403);

        $adminRoles = ['super_admin', 'school_admin', 'academic_director', 'head_of_department'];

        if (in_array($user->role, $adminRoles, true)) {
            if (!$teacher || $teacher->school_id !== $school->id) {
                $teacher = User::where('school_id', $school->id)
                    ->whereIn('role', ['teacher', 'head_of_department', 'academic_director'])
                    ->orderBy('name')
                    ->first();
            }
        } else {
            abort_unless($user->role === 'teacher', 403);
            $teacher = $user;
        }

        abort_unless($teacher !== null, 404);

        $data = $this->buildTeacherGridData($school, $teacher, $request);

        $logoPath = !empty($school->logo_path) && is_file(\Storage::disk('public')->path($school->logo_path))
            ? \Storage::disk('public')->path($school->logo_path)
            : null;

        $pdf = Pdf::loadView('timetable.teacher-pdf', array_merge($data, [
            'files' => ['logo' => $logoPath],
        ]))->setPaper('A4', 'landscape');

        $filename = 'timetable-'
            . str_replace(' ', '-', strtolower($teacher->name))
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Pre-fill the email modal with the same template the sender will
     * fall back to if the textarea is emptied. The guardian name is left
     * as the literal "Parent" because it is substituted per recipient
     * when the email actually goes out.
     */
    private function buildDefaultEmailMessage(Classroom $classroom, ?Term $selectedTerm): string
    {
        $template = app(\App\Http\Controllers\TimetableEmailController::class)
            ->resolveTemplate($classroom->school_id);

        return str_replace(
            ['{{guardian_name}}', '{{class_name}}', '{{term}}', '{{academic_year}}', '{{school_name}}'],
            [
                'Parent',
                $classroom->name,
                $selectedTerm?->name ?? '',
                $selectedTerm?->academicYear->label ?? (string) now()->year,
                $classroom->school->name,
            ],
            $template['body']
        );
    }

    public function save(Classroom $classroom, Request $request)
    {
        $user = auth()->user();
        abort_unless($classroom->school_id === $user->school_id, 403);
        abort_unless($this->canEdit($user, $classroom), 403);

        $data = $request->validate([
            'term_id' => 'required|integer|exists:terms,id',
            'slots' => 'nullable|array',
            'slots.*.day' => 'required|integer|between:1,5',
            'slots.*.period_id' => 'required|integer|exists:timetable_periods,id',
            'slots.*.subject_id' => 'nullable|integer|exists:subjects,id',
            'slots.*.teacher_id' => 'nullable|integer|exists:users,id',
        ]);

        $term = Term::findOrFail($data['term_id']);
        abort_unless($term->academicYear->school_id === $classroom->school_id, 403);

        $teachingPeriodIds = TimetablePeriod::forSchool($classroom->school_id)
            ->teaching()
            ->pluck('id')
            ->all();

        DB::transaction(function () use ($classroom, $term, $data, $teachingPeriodIds) {
            ClassTimetable::where('class_id', $classroom->id)
                ->where('term_id', $term->id)
                ->delete();

            foreach ($data['slots'] ?? [] as $slot) {
                if (!in_array((int) $slot['period_id'], $teachingPeriodIds, true)) {
                    continue;
                }

                if (empty($slot['subject_id'])) {
                    continue;
                }

                ClassTimetable::create([
                    'school_id' => $classroom->school_id,
                    'class_id' => $classroom->id,
                    'term_id' => $term->id,
                    'day_of_week' => (int) $slot['day'],
                    'period_id' => (int) $slot['period_id'],
                    'subject_id' => (int) $slot['subject_id'],
                    'teacher_id' => !empty($slot['teacher_id']) ? (int) $slot['teacher_id'] : null,
                ]);
            }
        });

        return redirect()
            ->route('timetable.show', ['classroom' => $classroom->id, 'term' => $term->id])
            ->with('success', 'Timetable saved.');
    }

    /**
     * Download the timetable as a PDF.
     */
    public function downloadPdf(Classroom $classroom, Request $request)
    {
        abort_unless($classroom->school_id === auth()->user()->school_id, 403);

        $data = $this->buildGridData($classroom, $request);
        $data = $this->attachPdfFilePaths($data, $classroom);

        $pdf = Pdf::loadView('timetable.pdf', $data)
            ->setPaper('A4', 'landscape');

        $filename = 'timetable-'
            . str_replace(' ', '-', strtolower($classroom->name))
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download the timetable as an Excel file.
     */
    public function downloadXlsx(Classroom $classroom, Request $request)
    {
        abort_unless($classroom->school_id === auth()->user()->school_id, 403);

        $data = $this->buildGridData($classroom, $request);
        $school = $classroom->school;
        $days = ClassTimetable::days();
        $periods = $data['periods'];
        $grid = $data['grid'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($classroom->name, 0, 31));

        $lastColLetter = $this->columnLetter(1 + count($days));

        $sheet->setCellValue('A1', $school->name ?? 'School');
        $sheet->mergeCells('A1:' . $lastColLetter . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0F0F0F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->setCellValue('A2', 'Class Timetable - ' . $classroom->name);
        $sheet->mergeCells('A2:' . $lastColLetter . '2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '5E9A31']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $sheet->setCellValue('A3', ($data['term']) . ' - ' . ($data['academicYear']) . ' - Generated ' . now()->format('d M Y H:i'));
        $sheet->mergeCells('A3:' . $lastColLetter . '3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $headerRow = 5;
        $sheet->setCellValue('A' . $headerRow, 'Period');

        $colIdx = 2;
        foreach ($days as $dayName) {
            $sheet->setCellValue($this->columnLetter($colIdx) . $headerRow, $dayName);
            $colIdx++;
        }

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
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        $rowNum = $headerRow + 1;
        foreach ($periods as $period) {
            $sheet->setCellValue('A' . $rowNum, $period->name . ' (' . $period->time_label . ')');
            $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);

            $colIdx = 2;
            foreach ($days as $dayNum => $dayName) {
                $slot = $grid[$dayNum][$period->id] ?? null;

                $cellValue = '';
                if (!$period->is_break && $slot && $slot->subject) {
                    $cellValue = $slot->subject->name;
                    if ($slot->teacher) {
                        $cellValue .= "\n" . $slot->teacher->name;
                    }
                } elseif ($period->is_break) {
                    $cellValue = $period->name;
                }

                $sheet->setCellValue($this->columnLetter($colIdx) . $rowNum, $cellValue);
                $sheet->getStyle($this->columnLetter($colIdx) . $rowNum)
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setHorizontal('center')
                    ->setVertical('center');
                $colIdx++;
            }

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

        $sheet->getColumnDimension('A')->setWidth(22);
        for ($i = 2; $i <= 1 + count($days); $i++) {
            $sheet->getColumnDimension($this->columnLetter($i))->setWidth(20);
        }

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $filename = 'timetable-'
            . str_replace(' ', '-', strtolower($classroom->name))
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
     * Build the shared grid data used by show, PDF, and Excel.
     */
    private function buildGridData(Classroom $classroom, Request $request): array
    {
        $user = auth()->user();
        abort_unless($classroom->school_id === $user->school_id, 403);

        $school = $classroom->school;

        $terms = Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('sequence')
            ->get();

        $currentTerm = $terms->firstWhere('is_current', true) ?? $terms->first();

        $selectedTermId = $request->query('term') ?? $currentTerm?->id;
        $selectedTerm = $terms->firstWhere('id', (int) $selectedTermId) ?? $currentTerm;

        $periods = TimetablePeriod::forSchool($school->id)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();

        $classSubjects = DB::table('class_subject')
            ->join('subjects', 'subjects.id', '=', 'class_subject.subject_id')
            ->where('class_subject.class_id', $classroom->id)
            ->select('subjects.id', 'subjects.name', 'class_subject.teacher_id')
            ->orderBy('subjects.name')
            ->get();

        $teachersBySubject = [];
        foreach ($classSubjects as $row) {
            if ($row->teacher_id) {
                $teachersBySubject[$row->id][] = (int) $row->teacher_id;
            }
        }

        $allTeachers = User::where('school_id', $school->id)
            ->whereIn('role', ['teacher', 'head_of_department', 'academic_director'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $rows = ClassTimetable::where('class_id', $classroom->id)
            ->where('term_id', $selectedTerm?->id)
            ->get();

        $grid = [];
        foreach (ClassTimetable::days() as $dayNum => $dayName) {
            $grid[$dayNum] = [];
            foreach ($periods as $period) {
                $row = $rows->first(fn($r) => $r->day_of_week === $dayNum && $r->period_id === $period->id);
                $grid[$dayNum][$period->id] = $row;
            }
        }

        return [
            'classroom' => $classroom,
            'school' => $school,
            'terms' => $terms,
            'selectedTerm' => $selectedTerm,
            'periods' => $periods,
            'classSubjects' => $classSubjects,
            'teachersBySubject' => $teachersBySubject,
            'allTeachers' => $allTeachers,
            'grid' => $grid,
            'term' => $selectedTerm?->name ?? 'Current Term',
            'academicYear' => $selectedTerm?->academicYear->label ?? now()->year,
            'address' => method_exists($school, 'fullAddress') ? $school->fullAddress() : ($school->address ?? ''),
            'primary' => $this->hex($school->primary_color ?? null, '#0F0F0F'),
            'accent' => $this->hex($school->accent_color ?? null, '#7AC142'),
        ];
    }

    /**
     * Build the data needed by the teacher timetable view and PDF.
     */
    private function buildTeacherGridData($school, User $teacher, Request $request): array
    {
        $terms = Term::whereHas('academicYear', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('sequence')
            ->get();

        $currentTerm = $terms->firstWhere('is_current', true) ?? $terms->first();
        $selectedTermId = $request->query('term') ?? $currentTerm?->id;
        $selectedTerm = $terms->firstWhere('id', (int) $selectedTermId) ?? $currentTerm;

        $periods = TimetablePeriod::forSchool($school->id)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();

        $rows = collect();
        if ($selectedTerm) {
            $rows = DB::table('class_timetables')
                ->join('classes', 'classes.id', '=', 'class_timetables.class_id')
                ->join('subjects', 'subjects.id', '=', 'class_timetables.subject_id')
                ->where('class_timetables.school_id', $school->id)
                ->where('class_timetables.term_id', $selectedTerm->id)
                ->where('class_timetables.teacher_id', $teacher->id)
                ->select(
                    'class_timetables.day_of_week',
                    'class_timetables.period_id',
                    'classes.name as class_name',
                    'subjects.name as subject_name'
                )
                ->get();
        }

        $grid = [];
        foreach (ClassTimetable::days() as $dayNum => $dayName) {
            $grid[$dayNum] = [];
            foreach ($periods as $period) {
                $grid[$dayNum][$period->id] = $rows->first(
                    fn($r) => $r->day_of_week === $dayNum && $r->period_id === $period->id
                );
            }
        }

        return [
            'school' => $school,
            'teacher' => $teacher,
            'terms' => $terms,
            'selectedTerm' => $selectedTerm,
            'periods' => $periods,
            'grid' => $grid,
            'term' => $selectedTerm?->name ?? 'Current Term',
            'academicYear' => $selectedTerm?->academicYear->label ?? now()->year,
            'address' => method_exists($school, 'fullAddress') ? $school->fullAddress() : ($school->address ?? ''),
            'primary' => $this->hex($school->primary_color ?? null, '#0F0F0F'),
            'accent' => $this->hex($school->accent_color ?? null, '#7AC142'),
            'hasEntries' => $rows->isNotEmpty(),
        ];
    }

    /**
     * Attach filesystem paths for the PDF blade. DomPDF needs real paths,
     * not URLs.
     */
    private function attachPdfFilePaths(array $data, Classroom $classroom): array
    {
        $school = $classroom->school;

        $data['files'] = [
            'logo' => $this->publicFile($school->logo_path ?? null),
        ];

        return $data;
    }

    private function publicFile(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $full = \Storage::disk('public')->path($path);

        return is_file($full) ? $full : null;
    }

    private function hex(?string $value, string $fallback): string
    {
        return ($value && preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) ? $value : $fallback;
    }

    private function detectConflicts(int $schoolId, ?int $termId, int $excludeClassId): array
    {
        if (!$termId) {
            return [];
        }

        $rows = DB::table('class_timetables')
            ->where('school_id', $schoolId)
            ->where('term_id', $termId)
            ->whereNotNull('teacher_id')
            ->select('teacher_id', 'day_of_week', 'period_id')
            ->get();

        $counts = [];
        foreach ($rows as $row) {
            $key = $row->teacher_id . ':' . $row->day_of_week . ':' . $row->period_id;
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        return array_filter($counts, fn($n) => $n > 1);
    }

    private function canEdit(User $user, Classroom $classroom): bool
    {
        if (in_array($user->role, ['super_admin', 'school_admin', 'academic_director', 'head_of_department'], true)) {
            return true;
        }

        if ($user->role === 'teacher') {
            return DB::table('class_subject')
                ->where('class_id', $classroom->id)
                ->where('teacher_id', $user->id)
                ->exists();
        }

        return false;
    }

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