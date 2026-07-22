<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TELLER = 'teller';
    case SUPERVISOR = 'supervisor';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::TELLER => 'Teller',
            self::SUPERVISOR => 'Supervisor',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role): array => [$role->value => $role->label()])
            ->all();
    }
}
