<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container">
        <header class="flex divider">
            <h2>Tambah Atlet</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form class="atlet">
                <label for="nama">Nama Atlet</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Atlet">
                <label for="umur">Umur</label>
                <input type="number" id="umur" name="umur" placeholder="Umur">
                <label for="jenisKelamin">Jenis Kelamin</label>
                <select id="jenisKelamin" name="jenisKelamin">
                    <option value="pria">Pria</option>
                    <option value="wanita">Wanita</option>
                </select>
                {{-- pake apa ini?, konek ke database gmn --}}
                <div class="flex center">
                    <button class="w50">Kirim</button>
                </div>
            </form>
        </section>
    </div>
</div>