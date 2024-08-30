@extends('admin.admin-dashboard-layout')
@section('content')
<div class="main-content">
    <div class="all-container all-card w100">
        <header class="flex divider">
            <h2>Edit {{$acara->nama}}</h2>
        </header>
        <section>
            <form class="tambah-container" method="POST" action="{{ route('dashboard.admin.updateacara', $acara->id) }}">
                @csrf
                @method('put')

                <label for="nomor">Nomor Acara*</label>
                <input type="number" id="nomor" name="nomor" placeholder="Nomor acara" value="{{ $acara->nomor_lomba }}">
                <label for="nama">Nama Acara*</label>
                <input type="text" id="nama" name="nama" placeholder="Nama acara" value="{{ $acara->nama }}">
                <label for="kategori">Kategori*</label>
                <select id="kategori" name="kategori" >
                    <option value="campuran" {{ $acara->kategori === "Campuran" ? "selected" : "" }}>Campuran</option>
                    <option value="pria" {{ $acara->kategori === "Pria" ? "selected" : "" }}>Pria</option>
                    <option value="wanita" {{ $acara->kategori === "Wanita" ? "selected" : "" }}>Wanita</option>
                </select>
                <label for="harga">Harga*</label>
                <p><i style="font-size: 12px">(tanpa titik, contoh: 20.000 menjadi 20000)</i></p>
                <input type="number" id="harga" name="harga" placeholder="Harga" value="{{ $acara->harga }}">
                <label for="kuota">Kuota Peserta*</label>
                <input type="number" id="kuota" name="kuota" placeholder="Kuota peserta" value="{{ $acara->kuota }}">
                <label for="grup">Grup*</label>
                <input type="text" id="grup" name="grup" placeholder="Grup" value="{{ $acara->grup }}">
                <label for="minumur">Min Umur Peserta*</label>
                <input type="number" id="minumur" name="minumur" placeholder="Minimal umur peserta" value="{{ $acara->min_umur }}">
                <label for="maxumur">Max Umur Peserta*</label>
                <input type="number" id="maxumur" name="maxumur" placeholder="Maximal umur peserta" value="{{ $acara->max_umur }}">
                <input type="hidden" name="kompe_id" value="{{ $acara->kompetisi->id }}">
                <input type="hidden" name="id" value="{{ $acara->id }}">

                <div class="flex center">   
                    <button type="submit" class="w50">Simpan</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection