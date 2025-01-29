@extends('layouts.dashboard-layout')
@section('title', 'Kompetisi Saya')
@section('style')
    <style>
        p {
            margin-bottom: 5px;
        }
        .bold {
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon red">
                        <i class='bx bx-swim'></i>
                    </div>
                    <div class="card-content">
                        <h1>Kompetisi Saya</h1>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Kompetisi</li>
                <li><a href="{{ route('dashboard.kompe-saya') }}">Kompetisi Saya</a></li>
            </ul>
        </nav>
        <div class="bottom-container grid">
            @foreach ($kompetisis as $kompetisi)
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>{{ $kompetisi->nama }}</h2>
                    <a href="{{ route('dashboard.kompe-saya.acara', $kompetisi->id) }}"><button>Lihat</button></a>
                </header>
                <div>
                    <h3 class="mtopbot">
                        @if (now() > $kompetisi->waktu_kompetisi)
                        <p>Status: <span class="status tutup smaller">Selesai</span></p>
                        @elseif (now() >= $kompetisi->tutup_pendaftaran)
                        <p>Status: <span class="status buka smaller">Tutup Registrasi</span></p>
                        @else
                        <p>Status: <span class="status buka smaller">Registrasi</span></p>
                        @endif
                    </h3>
                    
                    <p><strong>Open Registration :</strong> {{ \Carbon\Carbon::parse($kompetisi->buka_pendaftaran)->format('d M Y') }}</p>
                    <p><strong>Closed Registration :</strong> {{ \Carbon\Carbon::parse($kompetisi->tutup_pendaftaran)->format('d M Y') }}</p>
                    <p><strong>Tech Meeting :</strong> {{ $kompetisi->waktu_techmeeting? \Carbon\Carbon::parse($kompetisi->waktu_techmeeting)->format('d M Y') : '-' }}</p>
                    <p><strong>Tanggal Kompetisi :</strong> {{ \Carbon\Carbon::parse($kompetisi->waktu_kompetisi)->format('d M Y') }}</p>
                    <p><strong>Lokasi :</strong> {{ $kompetisi->lokasi }}</p>
                    <p style="margin-bottom: 1em;"></p>
                    <p>{!! $kompetisi->deskripsi !!}</p>
                </div>
            </section>
            @endforeach
        </div>
    </div>
@endsection
