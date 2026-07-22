<?php

namespace App\Exceptions;

use RuntimeException;

class InactiveCustomerException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Rekening nasabah tidak aktif.');
    }
}
