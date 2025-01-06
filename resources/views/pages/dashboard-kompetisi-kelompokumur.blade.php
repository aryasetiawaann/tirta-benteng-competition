@extends('layouts.dashboard-layout')
@section('title', 'Daftar Kelompok Umur')
@section('style')
@endsection
@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon red">
                        <i class='bx bx-swim'></i>
                    </div>
                    <div class="card-content">
                        <h1>Kelompok Umur - {{ $nama_kompetisi }}</h1>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Kompetisi</li>
                <li><a href="{{ route('dashboard.kompetisi') }}">Daftar</a></li>
                <li><a href="{{ route('dashboard.kompetisi.kelompokumur', $id_kompetisi) }}">Kelompok Umur</a></li>
            </ul>
        </nav>
        <div class="bottom-container grid">
            <section class="all-container all-card">
            @foreach ($grupList as $grup )
                <header class="flex divider">
                    <h2>KU {{ $grup }}</h2>
                    <a href="{{ route('dashboard.acara', ['kelompok' => $grup, 'id' => $id_kompetisi]) }}"><button>Daftar</button></a>
                </header>
            @endforeach
            </section>
        </div>
    </div>
@endsection
