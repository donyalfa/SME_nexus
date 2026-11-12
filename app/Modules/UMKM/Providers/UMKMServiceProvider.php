<?php

namespace App\Modules\UMKM\Providers;

use Illuminate\Support\ServiceProvider;

class UMKMServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/umkm.php'));
    }
}
