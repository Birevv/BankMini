<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Transaksi> */
class TransaksiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_nasabah' => Nasabah::factory(),
            'id_user' => User::factory()->teller(),
            'tanggal' => now(),
            'jenis_trans' => TransactionType::DEPOSIT,
            'nominal' => fake()->numberBetween(1, 10) * 10_000,
        ];
    }
}
