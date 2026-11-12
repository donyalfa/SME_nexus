<?php

namespace App\Modules\UMKM\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Modules\UMKM\Requests\StoreBusinessProfileRequest;
use App\Modules\Intelligence\Models\BusinessCategory;
use App\Shared\Models\BusinessProfile;
use App\Shared\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Onboarding "Isi Profil Usaha" yang ditampilkan setelah registrasi,
 * sebelum user masuk ke halaman verifikasi email.
 */
class BusinessProfileController extends Controller
{
    /**
     * Tampilkan form onboarding profil usaha.
     */
    public function show(Request $request): RedirectResponse|View
    {
        /** @var User $user */
        $user = $request->user();

        // Sudah punya profil usaha? Langsung ke tujuan sebenarnya.
        if ($user->businessProfiles()->exists()) {
            return redirect()->to($this->destination($user));
        }

        return view('umkm.onboarding.business-profile', [
            'user' => $user,
            'categories' => BusinessCategory::orderBy('category_name')->get(),
            'skipUrl' => $this->destination($user),
        ]);
    }

    /**
     * Simpan profil usaha hasil onboarding.
     */
    public function store(StoreBusinessProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->businessProfiles()->exists()) {
            // Pengaman dobel submit: satu akun cukup satu profil hasil onboarding.
            BusinessProfile::create(
                $request->validated() + ['id_user' => $user->id_user]
            );

            $verificationEnabled = (bool) config('app.email_verification_enabled');

            $message = ! $verificationEnabled || $user->hasVerifiedEmail()
                ? 'Profil usaha berhasil disimpan.'
                : 'Profil usaha berhasil disimpan. Silakan lanjut verifikasi email Anda.';
        } else {
            $message = 'Profil usaha sudah tersimpan sebelumnya.';
        }

        return redirect()
            ->to($this->destination($user))
            ->with('success', $message);
    }

    /**
     * Tujuan setelah onboarding: verifikasi email bila belum terverifikasi,
     * atau dashboard bila sudah.
     */
    private function destination(User $user): string
    {
        if (! config('app.email_verification_enabled') || $user->hasVerifiedEmail()) {
            return route('dashboard');
        }

        return route('verification.notice');
    }
}