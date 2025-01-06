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

        @media screen and (max-width: 1280px) {
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
                    <div class="card-content" id="acara" data-acara-name="{{ $nama_kompetisi }}">
                        <h1>{{ $kelompok }} - {{ $nama_kompetisi }} </h1>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Kompetisi</li>
                <li><a href="{{ route('dashboard.kompetisi') }}">Daftar</a></li>
                <li><a href="{{ route('dashboard.kompetisi.kelompokumur', $id_kompetisi) }}">Kelompok Umur</a></li>
                <li><a href="{{ route('dashboard.acara', ['kelompok'=> $kelompok, 'id' => $id_kompetisi]) }}">KU {{ $kelompok }}</a></li>
            </ul>
        </nav>
        <div class="bottom-container grid">
            @foreach ($acara as $aca)
                <section class="all-container all-card">
                    <header class="flex divider">
                        <h2>
                            {{ strtoupper($aca->nomor_lomba) }} - {{ strtoupper($aca->nama) }} 
                            @if($aca->kategori == 'Wanita')
                                PUTRI
                            @elseif($aca->kategori == 'Pria')
                                PUTRA
                            @elseif($aca->kategori == 'Campuran')
                                CAMPURAN
                            @else
                                {{ strtoupper($aca->kategori) }}
                            @endif
                            - KU {{ strtoupper($aca->grup) }}
                        </h2>

                        @if ($aca->peserta->count() < $aca->kuota)
                        <a href="{{ route('dashboard.acara.detail', ['kelompok' => $kelompok, 'id' => $aca->id]) }}"><button>Daftar</button></a>
                        @endif
                    </header>
                    <div class="card-info">
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
                        <p><strong>Nomor Grup :</strong> {{ $aca->grup }}</p>
                        <p><strong>Min Umur :</strong> {{ $aca->min_umur }}</p>
                        <p><strong>Max Umur :</strong> {{ $aca->max_umur }}</p>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection