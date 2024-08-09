@extends('layouts.dashboard-layout')
@section('title', 'Daftar')
@section('style')
    <style>
        p {
        margin-bottom: 5px
        }
    </style>
@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
            <div class="card-left">
                <div class="card-icon red">
                    <i class='bx bx-swim' ></i>
                </div>
                <div class="card-content">
                    <h1>Daftar Kompetisi</h1>
                </div>
            </div>
            <div class="card-right">
                <input type="text" class="search" placeholder="Cari...">
            </div>
            </div>
        </div>
        <div class="bottom-container grid">
            @foreach ($kompetisi as $kompe)
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>{{ $kompe->nama }}</h2>
                    @if (now() > $kompe->buka_pendaftaran && now() < $kompe->tutup_pendaftaran)
                    <a href="{{ route('dashboard.acara', $kompe->id) }}"><button>Daftar</button></a>
                    @endif
                </header>
                <div>
                    <h3 class="mtopbot">
                        @if (now() > $kompe->buka_pendaftaran && now() < $kompe->tutup_pendaftaran)
                        <p>Status: <span class="status buka smaller">Registrasi</span></p>
                        @elseif (now() < $kompe->buka_pendaftaran)
                        <p>Status: <span class="status buka smaller">Belum dibuka</span></p>
                        @else
                        <p>Status: <span class="status tutup smaller">Selesai</span></p>
                        @endif
                    </h3>
                    
                    <p>Open reg : {{ \Carbon\Carbon::parse($kompe->buka_pendaftaran)->format('d M Y') }}</p>
                    <p>Closed reg : {{ \Carbon\Carbon::parse($kompe->tutup_pendaftaran)->format('d M Y') }}</p>
                    <p>Lokasi : {{ $kompe->lokasi }}</p>
                    <p>{{ $kompe->deskripsi }}</p>
                </div>
            </section>
            @endforeach
        </div>
    </div>
@endsection