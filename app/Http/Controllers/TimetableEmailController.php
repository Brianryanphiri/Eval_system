<?php

namespace App\Http\Controllers;

use App\Mail\TimetableEmail;
use App\Models\Classroom;
use App\Models\ClassTimetable;
use App\Models\EmailTemplate;
use App\Models\Term;
use App\Models\TimetablePeriod;
use App\Services\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TimetableEmailController extends Controller
{
    public function send(Classroom $classroom, Request $request)
    {
        $user = auth()->user();
        abort_unless($classroom->school_id === $user->school_id, 403);
        abort_unless($this->canEdit($user, $classroom), 403);

        if (!Settings::get('notify.timetable_sent', false)) {
            return back()->withErrors([
                'timetable' => 'Timetable emails are disabled for this school. Enable them under Settings > Notifications.',
            ]);
        }

        $data = $request->validate([
            'term_id' => 'required|integer|exists:terms,id',
            'message' => 'nullable|string|max:5000',
        ]);

        $term = Term::with('academicYear')->findOrFail($data['term_id']);
        abort_unless($term->academicYear->school_id === $classroom->school_id, 403);

        $result = $this->guardiansForClass($classroom, $term);
        $guardians = $result['guardians'];

        if ($guardians->isEmpty()) {
            $message = $result['reason'] === 'no_students'
                ? 'No students are enrolled in this class for the selected term.'
                : 'Students are enrolled in this class, but no guardians are linked to them yet.';

            return back()->withErrors(['timetable' => $message]);
        }

        // Render once, save to disk, pass only the path on the queue.
        // Binary bytes cannot survive JSON encoding of the queued mailable.
        $pdfPath = $this->savePdfToDisk($classroom, $term);

        $subjectLine = 'Timetable for ' . $classroom->name;

        // If the admin left the textarea blank, fall back to the saved
        // template. Otherwise their edited text is the full body.
        $bodyTemplate = trim($data['message'] ?? '') !== ''
            ? $data['message']
            : $this->resolveTemplate($classroom->school_id)['body'];

        foreach ($guardians as $guardian) {
            $bodyForThisGuardian = str_replace(
                ['{{guardian_name}}', '{{class_name}}', '{{term}}', '{{academic_year}}', '{{school_name}}'],
                [
                    $guardian->name,
                    $classroom->name,
                    $term->name,
                    $term->academicYear->label ?? (string) now()->year,
                    $classroom->school->name,
                ],
                $bodyTemplate
            );

            Mail::to($guardian->email)->queue(new TimetableEmail(
                classroom: $classroom,
                termName: $term->name,
                academicYear: $term->academicYear->label ?? (string) now()->year,
                schoolName: $classroom->school->name,
                pdfPath: $pdfPath,
                subjectLine: $subjectLine,
                bodyText: $bodyForThisGuardian,
                guardianName: $guardian->name,
            ));

            DB::table('email_log')->insert([
                'school_id' => $classroom->school_id,
                'recipient' => $guardian->email,
                'subject' => $subjectLine,
                'body_preview' => mb_substr($bodyForThisGuardian, 0, 250),
                'template_key' => 'timetable',
                'status' => 'queued',
                'reference_type' => 'classroom',
                'reference_id' => $classroom->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()
            ->with('success', 'Timetable queued to ' . $guardians->count() . ' guardian(s).')
            ->with('timetable_sent_count', $guardians->count());
    }

    /**
     * Return the guardians of every active student in the class, plus a
     * reason code if the list came back empty so we can show a useful
     * message instead of a generic "no guardians" one.
     */
    private function guardiansForClass(Classroom $classroom, Term $term): array
    {
        $studentIds = DB::table('enrollments')
            ->where('class_id', $classroom->id)
            ->where('academic_year_id', $term->academic_year_id)
            ->where('status', 'active')
            ->pluck('student_id');

        if ($studentIds->isEmpty()) {
            return ['guardians' => collect(), 'reason' => 'no_students'];
        }

        $guardians = DB::table('users')
            ->join('student_guardian', 'student_guardian.guardian_id', '=', 'users.id')
            ->whereIn('student_guardian.student_id', $studentIds)
            ->whereNotNull('users.email')
            ->where('users.email', '!=', '')
            ->select('users.id', 'users.name', 'users.email')
            ->distinct()
            ->get();

        return [
            'guardians' => $guardians,
            'reason' => $guardians->isEmpty() ? 'no_guardians' : null,
        ];
    }

    /**
     * Render the timetable PDF and write it to storage. Returns the absolute
     * path so the queued mailable can read it later.
     */
    private function savePdfToDisk(Classroom $classroom, Term $term): string
    {
        $school = $classroom->school;

        $periods = TimetablePeriod::forSchool($school->id)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();

        $rows = ClassTimetable::where('class_id', $classroom->id)
            ->where('term_id', $term->id)
            ->get();

        $grid = [];
        foreach (ClassTimetable::days() as $dayNum => $dayName) {
            $grid[$dayNum] = [];
            foreach ($periods as $period) {
                $row = $rows->first(fn($r) => $r->day_of_week === $dayNum && $r->period_id === $period->id);
                $grid[$dayNum][$period->id] = $row;
            }
        }

        $logoPath = !empty($school->logo_path) && is_file(\Storage::disk('public')->path($school->logo_path))
            ? \Storage::disk('public')->path($school->logo_path)
            : null;

        $binary = Pdf::loadView('timetable.pdf', [
            'classroom' => $classroom,
            'school' => $school,
            'periods' => $periods,
            'grid' => $grid,
            'term' => $term->name,
            'academicYear' => $term->academicYear->label ?? (string) now()->year,
            'address' => method_exists($school, 'fullAddress') ? $school->fullAddress() : ($school->address ?? ''),
            'primary' => $this->hex($school->primary_color ?? null, '#0F0F0F'),
            'accent' => $this->hex($school->accent_color ?? null, '#7AC142'),
            'files' => ['logo' => $logoPath],
        ])->setPaper('A4', 'landscape')->output();

        $dir = storage_path('app/timetable-exports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'timetable-' . $classroom->id . '-' . $term->id . '-' . uniqid() . '.pdf';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        file_put_contents($path, $binary);

        return $path;
    }

    /**
     * Public so the show view can pre-fill the email modal with the same
     * template the sender will fall back to.
     */
    public function resolveTemplate(int $schoolId): array
    {
        $defaults = [
            'subject' => 'Timetable for {{class_name}}',
            'body' => "Dear {{guardian_name}},\n\nPlease find attached the class timetable for {{class_name}} for {{term}}, {{academic_year}}.\n\nIf you have any questions about your child's schedule, please contact the school office.\n\n{{school_name}}",
        ];

        $stored = EmailTemplate::forSchool($schoolId)
            ->where('key', 'timetable')
            ->first();

        if ($stored && $stored->is_active) {
            return [
                'subject' => $stored->subject ?: $defaults['subject'],
                'body' => $stored->body ?: $defaults['body'],
            ];
        }

        return $defaults;
    }

    private function substitute(string $text, Classroom $classroom, Term $term, string $schoolName, ?string $customMessage): string
    {
        $replacements = [
            '{{class_name}}' => $classroom->name,
            '{{term}}' => $term->name,
            '{{academic_year}}' => $term->academicYear->label ?? (string) now()->year,
            '{{school_name}}' => $schoolName,
            '{{guardian_name}}' => '',
            '{{custom_message}}' => $customMessage ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    private function hex(?string $value, string $fallback): string
    {
        return ($value && preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) ? $value : $fallback;
    }

    private function canEdit($user, Classroom $classroom): bool
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
}