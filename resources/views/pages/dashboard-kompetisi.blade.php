@extends('layouts.dashboard-layout')
@section('title', 'Daftar')
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
                        <h1>Daftar Kompetisi</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengumuman jika nomor telepon belum diisi -->
        @if (auth()->user()->phone == null)
            <div class="alert alert-warning">
                <strong>Perhatian!</strong> Anda belum bisa mendaftar kompetisi karena belum mengisi nomor telepon. Silakan isi nomor telepon anda pada halaman <a href="{{ route('profile.edit') }}">profil</a>
            </div>
        @endif

        <nav class="breadcrumb">
            <ul>
                <li>Kompetisi</li>
                <li><a href="{{ route('dashboard.kompetisi') }}">Daftar</a></li>
            </ul>
        </nav>
        
        <div class="bottom-container grid">
            @foreach ($kompetisi as $kompe)
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>{{ $kompe->nama }}</h2>
                    @if (now() >= $kompe->buka_pendaftaran && now() < $kompe->tutup_pendaftaran)
                        <!-- Menonaktifkan tombol jika nomor telepon belum diisi -->
                        @if (auth()->user()->phone == null)
                            <button disabled>Daftar</button>
                        @else
                            <a href="{{ route('dashboard.kompetisi.kelompokumur', $kompe->id) }}"><button>Daftar</button></a>
                        @endif
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
            @endforeach
        </div>
    </div>
@endsection
