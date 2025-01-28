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
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Kompetisi</th>
                                    <th>Nomor Lomba</th>
                                    <th>Jumlah Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>                            
                                @if ($atlets->isEmpty())
                                <tr><td colspan="5" style="text-align:center;">Belum ada riwayat pembayaran</td></tr>
                                @else
                                @php $counter = 1; @endphp
                                @foreach ($atlets as $atlet)
                                    @foreach ($atlet->acara as $acara)
                                    @if ($acara->pivot->status_pembayaran == 'Selesai')   
                                    <tr>
                                        <td>{{ $counter++}}</td>
                                        <td>{{ $atlet->name }}</td>
                                        <td>{{ $acara->kompetisi->nama }}</td>
                                        <td>{{ $acara->nomor_lomba }} - {{$acara->nama}}</td>
                                        <td><span class="status bayar">Rp.{{ number_format($acara->harga, 2, ',', '.') }}</span></td>
                                    </tr>
                                    @endif
                                    @endforeach
                                @endforeach
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
        </div>
    </div>
@endsection