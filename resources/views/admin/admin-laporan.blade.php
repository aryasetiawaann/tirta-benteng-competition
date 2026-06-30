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
    .laporan-metrics {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

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
                                <p class="smaller">Tanggal lomba: {{ \Carbon\Carbon::parse($k->waktu_kompetisi)->format('d/m/Y') }}</p>
                            </div>
                            <div class="laporan-metrics">
                                <span>Peserta: <strong>{{ $s['peserta'] ?? 0 }}</strong></span>
                                <span>Nomor: <strong>{{ $s['nomor'] ?? 0 }}</strong></span>
                                <span>Club: <strong>{{ $s['club'] ?? 0 }}</strong></span>
                                <span>Selesai: <strong>{{ $s['selesai'] ?? 0 }}</strong></span>
                                <span>Menunggu: <strong>{{ $s['menunggu'] ?? 0 }}</strong></span>
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
