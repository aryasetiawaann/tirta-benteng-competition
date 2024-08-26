@extends('admin.admin-dashboard-layout')
@section('content')
<div class="main-content">
    <div class="admin-container">
        <div class="tambah-kompetisi card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Tambah Kompetisi</h2>
                </header>
                <section>
                    <form class="tambah-container" method="POST" action="">
                        @csrf

                        <label for="namakomp">Nama Kompetisi</label>
                        <input type="text" id="namakomp" name="namakomp" placeholder="Nama Kompetisi">
                        <label for="openreg">Open Registrasi</label>
                        <input type="date" id="openreg" name="openreg" placeholder="Open Registrasi">
                        <label for="closereg">Close Registrasi</label>
                        <input type="date" id="closereg" name="closereg" placeholder="Close Registrasi">
                        <label for="lokasi">Lokasi</label>
                        <input type="text" id="lokasi" name="lokasi" placeholder="Lokasi">
                        <label for="deskripsi">Deskripsi</label>
                        <input type="text" id="deskripsi" name="deskripsi" placeholder="Deskripsi">
                        <div class="flex center">   
                            <button type="submit" class="w100">Tambah</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>

        {{-- DIVIDER --}}

        <div class="tambah-acara card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Tambah Acara</h2>
                </header>
                <section>
                    <form class="tambah-container" method="POST" action="">
                        @csrf

                        <label for="namaaca">Nama Acara</label>
                        <input type="text" id="namaaca" name="namaaca" placeholder="Nama Acara">
                        <label for="harga">Harga</label>
                        <input type="number" id="harga" name="harga" placeholder="Harga">
                        <label for="kuota">Kuota</label>
                        <input type="number" id="kuota" name="kuota" placeholder="Kuota">
                        <label for="nogrup">Nomor Grup</label>
                        <input type="text" id="nogrup" name="nogrup" placeholder="Nomor Grup">
                        <label for="minumur">Minimal Umur</label>
                        <input type="number" id="minumur" name="minumur" placeholder="Minimal Umur">
                        <label for="maxumur">Maximal Umur</label>
                        <input type="number" id="maxumur" name="maxumur" placeholder="Maximal Umur">
                        <div class="flex center">   
                            <button type="submit" class="w100">Tambah</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>

    {{-- DIVIDER --}}

    <div class="admin-container">
        <div class="download card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Download</h2>
                </header>
                <section>
                    <div>
                        <h4 style="margin-bottom: 10px">EXCEL</h4>
                        <button class="button-blue"><i class='bx bx-download'></i></button>
                        <button class="button-green"><i class='bx bxs-show'></i></button>
                    </div>
                </section>
            </div>
        </div>
        <div class="upload card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Upload</h2>
                </header>
                <section>
                    <div>
                        <h4 style="margin-bottom: 10px">PDF</h4>
                        <button class="button-blue"><i class='bx bx-upload'></i></button>
                    </div>
                </section>
            </div>
        </div>
    </div>
    
    {{-- DIVIDER --}}

    <div class="admin-container">
        <div class="card100">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>idk</h2>
                </header>
                <section>
                    <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Impedit, quas ex vero soluta earum, dolores facilis numquam aliquid quidem aut est pariatur eius nulla ducimus quae voluptas nesciunt totam sequi.
                </section>
            </div>
        </div>
    </div>
</div>
@endsection