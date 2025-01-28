@extends('layouts.dashboard-layout')
@section('title', 'Daftar Kompetisi')
@section('style')
    <style>
        p {
            margin-bottom: 5px;
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
                    <div class="card-content" id="competisi" data-competisi-name="{{ $nama_kompetisi }}">
                        <h1>{{$nama_kompetisi}}</h1>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Kompetisi</li>
                <li><a href="{{ route('dashboard.kompe-saya') }}">Kompetisi Saya</a></li>
                <li><a href="{{ route('dashboard.kompe-saya.acara', $id_kompetisi) }}">{{ $nama_kompetisi }}</a></li>
            </ul>
        </nav>
        <div class="bottom-container grid">
            @foreach ($acaras as $acara)
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>
                        {{ strtoupper($acara->nomor_lomba) }} - {{ strtoupper($acara->nama) }} 
                        @if($acara->kategori == 'Wanita')
                            PUTRI
                        @elseif($acara->kategori == 'Pria')
                            PUTRA
                        @elseif($acara->kategori == 'Campuran')
                            CAMPURAN
                        @else
                            {{ strtoupper($acara->kategori) }}
                        @endif    
                        - KU {{ strtoupper($acara->grup) }}
                    </h2>
                    <a href="{{ route('dashboard.kompe-saya.acara.detail', $acara->id) }}"><button>Lihat</button></a>
                </header>
                <div>
                    <h3 class="mtopbot">
                        Harga : <span class="status harga smaller">Rp.{{ number_format($acara->harga, 2, ',', '.') }}</span>
                    </h3>
                    <p><strong>Nomor Grup :</strong> {{ $acara->grup }}</p>
                    <p><strong>Tahun :</strong> {{ $acara->max_umur != null ? $acara->min_umur . ' - ' . $acara->max_umur : $acara->min_umur }}</p>
                </div>
            </section>
            @endforeach
        </div>
    </div>
@endsection