@extends('layouts.dashboard-layout')
@section('title', 'Tagihan')

@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card">
                <div class="lunas-card-icon">
                    <i class="icon ph-bold ph-wallet"></i>
                </div>
                <div class="card-content">
                    <h1>INFORMASI PEMBAYARAN</h1>
                </div>
            </div>
        </div>
        <div class="bottom-container">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Tagihan Belum Dibayarkan</h1>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Arya</td>
                            <td>Swimming Competition 2024</td>
                            <td>120 - 50m Gaya Dada Putra</td>
                            <td><span class="status bayar">Rp150.000,00</span></td>
                            
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Tai</td>
                            <td>Swimming Competition 2024</td>
                            <td>121 - 50m Gaya Dada Putra</td>
                            <td><span class="status bayar">Rp150.000,00</span></td>
                            
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Kucing</td>
                            <td>Swimming Competition 2024</td>
                            <td>122 - 50m Gaya Dada Putra</td>
                            <td><span class="status bayar">Rp150.000,00</span></td>
                            
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Bool</td>
                            <td>Swimming Competition 2024</td>
                            <td>123 - 50m Gaya Dada Putra</td>
                            <td><span class="status bayar">Rp150.000,00</span></td>
                            
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Sapi</td>
                            <td>Swimming Competition 2024</td>
                            <td>124 - 50m Gaya Dada Putra</td>
                            <td><span class="status bayar">Rp150.000,00</span></td>
                            
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Silit</td>
                            <td>Swimming Competition 2024</td>
                            <td>125 - 50m Gaya Dada Putra</td>
                            <td><span class="status bayar">Rp150.000,00</span></td>
                            
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Asu</td>
                            <td>Swimming Competition 2024</td>
                            <td>126 - 50m Gaya Dada Putra</td>
                            <td><span class="status bayar">Rp150.000,00</span></td>
                            
                        </tr>
                        <!-- Add more rows as needed -->
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