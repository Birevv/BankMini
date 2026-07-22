<?php

namespace Database\Factories;

use App\Models\Nasabah;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<Nasabah> */
class NasabahFactory extends Factory
{
    protected static int $sequence = 1;

    public function definition(): array
    {
        $sequence = self::$sequence++;

        return [
            'no_rekening' => 'BM-'.now()->format('Y').'-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
            'nis' => fake()->unique()->numerify('##########'),
            'nama_siswa' => fake()->name(),
            'kelas' => fake()->randomElement(['X RPL 1', 'XI RPL 1', 'XII RPL 1']),
            'pin_keamanan' => Hash::make('123456'),
            'portal_password' => Hash::make('nasabah123'),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
