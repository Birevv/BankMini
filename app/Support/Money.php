<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

final class Money
{
    public static function fromInput(mixed $value, string $field = 'nominal', bool $allowZero = false): int
    {
        if (is_int($value)) {
            $amount = $value;
        } elseif (is_float($value) && is_finite($value) && floor($value) === $value && $value <= PHP_INT_MAX) {
            $amount = (int) $value;
        } elseif (is_string($value) && ctype_digit($value)) {
            $normalizedValue = ltrim($value, '0') ?: '0';
            $maximumInteger = (string) PHP_INT_MAX;

            if (strlen($normalizedValue) > strlen($maximumInteger) || (strlen($normalizedValue) === strlen($maximumInteger) && strcmp($normalizedValue, $maximumInteger) > 0)) {
                throw ValidationException::withMessages([
                    $field => 'Nominal melebihi batas yang didukung sistem.',
                ]);
            }

            $amount = (int) $normalizedValue;
        } else {
            throw ValidationException::withMessages([
                $field => 'Nominal harus berupa bilangan bulat tanpa desimal.',
            ]);
        }

        if ($amount < 0 || ((! $allowZero) && $amount === 0)) {
            throw ValidationException::withMessages([
                $field => $allowZero ? 'Nominal tidak boleh negatif.' : 'Nominal harus lebih dari nol.',
            ]);
        }

        return $amount;
    }

    public static function format(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }
}
