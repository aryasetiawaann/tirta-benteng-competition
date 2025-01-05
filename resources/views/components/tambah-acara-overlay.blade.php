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

                <label for="jenis_lomba">Jenis Lomba *</label>
                <select name="jenis_lomba" id="jenis_lomba">
                    <option value="tidak ada">Tidak Ada</option>
                    <option value="25m papan gaya bebas">25m Papan Gaya Bebas</option>
                    <option value="25m fins gaya bebas">25m Fins Gaya Bebas</option>
                    <option value="25m gaya bebas">25m Gaya Bebas</option>
                    <option value="50m gaya bebas">50m Gaya Bebas</option>
                    <option value="100m gaya bebas">100m Gaya Bebas</option>
                    <option value="200m gaya bebas">200m Gaya Bebas</option>
                    <option value="400m gaya bebas">400m Gaya Bebas</option>
                    <option value="800m gaya bebas">800m Gaya Bebas</option>
                    <option value="1500m gaya bebas">1500m Gaya Bebas</option>
                    <option value="25m fins gaya kupu-kupu">25m Fins Gaya Kupu-Kupu</option>
                    <option value="25m gaya kupu-kupu">25m Gaya Kupu-Kupu</option>
                    <option value="50m gaya kupu-kupu">50m Gaya Kupu-Kupu</option>
                    <option value="100m gaya kupu-kupu">100m Gaya Kupu-Kupu</option>
                    <option value="200m gaya kupu-kupu">200m Gaya Kupu-Kupu</option>
                    <option value="25m gaya punggung">25m Gaya Punggung</option>
                    <option value="50m gaya punggung">50m Gaya Punggung</option>
                    <option value="100m gaya punggung">100m Gaya Punggung</option>
                    <option value="200m gaya punggung">200m Gaya Punggung</option>
                    <option value="25m gaya dada">25m Gaya Dada</option>
                    <option value="50m gaya dada">50m Gaya Dada</option>
                    <option value="100m gaya dada">100m Gaya Dada</option>
                    <option value="200m gaya dada">200m Gaya Dada</option>
                    <option value="200m gaya ganti">200m Gaya Ganti</option>
                    <option value="400m gaya ganti">400m Gaya Ganti</option>
                </select>

                <label for="harga">Harga*</label>
                <p><i style="font-size: 12px">(tanpa titik, contoh: 20.000 menjadi 20000)</i></p>
                <input type="number" id="harga" name="harga" placeholder="Harga">

                <label for="kuota">Kuota Peserta*</label>
                <input type="number" id="kuota" name="kuota" placeholder="Kuota Peserta">

                <label for="grup">Kelompok Umur*</label>
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