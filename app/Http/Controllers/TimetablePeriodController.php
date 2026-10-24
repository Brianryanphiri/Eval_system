<?php

namespace App\Http\Controllers;

use App\Models\TimetablePeriod;
use Illuminate\Http\Request;

class TimetablePeriodController extends Controller
{
    public function index()
    {
        $school = auth()->user()->school;

        $periods = TimetablePeriod::forSchool($school->id)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();

        return view('settings.timetable-periods', [
            'periods' => $periods,
        ]);
    }

    public function store(Request $request)
    {
        $school = auth()->user()->school;

        $data = $request->validate([
            'name' => 'required|string|max:64',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_break' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ]);

        $overlap = TimetablePeriod::forSchool($school->id)
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['start_time' => "{$data['start_time']} to {$data['end_time']} overlaps an existing period."])
                ->withInput();
        }

        $nextOrder = $data['sort_order']
            ?? ((int) TimetablePeriod::forSchool($school->id)->max('sort_order')) + 1;

        TimetablePeriod::create([
            'school_id' => $school->id,
            'name' => $data['name'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'is_break' => $request->boolean('is_break'),
            'sort_order' => $nextOrder,
        ]);

        return back()->with('success', "Period \"{$data['name']}\" added.");
    }

    public function update(Request $request, TimetablePeriod $period)
    {
        abort_unless($period->school_id === auth()->user()->school_id, 403);

        $data = $request->validate([
            'name' => 'required|string|max:64',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_break' => 'nullable|boolean',
        ]);

        $overlap = TimetablePeriod::forSchool($period->school_id)
            ->where('id', '!=', $period->id)
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['start_time' => "{$data['start_time']} to {$data['end_time']} overlaps another period."])
                ->withInput();
        }

        $period->update([
            'name' => $data['name'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'is_break' => $request->boolean('is_break'),
        ]);

        return back()->with('success', 'Period updated.');
    }

    public function destroy(TimetablePeriod $period)
    {
        abort_unless($period->school_id === auth()->user()->school_id, 403);

        if ($period->slots()->exists()) {
            return back()->withErrors([
                'period' => "Cannot delete \"{$period->name}\" because it is already used in one or more timetables.",
            ]);
        }

        $name = $period->name;
        $period->delete();

        return back()->with('success', "Period \"{$name}\" removed.");
    }

    public function seedDefaults()
    {
        $school = auth()->user()->school;

        $existing = TimetablePeriod::forSchool($school->id)->count();
        if ($existing > 0) {
            return back()->withErrors([
                'seed' => 'This school already has periods defined.',
            ]);
        }

        $defaults = [
            ['Period 1', '08:00', '08:40', false],
            ['Period 2', '08:40', '09:20', false],
            ['Short Break', '09:20', '09:40', true],
            ['Period 3', '09:40', '10:20', false],
            ['Period 4', '10:20', '11:00', false],
            ['Lunch', '11:00', '11:40', true],
            ['Period 5', '11:40', '12:20', false],
            ['Period 6', '12:20', '13:00', false],
        ];

        foreach ($defaults as $i => [$name, $start, $end, $isBreak]) {
            TimetablePeriod::create([
                'school_id' => $school->id,
                'name' => $name,
                'start_time' => $start,
                'end_time' => $end,
                'is_break' => $isBreak,
                'sort_order' => $i + 1,
            ]);
        }

        return back()->with('success', 'Default bell schedule added. Adjust as needed.');
    }
}