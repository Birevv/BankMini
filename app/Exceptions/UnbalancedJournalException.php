<?php

namespace App\Exceptions;

use RuntimeException;

class UnbalancedJournalException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Jurnal transaksi tidak seimbang.');
    }
}
