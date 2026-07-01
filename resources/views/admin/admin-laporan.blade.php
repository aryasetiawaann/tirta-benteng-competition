{{-- resources/views/admin/admin-laporan.blade.php --}}
@extends('admin.admin-dashboard-layout')

@section('style')
<style>
    .laporan-card {
        position: relative;
        padding: 22px 24px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #d4d9e0;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
        margin: 0 0 18px;
    }
    .laporan-card .card-head {
        padding-right: 130px; /* leave room for the absolute Export button on desktop */
    }
    .laporan-export {
        position: absolute;
        top: 22px;
        right: 24px;
    }
    .laporan-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    .laporan-stats .stat {
        background: #f5f6f8;
        border-radius: 8px;
        padding: 12px 10px;
        text-align: center;
    }
    .laporan-stats .stat .num {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.1;
        color: #111827;
    }
    .laporan-stats .stat .lbl {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6b7280;
        margin-top: 4px;
    }
    .laporan-stats .stat.is-ok .num { color: #16a34a; }   /* Selesai */
    .laporan-stats .stat.is-wait .num { color: #d97706; }  /* Menunggu */

    .laporan-detail { margin-top: 14px; }
    .laporan-detail > summary {
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        list-style: none;
        user-select: none;
    }
    .laporan-detail > summary::-webkit-details-marker { display: none; }
    .laporan-detail > summary::before { content: '\25B8'; margin-right: 6px; }
    .laporan-detail[open] > summary::before { content: '\25BE'; }
    .laporan-detail-body {
        margin-top: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .laporan-detail-group {
        flex: 1 1;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 14px;
    }
    .laporan-detail-group .grp-title {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6b7280;
        font-weight: 700;
        margin-bottom: 8px;
        padding-bottom: 6px;
        border-bottom: 1px solid #e5e7eb;
    }
    .laporan-detail-group dl {
        margin: 0;
        display: grid;
        grid-template-columns: 1fr auto;
        column-gap: 12px;
        font-size: 0.85rem;
    }
    .laporan-detail-group dt,
    .laporan-detail-group dd {
        padding: 6px 0;
        border-bottom: 1px solid #edeff2;
    }
    .laporan-detail-group dt { color: #4b5563; }
    .laporan-detail-group dd { margin: 0; font-weight: 600; color: #111827; text-align: right; }
    .laporan-detail-group dl > dt:last-of-type,
    .laporan-detail-group dl > dd:last-child { border-bottom: none; padding-bottom: 0; }
    .laporan-detail-group .sub {
        display: block;
        font-size: 0.72rem;
        font-weight: 400;
        color: #9ca3af;
        margin-top: 1px;
    }

    .trend-chart-wrap { position: relative; height: 340px; }
    .trend-toggle button {
        font-size: 0.75rem;
        padding: 4px 10px;
        border: 1px solid #d4d9e0;
        background: #fff;
        border-radius: 6px;
        cursor: pointer;
        margin-left: 6px;
    }
    .trend-toggle button.is-active { background: #111827; color: #fff; border-color: #111827; }

    /* Busy state while an export is being generated. */
    a[data-export].is-exporting { pointer-events: none; opacity: 0.5; }
    a[data-export] button:disabled { cursor: not-allowed; }

    /* Mobile: Export Semua stays in the header; the per-competition Export
       button drops to the end of the card as a full-width button. */
    @media (max-width: 768px) {
        .laporan-card .card-head {
            padding-right: 0;
        }
        .laporan-export {
            position: static;
            display: block;
            margin-top: 14px;
        }
        .laporan-export button {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
    <div class="main-content">
        @if (session('error'))
            <x-error-list>
                <x-error-item>{{ session('error') }}</x-error-item>
            </x-error-list>
        @endif

        <section class="all-container all-card w100">
            <header class="divider flex" style="justify-content: space-between; align-items: center;">
                <h1>Laporan Kompetisi Aktif</h1>
                @if ($competitions->isNotEmpty())
                    <a href="{{ route('admin.laporan.export-all') }}" data-export>
                        <button type="button">Export Semua Aktif</button>
                    </a>
                @endif
            </header>

            @if ($competitions->isEmpty())
                <p class="m10">Tidak ada kompetisi aktif saat ini.</p>
            @else
                <div class="m10">
                    @foreach ($competitions as $k)
                        @php $s = $summaries->get($k->id); @endphp
                        <div class="all-card mtopbot laporan-card">
                            <div class="card-head">
                                <h2 style="margin-bottom: 4px;">{{ $k->nama }}</h2>
                                <p class="smaller">{{ \Carbon\Carbon::parse($k->waktu_kompetisi)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                            </div>
                            <div class="laporan-stats">
                                <div class="stat">
                                    <div class="num">{{ $s['peserta'] ?? 0 }}</div>
                                    <div class="lbl">Peserta</div>
                                </div>
                                <div class="stat">
                                    <div class="num">{{ $s['nomor'] ?? 0 }}</div>
                                    <div class="lbl">Nomor</div>
                                </div>
                                <div class="stat">
                                    <div class="num">{{ $s['nomor_lomba_count'] ?? 0 }}</div>
                                    <div class="lbl">Acara</div>
                                </div>
                                <div class="stat">
                                    <div class="num">{{ $s['club'] ?? 0 }}</div>
                                    <div class="lbl">Club</div>
                                </div>
                                <div class="stat is-ok">
                                    <div class="num">{{ $s['selesai'] ?? 0 }}</div>
                                    <div class="lbl">Selesai</div>
                                </div>
                                <div class="stat is-wait">
                                    <div class="num">{{ $s['menunggu'] ?? 0 }}</div>
                                    <div class="lbl">Menunggu</div>
                                </div>
                            </div>
                            @php
                                $tutup = $k->tutup_pendaftaran ? \Carbon\Carbon::parse($k->tutup_pendaftaran) : null;
                                $sisaHari = $tutup ? now()->startOfDay()->diffInDays($tutup->copy()->startOfDay(), false) : null;
                                $rp = fn ($v) => 'Rp ' . number_format((int) $v, 0, ',', '.');
                            @endphp
                            <details class="laporan-detail">
                                <summary>Detail</summary>
                                <div class="laporan-detail-body">
                                    <div class="laporan-detail-group">
                                        <div class="grp-title">Keuangan</div>
                                        <dl>
                                            <dt>Terkumpul</dt><dd>{{ $rp($s['pendapatan_terkumpul'] ?? 0) }}</dd>
                                            <dt>Tertunda</dt><dd>{{ $rp($s['pendapatan_tertunda'] ?? 0) }}</dd>
                                            <dt>Tingkat Pelunasan</dt><dd>{{ $s['tingkat_pelunasan'] ?? 0 }}%</dd>
                                        </dl>
                                    </div>
                                    <div class="laporan-detail-group">
                                        <div class="grp-title">Operasional</div>
                                        <dl>
                                            <dt>Sisa Hari Pendaftaran</dt>
                                            <dd>{{ $sisaHari === null ? '—' : ($sisaHari < 0 ? 'Ditutup' : $sisaHari . ' hari') }}</dd>
                                        </dl>
                                    </div>
                                    <div class="laporan-detail-group">
                                        <div class="grp-title">Partisipasi</div>
                                        <dl>
                                            <dt>Komposisi Gender<span class="sub">(L / P)</span></dt><dd>{{ $s['gender_l'] ?? 0 }} / {{ $s['gender_p'] ?? 0 }}</dd>
                                            <dt>Rata-rata Nomor per Atlet</dt><dd>{{ $s['nomor_per_atlet'] ?? 0 }}</dd>
                                            <dt>Club Terbanyak</dt>
                                            <dd>{{ $s['club_terbanyak'] ?? '—' }}@if (($s['club_terbanyak'] ?? null) !== null)<span class="sub">({{ $s['club_terbanyak_peserta'] ?? 0 }} peserta, {{ $s['club_terbanyak_nomor'] ?? 0 }} nomor)</span>@endif</dd>
                                        </dl>
                                    </div>
                                </div>
                            </details>
                            <a class="laporan-export" href="{{ route('admin.laporan.export', $k->id) }}" data-export>
                                <button type="button">Export</button>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="all-container all-card w100 mtopbot">
            <header class="divider flex" style="justify-content: space-between; align-items: center;">
                <h1>Tren Kompetisi Selesai</h1>
                @if (!empty($completedTrend))
                    <div class="trend-toggle">
                        <button type="button" data-range="10" class="is-active">10 Terakhir</button>
                        <button type="button" data-range="all">Semua</button>
                    </div>
                @endif
            </header>

            @if (empty($completedTrend))
                <p class="m10">Belum ada kompetisi selesai.</p>
            @else
                <div class="m10 trend-chart-wrap">
                    <canvas id="trendChart"></canvas>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function getCookie(name) {
                var match = document.cookie.split('; ').find(function (row) {
                    return row.indexOf(name + '=') === 0;
                });
                return match ? match.split('=')[1] : null;
            }
            function clearCookie(name) {
                document.cookie = name + '=; Max-Age=0; path=/';
            }

            document.querySelectorAll('a[data-export]').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (link.classList.contains('is-exporting')) {
                        return;
                    }

                    var btn = link.querySelector('button');
                    var token = 'dl' + Date.now() + Math.floor(Math.random() * 1000000);

                    var url = new URL(link.href, window.location.origin);
                    url.searchParams.set('download_token', token);

                    link.classList.add('is-exporting');
                    if (btn) {
                        btn.disabled = true;
                    }

                    function finish() {
                        clearInterval(poll);
                        clearTimeout(safety);
                        clearCookie('download_token');
                        link.classList.remove('is-exporting');
                        if (btn) {
                            btn.disabled = false;
                        }
                    }

                    // The server echoes our token back as the `download_token`
                    // cookie on the download response, so the button re-enables
                    // exactly when the file starts downloading.
                    var poll = setInterval(function () {
                        if (getCookie('download_token') === token) {
                            finish();
                        }
                    }, 400);
                    // Safety net in case the cookie never arrives.
                    var safety = setTimeout(finish, 120000);

                    window.location.href = url.toString();
                });
            });
        });
    </script>

    @if (!empty($completedTrend))
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var raw = @json($completedTrend);
            var canvas = document.getElementById('trendChart');
            if (!canvas || !window.Chart || !raw.length) {
                return;
            }

            function rpShort(v) {
                if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1).replace('.', ',') + 'jt';
                if (v >= 1000) return 'Rp ' + Math.round(v / 1000) + 'rb';
                return 'Rp ' + v;
            }
            function rpFull(v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); }

            var chart = null;
            function render(range) {
                var data = range === 'all' ? raw : raw.slice(-10);
                var cfg = {
                    type: 'line',
                    data: {
                        labels: data.map(function (d) { return d.nama; }),
                        datasets: [
                            { label: 'Peserta', data: data.map(function (d) { return d.peserta; }), yAxisID: 'y', borderColor: '#2563eb', backgroundColor: '#2563eb', tension: 0.25 },
                            { label: 'Nomor', data: data.map(function (d) { return d.nomor; }), yAxisID: 'y', borderColor: '#16a34a', backgroundColor: '#16a34a', tension: 0.25 },
                            { label: 'Revenue', data: data.map(function (d) { return d.revenue; }), yAxisID: 'y1', borderColor: '#d97706', backgroundColor: '#d97706', tension: 0.25 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    afterTitle: function (items) {
                                        var d = data[items[0].dataIndex];
                                        return d ? d.tanggal : '';
                                    },
                                    label: function (ctx) {
                                        if (ctx.dataset.label === 'Revenue') {
                                            return 'Revenue: ' + rpFull(ctx.parsed.y);
                                        }
                                        return ctx.dataset.label + ': ' + ctx.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { type: 'linear', position: 'left', beginAtZero: true, title: { display: true, text: 'Peserta / Nomor' } },
                            y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue' }, ticks: { callback: function (v) { return rpShort(v); } } }
                        }
                    }
                };
                if (chart) { chart.destroy(); }
                chart = new Chart(canvas, cfg);
            }

            render('10');

            document.querySelectorAll('.trend-toggle button').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.trend-toggle button').forEach(function (b) { b.classList.remove('is-active'); });
                    btn.classList.add('is-active');
                    render(btn.getAttribute('data-range'));
                });
            });
        });
    </script>
    @endif
@endsection
