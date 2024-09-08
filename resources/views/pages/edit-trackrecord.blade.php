@extends('layouts.dashboard-layout')
@section('title', 'Edit Track Record')
@section('content')
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
                    <h1>Edit {{ $record->kompetisi}}</h1>
                </header>
                <div>
                    <form class="edit-atlet" method="POST" action="{{ route('dashboard.track-record.update', $record->id) }}">
                        @csrf
                        @method('put')

                        <label for="kompetisi">Kompetisi *</label>
                        <input type="text" id="kompetisi" name="kompetisi" placeholder="Nama Kompetisi" value="{{ $record->kompetisi }}">

                        <label for="kategori">Nomor Lomba *</label>
                        <select name="kategori" id="kategori">
                            <option value="50m Gaya Bebas" {{ $record->nomor_lomba === "50m Gaya Bebas" ? "selected" : "" }}>50m Gaya Bebas</option>
                            <option value="100m Gaya Bebas" {{ $record->nomor_lomba === "100m Gaya Bebas" ? "selected" : "" }}>100m Gaya Bebas</option>
                            <option value="200m Gaya Bebas" {{ $record->nomor_lomba === "200m Gaya Bebas" ? "selected" : "" }}>200m Gaya Bebas</option>
                            <option value="400m Gaya Bebas" {{ $record->nomor_lomba === "400m Gaya Bebas" ? "selected" : "" }}>400m Gaya Bebas</option>
                            <option value="800m Gaya Bebas" {{ $record->nomor_lomba === "800m Gaya Bebas" ? "selected" : "" }}>800m Gaya Bebas</option>
                            <option value="1500m Gaya Bebas" {{ $record->nomor_lomba === "1500m Gaya Bebas" ? "selected" : "" }}>1500m Gaya Bebas</option>
                            <option value="50m Gaya Kupu-Kupu" {{ $record->nomor_lomba === "50m Gaya Kupu-Kupu" ? "selected" : "" }}>50m Gaya Kupu-Kupu</option>
                            <option value="100m Gaya Kupu-Kupu" {{ $record->nomor_lomba === "100m Gaya Kupu-Kupu" ? "selected" : "" }}>100m Gaya Kupu-Kupu</option>
                            <option value="200m Gaya Kupu-Kupu" {{ $record->nomor_lomba === "200m Gaya Kupu-Kupu" ? "selected" : "" }}>200m Gaya Kupu-Kupu</option>
                            <option value="50m Gaya Punggung" {{ $record->nomor_lomba === "50m Gaya Punggung" ? "selected" : "" }}>50m Gaya Punggung</option>
                            <option value="100m Gaya Punggung" {{ $record->nomor_lomba === "100m Gaya Punggung" ? "selected" : "" }}>100m Gaya Punggung</option>
                            <option value="200m Gaya Punggung" {{ $record->nomor_lomba === "200m Gaya Punggung" ? "selected" : "" }}>200m Gaya Punggung</option>
                            <option value="50m Gaya Dada" {{ $record->nomor_lomba === "50m Gaya Dada" ? "selected" : "" }}>50m Gaya Dada</option>
                            <option value="100m Gaya Dada" {{ $record->nomor_lomba === "100m Gaya Dada" ? "selected" : "" }}>100m Gaya Dada</option>
                            <option value="200m Gaya Dada" {{ $record->nomor_lomba === "200m Gaya Dada" ? "selected" : "" }}>200m Gaya Dada</option>
                            <option value="200m Gaya Ganti" {{ $record->nomor_lomba === "200m Gaya Ganti" ? "selected" : "" }}>200m Gaya Ganti</option>
                            <option value="400m Gaya Ganti" {{ $record->nomor_lomba === "400m Gaya Ganti" ? "selected" : "" }}>400m Gaya Ganti</option>
                        </select>

                        <label for="record">Durasi Renang *</label>
                        <p><i style="font-size: 12px">(Tulis 0 jika tidak ada)</i></p>
                            <div class="record">
                                <input value="{{ floor($record->time / 60) }}"  type="number" id="record_minute" name="record_minute" placeholder="Menit" min="0" step="1" style="width: 30%;">
                                <input value="{{ floor(fmod($record->time, 60)) }}" type="number" id="record_second" name="record_second" placeholder="Detik" min="0" max="59" step="1" style="width: 30%;">
                                <input value="{{ ceil(($record->time - floor($record->time)) * 100) }}" type="number" id="record_millisecond" name="record_millisecond" placeholder="Milidetik" min="0" max="99" step="1" style="width: 30%;">
                            </div>

                        <input type="hidden" name="atlet_id" value="{{ $record->atlet->id }}">
                        
                        <div class="flex center" style="margin-top:20px">   
                            <button type="submit" class="submit-button">Simpan</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection