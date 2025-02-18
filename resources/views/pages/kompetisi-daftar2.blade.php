@extends('layouts.dashboard-layout')
@section('title', 'Daftar Kompetisi')
@section('content')
@include('components.daftar-kompetisi-overlay')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon">
                        <i class="bx bxs-grid-alt"></i>
                    </div>
                    <div class="card-content" id="acara-detail" data-acara-detail="{{ $acara->nama }}">
                        <h1>{{ $acara->kompetisi->nama }} - {{ $acara->nama }}</h1>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Kompetisi</li>
                <li><a href="{{ route('dashboard.kompetisi') }}">Daftar</a></li>
                <li><a href="{{ route('dashboard.kompetisi.kelompokumur', $acara->kompetisi->id) }}">Kelompok Umur</a></li>
                <li><a href="{{ route('dashboard.acara', ['kelompok'=> $kelompok, 'id' => $acara->kompetisi->id]) }}">KU {{ $kelompok }}</a></li>
                <li><a href="{{ route('dashboard.acara.detail', ['kelompok'=> $kelompok, 'id' => $acara->id]) }}">{{ $acara->nama }}</a></li>
            </ul>
        </nav>
        @if ($errors->any())
        <x-error-list>
            @foreach ($errors->all() as $error)
                <x-error-item>{{ $error }}</x-error-item>
            @endforeach
        </x-error-list>
        @endif
        @if (session('success'))
            <x-success-list>
                <x-success-item>{{ session('success') }}</x-success-item>
            </x-success-list>
        @endif
        <div class="bottom-container">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Atlet</h1>
                    @if ($acara->peserta->count() < $acara->kuota)
                    <a id="openOverlay"><button>Daftar</button></a>
                    @else
                    <a id="openOverlay"><button disabled style="background-color: gray">Kuota Penuh</button></a>
                    @endif
                </header>
                <div class="table-container">
                    <label for="entries">Tampilkan
                        <select id="entries" name="entries">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select> 
                        entri
                    </label>
                    <input type="text" id="search" placeholder="Cari...">
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Tahun Lahir</th>
                                    <th>Jenis Kelamin</th>
                                    {{-- <th>Club</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @if ($atletsList->count() > 0)
                                    @foreach ($atletsList as $peserta)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $peserta->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($peserta->umur)->year }}</td>
                                        <td>{{ $peserta->jenis_kelamin }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" style="text-align:center;">Belum ada atlet terdaftar</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination">
                        <button class="prev" disabled>Sebelumnya</button>
                        <div class="page-numbers"></div>
                        <button class="next" disabled>Selanjutnya</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection