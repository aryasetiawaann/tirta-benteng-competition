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
    <div class="all-container all-card w100">
        <header class="flex divider">
            <h2>Edit {{$acara->nama}}</h2>
        </header>
        <section>
            <form class="edit-container" method="POST" action="{{ route('dashboard.admin.updateacara', $acara->id) }}">
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

                <label for="jenis_lomba">Jenis Lomba *</label>
                <select name="jenis_lomba" id="jenis_lomba">
                    <option value="25m papan gaya bebas" {{ $acara->jenis_lomba === "25m papan gaya bebas" ? "selected" : "" }}>25m Papan Gaya Bebas</option>
                    <option value="25m gaya bebas" {{ $acara->jenis_lomba === "25m gaya bebas" ? "selected" : "" }}>25m Gaya Bebas</option>
                    <option value="25m fins gaya bebas" {{ $acara->jenis_lomba === "25m fins gaya bebas" ? "selected" : "" }}>25m Fins Gaya Bebas</option>
                    <option value="50m gaya bebas" {{ $acara->jenis_lomba === "50m gaya bebas" ? "selected" : "" }}>50m Gaya Bebas</option>
                    <option value="50m fins gaya bebas" {{ $acara->jenis_lomba === "50m fins gaya bebas" ? "selected" : "" }}>50m Fins Gaya Bebas</option>
                    <option value="100m gaya bebas" {{ $acara->jenis_lomba === "100m gaya bebas" ? "selected" : "" }}>100m Gaya Bebas</option>
                    <option value="200m gaya bebas" {{ $acara->jenis_lomba === "200m gaya bebas" ? "selected" : "" }}>200m Gaya Bebas</option>
                    <option value="400m gaya bebas" {{ $acara->jenis_lomba === "400m gaya bebas" ? "selected" : "" }}>400m Gaya Bebas</option>
                    <option value="800m gaya bebas" {{ $acara->jenis_lomba === "800m gaya bebas" ? "selected" : "" }}>800m Gaya Bebas</option>
                    <option value="1500m gaya bebas" {{ $acara->jenis_lomba === "1500m gaya bebas" ? "selected" : "" }}>1500m Gaya Bebas</option>
                    <option value="25m fins gaya kupu-kupu" {{ $acara->jenis_lomba === "25m fins gaya kupu-kupu" ? "selected" : "" }}>25m Fins Gaya Kupu-Kupu</option>
                    <option value="50m fins gaya kupu-kupu" {{ $acara->jenis_lomba === "50m fins gaya kupu-kupu" ? "selected" : "" }}>50m Fins Gaya Kupu-Kupu</option>
                    <option value="25m gaya kupu-kupu" {{ $acara->jenis_lomba === "25m gaya kupu-kupu" ? "selected" : "" }}>25m Gaya Kupu-Kupu</option>
                    <option value="50m gaya kupu-kupu" {{ $acara->jenis_lomba === "50m gaya kupu-kupu" ? "selected" : "" }}>50m Gaya Kupu-Kupu</option>
                    <option value="100m gaya kupu-kupu" {{ $acara->jenis_lomba === "100m gaya kupu-kupu" ? "selected" : "" }}>100m Gaya Kupu-Kupu</option>
                    <option value="200m gaya kupu-kupu" {{ $acara->jenis_lomba === "200m gaya kupu-kupu" ? "selected" : "" }}>200m Gaya Kupu-Kupu</option>
                    <option value="25m gaya punggung" {{ $acara->jenis_lomba === "25m gaya punggung" ? "selected" : "" }}>25m Gaya Punggung</option>
                    <option value="50m gaya punggung" {{ $acara->jenis_lomba === "50m gaya punggung" ? "selected" : "" }}>50m Gaya Punggung</option>
                    <option value="100m gaya punggung" {{ $acara->jenis_lomba === "100m gaya punggung" ? "selected" : "" }}>100m Gaya Punggung</option>
                    <option value="200m gaya punggung" {{ $acara->jenis_lomba === "200m gaya punggung" ? "selected" : "" }}>200m Gaya Punggung</option>
                    <option value="25m gaya dada" {{ $acara->jenis_lomba === "25m gaya dada" ? "selected" : "" }}>25m Gaya Dada</option>
                    <option value="50m gaya dada" {{ $acara->jenis_lomba === "50m gaya dada" ? "selected" : "" }}>50m Gaya Dada</option>
                    <option value="100m gaya dada" {{ $acara->jenis_lomba === "100m gaya dada" ? "selected" : "" }}>100m Gaya Dada</option>
                    <option value="200m gaya dada" {{ $acara->jenis_lomba === "200m gaya dada" ? "selected" : "" }}>200m Gaya Dada</option>
                    <option value="200m gaya ganti" {{ $acara->jenis_lomba === "200m gaya ganti" ? "selected" : "" }}>200m Gaya Ganti</option>
                    <option value="400m gaya ganti" {{ $acara->jenis_lomba === "400m gaya ganti" ? "selected" : "" }}>400m Gaya Ganti</option>
                    <option value="50m snorkling bifin" {{ $acara->jenis_lomba === "50m snorkling bifin" ? "selected" : "" }}>50m Snorkling Bifin</option>
                    <option value="50m bifin board" {{ $acara->jenis_lomba === "50m bifin board" ? "selected" : "" }}>50m Bifin Board</option>
                    <option value="50m bifin" {{ $acara->jenis_lomba === "50m bifin" ? "selected" : "" }}>50m Bifin</option>
                    <option value="100m bifin" {{ $acara->jenis_lomba === "100m bifin" ? "selected" : "" }}>100m Bifin</option>
                    <option value="200m bifin" {{ $acara->jenis_lomba === "200m bifin" ? "selected" : "" }}>200m Bifin</option>
                    <option value="400m bifin" {{ $acara->jenis_lomba === "400m bifin" ? "selected" : "" }}>400m Bifin</option>
                    <option value="50m surface board" {{ $acara->jenis_lomba === "50m surface board" ? "selected" : "" }}>50m Surface Board</option>
                    <option value="50m surface" {{ $acara->jenis_lomba === "50m surface" ? "selected" : "" }}>50m Surface</option>
                    <option value="100m surface" {{ $acara->jenis_lomba === "100m surface" ? "selected" : "" }}>100m Surface</option>
                    <option value="200m surface" {{ $acara->jenis_lomba === "200m surface" ? "selected" : "" }}>200m Surface</option>
                    <option value="400m surface" {{ $acara->jenis_lomba === "400m surface" ? "selected" : "" }}>400m Surface</option>
                    <option value="800m surface" {{ $acara->jenis_lomba === "800m surface" ? "selected" : "" }}>800m Surface</option>
                    <option value="50m apnea" {{ $acara->jenis_lomba === "50m apnea" ? "selected" : "" }}>50m Apnea</option>
                </select>

                <label for="harga">Harga*</label>
                <p><i style="font-size: 12px">(tanpa titik, contoh: 20.000 menjadi 20000)</i></p>
                <input type="number" id="harga" name="harga" placeholder="Harga" value="{{ $acara->harga }}">

                <label for="kuota">Kuota Peserta*</label>
                <input type="number" id="kuota" name="kuota" placeholder="Kuota peserta" value="{{ $acara->kuota }}">

                <label for="grup">Kelompok Umur*</label>
                <input type="text" id="grup" name="grup" placeholder="Grup" value="{{ $acara->grup }}">
                <label for="minumur">Min. Tahun*</label>
                <input type="number" id="minumur" name="minumur" placeholder="Minimal tahun lahir peserta" value="{{ $acara->min_umur }}">
                <label for="maxumur">Max. Tahun (opsional)</label>
                <input type="number" id="maxumur" name="maxumur" placeholder="Maximal tahun lahir peserta" value="{{ $acara->max_umur }}">
                <input type="hidden" name="kompe_id" value="{{ $acara->kompetisi->id }}">
                <input type="hidden" name="id" value="{{ $acara->id }}">

                <div class="flex center">   
                    <button type="submit" class="submit-button">Simpan</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection