<?php

namespace App\Modules\Intelligence\Providers;

use Illuminate\Support\ServiceProvider;

class IntelligenceServiceProvider extends ServiceProvider
{
    /**
     * Register layanan modul Intelligence.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap layanan modul Intelligence.
     */
    public function boot(): void
    {
        // Rute publik modul Intelligence dimiliki area UMKM; konfigurasi admin berada di modul Admin.
    }
}
