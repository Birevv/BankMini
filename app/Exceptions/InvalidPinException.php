<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidPinException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Transaksi ditolak karena verifikasi keamanan gagal.');
    }
}
