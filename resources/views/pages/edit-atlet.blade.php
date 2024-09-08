@extends('layouts.dashboard-layout')
@section('title', 'Atlet Saya')
@section('content')
@include('components.daftar-atlet-overlay')
    <div class="main-content">
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
                        <label for="umur">Umur *</label>
                        <input type="date" id="umur" name="umur" placeholder="Umur" value="{{ $atlet->umur }}">
                        <label for="jenisKelamin">Jenis Kelamin *</label>
                        <select id="jenisKelamin" name="jenisKelamin">
                            <option value="pria" {{ $atlet->jenis_kelamin === "Pria" ? "selected" : "" }}>Pria</option>
                            <option value="wanita" {{ $atlet->jenis_kelamin === "Wanita" ? "selected" : "" }}>Wanita</option>
                        </select>
                        {{-- <label for="record">Track Record</label>
                        <p><i style="font-size: 12px">(Tulis 0 Jika tidak ada)</i></p>
                            <div class="record">
                                <input type="number" id="record_minute" name="record_minute" placeholder="Menit" min="0" step="1" value="{{ floor($atlet->track_record / 60) }}" style="width: 30%;">
                                <input type="number" id="record_second" name="record_second" placeholder="Detik" min="0" max="59" step="1" value="{{ floor(fmod($atlet->track_record, 60)) }}" style="width: 30%;">
                                <input type="number" id="record_millisecond" name="record_millisecond" placeholder="Milidetik" min="0" max="99" step="1" value="{{ intval(($atlet->track_record - floor($atlet->track_record)) * 100) }}" style="width: 30%;">
                            </div> --}}
                        <label for="">Upload Dokumen *</label>
                        <p><i style="font-size: 12px">(Akte / KTP *.pdf)</i></p>
                        <input type="file" name="dokumen" id="dokumen" accept=".pdf" value="{{ $atlet->dokumen }}">
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