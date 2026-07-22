<?php

namespace Tests\Unit;

use App\Support\AccountNumber;
use PHPUnit\Framework\TestCase;

class AccountNumberTest extends TestCase
{
    public function testAccountNumberIsMaskedExceptSafePrefixAndSuffix(): void
    {
        $masked = AccountNumber::mask('BM-2026-000001');

        $this->assertStringStartsWith('BM-2026-', $masked);
        $this->assertStringEndsWith('01', $masked);
        $this->assertStringContainsString('****', $masked);
    }
}
