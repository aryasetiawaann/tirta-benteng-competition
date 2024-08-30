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
    @if (session('success'))
    <div style="color: green;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Menampilkan Pesan Error -->
    @if (session('error'))
        <div style="color: red;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Menampilkan Validasi Error -->
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <nav class="breadcrumb">
        <ul>
            <li><a href="{{ route('dashboard.admin.acara') }}">List Kompetisi</a></li>
            <li><a href="{{ route('dashboard.admin.listacara', $id_kompetisi) }}">{{ $nama_kompetisi }}</a></li>
        </ul>
    </nav>
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
                <a href="{{ route('dashboard.admin.editacara', $ac->id) }}">
                    <button>Edit</button>
                </a>
                <form action="{{ route('dashboard.admin.acara.destroy', $ac->id) }}" method="post">
                    @csrf
                    @method('delete')
                    <button class="button-red button-gap" onclick="return confirm('-- PERINGATAN!! --\nMenghapus acara yang sedang berjalan atau sudah selesai akan menghapus seluruh data peserta dan semua history kompetisi pada pengguna akan terhapus.')">
                        <i class='bx bx-xs bxs-trash'></i>
                    </button>
                </form>
            </div>
        </section>
        @endforeach
        
    </div>
</div>
@endsection