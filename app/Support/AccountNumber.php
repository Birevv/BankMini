<?php

namespace App\Support;

final class AccountNumber
{
    public static function mask(string $accountNumber): string
    {
        $visibleCharacters = 2;
        $length = mb_strlen($accountNumber);

        if ($length <= $visibleCharacters) {
            return str_repeat('*', $length);
        }

        return mb_substr($accountNumber, 0, min(8, $length - $visibleCharacters))
            .str_repeat('*', max(4, $length - min(8, $length - $visibleCharacters) - $visibleCharacters))
            .mb_substr($accountNumber, -$visibleCharacters);
    }
}
