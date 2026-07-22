<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'admin'],
            ['nama_petugas' => 'Administrator Bank Mini', 'role' => UserRole::ADMIN, 'is_active' => true, 'password' => 'admin12345'],
        );
    }
}
