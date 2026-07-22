<?php

namespace App\Exceptions;

use RuntimeException;

class DailyReportMismatchException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Laporan hanya dapat diajukan atau disetujui jika selisih kas Rp0.');
    }
}
