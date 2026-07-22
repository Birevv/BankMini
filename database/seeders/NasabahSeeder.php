<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use Illuminate\Database\Seeder;

class NasabahSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 8) as $sequence) {
            Nasabah::query()->updateOrCreate(
                ['nis' => '2026'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT)],
                [
                    'no_rekening' => 'BM-'.now()->format('Y').'-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
                    'nama_siswa' => 'Nasabah Development '.$sequence,
                    'kelas' => fake()->randomElement(['X RPL 1', 'XI RPL 1', 'XII RPL 1']),
                     'portal_password' => 'nasabah123',
                    'is_active' => true,
                ],
            );
        }
    }
}
