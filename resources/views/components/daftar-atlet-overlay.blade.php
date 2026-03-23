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
                <select id="provinsi" name="provinsi" required>
                    <option value="" disabled selected>Pilih Provinsi</option>
                </select>
                <div class="region-group">
                    <select id="kota" name="kota" required disabled>
                        <option value="" disabled selected>Pilih Kota/Kabupaten</option>
                    </select>
                    <select id="kecamatan" name="kecamatan" required disabled>
                        <option value="" disabled selected>Pilih Kecamatan</option>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('provinsi');
    const regencySelect = document.getElementById('kota');
    const districtSelect = document.getElementById('kecamatan');
    
    const oldProvinsi = "{{ old('provinsi') }}";
    const oldKota = "{{ old('kota') }}";
    const oldKecamatan = "{{ old('kecamatan') }}";

    function fetchRegencies(provinceCode, callback) {
        if(!provinceCode) return;
        regencySelect.innerHTML = '<option value="" disabled selected>Memuat...</option>';
        regencySelect.disabled = true;
        fetch(`/api/regencies/${provinceCode}`)
            .then(res => res.json())
            .then(result => {
                regencySelect.innerHTML = '<option value="" disabled selected>Pilih Kota/Kabupaten</option>';
                result.data.forEach(reg => {
                    const opt = document.createElement('option');
                    opt.value = reg.name;
                    opt.dataset.code = reg.code;
                    opt.textContent = reg.name;
                    if(reg.name === oldKota) opt.selected = true;
                    regencySelect.appendChild(opt);
                });
                regencySelect.disabled = false;
                if(callback) callback();
            });
    }

    function fetchDistricts(regencyCode) {
        if(!regencyCode) return;
        districtSelect.innerHTML = '<option value="" disabled selected>Memuat...</option>';
        districtSelect.disabled = true;
        fetch(`/api/districts/${regencyCode}`)
            .then(res => res.json())
            .then(result => {
                districtSelect.innerHTML = '<option value="" disabled selected>Pilih Kecamatan</option>';
                result.data.forEach(dist => {
                    const opt = document.createElement('option');
                    opt.value = dist.name;
                    opt.dataset.code = dist.code;
                    opt.textContent = dist.name;
                    if(dist.name === oldKecamatan) opt.selected = true;
                    districtSelect.appendChild(opt);
                });
                districtSelect.disabled = false;
            });
    }

    provinceSelect.innerHTML = '<option value="" disabled selected>Memuat...</option>';
    provinceSelect.disabled = true;
    fetch('/api/provinces')
        .then(response => response.json())
        .then(result => {
            provinceSelect.innerHTML = '<option value="" disabled selected>Pilih Provinsi</option>';
            result.data.forEach(prov => {
                const opt = document.createElement('option');
                opt.value = prov.name;
                opt.dataset.code = prov.code;
                opt.textContent = prov.name;
                if(prov.name === oldProvinsi) opt.selected = true;
                provinceSelect.appendChild(opt);
            });
            provinceSelect.disabled = false;
            
            if(oldProvinsi) {
                const selectedOpt = Array.from(provinceSelect.options).find(o => o.value === oldProvinsi);
                if(selectedOpt) fetchRegencies(selectedOpt.dataset.code, () => {
                    if(oldKota) {
                        const selectedReg = Array.from(regencySelect.options).find(o => o.value === oldKota);
                        if(selectedReg) fetchDistricts(selectedReg.dataset.code);
                    }
                });
            }
        });

    provinceSelect.addEventListener('change', function() {
        districtSelect.innerHTML = '<option value="" disabled selected>Pilih Kecamatan</option>';
        districtSelect.disabled = true;
        const selectedOpt = this.options[this.selectedIndex];
        fetchRegencies(selectedOpt.dataset.code);
    });

    regencySelect.addEventListener('change', function() {
        const selectedOpt = this.options[this.selectedIndex];
        fetchDistricts(selectedOpt.dataset.code);
    });
});
</script>