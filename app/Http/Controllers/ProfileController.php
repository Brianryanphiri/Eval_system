<?php

namespace App\Http\Controllers;

use App\Services\PasswordPolicy;
use App\Services\Settings;
use App\Services\SessionPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the logged-in user's profile form.
     */
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the logged-in user's basic info (name, email, phone, avatar).
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // If PHP already rejected the upload at the receiver stage, tell the user why.
        // This runs BEFORE validation so we don't just see "The avatar failed to upload."
        if ($request->hasFile('avatar') && !$request->file('avatar')->isValid()) {
            $err = $request->file('avatar')->getError();

            $reasons = [
                UPLOAD_ERR_INI_SIZE => 'File is larger than the server upload limit.',
                UPLOAD_ERR_FORM_SIZE => 'File is larger than the form limit.',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded. Try again.',
                UPLOAD_ERR_NO_FILE => 'No file was sent.',
                UPLOAD_ERR_NO_TMP_DIR => 'Server has no writable temporary folder. (Check php.ini upload_tmp_dir.)',
                UPLOAD_ERR_CANT_WRITE => 'Server could not write the file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension blocked the upload.',
            ];

            return back()->withErrors([
                'avatar' => $reasons[$err] ?? ('Upload error code: ' . $err),
            ])->withInput();
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'avatar' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        // If email changed, clear verification
        if ($data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete the old file so we don't leave orphaned uploads behind
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        // 'avatar' is the raw UploadedFile, not a column — drop it before saving
        unset($data['avatar']);

        $user->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated.');
    }

    /**
     * Change the logged-in user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', PasswordPolicy::rule()],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Your current password is incorrect.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($data['password']),
            'password_changed_at' => now(),
        ]);

        if (Settings::get('session.force_logout_on_password', true)) {
            SessionPolicy::revokeAllExceptCurrent($user, $request->session()->getId());
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Password changed.');
    }

    /**
     * Remove the user's avatar.
     */
    public function destroyAvatar()
    {
        $user = auth()->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile photo removed.');
    }
}