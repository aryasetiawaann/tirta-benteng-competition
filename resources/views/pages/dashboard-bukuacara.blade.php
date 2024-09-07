@extends('layouts.dashboard-layout')
@section('title', 'Buku Acara')

@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon blue">
                        <i class="icon ph-bold ph-book-open-text"></i>
                    </div>
                    <div class="card-content">
                        <h1>Buku Acara</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="nav-page nav-card">
            <p>
                <a href="#">#</a> / 
            </p>
        </div> -->
        <div class="bottom-container">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Buku Acara</h1>
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
                            <th>Kompetisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($competitions->isEmpty())
                            <tr><td colspan="4" style="text-align:center;">Belum ada data</td></tr>
                        @else
                            @foreach ( $competitions as $competition ) 
                            <tr>
                                <td>{{ $counter = isset($counter) ? $counter + 1 : 1 }}</td>
                                <td>{{ $competition->nama }}</td>
                                @if ( now() > $competition->waktu_kompetisi)
                                <td><span class="status registration">Selesai</span></td>
                                <td>
                                    <a href="{{ route('dashboard.bukuacara.view', $competition->id) }}"><button class="button-green"><i class='fa fa-arrow-right'></i></button></a>
                                </td>
                                @elseif (now() >= $competition->tutup_pendaftaran)
                                <td><span class="status registration">Berjalan</span></td>
                                <td>
                                    <a href="{{ route('dashboard.bukuacara.view', $competition->id) }}"><button class="button-green"><i class='fa fa-arrow-right'></i></button></a>
                                </td>
                                @else
                                <td><span class="status registration">Registrasi</span></td>
                                @endif
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div class="pagination">
                    <button class="prev" disabled>Sebelumnya</button>
                    <div class="page-numbers"></div>
                    <button class="next" disabled>Selanjutnya</button>
                </div>
            </div>
        </div>
    </div>
@endsection