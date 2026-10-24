<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserInviteService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staff = User::where('school_id', $request->user()->school_id)
            ->whereIn('role', ['school_admin', 'academic_director', 'head_of_department', 'teacher', 'examiner'])
            ->orderBy('name')
            ->get();

        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request, UserInviteService $inviteService)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:academic_director,head_of_department,teacher,examiner',
        ]);

        $inviteService->invite(
            $request->user()->school,
            $data['name'],
            $data['email'],
            $data['role'],
            $data['phone'] ?? null,
        );

        return redirect()->route('staff.index')->with('success', "{$data['name']} has been invited. Login details were emailed to them.");
    }
}