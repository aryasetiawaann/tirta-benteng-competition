@extends('admin.admin-dashboard-layout')
@section('content')
<div class="main-content">
    @if (session('success'))
        <x-success-list>
            <x-success-item>{{ session('success') }}</x-success-item>
        </x-success-list>
    @endif

    <!-- Menampilkan Pesan Error -->
    @if (session('error'))
        <x-error-list>
            <x-error-item>{{ session('error') }}</x-error-item>
        </x-error-list>
    @endif

    <!-- Menampilkan Validasi Error -->
    @if ($errors->any())
        <x-error-list>
            @foreach ($errors->all() as $error)
                <x-error-item>{{ $error }}</x-error-item>
            @endforeach
        </x-error-list>
    @endif
    <div class="admin-container tambah-kompetisi">
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
                    <input id="deskripsi" type="hidden" name="deskripsi">
                    <trix-editor input="deskripsi" style="height:200px;"></trix-editor>
                    <div class="flex center">   
                        <button type="submit" class="w100">Tambah</button>
                    </div>
                </form>
            </section>
        </div>

        <div class="all-container all-card w100">
            <header class="flex divider">
                <h2>Tambah Logo PDF</h2>
            </header>
            <section>
                <form class="tambah-container" enctype="multipart/form-data" method="POST" action="{{ route('dashboard.admin.kompetisi.logo.create') }}">
                    @csrf
    
                    <label for="kompetisi">Pilih Kompetisi</label>
                    <select name="kompetisi" id="kompetisi">
                        @if ($kompetisis->count() > 0 )
                            @foreach ($kompetisis as $key => $kompetisi)
                                @if ($key == 0)
                                <option value="{{ $kompetisi->id }}" selected>{{ $kompetisi->nama }}</option>
                                @else
                                <option value="{{ $kompetisi->id }}">{{ $kompetisi->nama }}</option>
                                @endif
                        @endforeach    
                        @else
                            <option value="" selected>Tidak ada kompetisi</option>                  
                        @endif
                    </select>

                    <label for="logo">Masukan Logo</label>
                    <input type="file" name="logo[]" multiple id="logo">

                    @if ($kompetisis->count() > 0)
                    <div class="flex center">
                        <button class="w50" type="submit">Simpan</button>
                    </div>
                    @endif
                </form>
            </section>
        </div>

        <div class="all-container all-card w100">
            <header class="flex divider">
                <h2>Tambah Detail Harga</h2>
            </header>
            <section>
                <form class="tambah-container" enctype="multipart/form-data" method="POST" action="{{ route('dashboard.admin.kompetisi.detail-harga.create') }}">
                    @csrf
    
                    <label for="kompetisi">Pilih Kompetisi</label>
                    <select name="kompetisi" id="kompetisi">
                        @if ($kompetisis->count() > 0 )
                            @foreach ($kompetisis as $key => $kompetisi)
                                @if ($key == 0)
                                <option value="{{ $kompetisi->id }}" selected>{{ $kompetisi->nama }}</option>
                                @else
                                <option value="{{ $kompetisi->id }}">{{ $kompetisi->nama }}</option>
                                @endif
                        @endforeach    
                        @else
                            <option value="" selected>Tidak ada kompetisi</option>                  
                        @endif
                    </select>

                    <label for="judul">Judul*</label>
                    <input type="text" name="judul" id="judul">

                    <label for="harga">Harga*</label>
                    <p><i style="font-size: 12px">(tanpa titik, contoh: 20.000 menjadi 20000)</i></p>
                    <input type="number" name="harga" id="harga">

                    <label for="deskripsiHarga">Deskripsi</label>
                    <input id="deskripsiHarga" type="hidden" name="deskripsiHarga">
                    <trix-editor input="deskripsiHarga" style="height:200px;"></trix-editor>

                    @if ($kompetisis->count() > 0)
                    <div class="flex center">
                        <button class="w50" type="submit">Simpan</button>
                    </div>
                    @endif
                </form>
            </section>
        </div>
    </div>
</div>

@endsection