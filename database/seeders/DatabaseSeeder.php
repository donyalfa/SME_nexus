<?php

namespace Database\Seeders;

use App\Shared\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun demo dengan password literal hanya untuk pengembangan/tes.
        // Jangan pernah membuatnya di production.
        if (app()->environment(['local', 'testing'])) {
            User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'full_name' => 'Administrator',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'is_verified' => true,
                ]
            );

            User::firstOrCreate(
                ['email' => 'test@example.com'],
                [
                    'full_name' => 'Test User',
                    'password' => Hash::make('password'),
                    'role' => 'sme_owner',
                    'is_verified' => true,
                ]
            );
        }

        $this->call(IntelligenceMasterSeeder::class);
    }
}
