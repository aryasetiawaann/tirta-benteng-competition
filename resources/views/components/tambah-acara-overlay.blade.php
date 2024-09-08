<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container w100">
        <header class="flex divider">
            <h2>Tambah Acara</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form class="atlet" method="POST" action="{{ route('dashboard.admin.tambahacara') }}">
                @csrf

                <label for="nomor">Nomor Acara*</label>
                <input type="number" id="nomor" name="nomor" placeholder="Nomor Acara">
                <label for="nama">Nama Acara*</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Acara">
                <label for="kategori">Kategori*</label>
                <select id="kategori" name="kategori">
                    <option value="campuran">Campuran</option>
                    <option value="pria">Pria</option>
                    <option value="wanita">Wanita</option>
                </select>
                <label for="harga">Harga*</label>
                <p><i style="font-size: 12px">(tanpa titik, contoh: 20.000 menjadi 20000)</i></p>
                <input type="number" id="harga" name="harga" placeholder="Harga">
                <label for="kuota">Kuota Peserta*</label>
                <input type="number" id="kuota" name="kuota" placeholder="Kuota Peserta">
                <label for="grup">Grup*</label>
                <input type="text" id="grup" name="grup" placeholder="Grup">
                <label for="minumur">Min. Umur Peserta*</label>
                <input type="number" id="minumur" name="minumur" placeholder="Minimal umur peserta">
                <label for="maxumur">Max. Umur Peserta*</label>
                <input type="number" id="maxumur" name="maxumur" placeholder="Maksimal umur peserta">
                <input type="hidden" name="kompe_id" value="{{ $id_kompetisi }}">

                <div class="flex center">   
                    <button type="submit" class="submit-button">Simpan</button>
                </div>
            </form>
        </section>
    </div>
</div>