@extends('layouts.dashboard-layout')
@section('title', 'Kompetisi Saya')
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
                    <h1>Kompetisi Saya</h1>
                </div>
            </div>
            <div class="card-right">
                <input type="text" class="search" placeholder="Cari...">
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
                        @if (now() < $kompetisi->tutup_pendaftaran)
                        <p>Status: <span class="status buka smaller">Menunggu</span></p>
                        @else
                        <p>Status: <span class="status tutup smaller">Selesai</span></p>
                        @endif
                    </h3>
                    
                    <p>Open Registration : {{ \Carbon\Carbon::parse($kompetisi->buka_pendaftaran)->format('d M Y') }}</p>
                    <p>Closed Registration : {{ \Carbon\Carbon::parse($kompetisi->tutup_pendaftaran)->format('d M Y') }}</p>
                    <p>Lokasi : {{ $kompetisi->lokasi }}</p>
                    <p>{{ $kompetisi->deskripsi }}</p>
                </div>
            </section>
            @endforeach
        </div>
    </div>
@endsection