@extends('layouts.dashboard-layout')
@section('title', 'Daftar Kompetisi')
@section('content')
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
                <li><a href="{{ route('dashboard.kompe-saya') }}">Kompetisi Saya</a></li>
                <li><a href="{{ route('dashboard.kompe-saya.acara', $acara->kompetisi->id) }}">{{ $acara->kompetisi->nama }}</a></li>
                <li><a href="{{ route('dashboard.kompe-saya.acara.detail', $acara->id) }}">{{ $acara->nama }}</a></li>
            </ul>
        </nav>
        <div class="bottom-container">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Atlet</h1>
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
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Umur</th>
                                <th>Jenis Kelamin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($atlets->count() > 0)
                                @foreach ($atlets as $key => $peserta) 
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $peserta->name }}</td>
                                        <td>{{ now()->diffInYears(\Carbon\Carbon::parse($peserta->umur)) }}</td>
                                        <td>{{ $peserta->jenis_kelamin }}</td>
                                    </tr> 
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" style="text-align:center;">Belum ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
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