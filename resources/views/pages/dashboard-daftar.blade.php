@extends('layouts.dashboard-layout')
@section('title', 'Daftar')
@section('style')
    <style>
        
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
                    @if ( now() < $kompe->tutup_pendaftaran)
                    <a href="{{ route('dashboard.acara', $kompe->id) }}"><button>Daftar</button></a>
                    @endif
                </header>
                <div>
                    @if ( now() < $kompe->tutup_pendaftaran)
                    <p>Status: <span>Registrasi</span></p>
                    @else
                    <p>Status: <span>Selesai</span></p>
                    @endif
                    
                    <p>Open reg: {{ $kompe->buka_pendaftaran }}</p>
                    <p>Closed reg: {{ $kompe->tutup_pendaftaran }}</p>
                    <p>lokasi: {{ $kompe->lokasi }}</p>
                    <p>{{ $kompe->deskripsi }}</p>
                </div>
            </section>
            @endforeach
        </div>
    </div>
@endsection