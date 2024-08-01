@extends('layouts.dashboard-layout')
@section('title', 'Dashboard')
@section('content')
    <div class="main-content">
        <div class="top-container">
            <!-- Cards Content -->
            <div class="top-card all-card">
                <div class="card-icon red">
                    <i class='bx bx-swim' ></i>
                </div>
                <div class="card-content">
                    <p>Kompetisi Aktif</p>
                    <h1>{{ $kompetisi_count }}</h1>
                </div>
            </div>
        <div class="top-card all-card">
            <div class="card-icon orange">
                <i class='bx bxs-trophy' ></i>
            </div>
            <div class="card-content">
                <p>Kompetisi Terdaftar</p>
                <h1>aw</h1>
            </div>
        </div>
        <div class="top-card all-card">
            <div class="card-icon green">
                <i class="icon ph-bold ph-wallet"></i>
            </div>
            <div class="card-content">
                <p>Tagihan Terbayar</p>
                <h1>0 / 5</h1>
            </div>
        </div>
        <div class="top-card all-card">
            <div class="card-icon">
                <i class='bx bxs-user' ></i>
            </div>
            <div class="card-content">
                <p>Atlet Terdaftar</p>
                <h1>1</h1>
            </div>
        </div>
    </div>
    <div class="bottom-container">
            <div class="left-container">
                <section class="all-container all-card">
                    <header class="divider">
                        <h1>Daftar Peserta</h1>
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
                        {{-- <div class="loading-overlay" id="loading-overlay">
                            <div class="spinner"></div>
                        </div> --}}
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Kompetisi</th>
                                    <th>Nomor Lomba</th>
                                    <th>Status Peserta</th>
                                    <th>Status Kompetisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Arya</td>
                                    <td>Agung Tirtayasa Competition</td>
                                    <td>501</td>
                                    <td><span class="status waiting">Menunggu</span></td>
                                    <td><span class="status registration">Mantan Napi</span></td>
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
                </section>
            </div>
            <div class="right-container">
                <div class="all-container all-card">
                    <header class="divider">
                        <h2>Aktivitas terbaru</h2>
                    </header>
                    <div class="activity-content">
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut in porro dolorum. Minus quasi beatae nam eius neque illo quaerat, tempora, porro debitis libero sunt voluptate ratione harum facere temporibus.</p> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection