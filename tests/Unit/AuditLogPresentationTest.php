<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditLogPresentation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AuditLogPresentationTest extends TestCase
{
    public function testItUsesTheActorsRoleForSuccessfulInternalLogin(): void
    {
        $record = new AuditLog(['action' => 'auth.login.succeeded']);
        $record->setRelation('actor', new User(['role' => UserRole::ADMIN]));

        $this->assertSame('Login administrator berhasil', AuditLogPresentation::activityLabel($record));
    }

    #[DataProvider('eventPresentationProvider')]
    public function testItMapsEventCodesToReadableLabelsAndSemanticColors(
        string $eventCode,
        string $expectedLabel,
        string $expectedColor,
    ): void {
        $this->assertSame($expectedLabel, AuditLogPresentation::activityLabelForCode($eventCode));
        $this->assertSame($expectedColor, AuditLogPresentation::activityColor($eventCode));
    }

    /** @return array<string, array{string, string, string}> */
    public static function eventPresentationProvider(): array
    {
        return [
            'customer login failed' => ['customer_portal.login.failed', 'Login nasabah gagal', 'danger'],
            'daily report approved' => ['daily_report.approved', 'Laporan harian disetujui', 'success'],
            'warning' => ['ledger.balance.warning', 'Ledger Balance Warning', 'warning'],
            'neutral' => ['customer.updated', 'Data nasabah diperbarui', 'info'],
        ];
    }

    public function testItExtractsOptionalRequestAndChangeDetailsFromMetadata(): void
    {
        $record = new AuditLog([
            'action' => 'customer.updated',
            'metadata' => [
                'request' => ['user_agent' => 'Example Browser'],
                'before' => ['kelas' => '7A'],
                'after' => ['kelas' => '7B'],
            ],
        ]);

        $this->assertSame('Example Browser', AuditLogPresentation::userAgent($record));
        $this->assertSame(['kelas' => '7A'], AuditLogPresentation::before($record));
        $this->assertSame(['kelas' => '7B'], AuditLogPresentation::after($record));
    }
}
