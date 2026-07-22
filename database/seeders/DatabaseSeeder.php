<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('Seeder dummy tidak dijalankan pada environment production.');

            return;
        }

        $this->call([
            AdminUserSeeder::class,
            TellerUserSeeder::class,
            SupervisorUserSeeder::class,
            NasabahSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
