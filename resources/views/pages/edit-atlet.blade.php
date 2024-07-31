@extends('layouts.dashboard-layout')
@section('title', 'Atlet Saya')
@section('content')
@include('components.daftar-atlet-overlay')
    <div class="main-content">
        <div class="bottom-container">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Edit {{ $atlet->name}}</h1>
                </header>
                <div>
                    <form class="atlet" method="POST" action="{{ route('dashboard.atlet.update', $atlet->id) }}">
                        @csrf
                        @method('put')

                        <label for="nama">Nama Atlet</label>
                        <input type="text" id="nama" name="nama" placeholder="Nama Atlet" value="{{ $atlet->name }}">
                        <label for="umur">Umur</label>
                        <input type="number" id="umur" name="umur" placeholder="Umur" value="{{ $atlet->umur }}">
                        <label for="jenisKelamin">Jenis Kelamin</label>
                        <select id="jenisKelamin" name="jenisKelamin">
                            <option value="pria" {{ $atlet->jenis_kelamin === "Pria" ? "selected" : "" }}>Pria</option>
                            <option value="wanita" {{ $atlet->jenis_kelamin === "Wanita" ? "selected" : "" }}>Wanita</option>
                        </select>
                        <label for="record">Track Record</label>
                        <input type="number" id="record" name="record" placeholder="contoh: 3,25 (Menit)" value="{{ $atlet->track_record }}" step="0.01">
                        <input type="hidden" name="atlet_id" value="{{ $atlet->id }}">
                        <div class="flex center">   
                            <button type="submit" class="w50">Kirim</button>
                        </div>
                        
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection