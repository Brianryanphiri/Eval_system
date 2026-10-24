<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\EmailTemplate;
use App\Models\Student;
use App\Models\User;
use App\Services\BulkCommunicationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunicationsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $school = $user->school;

        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $gradeLevels = Classroom::where('school_id', $school->id)
            ->whereNotNull('grade_level')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');

        $templates = EmailTemplate::forSchool($school->id)
            ->whereIn('key', ['bulk_announcement', 'guardian_update'])
            ->where('is_active', true)
            ->get()
            ->keyBy('key');

        $defaultTemplates = [
            'bulk_announcement' => [
                'name' => 'Bulk Announcement',
                'subject' => '{{school_name}}: Announcement',
                'body' => "Dear {{guardian_name}},\n\nPlease note the following announcement from {{school_name}}:\n\n{{message}}\n\nRegards,\n{{school_name}}",
            ],
            'guardian_update' => [
                'name' => 'Guardian Progress Update',
                'subject' => 'Progress update - {{student_name}}',
                'body' => "Dear {{guardian_name}},\n\nThis is a progress update regarding {{student_name}}.\n\n{{message}}\n\n{{school_name}}",
            ],
        ];

        $templatesForView = collect($defaultTemplates)->map(function ($default, $key) use ($templates) {
            $saved = $templates->get($key);
            return [
                'key' => $key,
                'name' => $saved->name ?? $default['name'],
                'subject' => $saved->subject ?? $default['subject'],
                'body' => $saved->body ?? $default['body'],
            ];
        })->values();

        return view('communications.index', [
            'classes' => $classes,
            'gradeLevels' => $gradeLevels,
            'templates' => $templatesForView,
        ]);
    }

    public function previewRecipients(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->school_id !== null, 403);

        $data = $request->validate([
            'group' => 'required|in:class,year,school,guardian',
            'class_id' => 'nullable|integer|exists:classes,id',
            'grade_level' => 'nullable|string|max:64',
            'guardian_id' => 'nullable|integer|exists:users,id',
        ]);

        $school = $user->school;
        $studentIds = collect();

        if ($data['group'] === 'class') {
            if (empty($data['class_id'])) {
                return response()->json(['error' => 'Class is required.'], 422);
            }
            $studentIds = $this->studentIdsInClass($school->id, (int) $data['class_id']);
        } elseif ($data['group'] === 'year') {
            if (empty($data['grade_level'])) {
                return response()->json(['error' => 'Year group is required.'], 422);
            }
            $classIds = Classroom::where('school_id', $school->id)
                ->where('grade_level', $data['grade_level'])
                ->pluck('id');
            $studentIds = $this->studentIdsInClasses($school->id, $classIds);
        } elseif ($data['group'] === 'school') {
            $studentIds = $this->allActiveStudentIds($school->id);
        } elseif ($data['group'] === 'guardian') {
            if (empty($data['guardian_id'])) {
                return response()->json(['error' => 'Guardian is required.'], 422);
            }

            $guardian = User::where('id', $data['guardian_id'])
                ->where('school_id', $school->id)
                ->first();

            if (!$guardian) {
                return response()->json(['error' => 'Guardian not found in your school.'], 422);
            }

            return response()->json([
                'count' => 1,
                'preview' => [
                    ['name' => $guardian->name, 'email' => $guardian->email],
                ],
            ]);
        }

        $guardians = $this->guardiansForStudentIds($studentIds);

        return response()->json([
            'count' => $guardians->count(),
            'preview' => $guardians->take(5)->map(fn($g) => [
                'name' => $g->name,
                'email' => $g->email,
            ])->values(),
        ]);
    }

    public function searchGuardians(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->school_id !== null, 403);

        $q = trim((string) $request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $school = $user->school;

        $guardians = User::where('school_id', $school->id)
            ->where('role', 'parent')
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($guardians);
    }

    public function send(Request $request, BulkCommunicationService $service)
    {
        $user = auth()->user();
        abort_unless($user->school_id !== null, 403);

        $data = $request->validate([
            'group' => 'required|in:class,year,school,guardian',
            'class_id' => 'nullable|integer|exists:classes,id',
            'grade_level' => 'nullable|string|max:64',
            'guardian_id' => 'nullable|integer|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|min:10|max:10000',
        ]);

        $school = $user->school;
        $recipients = $service->resolveRecipients($school, $data['group'], $data);

        if ($recipients->isEmpty()) {
            return back()->withErrors([
                'group' => 'No recipients match this selection. Nothing was sent.',
            ]);
        }

        $currentTerm = $school->currentAcademicYear()?->currentTerm();

        $count = $service->sendToRecipients(
            $school,
            $recipients,
            $data['subject'],
            $data['body'],
            $currentTerm
        );

        return back()
            ->with('success', "Bulk communication queued to {$count} recipient(s).")
            ->with('bulk_sent_count', $count);
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->school_id !== null, 403);

        $school = $user->school;

        $query = DB::table('email_log')
            ->where('school_id', $school->id)
            ->where('template_key', 'bulk_communication');

        if ($request->filled('status')) {
            $request->validate(['status' => 'in:queued,sent,delivered,bounced,failed']);
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('from')) {
            $request->validate(['from' => 'date']);
            $query->where('created_at', '>=', $request->date('from')->startOfDay());
        }

        if ($request->filled('to')) {
            $request->validate(['to' => 'date']);
            $query->where('created_at', '<=', $request->date('to')->endOfDay());
        }

        $batches = $query
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as batch_key')
            ->selectRaw('MIN(created_at) as sent_at')
            ->selectRaw('MIN(subject) as subject')
            ->selectRaw('COUNT(*) as recipient_count')
            ->selectRaw('SUM(CASE WHEN status IN ("sent","delivered") THEN 1 ELSE 0 END) as delivered_count')
            ->selectRaw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count')
            ->selectRaw('SUM(CASE WHEN status = "queued" THEN 1 ELSE 0 END) as queued_count')
            ->groupBy('batch_key')
            ->orderByDesc('batch_key')
            ->paginate(20)
            ->withQueryString();

        return view('communications.history', [
            'batches' => $batches,
            'filters' => [
                'status' => $request->query('status'),
                'from' => $request->query('from'),
                'to' => $request->query('to'),
            ],
        ]);
    }

    public function historyDetail(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->school_id !== null, 403);

        $data = $request->validate([
            'batch_key' => 'required|date_format:Y-m-d H:i',
        ]);

        $school = $user->school;
        $start = Carbon::createFromFormat('Y-m-d H:i', $data['batch_key']);
        $end = $start->copy()->addMinute();

        $rows = DB::table('email_log')
            ->where('school_id', $school->id)
            ->where('template_key', 'bulk_communication')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->orderBy('recipient')
            ->get([
                'id',
                'recipient',
                'subject',
                'status',
                'sent_at',
                'error',
                'created_at',
            ]);

        return view('communications.history-detail', [
            'rows' => $rows,
            'batchKey' => $data['batch_key'],
            'subject' => $rows->first()->subject ?? '(no subject)',
        ]);
    }

    private function studentIdsInClass(int $schoolId, int $classId)
    {
        $currentYearId = AcademicYear::where('school_id', $schoolId)
            ->where('is_current', true)
            ->value('id');

        if (!$currentYearId) {
            return collect();
        }

        return DB::table('enrollments')
            ->where('class_id', $classId)
            ->where('academic_year_id', $currentYearId)
            ->where('status', 'active')
            ->pluck('student_id');
    }

    private function studentIdsInClasses(int $schoolId, $classIds)
    {
        if ($classIds->isEmpty()) {
            return collect();
        }

        $currentYearId = AcademicYear::where('school_id', $schoolId)
            ->where('is_current', true)
            ->value('id');

        if (!$currentYearId) {
            return collect();
        }

        return DB::table('enrollments')
            ->whereIn('class_id', $classIds)
            ->where('academic_year_id', $currentYearId)
            ->where('status', 'active')
            ->pluck('student_id');
    }

    private function allActiveStudentIds(int $schoolId)
    {
        return DB::table('enrollments')
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->where('students.school_id', $schoolId)
            ->where('enrollments.status', 'active')
            ->pluck('enrollments.student_id');
    }

    private function guardiansForStudentIds($studentIds)
    {
        if ($studentIds->isEmpty()) {
            return collect();
        }

        return User::whereIn('id', function ($q) use ($studentIds) {
            $q->select('guardian_id')
                ->from('student_guardian')
                ->whereIn('student_id', $studentIds);
        })
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'name', 'email')
            ->distinct()
            ->orderBy('name')
            ->get();
    }
}