<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupervisorUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'supervisor'],
            ['nama_petugas' => 'Supervisor Development', 'role' => UserRole::SUPERVISOR, 'is_active' => true, 'password' => 'supervisor12345'],
        );
    }
}
