<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => \App\Shared\Middleware\Authenticate::class,
            'guest' => \App\Shared\Middleware\RedirectIfAuthenticated::class,
            'role' => \App\Shared\Middleware\EnsureUserRole::class,
            'verified' => \App\Shared\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 419 (token CSRF kedaluwarsa) tidak boleh jalan buntu:
        // kembalikan user ke form dengan isian tetap terisi via old().
        // Kolom password tidak ikut di-flash ke sesi demi keamanan.
        // Catatan: prepareException() membungkus TokenMismatchException
        // menjadi HttpException(419) dengan asal disimpan di getPrevious().
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419 || ! $e->getPrevious() instanceof TokenMismatchException) {
                return null; // bukan kasus CSRF, serahkan ke penanganan default
            }

            // Laravel tidak melaporkan TokenMismatchException pada report bawaan,
            // jadi konteks kejadian dicatat di sini untuk memudahkan penyelidikan.
            \Illuminate\Support\Facades\Log::info('csrf_expired_recovery', [
                'user' => $request->user()?->id_user,
                'url' => $request->fullUrl(),
                'referer' => substr((string) $request->header('referer'), 0, 200),
            ]);

            $input = $request->except(['password', 'password_confirmation']);

            return redirect()
                ->back()
                ->withInput($input)
                ->with('csrf_expired', 'Sesi keamanan halaman berakhir. Isian form masih tersimpan — silakan tekan tombol simpan sekali lagi.');
        });
    })->create();
