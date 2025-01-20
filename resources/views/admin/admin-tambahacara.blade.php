@extends('admin.admin-dashboard-layout')
@section('style')
    <!-- <style>
        .pagination button, button {
            margin-top: 20px;
        }
    </style> -->
@section('content')
<div class="main-content">

    @if (session('success'))
        <x-success-list>
            <x-success-item>{{ session('success') }}</x-success-item>
        </x-success-list>
    @endif

    <!-- Menampilkan Pesan Error -->
    @if (session('error'))
        <x-error-list>
            <x-error-item>{{ session('error') }}</x-error-item>
        </x-error-list>
    @endif

    <!-- Menampilkan Validasi Error -->
    @if ($errors->any())
        <x-error-list>
            @foreach ($errors->all() as $error)
                <x-error-item>{{ $error }}</x-error-item>
            @endforeach
        </x-error-list>
    @endif

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

    <div class="bottom-container grid">
        @foreach ($kompetisi as $kompe)
        <div class="all-container all-card">
            <header class="flex divider">
                <h2>{{ $kompe->nama }}</h2>
            </header>
            <div class="info">
                <h3 class="mtopbot">
                    @if(now() > $kompe->waktu_kompetisi)
                    <p>Status: <span class="status tutup smaller">Selesai</span></p>
                    @elseif (now() >= $kompe->tutup_pendaftaran)
                    <p>Status: <span class="status buka smaller">Berjalan</span></p>
                    @elseif (now() >= $kompe->buka_pendaftaran && now() < $kompe->tutup_pendaftaran)
                    <p>Status: <span class="status buka smaller">Registrasi</span></p>
                    @else
                    <p>Status: <span class="status buka smaller">Belum dibuka</span></p>
                    @endif
                </h3>

                <p>Kategori: {{ $kompe->kategori }}</p>
                <p>Open Registration : {{ \Carbon\Carbon::parse($kompe->buka_pendaftaran)->format('d M Y') }}</p>
                <p>Closed Registration : {{ \Carbon\Carbon::parse($kompe->tutup_pendaftaran)->format('d M Y') }}</p>
                <p>Tech Meeting : {{ $kompe->waktu_techmeeting? \Carbon\Carbon::parse($kompe->waktu_techmeeting)->format('d M Y') : '-' }}</p>
                <p>Tanggal Kompetisi : {{ \Carbon\Carbon::parse($kompe->waktu_kompetisi)->format('d M Y') }}</p>
                <p>Lokasi : {{ $kompe->lokasi }}</p>
                    <p style="margin-bottom: 1em;"></p>
                <div>{!! $kompe->deskripsi !!}</div>
            </div>
            <div class="actions">
                <a href="{{ route('dashboard.admin.listacara', $kompe->id) }}">
                    <button class="button">Lihat Acara</button>
                </a>
                <a href="{{ route('dashboard.admin.editkompetisi', $kompe->id) }}">
                    <button class="button">Edit</button>
                </a>
                <form action="{{ route('dashboard.admin.kompetisi.destroy', $kompe->id) }}" method="post">
                    @csrf
                    @method('delete')
                    <button class="button button-red button-gap2" onclick="return confirm('-- PERINGATAN!! --\nMenghapus kompetisi yang sedang berjalan atau sudah selesai akan menghapus seluruh data peserta dan semua history kompetisi pada pengguna akan terhapus.')">
                        <i class='bx bx-xs bxs-trash'></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection