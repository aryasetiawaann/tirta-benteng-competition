<div id="overlay" class="overlay">
    <div class="all-container all-card overlay-container w100">
        <header class="flex divider">
            <h2>Tambah Atlet</h2>
            <span id="closeOverlay" class="bx bx-md bx-x"></span>
        </header>
        <section>
            <form class="atlet" method="POST" action="{{ route('dashboard.atlet.store') }}" enctype="multipart/form-data">
                @csrf

                <label for="nama">Nama Atlet *</label>
                <input type="text" id="nama" name="nama" placeholder="Nama Atlet">
                
                <label for="nik">NIK *</label>
                <input type="text" id="nik" name="nik" placeholder="Masukkan NIK" maxlength="16">

                <label>Keterangan Daerah *</label>
                <select id="provinsi" name="provinsi">
                    <option value="" disabled selected>Pilih Provinsi</option>
                    <option value="DKI Jakarta">DKI Jakarta</option>
                    <option value="Jawa Barat">Jawa Barat</option>
                    <option value="Jawa Tengah">Jawa Tengah</option>
                    <option value="DI Yogyakarta">DI Yogyakarta</option>
                    <option value="Jawa Timur">Jawa Timur</option>
                </select>
                <div class="region-group">
                    <select id="kota" name="kota">
                        <option value="" disabled selected>Pilih Kota/Kabupaten</option>
                        <option value="Kota Bandung">Kota Bandung</option>
                        <option value="Kota Semarang">Kota Semarang</option>
                        <option value="Kota Surabaya">Kota Surabaya</option>
                    </select>
                    <select id="kecamatan" name="kecamatan">
                        <option value="" disabled selected>Pilih Kecamatan</option>
                        <option value="Kecamatan A">Kecamatan A</option>
                        <option value="Kecamatan B">Kecamatan B</option>
                    </select>
                </div>
                <p style="margin-bottom: 20px; color: #666;"><i style="font-size: 12px">Data keterangan daerah mengikuti base klub atlet.</i></p>
                <label for="umur">Tanggal lahir *</label>
                <input type="date" id="umur" name="umur" placeholder="Umur">
                <label for="jenisKelamin">Jenis Kelamin *</label>
                <select id="jenisKelamin" name="jenisKelamin">
                    <option value="pria">Pria</option>
                    <option value="wanita">Wanita</option>
                </select>
                <label for="dokumen">Dokumen</label>
                <p><i style="font-size: 12px">(Akte / KTP .pdf, .jpg, .png)</i></p>
                <input type="file" name="dokumen" id="dokumen" accept=".pdf,.jpg,.jpeg,.png">
                <div class="flex center" style="margin-top: 20px;">
                    <button type="submit" class="submit-button">Kirim</button>
                </div>
            </form>
        </section>
    </div>
</div>