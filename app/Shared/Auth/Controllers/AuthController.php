<?php

namespace App\Shared\Auth\Controllers;


use App\Shared\Http\Controllers\Controller;
use App\Shared\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Satu view dipakai untuk /login dan /register.
    public function show()
    {
        return view('auth.auth');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email', 'max:100'],
            'password' => ['required'],
        ]);

        // Kunci throttle menggabungkan email dan IP agar satu alamat IP yang
        // dipakai bersama tidak memblokir semua akun sekaligus.
        $throttleKey = Str::lower((string) $request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors(['email' => 'Terlalu banyak percobaan masuk. Coba lagi dalam '.ceil($seconds / 60).' menit.'])
                ->onlyInput('email', 'form');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            // Verifikasi hanya diwajibkan jika fitur sedang aktif.
            if (config('app.email_verification_enabled') && $user && ! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // Admin langsung ke halaman admin, user lain tetap ke dashboard.
            $default = $user && $user->role === 'admin' ? '/admin/intelligence' : '/dashboard';

            return redirect()->intended($default);
        }

        RateLimiter::hit($throttleKey, 60);

        // 'form' ikut dikirim balik agar halaman tetap membuka panel login.
        return back()
            ->withErrors(['email' => 'Email atau kata sandi salah.'])
            ->onlyInput('email', 'form');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms'    => ['accepted'],
        ], [
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'terms.accepted'     => 'Setujui syarat dan ketentuan untuk melanjutkan.',
        ]);

        $verificationEnabled = config('app.email_verification_enabled');

        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'sme_owner',
            // Verifikasi dimatikan: akun langsung dianggap terverifikasi.
            'is_verified' => ! $verificationEnabled,
        ]);

        // Kirim email verifikasi hanya saat verifikasi email aktif (jika mail dikonfigurasi).
        if ($verificationEnabled) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                // Jika mail belum dikonfigurasi, jangan gagalkan registrasi.
                \Illuminate\Support\Facades\Log::warning('Email verification notification failed', ['error' => $e->getMessage()]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Setelah registrasi, user diarahkan mengisi profil usaha (onboarding),
        // lalu lanjut ke dashboard (atau verifikasi email jika verifikasi aktif).
        return redirect()->route('onboarding.business-profile.show');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
