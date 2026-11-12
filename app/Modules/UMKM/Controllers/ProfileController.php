<?php

namespace App\Modules\UMKM\Controllers;


use App\Shared\Http\Controllers\Controller;
use App\Modules\UMKM\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('umkm.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $emailChanged = $user->fill($request->validated())->isDirty('email');

        $verificationEnabled = (bool) config('app.email_verification_enabled');

        if ($emailChanged && $verificationEnabled) {
            $user->is_verified = false;
        }

        $user->save();

        if ($emailChanged && $verificationEnabled) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                Log::warning('Email verification notification failed', ['error' => $e->getMessage()]);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Akun dengan profil UMKM tidak boleh menghapus dirinya sendiri karena
        // relasi sme_profiles memakai ON DELETE CASCADE dan akan menghapus
        // seluruh data bisnis tanpa konfirmasi/transfer kepemilikan.
        if ($user->smeProfiles()->exists()) {
            return Redirect::back()->withErrors([
                'password' => 'Akun tidak dapat dihapus karena masih memiliki profil UMKM. Hapus atau pindahkan profil terlebih dahulu.',
            ], 'userDeletion');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
