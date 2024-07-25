@extends('layouts.dashboard-layout')
@section('title', 'Atlet Saya')

@section('style')
    <style>
        .table-container {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <p>List Atlet</p>
                    <h1>100</h1>
                </div>
            </div>
        </div>
        <div class="bottom-container">
            <div class="table-container">
                <div class="table-header">
                    <h1>Daftar Atlet</h1>
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
                            <th>Umur</th>
                            <th>Jenis Kelamin</th>
                            <th>Kelengkapan Dokumen</th>
                            <th>Track Record</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Arya</td>
                            <td>20</td>
                            <td>Pria</td>
                            <td><span class="status waiting">Menunggu</span></td>
                            <td><span class="status registration">Mantan Napi</span></td>
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
    </div>
@endsection