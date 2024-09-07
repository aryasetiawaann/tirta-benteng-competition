@extends('layouts.dashboard-layout')
@section('title', 'Daftar Pembayaran')

@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon dark-green">
                        <i class="icon ph-bold ph-wallet"></i>
                    </div>
                    <div class="card-content">
                        <h1>Riwayat Pembayaran</h1>
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
                    <h1>Daftar Riwayat Pembayaran</h1>
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
                            <th>Kompetisi</th>
                            <th>Nomor Lomba</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Status Pembayaran</th>
                            <th>Waktu Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>                            
                        @if ($atlets->isEmpty())
                        <tr><td colspan="7" style="text-align:center;">Belum ada data</td></tr>
                        @else
                        <?php $total = 0 ?>
                        @foreach ($atlets as $atlet)
                            @foreach ($atlet->acara as $acara)
                                    <tr>
                                        <td>{{ $counter = isset($counter) ? $counter + 1 : 1 }}</td>
                                        <td>{{ $atlet->name }}</td>
                                        <td>{{ $acara->kompetisi->nama }}</td>
                                        <td>{{ $acara->nomor_lomba }} - {{$acara->nama}}</td>
                                        <td><span class="status bayar">Rp.{{ number_format($acara->harga, 2, ',', '.') }}</span></td>
                                        <td><span class="status bayar">{{ $acara->pivot->status_pembayaran }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($acara->pivot->waktu_pembayaran)->format('d-m-Y') }}</td>
                                    </tr>
                            @endforeach
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