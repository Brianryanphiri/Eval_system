<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (!$school) {
            return view('announcements.index', [
                'announcements' => collect(),
                'classes' => collect(),
                'selectedAudience' => 'all',
                'selectedPriority' => 'all',
                'canPost' => false,
                'counts' => [],
            ]);
        }

        $canPost = self::userCanPost($user);

        $selectedAudience = $request->query('audience', 'all');
        $selectedPriority = $request->query('priority', 'all');

        $query = Announcement::with(['author', 'classroom'])
            ->where('school_id', $school->id)
            ->published()
            ->active()
            ->visibleTo($user->role);

        if ($selectedAudience !== 'all') {
            $query->where('audience', $selectedAudience);
        }

        if ($selectedPriority !== 'all') {
            $query->where('priority', $selectedPriority);
        }

        $announcements = $query
            ->byImportance()
            ->paginate(20)
            ->withQueryString();

        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        $counts = [
            'total' => Announcement::where('school_id', $school->id)->published()->active()->visibleTo($user->role)->count(),
            'urgent' => Announcement::where('school_id', $school->id)->published()->active()->visibleTo($user->role)->where('priority', 'urgent')->count(),
            'high' => Announcement::where('school_id', $school->id)->published()->active()->visibleTo($user->role)->where('priority', 'high')->count(),
        ];

        return view('announcements.index', [
            'announcements' => $announcements,
            'classes' => $classes,
            'selectedAudience' => $selectedAudience,
            'selectedPriority' => $selectedPriority,
            'canPost' => $canPost,
            'counts' => $counts,
        ]);
    }

    public function show(Announcement $announcement)
    {
        $user = auth()->user();

        abort_unless($announcement->school_id === $user->school_id, 403);

        $visible = Announcement::where('id', $announcement->id)
            ->published()
            ->active()
            ->visibleTo($user->role)
            ->exists();

        if (!$visible) {
            abort(404);
        }

        $announcement->load(['author', 'classroom']);

        return view('announcements.show', [
            'announcement' => $announcement,
            'canPost' => self::userCanPost($user),
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        abort_unless(self::userCanPost($user), 403, 'Only admins can create announcements.');

        $school = $user->school;

        $classes = Classroom::where('school_id', $school->id)
            ->orderBy('name')
            ->get();

        return view('announcements.create', [
            'classes' => $classes,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        abort_unless(self::userCanPost($user), 403, 'Only admins can create announcements.');

        $school = $user->school;

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:5|max:5000',
            'audience' => 'required|in:all,staff,teachers,students,guardians,class',
            'priority' => 'required|in:low,normal,high,urgent',
            'publish_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:publish_at',
            'audience_class_id' => 'nullable|integer|exists:classes,id',
            'publish_now' => 'nullable|boolean',
        ]);

        if ($data['audience'] === 'class' && empty($data['audience_class_id'])) {
            return back()
                ->withErrors(['audience_class_id' => 'Please pick a class when the audience is "Class".'])
                ->withInput();
        }

        if ($data['audience'] !== 'class') {
            $data['audience_class_id'] = null;
        }

        $publishNow = $request->boolean('publish_now', true);

        $announcement = Announcement::create([
            'school_id' => $school->id,
            'author_id' => $user->id,
            'title' => $data['title'],
            'body' => $data['body'],
            'audience' => $data['audience'],
            'audience_class_id' => $data['audience_class_id'],
            'priority' => $data['priority'],
            'publish_at' => $publishNow ? now() : ($data['publish_at'] ?? now()),
            'expires_at' => $data['expires_at'] ?? null,
            'is_published' => $publishNow || !empty($data['publish_at']),
        ]);

        return redirect()
            ->route('announcements.show', $announcement->id)
            ->with('success', 'Announcement posted.');
    }

    public function destroy(Announcement $announcement)
    {
        $user = auth()->user();

        abort_unless(self::userCanPost($user), 403);

        abort_unless($announcement->school_id === $user->school_id, 403);

        $announcement->delete();

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement removed.');
    }

    /**
     * Single source of truth for who can post or delete announcements.
     */
    private static function userCanPost(User $user): bool
    {
        return in_array($user->role, ['school_admin', 'super_admin'], true);
    }
}