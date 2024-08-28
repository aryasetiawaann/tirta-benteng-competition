@extends('admin.admin-dashboard-layout')
@section('content')
@include('components.tambah-acara-overlay')
<div class="main-content">
    <div class="top-container">
        <div class="top-card all-card flex">
        <div class="card-left">
            <div class="card-icon">
                <i class="bx bxs-grid-alt"></i>
            </div>
            <div class="card-content">
                <h1>{{$nama_kompetisi}}</h1>
            </div>
        </div>
        <div class="card-right">
        </div>
        </div>
    </div>
    <div>
        <h2>Peringatan!!</h2>
        <p>Menghapus acara yang sedang berjalan atau sudah selesai akan menghapus seluruh data peserta dan semua history kompetisi pada pengguna akan terhapus.</p>
    </div>
    {{-- <nav class="breadcrumb">
        <ul>
            <li>Kompetisi</li>
            <li><a href="{{ route('dashboard.kompe-saya') }}">Kompetisi Saya</a></li>
            <li><a href="{{ route('dashboard.kompe-saya.acara', $id_kompetisi) }}">{{ $nama_kompetisi }}</a></li>
        </ul>
    </nav> --}}
    <div>
        <button id="openOverlay">Tambah</button>
    </div>
    <div class="bottom-container grid">
        
        @foreach ($acara as $ac)
        <section class="all-container all-card">
            <header class="flex divider">
                <h2>{{ $ac->nomor_lomba }} - {{ $ac->nama }} - {{ $ac->grup }}</h2>
            </header>
            <div>
                <h3 class="mtopbot">
                    Harga : <span class="status harga smaller">Rp.{{ number_format($ac->harga, 2, ',', '.') }}</span>
                </h3>
                <p>Kuota : {{ $ac->peserta->count() }} / {{$ac->kuota}}</p>
                <p>Nomor Grup : {{ $ac->grup }}</p>
                <p>Min Umur : {{ $ac->min_umur }}</p>
                <p>Max Umur : {{ $ac->max_umur }}</p>
            </div>
            <div>
                <button>edit</button>
                <form action="{{ route('dashboard.admin.acara.destroy', $ac->id) }}" method="post">
                    @csrf
                    @method('delete')
                    <button class="button-red button-gap" onclick="return confirm('Apakah kamu yakin ingin menghapus? ')">
                        <i class='bx bx-xs bxs-trash'></i>
                    </button>
                </form>
            </div>
        </section>
        @endforeach
        
    </div>
</div>
@endsection