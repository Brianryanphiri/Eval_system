<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // Administrator details
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', \App\Services\PasswordPolicy::rule()],

            // School details
            'school_name' => 'required|string|max:255',
            'institution_type' => 'required|in:primary,secondary,international,college',
            'country' => 'required|string|max:100',

            'terms' => 'accepted',
        ]);

        [$user, $school] = DB::transaction(function () use ($data) {
            // Create the school first — it has no owner yet, but users.school_id
            // is nullable so this ordering works without a chicken-and-egg problem
            $school = School::create([
                'name' => $data['school_name'],
                'institution_type' => $data['institution_type'],
                'country' => $data['country'],
                'setup_completed' => false,
                'setup_step' => 1,
            ]);

            $user = User::create([
                'school_id' => $school->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'role' => 'school_admin', // first user of a new school is always the admin
                'password' => Hash::make($data['password']),
            ]);

            return [$user, $school];
        });

        event(new Registered($user));

        Auth::login($user);

        // EnsureSetupCompleted middleware will catch this on the next request
        // and redirect into the wizard automatically, but redirecting explicitly
        // here avoids an unnecessary extra round-trip
        return redirect()->route('setup.step1');
    }
}