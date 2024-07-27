@extends('layouts.dashboard-layout')
@section('title', 'Daftar')
@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
            <div class="card-left">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <h1>Daftar Kompetisi</h1>
                </div>
            </div>
            <div class="card-right">
                <input type="text" id="search" placeholder="Cari...">
            </div>
            </div>
        </div>
        <div class="bottom-container grid">
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>Agung Tirtayasa Competition</h2>
                    <button>Daftar</button>
                </header>
                <div>
                    <p>Deskripsi</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.</p>
                </div>
            </section>
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>Agung Tirtayasa Competition</h2>
                    <button>Daftar</button>
                </header>
                <div>
                    <p>Deskripsi</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.</p>
                </div>
            </section>
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>Agung Tirtayasa Competition</h2>
                    <button>Daftar</button>
                </header>
                <div>
                    <p>Deskripsi</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.</p>
                </div>
            </section>
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>Agung Tirtayasa Competition</h2>
                    <button>Daftar</button>
                </header>
                <div>
                    <p>Deskripsi</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.</p>
                </div>
            </section>
        </div>
    </div>
@endsection