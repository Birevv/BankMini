<?php

namespace App\Exceptions;

use RuntimeException;

class MinimumBalanceViolationException extends RuntimeException
{
    public function __construct(int $minimumBalance)
    {
        parent::__construct('Saldo setelah penarikan harus tersisa minimal Rp'.number_format($minimumBalance, 0, ',', '.').'.');
    }
}
