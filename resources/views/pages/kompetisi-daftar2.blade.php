@extends('layouts.dashboard-layout')
@section('title', 'Daftar Kompetisi')
@section('content')
@include('components.daftar-kompetisi-overlay')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <h1>{{ $acara->kompetisi->nama }} - {{ $acara->nama }}</h1>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Kompetisi</li>
                <li><a href="{{ route('dashboard.kompetisi') }}">Daftar</a></li>
                <li><a href="#">Acara 1</a></li>
                <li><a href="#">Nomor Perlombaan 1</a></li>
            </ul>
        </nav>
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
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Umur</th>
                                <th>Jenis Kelamin</th>
                                <th>Club</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($acara->peserta->count() > 0)
                                @foreach ($acara->peserta as $key => $peserta) 
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $peserta->name }}</td>
                                    <td>{{ now()->diffInYears(\Carbon\Carbon::parse($peserta->umur)) }}</td>
                                    <td>{{ $peserta->jenis_kelamin }}</td>
                                    <td>{{ $peserta->user->club }}</td>
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