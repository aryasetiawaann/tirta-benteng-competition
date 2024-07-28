@extends('layouts.dashboard-layout')
@section('title', 'Buku Acara')

@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card">
                <div class="acara-card-icon">
                    <i class="icon ph-bold ph-book-open-text"></i>
                </div>
                <div class="card-content">
                    <h1>BUKU ACARA</h1>
                </div>
            </div>
        </div>
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
                        <tr>
                            <td>1</td>
                            <td>Swimming Competition 2024</td>
                            <td><span class="status registration">Registrasi</span></td>
                            <td>
                                <a href="#"><button class="button-green"><i class='fa fa-arrow-right'></i></button></a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Catur Competition 2024</td>
                            <td><span class="status registration">Registrasi</span></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Valorant Competition 2024</td>
                            <td><span class="status registration">Registrasi</span></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Tidur Competition 2024</td>
                            <td><span class="status registration">Registrasi</span></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Rebahan Competition 2024</td>
                            <td><span class="status registration">Registrasi</span></td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Makan Competition 2024</td>
                            <td><span class="status registration">Registrasi</span></td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Main Competition 2024</td>
                            <td><span class="status registration">Registrasi</span></td>
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