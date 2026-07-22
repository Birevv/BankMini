<?php

namespace Database\Seeders;

use App\Actions\Transactions\CreateDepositAction;
use App\Actions\Transactions\CreateWithdrawalAction;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        if (Transaksi::query()->exists()) {
            return;
        }

        $teller = User::query()->where('username', 'teller')->firstOrFail();
        $customers = Nasabah::query()->orderBy('id')->limit(5)->get();

        foreach ($customers as $customer) {
            app(CreateDepositAction::class)->execute($teller, (int) $customer->getKey(), 100_000, true, true);
        }

        if ($customer = $customers->first()) {
            app(CreateWithdrawalAction::class)->execute($teller, (int) $customer->getKey(), 25_000, '123456');
        }
    }
}
