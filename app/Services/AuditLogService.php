<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class AuditLogService
{
    /** @param array<string, mixed> $metadata */
    public function record(string $action, ?Model $subject = null, ?User $actor = null, array $metadata = []): ?AuditLog
    {
        if (! Schema::hasTable('audit_logs')) {
            return null;
        }

        $safeMetadata = $this->removeSensitiveValues($metadata);

        return AuditLog::query()->create([
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'metadata' => $safeMetadata === [] ? null : $safeMetadata,
            'ip_address' => request()?->ip(),
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    private function removeSensitiveValues(array $metadata): array
    {
        return collect($metadata)
            ->reject(function (mixed $value, string|int $key): bool {
                $normalizedKey = str((string) $key)->lower();

                return $normalizedKey->contains(['password', 'pin', 'secret', 'token']);
            })
            ->map(fn (mixed $value): mixed => is_array($value) ? $this->removeSensitiveValues(Arr::wrap($value)) : $value)
            ->all();
    }
}
