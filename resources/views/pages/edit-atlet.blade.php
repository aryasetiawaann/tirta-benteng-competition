@extends('layouts.dashboard-layout')
@section('title', 'Atlet Saya')
@section('content')
@include('components.daftar-atlet-overlay')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon">
                        <i class='bx bxs-user' ></i>
                    </div>
                    <div class="card-content">
                        <h1>Edit Atlet</h1>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Atlet Saya</li>
                <li><a href="{{ route('dashboard.atlet.index') }}">Daftar Atlet</a></li>
                <li><a href="{{ route('dashboard.atlet.edit', $atlet->id) }}">Edit - {{ $atlet->name }}</a></li>
            </ul>
        </nav>

        @if ($errors->any())
        <x-error-list>
            @foreach ($errors->all() as $error)
                <x-error-item>{{ $error }}</x-error-item>
            @endforeach
        </x-error-list>
        @endif
        @if (session('success'))
            <x-success-list>
                <x-success-item>{{ session('success') }}</x-success-item>
            </x-success-list>
        @endif

        <div class="bottom-container center w768">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Edit {{ $atlet->name}}</h1>
                </header>
                <div>
                    <form class="edit-atlet" method="POST" action="{{ route('dashboard.atlet.update', $atlet->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <label for="nama">Nama Atlet *</label>
                        <input type="text" id="nama" name="nama" placeholder="Nama Atlet" value="{{ $atlet->name }}">

                        <label for="nik">NIK *</label>
                        <input type="text" id="nik" name="nik" placeholder="Masukkan NIK" value="{{ $atlet->nik ?? '' }}" maxlength="16" required>

                        <label>Keterangan Daerah *</label>
                        <select id="edit_provinsi" name="provinsi" required>
                            <option value="" disabled selected>Pilih Provinsi</option>
                        </select>
                        <div class="region-group">
                            <select id="edit_kota" name="kota" required disabled>
                                <option value="" disabled selected>Pilih Kota/Kabupaten</option>
                            </select>
                            <select id="edit_kecamatan" name="kecamatan" required disabled>
                                <option value="" disabled selected>Pilih Kecamatan</option>
                            </select>
                        </div>
                        <p style="margin-bottom: 20px; color: #666;"><i style="font-size: 12px">Data keterangan daerah mengikuti base klub atlet.</i></p>

                        <label for="umur">Umur *</label>
                        <input type="date" id="umur" name="umur" placeholder="Umur" value="{{ $atlet->umur }}">
                        <label for="jenisKelamin">Jenis Kelamin *</label>
                        <select id="jenisKelamin" name="jenisKelamin">
                            <option value="pria" {{ $atlet->jenis_kelamin === "Pria" ? "selected" : "" }}>Pria</option>
                            <option value="wanita" {{ $atlet->jenis_kelamin === "Wanita" ? "selected" : "" }}>Wanita</option>
                        </select>
                        <label for="">Upload Dokumen</label>
                        <p><i style="font-size: 12px">(Akte / KTP .pdf, .jpg, .png)</i></p>
                        <input type="file" name="dokumen" id="dokumen" accept=".pdf,.jpg,.jpeg,.png" value="{{ $atlet->dokumen }}">
                            
                        {{-- <label for="record">Track Record</label>
                        <p><i style="font-size: 12px">(Tulis 0 Jika tidak ada)</i></p>
                            <div class="record">
                                <input type="number" id="record_minute" name="record_minute" placeholder="Menit" min="0" step="1" value="{{ floor($atlet->track_record / 60) }}" style="width: 30%;">
                                <input type="number" id="record_second" name="record_second" placeholder="Detik" min="0" max="59" step="1" value="{{ floor(fmod($atlet->track_record, 60)) }}" style="width: 30%;">
                                <input type="number" id="record_millisecond" name="record_millisecond" placeholder="Milidetik" min="0" max="99" step="1" value="{{ intval(($atlet->track_record - floor($atlet->track_record)) * 100) }}" style="width: 30%;">
                            </div> --}}
                        
                        <input type="hidden" name="atlet_id" value="{{ $atlet->id }}">

                        <div class="flex center" style="margin-top:20px">   
                            <button type="submit" class="submit-button">Simpan</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('edit_provinsi');
    const regencySelect = document.getElementById('edit_kota');
    const districtSelect = document.getElementById('edit_kecamatan');
    
    const oldProvinsi = "{{ old('provinsi', $atlet->province ?? '') }}";
    const oldKota = "{{ old('kota', $atlet->regency ?? '') }}";
    const oldKecamatan = "{{ old('kecamatan', $atlet->district ?? '') }}";

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
@endsection