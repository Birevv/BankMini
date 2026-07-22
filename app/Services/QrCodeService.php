<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeService
{
    public function accountDataUri(string $accountNumber): string
    {
        $options = new QROptions([
            'outputBase64' => true,
            'scale' => 8,
            'addQuietzone' => true,
        ]);

        return (new QRCode($options))->render($accountNumber);
    }
}
