@php
    use App\Enums\TransactionType;
    use App\Support\Money;

    $maximumFlow = max(
        1,
        ...array_map(
            static fn (array $day): int => max($day['deposits'], $day['withdrawals']),
            $daily_flow,
        ),
    );
@endphp

<x-filament-widgets::widget class="bank-admin-widget">
    <section class="bank-admin-section" aria-labelledby="admin-summary-title">
        <div class="bank-admin-section-heading">
            <div>
                <h2 id="admin-summary-title">Ringkasan operasional</h2>
                <p>Kondisi Bank Mini Sekolah saat ini.</p>
            </div>
            <span class="bank-status-pill">Sistem aktif</span>
        </div>

        <div class="bank-admin-stats">
            <article class="bank-admin-stat">
                <div class="bank-admin-stat-icon">NS</div>
                <p>Nasabah aktif</p>
                <strong>{{ number_format($metrics['customers'], 0, ',', '.') }}</strong>
                <small>Rekening aktif terdaftar</small>
            </article>
            <article class="bank-admin-stat bank-admin-stat-accent">
                <div class="bank-admin-stat-icon">SL</div>
                <p>Saldo tersimpan</p>
                <strong>{{ Money::format($metrics['total_balance']) }}</strong>
                <small>Ledger seluruh rekening</small>
            </article>
            <article class="bank-admin-stat">
                <div class="bank-admin-stat-icon">PT</div>
                <p>Petugas aktif</p>
                <strong>{{ number_format($metrics['active_staff'], 0, ',', '.') }}</strong>
                <small>Teller dan Supervisor</small>
            </article>
            <article class="bank-admin-stat">
                <div class="bank-admin-stat-icon">TR</div>
                <p>Transaksi hari ini</p>
                <strong>{{ number_format($metrics['transactions_today'], 0, ',', '.') }}</strong>
                <small>Setoran dan penarikan</small>
            </article>
        </div>
    </section>

    <div class="bank-admin-detail-grid">
        <section class="bank-admin-section" aria-labelledby="transaction-flow-title">
            <div class="bank-admin-section-heading bank-admin-section-heading-compact">
                <div>
                    <h2 id="transaction-flow-title">Arus transaksi 7 hari</h2>
                    <p>Perbandingan setoran dan penarikan.</p>
                </div>
                <div class="bank-chart-legend" aria-label="Legenda grafik">
                    <span><i class="bank-legend-deposit"></i> Setoran</span>
                    <span><i class="bank-legend-withdrawal"></i> Penarikan</span>
                </div>
            </div>

            <div class="bank-flow-chart" role="img" aria-label="Grafik arus transaksi selama tujuh hari terakhir">
                @foreach ($daily_flow as $day)
                    <div class="bank-flow-day">
                        <div class="bank-flow-bars">
                            <span
                                class="bank-flow-bar bank-flow-bar-deposit"
                                style="--bank-bar-height: {{ max(4, (int) round(($day['deposits'] / $maximumFlow) * 100)) }}%"
                                title="Setoran {{ Money::format($day['deposits']) }}"
                            ></span>
                            <span
                                class="bank-flow-bar bank-flow-bar-withdrawal"
                                style="--bank-bar-height: {{ max(4, (int) round(($day['withdrawals'] / $maximumFlow) * 100)) }}%"
                                title="Penarikan {{ Money::format($day['withdrawals']) }}"
                            ></span>
                        </div>
                        <span>{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="bank-admin-section" aria-labelledby="recent-activity-title">
            <div class="bank-admin-section-heading bank-admin-section-heading-compact">
                <div>
                    <h2 id="recent-activity-title">Aktivitas terbaru</h2>
                    <p>Transaksi terakhir yang tercatat pada ledger.</p>
                </div>
                <a href="{{ \App\Filament\Admin\Resources\TransaksiResource::getUrl('index') }}">Lihat semua</a>
            </div>

            <div class="bank-activity-list">
                @forelse ($recent_transactions as $transaction)
                    <div class="bank-activity-row">
                        <span class="bank-activity-code">TRX-{{ str_pad((string) $transaction->getKey(), 6, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <strong>{{ $transaction->nasabah?->nama_siswa ?? 'Nasabah' }}</strong>
                            <small>{{ $transaction->jenis_trans->label() }} · {{ Money::format($transaction->nominal) }}</small>
                        </div>
                        <time datetime="{{ $transaction->tanggal->toIso8601String() }}">
                            {{ $transaction->tanggal->format('H:i') }} WIB
                        </time>
                    </div>
                @empty
                    <div class="bank-empty-state">
                        <strong>Belum ada transaksi</strong>
                        <span>Aktivitas setoran dan penarikan akan tampil di sini.</span>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-filament-widgets::widget>
