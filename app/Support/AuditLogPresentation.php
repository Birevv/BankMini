<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\LaporanHarianTeller;
use App\Models\Nasabah;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class AuditLogPresentation
{
    public static function activityLabel(AuditLog $record): string
    {
        if ($record->action === 'auth.login.succeeded') {
            $role = $record->actor?->role?->label() ?? 'Petugas';

            return 'Login '.Str::lower($role).' berhasil';
        }

        return self::activityLabelForCode($record->action);
    }

    public static function activityLabelForCode(string $eventCode): string
    {
        return match ($eventCode) {
            'auth.login.succeeded' => 'Login petugas berhasil',
            'auth.login.failed' => 'Login petugas gagal',
            'customer_portal.login.succeeded' => 'Login nasabah berhasil',
            'customer_portal.login.failed' => 'Login nasabah gagal',
            'customer_portal.logout' => 'Nasabah keluar dari portal',
            'user.created' => 'Akun petugas dibuat',
            'user.updated' => 'Akun petugas diperbarui',
            'customer.created' => 'Nasabah baru ditambahkan',
            'customer.updated' => 'Data nasabah diperbarui',
            'customer.pin.reset' => 'PIN nasabah direset',
            'transaction.deposit.created' => 'Setoran nasabah dicatat',
            'transaction.withdrawal.created' => 'Penarikan nasabah dicatat',
            'daily_report.draft.saved' => 'Draf laporan harian disimpan',
            'daily_report.submitted' => 'Laporan harian diajukan',
            'daily_report.approved' => 'Laporan harian disetujui',
            'daily_report.rejected' => 'Laporan harian ditolak',
            default => Str::of($eventCode)
                ->replace(['.', '_'], ' ')
                ->headline()
                ->toString(),
        };
    }

    public static function activityColor(string $eventCode): string
    {
        if (Str::contains($eventCode, ['failed', 'error', 'rejected', 'denied'])) {
            return 'danger';
        }

        if (Str::contains($eventCode, ['warning', 'warned', 'alert'])) {
            return 'warning';
        }

        if (Str::contains($eventCode, ['succeeded', 'created', 'approved', 'submitted', 'saved', 'reset'])) {
            return 'success';
        }

        return 'info';
    }

    public static function subjectLabel(AuditLog $record): string
    {
        if (blank($record->subject_type) && blank($record->subject_id)) {
            return 'Tidak ada subjek';
        }

        $subject = $record->subject;
        $type = match (true) {
            $subject instanceof User, $record->subject_type === User::class => 'Petugas',
            $subject instanceof Nasabah, $record->subject_type === Nasabah::class => 'Nasabah',
            $subject instanceof Transaksi, $record->subject_type === Transaksi::class => 'Transaksi',
            $subject instanceof LaporanHarianTeller, $record->subject_type === LaporanHarianTeller::class => 'Laporan harian',
            default => Str::headline(class_basename((string) $record->subject_type)),
        };

        $identifier = match (true) {
            $subject instanceof User => $subject->nama_petugas,
            $subject instanceof Nasabah => "{$subject->nama_siswa} ({$subject->no_rekening})",
            $subject instanceof LaporanHarianTeller => $subject->tanggal?->format('d M Y'),
            default => null,
        };

        $label = $type;

        if (filled($record->subject_id)) {
            $label .= ' #'.$record->subject_id;
        }

        return filled($identifier) ? $label.' - '.$identifier : $label;
    }

    public static function userAgent(AuditLog $record): ?string
    {
        $value = self::firstMetadataValue($record, [
            'user_agent',
            'userAgent',
            'request.user_agent',
            'request.userAgent',
        ]);

        return is_scalar($value) && filled((string) $value) ? (string) $value : null;
    }

    public static function before(AuditLog $record): mixed
    {
        return self::firstMetadataValue($record, [
            'before',
            'old',
            'previous',
            'properties.before',
            'changes.before',
        ]);
    }

    public static function after(AuditLog $record): mixed
    {
        $value = self::firstMetadataValue($record, [
            'after',
            'new',
            'current',
            'properties.after',
            'changes.after',
        ]);

        if ($value !== null) {
            return $value;
        }

        $changes = data_get($record->metadata ?? [], 'changes');

        return is_array($changes) ? $changes : null;
    }

    public static function prettyJson(mixed $value): string
    {
        return (string) json_encode(
            $value,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }

    /** @param list<string> $paths */
    private static function firstMetadataValue(AuditLog $record, array $paths): mixed
    {
        $metadata = $record->metadata ?? [];

        foreach ($paths as $path) {
            if (Arr::has($metadata, $path)) {
                return data_get($metadata, $path);
            }
        }

        return null;
    }
}
