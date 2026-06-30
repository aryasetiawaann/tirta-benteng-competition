{{-- resources/views/admin/admin-laporan.blade.php --}}
@extends('admin.admin-dashboard-layout')

@section('style')
<style>
    .laporan-card {
        position: relative;
        padding: 16px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
    }
    .laporan-card .card-head {
        padding-right: 130px; /* leave room for the absolute Export button on desktop */
    }
    .laporan-export {
        position: absolute;
        top: 16px;
        right: 16px;
    }
    .laporan-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(96px, 1fr));
        gap: 10px;
        margin-top: 14px;
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
                    <a href="{{ route('admin.laporan.export-all') }}">
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
                                <p class="smaller">Tanggal lomba: {{ \Carbon\Carbon::parse($k->waktu_kompetisi)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
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
                            <a class="laporan-export" href="{{ route('admin.laporan.export', $k->id) }}">
                                <button type="button">Export</button>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
