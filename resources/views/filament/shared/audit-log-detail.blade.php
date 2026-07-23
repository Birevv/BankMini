@php
    use App\Support\AuditLogPresentation;

    $metadata = $record->metadata ?? [];
    $before = AuditLogPresentation::before($record);
    $after = AuditLogPresentation::after($record);
@endphp

<div class="audit-log-detail">
    <dl class="audit-log-detail-summary">
        <div>
            <dt>Aktor</dt>
            <dd>{{ $record->actor?->nama_petugas ?? 'Sistem / Nasabah' }}</dd>
        </div>
        <div>
            <dt>Waktu</dt>
            <dd>{{ $record->created_at?->format('d M Y, H:i:s') ?? '-' }}</dd>
        </div>
        <div>
            <dt>Alamat IP</dt>
            <dd>{{ $record->ip_address ?: 'Tidak tersedia' }}</dd>
        </div>
        <div class="audit-log-detail-wide">
            <dt>User agent</dt>
            <dd>{{ AuditLogPresentation::userAgent($record) ?? 'Tidak tersedia pada log ini' }}</dd>
        </div>
        <div class="audit-log-detail-wide">
            <dt>Subjek</dt>
            <dd>{{ AuditLogPresentation::subjectLabel($record) }}</dd>
        </div>
        <div class="audit-log-detail-wide">
            <dt>Event code asli</dt>
            <dd><code>{{ $record->action }}</code></dd>
        </div>
    </dl>

    <section class="audit-log-detail-section">
        <h3>Properties</h3>

        @if ($metadata !== [])
            <pre>{{ AuditLogPresentation::prettyJson($metadata) }}</pre>
        @else
            <p>Tidak ada properties tambahan.</p>
        @endif
    </section>

    @if ($before !== null)
        <section class="audit-log-detail-section">
            <h3>Nilai sebelum perubahan</h3>
            <pre>{{ AuditLogPresentation::prettyJson($before) }}</pre>
        </section>
    @endif

    @if ($after !== null)
        <section class="audit-log-detail-section">
            <h3>Nilai setelah perubahan</h3>
            <pre>{{ AuditLogPresentation::prettyJson($after) }}</pre>
        </section>
    @endif
</div>
