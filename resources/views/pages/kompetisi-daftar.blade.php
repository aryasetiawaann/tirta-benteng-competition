@extends('layouts.dashboard-layout')
@section('title', 'Daftar Kompetisi')

@section('style')
<style>
    p {
        margin-bottom: 5px
    }

    .grid {
        grid-template-columns: 1fr 1fr 1fr;
    }

    @media screen and (max-width: 1024px) {
        .grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media screen and (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
            <div class="card-left">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <h1>{{ $nama_kompetisi }} </h1>
                </div>
            </div>
            <div class="card-right">
            </div>
            </div>
        </div>
        <div class="bottom-container grid">
            @foreach ($acara as $aca)
                <section class="all-container all-card">
                    <header class="flex divider">
                        <h2>{{ $aca->nomor_lomba }} - {{ $aca->nama }} - {{ $aca->grup }}</h2>

                        @if ($aca->peserta->count() < $aca->kuota)
                        <a href="{{ route('dashboard.acara.detail', $aca->id) }}"><button>Daftar</button></a>
                        @endif
                        
                    </header>
                    <div>
                        <h3 class="mtopbot">
                            @if ($aca->peserta->count() == $aca->kuota)
                            Status : <span class="status buka smaller">Tutup</span>
                            @else
                            Status : <span class="status buka smaller">Buka</span>
                            @endif
                        </h3>
                        <h3 class="mtopbot">
                            Harga : <span class="status harga smaller">Rp.{{ number_format($aca->harga, 2, ',', '.') }}</span>
                        </h3>
                        <p>Kuota : {{ $aca->peserta->count() }} / {{$aca->kuota}}</p>
                        <p>Nomor Grup : {{ $aca->grup }}</p>
                        <p>Min Umur : {{ $aca->min_umur }}</p>
                        <p>Max Umur : {{ $aca->max_umur }}</p>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection