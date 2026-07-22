<?php

namespace App\Exceptions;

use RuntimeException;

class UnauthorizedTransactionException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Hanya Teller aktif yang dapat memproses transaksi loket.');
    }
}
