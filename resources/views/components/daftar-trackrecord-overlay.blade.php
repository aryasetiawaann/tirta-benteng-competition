<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container w100">
        <header class="flex divider">
            <h2>Tambah Atlet</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form class="atlet" method="POST" action="{{ route('dashboard.track-record.create') }}">
                @csrf

                <label for="kompetisi">Kompetisi *</label>
                <input type="text" id="kompetisi" name="kompetisi" placeholder="Nama Kompetisi">

                <label for="kategori">Nomor Lomba *</label>
                <select name="kategori" id="kategori">
                    <option value="50m Gaya Bebas">50m Gaya Bebas</option>
                    <option value="100m Gaya Bebas">100m Gaya Bebas</option>
                    <option value="200m Gaya Bebas">200m Gaya Bebas</option>
                    <option value="400m Gaya Bebas">400m Gaya Bebas</option>
                    <option value="800m Gaya Bebas">800m Gaya Bebas</option>
                    <option value="1500m Gaya Bebas">1500m Gaya Bebas</option>
                    <option value="50m Gaya Kupu-Kupu">50m Gaya Kupu-Kupu</option>
                    <option value="100m Gaya Kupu-Kupu">100m Gaya Kupu-Kupu</option>
                    <option value="200m Gaya Kupu-Kupu">200m Gaya Kupu-Kupu</option>
                    <option value="50m Gaya Punggung">50m Gaya Punggung</option>
                    <option value="100m Gaya Punggung">100m Gaya Punggung</option>
                    <option value="200m Gaya Punggung">200m Gaya Punggung</option>
                    <option value="50m Gaya Dada">50m Gaya Dada</option>
                    <option value="100m Gaya Dada">100m Gaya Dada</option>
                    <option value="200m Gaya Dada">200m Gaya Dada</option>
                    <option value="200m Gaya Ganti">200m Gaya Ganti</option>
                    <option value="400m Gaya Ganti">400m Gaya Ganti</option>
                </select>

                <label for="record">Durasi Renang *</label>
                <p><i style="font-size: 12px">(Tulis 0 jika tidak ada)</i></p>
                    <div class="record">
                        <input type="number" id="record_minute" name="record_minute" placeholder="Menit" min="0" step="1" style="width: 30%;">
                        <input type="number" id="record_second" name="record_second" placeholder="Detik" min="0" max="59" step="1" style="width: 30%;">
                        <input type="number" id="record_millisecond" name="record_millisecond" placeholder="Milidetik" min="0" max="99" step="1" style="width: 30%;">
                    </div>

                <input type="hidden" name="atlet_id" value="{{ $atlet->id }}">

                <div class="flex center" style="margin-top: 20px;">   
                    <button type="submit" class="submit-button">Kirim</button>
                </div>
            </form>
        </section>
    </div>
</div>