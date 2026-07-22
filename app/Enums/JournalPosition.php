<?php

namespace App\Enums;

enum JournalPosition: string
{
    case DEBIT = 'debit';
    case CREDIT = 'kredit';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $position): array => [$position->value => $position->label()])
            ->all();
    }
}
