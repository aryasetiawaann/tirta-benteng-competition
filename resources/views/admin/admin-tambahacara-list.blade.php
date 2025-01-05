@extends('admin.admin-dashboard-layout')
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

        .button-container {
            margin: 20px 0 20px 0;
        }
    </style>
@endsection

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
        </div>
    </div>

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

    <nav class="breadcrumb">
        <ul>
            <li><a href="{{ route('dashboard.admin.acara') }}">List Kompetisi</a></li>
            <li><a href="{{ route('dashboard.admin.listacara', $id_kompetisi) }}">{{ $nama_kompetisi }}</a></li>
        </ul>
    </nav>

    <div class="button-container flex">
        <button id="openOverlay">Tambah</button>
    </div>

    <div class="bottom-container grid">
        @foreach ($acara as $ac)
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>
                        {{ strtoupper($ac->nomor_lomba) }} - {{ strtoupper($ac->nama) }}
                        @if($ac->kategori == 'Wanita')
                            PUTRI
                        @elseif($ac->kategori == 'Pria')
                            PUTRA
                        @elseif($ac->kategori == 'Campuran')
                            CAMPURAN
                        @else
                            {{ strtoupper($ac->kategori) }}
                        @endif
                        - KU {{ strtoupper($ac->grup) }}
                    </h2>
                </header>
                <div class="info">
                    <h3 class="mtopbot">
                        Harga: <span class="status harga smaller">Rp.{{ number_format($ac->harga, 2, ',', '.') }}</span>
                    </h3>
                    <p>Kuota: {{ $ac->peserta->count() }} / {{$ac->kuota}}</p>
                    <p>Nomor Grup: {{ $ac->grup }}</p>
                    <p>Min Umur: {{ $ac->min_umur }}</p>
                    <p>Max Umur: {{ $ac->max_umur }}</p>
                </div>
                <div class="actions">
                    <a href="{{ route('dashboard.admin.editacara', $ac->id) }}">
                        <button>Edit</button>
                    </a>
                    <form action="{{ route('dashboard.admin.acara.destroy', $ac->id) }}" method="post">
                        @csrf
                        @method('delete')
                        <button class="button-red" onclick="return confirm('-- PERINGATAN!! --\nMenghapus acara yang sedang berjalan atau sudah selesai akan menghapus seluruh data peserta dan semua history kompetisi pada pengguna akan terhapus.')">
                            <i class='bx bx-xs bxs-trash'></i>
                        </button>
                    </form>
                </div>
            </section>
        @endforeach
    </div>
</div>
@endsection
