<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container w100">
        <header class="flex divider">
            <h2>Tambah Atlet</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form class="atlet" method="POST" action="{{ route('dashboard.atlet.store') }}">
                @csrf

                <label for="nama">Nama Atlet</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Atlet">
                <label for="umur">Umur</label>
                <input type="date" id="umur" name="umur" placeholder="Umur">
                <label for="jenisKelamin">Jenis Kelamin</label>
                <select id="jenisKelamin" name="jenisKelamin">
                    <option value="pria">Pria</option>
                    <option value="wanita">Wanita</option>
                </select>
                <label for="record">Track Record</label>
                <input type="number" id="record" name="record" placeholder="contoh: 3,25 (Menit)" step="0.01">
                <div class="flex center">   
                    <button type="submit" class="w50">Kirim</button>
                </div>
            </form>
        </section>
    </div>
</div>