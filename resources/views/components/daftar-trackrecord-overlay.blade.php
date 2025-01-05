<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container w100">
        <header class="flex divider">
            <h2>Tambah Track Record</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form class="atlet" method="POST" action="{{ route('dashboard.track-record.create') }}">
                @csrf

                <label for="kompetisi">Kompetisi *</label>
                <select name="kompetisi" id="kompetisi" onchange="toggleLainnyaInput()">
                    <option value="" disabled selected>Pilih Kompetisi</option>
                    @foreach ($competitions as $competition)
                    <option value="{{ $competition->nama }}">{{ $competition->nama }}</option>
                    @endforeach
                    <option value="lainnya">Lainnya</option>
                </select>

                <div id="lainnyaInput" style="display: none; margin-top: 3px;">
                    <label for="kompetisi_lainnya" style="margin-right: 60%">Nama Kompetisi</label>
                    <input type="text" name="kompetisi_lainnya" id="kompetisi_lainnya" placeholder="Nama Kompetisi">
                </div>

                <label for="kategori">Nomor Lomba *</label>
                <select name="kategori" id="kategori">
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

<script>
    function toggleLainnyaInput() {
        const kompetisiSelect = document.getElementById('kompetisi');
        const lainnyaInput = document.getElementById('lainnyaInput');

        // Tampilkan input jika opsi 'Lainnya' dipilih
        if (kompetisiSelect.value === 'lainnya') {
            lainnyaInput.style.display = 'block';
        } else {
            lainnyaInput.style.display = 'none';
        }
    }
</script>