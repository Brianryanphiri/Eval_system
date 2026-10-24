<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index(Request $request)
    {
        $years = AcademicYear::where('school_id', $request->user()->school_id)
            ->with('terms')
            ->orderByDesc('label')
            ->get();

        return view('settings.academic-calendar', compact('years'));
    }

    public function storeYear(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $school = $request->user()->school;

        $exists = AcademicYear::where('school_id', $school->id)->where('label', $data['label'])->exists();
        if ($exists) {
            return back()->withErrors(['label' => 'An academic year with this label already exists.']);
        }

        AcademicYear::create([
            'school_id' => $school->id,
            'label' => $data['label'],
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'is_current' => false,
        ]);

        return back()->with('success', "Academic year {$data['label']} created.");
    }

    public function setCurrentYear(Request $request, AcademicYear $academicYear)
    {
        $this->authorizeSchool($request, $academicYear->school_id);

        AcademicYear::where('school_id', $academicYear->school_id)->update(['is_current' => false]);
        $academicYear->update(['is_current' => true]);

        return back()->with('success', "{$academicYear->label} is now the current academic year.");
    }

    public function storeTerm(Request $request, AcademicYear $academicYear)
    {
        $this->authorizeSchool($request, $academicYear->school_id);

        $data = $request->validate([
            'name' => 'required|string|max:50',
            'sequence' => 'required|integer|min:1|max:10',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Term::create([
            'academic_year_id' => $academicYear->id,
            'name' => $data['name'],
            'sequence' => $data['sequence'],
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'is_current' => false,
        ]);

        return back()->with('success', "{$data['name']} added to {$academicYear->label}.");
    }

    public function setCurrentTerm(Request $request, Term $term)
    {
        $this->authorizeSchool($request, $term->academicYear->school_id);

        // Only one term can be current across the whole school, not just within one year
        Term::whereIn('academic_year_id', AcademicYear::where('school_id', $term->academicYear->school_id)->pluck('id'))
            ->update(['is_current' => false]);

        $term->update(['is_current' => true]);

        return back()->with('success', "{$term->name} is now the current term.");
    }

    private function authorizeSchool(Request $request, int $schoolId): void
    {
        abort_unless($request->user()->school_id === $schoolId, 403);
    }
}