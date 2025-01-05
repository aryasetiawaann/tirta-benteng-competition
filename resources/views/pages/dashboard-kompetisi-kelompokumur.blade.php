@extends('layouts.dashboard-layout')
@section('title', 'Daftar Kelompok Umur')
@section('style')
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
                        <h1>Daftar Kelompok Umur</h1>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Kompetisi</li>
                <li><a href="{{ route('dashboard.kompetisi') }}">Daftar</a></li>
                <li><a href="{{ route('dashboard.kompetisi.kelompokumur') }}">Daftar Kelompok Umur</a></li>
            </ul>
        </nav>
        <div class="bottom-container grid">
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>KU A (Umur 10 - 12 Tahun) >///<</h2>
                    <a href="#"><button>Daftar</button></a>
                </header>
                <div class="card-info">
                    <h3 class="mtopbot">
                        <p>Status: <span class="status buka smaller">Status Kompetisi</span></p>
                    </h3>
                </div>
            </section>
            <!-- @foreach ($kompetisi as $kompe)
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>{{ $kompe->nama }}</h2>
                    @if (now() >= $kompe->buka_pendaftaran && now() < $kompe->tutup_pendaftaran)
                    <a href="{{ route('dashboard.acara', $kompe->id) }}"><button>Daftar</button></a>
                    @endif
                </header>
                <div class="card-info">
                    <h3 class="mtopbot">
                        @if(now() > $kompe->waktu_kompetisi)
                        <p>Status: <span class="status tutup smaller">Selesai</span></p>
                        @elseif (now() >= $kompe->tutup_pendaftaran)
                        <p>Status: <span class="status buka smaller">Tutup Registrasi</span></p>
                        @elseif (now() >= $kompe->buka_pendaftaran && now() < $kompe->tutup_pendaftaran)
                        <p>Status: <span class="status buka smaller">Registrasi</span></p>
                        @else
                        <p>Status: <span class="status buka smaller">Belum dibuka</span></p>
                        @endif
                    </h3>
                    
                    <p><strong>Open Registration :</strong> {{ \Carbon\Carbon::parse($kompe->buka_pendaftaran)->format('d M Y') }}</p>
                    <p><strong>Closed Registration :</strong> {{ \Carbon\Carbon::parse($kompe->tutup_pendaftaran)->format('d M Y') }}</p>
                    <p><strong>Tech Meeting :</strong> {{ $kompe->waktu_techmeeting ? \Carbon\Carbon::parse($kompe->waktu_techmeeting)->format('d M Y') : '-' }}</p>
                    <p><strong>Tanggal Kompetisi :</strong> {{ \Carbon\Carbon::parse($kompe->waktu_kompetisi)->format('d M Y') }}</p>
                    <p><strong>Lokasi :</strong> {{ $kompe->lokasi }}</p>
                    <p style="margin-bottom: 1em;"></p>
                    <p>{!! $kompe->deskripsi !!}</p>
                </div>
            </section>
            @endforeach -->
        </div>
    </div>
@endsection
