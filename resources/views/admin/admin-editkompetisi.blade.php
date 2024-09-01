@extends('admin.admin-dashboard-layout')
@section('content')
<div class="main-content">
    <div class="all-container all-card w100">
        <header class="flex divider">
            <h2>Edit {{$kompetisi->nama}}</h2>
        </header>
        <section>
            <form class="tambah-container" method="POST" action="{{ route('dashboard.admin.updatekompetisi', $kompetisi->id) }}">
                @csrf
                @method('put')

                <label for="nama">Nama Kompetisi*</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Kompetisi" value="{{ $kompetisi->nama}}">
                <label for="kategori">Kategori*</label>
                <select id="kategori" name="kategori">
                    <option value="fun" {{ $kompetisi->kategori === "Fun" ? "selected" : "" }}>Fun</option>
                    <option value="resmi" {{ $kompetisi->kategori === "Resmi" ? "selected" : "" }}>Resmi</option>
                </select>
                <label for="openreg">Open Registrasi*</label>
                <input type="date" id="openreg" name="openreg" placeholder="Open Registrasi" value="{{ $kompetisi->buka_pendaftaran }}">
                <label for="closereg">Close Registrasi*</label>
                <input type="date" id="closereg" name="closereg" placeholder="Close Registrasi" value="{{ $kompetisi->tutup_pendaftaran }}">
                <label for="techmeet">Technical Meeting</label>
                <input type="date" id="techmeet" name="techmeet" placeholder="Technical Meeting" value="{{ $kompetisi->waktu_techmeeting }}">
                <label for="datekompe">Tanggal Kompetisi*</label>
                <input type="date" id="datekompe" name="datekompe" value="{{ $kompetisi->waktu_kompetisi }}">
                <label for="lokasi">Lokasi*</label>
                <input type="text" id="lokasi" name="lokasi" placeholder="Lokasi" value="{{ $kompetisi->lokasi }}">
                <label for="deskripsi">Deskripsi</label>
                <input id="deskripsi" type="hidden" name="deskripsi" value="{{ $kompetisi->deskripsi }}">
                <trix-editor input="deskripsi" style="height:200px;"></trix-editor>
                <input type="hidden" name="id" value="{{ $kompetisi->id }}">
                <div class="flex center">    
                    <button type="submit" class="w100">Simpan</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection