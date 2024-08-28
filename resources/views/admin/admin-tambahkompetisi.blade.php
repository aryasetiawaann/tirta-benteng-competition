@extends('admin.admin-dashboard-layout')
@section('content')
<div class="main-content">
    <div class="tambah-kompetisi card">
        @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
        @endif

        <!-- Menampilkan Pesan Error -->
        @if (session('error'))
            <div style="color: red;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Menampilkan Validasi Error -->
        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="all-container all-card w100">
            <header class="flex divider">
                <h2>Tambah Kompetisi</h2>
            </header>
            <section>
                <form class="tambah-container" method="POST" action="{{ route('dashboard.admin.tambahkompetisi') }}">
                    @csrf
    
                    <label for="nama">Nama Kompetisi*</label>
                    <input type="text" id="nama" name="nama" placeholder="Nama Kompetisi">
                    <label for="kategori">Kategori*</label>
                    <select id="kategori" name="kategori">
                        <option value="fun">Fun</option>
                        <option value="resmi">Resmi</option>
                    </select>
                    <label for="openreg">Open Registrasi*</label>
                    <input type="date" id="openreg" name="openreg" placeholder="Open Registrasi">
                    <label for="closereg">Close Registrasi*</label>
                    <input type="date" id="closereg" name="closereg" placeholder="Close Registrasi">
                    <label for="techmeet">Technical Meeting</label>
                    <input type="date" id="techmeet" name="techmeet" placeholder="Technical Meeting">
                    <label for="datekompe">Tanggal Kompetisi*</label>
                    <input type="date" id="datekompe" name="datekompe">
                    <label for="lokasi">Lokasi*</label>
                    <input type="text" id="lokasi" name="lokasi" placeholder="Lokasi">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" id="" cols="30" rows="10"></textarea>
                    <div class="flex center">   
                        <button type="submit" class="w100">Tambah</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

@endsection