<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class TellerUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'teller'],
            ['nama_petugas' => 'Teller Development', 'role' => UserRole::TELLER, 'is_active' => true, 'password' => 'teller12345'],
        );
    }
}
