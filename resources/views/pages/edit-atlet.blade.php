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
                        <input type="text" id="nik" name="nik" placeholder="Masukkan NIK" value="{{ $atlet->nik ?? '' }}" maxlength="16">

                        <label>Keterangan Daerah *</label>
                        <select id="provinsi" name="provinsi">
                            <option value="" disabled {{ empty($atlet->provinsi) ? 'selected' : '' }}>Pilih Provinsi</option>
                            <option value="DKI Jakarta" {{ ($atlet->provinsi ?? '') == 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
                            <option value="Jawa Barat" {{ ($atlet->provinsi ?? '') == 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
                            <option value="Jawa Tengah" {{ ($atlet->provinsi ?? '') == 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
                            <option value="DI Yogyakarta" {{ ($atlet->provinsi ?? '') == 'DI Yogyakarta' ? 'selected' : '' }}>DI Yogyakarta</option>
                            <option value="Jawa Timur" {{ ($atlet->provinsi ?? '') == 'Jawa Timur' ? 'selected' : '' }}>Jawa Timur</option>
                        </select>
                        <div class="region-group">
                            <select id="kota" name="kota">
                                <option value="" disabled {{ empty($atlet->kota) ? 'selected' : '' }}>Pilih Kota/Kabupaten</option>
                                <option value="Kota Bandung" {{ ($atlet->kota ?? '') == 'Kota Bandung' ? 'selected' : '' }}>Kota Bandung</option>
                                <option value="Kota Semarang" {{ ($atlet->kota ?? '') == 'Kota Semarang' ? 'selected' : '' }}>Kota Semarang</option>
                                <option value="Kota Surabaya" {{ ($atlet->kota ?? '') == 'Kota Surabaya' ? 'selected' : '' }}>Kota Surabaya</option>
                            </select>
                            <select id="kecamatan" name="kecamatan">
                                <option value="" disabled {{ empty($atlet->kecamatan) ? 'selected' : '' }}>Pilih Kecamatan</option>
                                <option value="Kecamatan A" {{ ($atlet->kecamatan ?? '') == 'Kecamatan A' ? 'selected' : '' }}>Kecamatan A</option>
                                <option value="Kecamatan B" {{ ($atlet->kecamatan ?? '') == 'Kecamatan B' ? 'selected' : '' }}>Kecamatan B</option>
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
@endsection