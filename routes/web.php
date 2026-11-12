<?php

use App\Shared\Auth\Controllers\AuthController;
use App\Modules\UMKM\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'show'])->name('login');
    Route::get('/register', [AuthController::class, 'show'])->name('register');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');

    Route::get('/forgot-password', [\App\Shared\Auth\Controllers\PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [\App\Shared\Auth\Controllers\PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [\App\Shared\Auth\Controllers\NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [\App\Shared\Auth\Controllers\NewPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('umkm.dashboard');
    })->name('dashboard');

    Route::get('/dashboard/modules', function () {
        return view('umkm.dashboard.modules');
    })->name('dashboard.modules');

    Route::prefix('intelligence')->name('intelligence.')->group(function () {
        Route::get('/autopsy', [\App\Modules\Intelligence\Controllers\AutopsyController::class, 'index'])->name('autopsy');
        Route::get('/autopsy/{analysis}', [\App\Modules\Intelligence\Controllers\AutopsyController::class, 'show'])->name('autopsy.show');
        Route::post('/autopsy/analyze', [\App\Modules\Intelligence\Controllers\AutopsyController::class, 'analyze'])->name('analyze');
    });
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Onboarding profil usaha: boleh diakses sebelum email terverifikasi.
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('profil-usaha',  [\App\Modules\UMKM\Controllers\BusinessProfileController::class, 'show'])->name('business-profile.show');
        Route::post('profil-usaha', [\App\Modules\UMKM\Controllers\BusinessProfileController::class, 'store'])->middleware('throttle:10,1')->name('business-profile.store');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/business', [\App\Modules\UMKM\Controllers\BusinessProfileSettingsController::class, 'update'])->name('profile.business.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/verify-email', [\App\Shared\Auth\Controllers\EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [\App\Shared\Auth\Controllers\VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [\App\Shared\Auth\Controllers\EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/confirm-password', [\App\Shared\Auth\Controllers\ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('/confirm-password', [\App\Shared\Auth\Controllers\ConfirmablePasswordController::class, 'store']);
    Route::put('/password', [\App\Shared\Auth\Controllers\PasswordController::class, 'update'])->name('password.update');
});
