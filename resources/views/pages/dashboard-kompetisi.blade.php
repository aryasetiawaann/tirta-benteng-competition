@extends('layouts.dashboard-layout')
@section('title', 'Kompetisi Saya')
@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
            <div class="card-left">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <h1>Kompetisi Saya</h1>
                </div>
            </div>
            <div class="card-right">
            </div>
            </div>
        </div>
        <div class="bottom-container grid">
            <section class="all-container all-card">
                <header class="flex divider">
                    <h2>Agung Tirtayasa Competition A</h2>
                    <a href="{{ route('kompetisi.daftar') }}"><button>Lihat</button></a>
                </header>
                <div>
                    <p>Deskripsi</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.</p>
                </div>
            </section>
        </div>
    </div>
@endsection