<?php

namespace Database\Factories;

use App\Enums\DailyReportStatus;
use App\Models\LaporanHarianTeller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LaporanHarianTeller> */
class LaporanHarianTellerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_teller' => User::factory()->teller(),
            'tanggal' => today(),
            'saldo_awal' => 0,
            'total_setoran' => 0,
            'total_penarikan' => 0,
            'saldo_akhir_sistem' => 0,
            'saldo_fisik' => 0,
            'selisih' => 0,
            'status' => DailyReportStatus::DRAFT,
        ];
    }
}
