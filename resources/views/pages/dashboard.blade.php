@extends('layouts.dashboard-layout')
@section('title', 'Dashboard')
@section('content')
    <div class="main-content">
        <div class="top-container">
            <!-- Cards Content -->
            <div class="top-card">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <p>Ini card</p>
                    <h1>100</h1>
                </div>
            </div>
        <div class="top-card">
            <div class="card-icon">
                <i class="bx bxs-grid-alt"></i>
            </div>
            <div class="card-content">
                <p>Ini card</p>
                <h1>100</h1>
            </div>
        </div>
        <div class="top-card">
            <div class="card-icon">
                <i class="bx bxs-grid-alt"></i>
            </div>
            <div class="card-content">
                <p>Ini card</p>
                <h1>100</h1>
            </div>
        </div>
        <div class="top-card">
            <div class="card-icon">
                <i class="bx bxs-grid-alt"></i>
            </div>
            <div class="card-content">
                <p>Ini card</p>
                <h1>100</h1>
            </div>
        </div>
    </div>
    <div class="bottom-container">
            <div class="left-container">
                <div class="table-container">
                    <div class="table-header">
                        <h1>Daftar Peserta</h1>
                    </div>
                    <label for="entries">Tampilkan
                        <select id="entries" name="entries">
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
                                <th>Status Peserta</th>
                                <th>Status Kompetisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Arya</td>
                                <td>Gadjah Mada Swimming Competition 2024</td>
                                <td>120 - 50m Gaya Dada Putra</td>
                                <td><span class="status waiting">Menunggu</span></td>
                                <td><span class="status registration">Registrasi</span></td>
                            </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
                <div class="pagination">
                        <button class="prev">Sebelumnya</button>
                        <span class="current-page">1</span>
                        <button class="next">Selanjutnya</button>
                    </div>
                </div>
            </div>
            <div class="right-container">
                <div class="activity-container">
                    <div class="activity-header">
                        <h1>Aktivitas terbaru</h1>
                    </div>
                    <div class="activity-content">
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut in porro dolorum. Minus quasi beatae nam eius neque illo quaerat, tempora, porro debitis libero sunt voluptate ratione harum facere temporibus.</p> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection