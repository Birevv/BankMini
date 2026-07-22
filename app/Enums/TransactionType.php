<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'setoran';
    case WITHDRAWAL = 'penarikan';

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => 'Setoran',
            self::WITHDRAWAL => 'Penarikan',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])
            ->all();
    }
}
