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
                            <option value="25m papan gaya bebas" {{ $record->nomor_lomba === "25m papan gaya bebas" ? "selected" : "" }}>25m Papan Gaya Bebas</option>
                            <option value="25m fins gaya bebas" {{ $record->nomor_lomba === "25m fins gaya bebas" ? "selected" : "" }}>25m Fins Gaya Bebas</option>
                            <option value="25m gaya bebas" {{ $record->nomor_lomba === "25m gaya bebas" ? "selected" : "" }}>25m Gaya Bebas</option>
                            <option value="50m gaya bebas" {{ $record->nomor_lomba === "50m gaya bebas" ? "selected" : "" }}>50m Gaya Bebas</option>
                            <option value="100m gaya bebas" {{ $record->nomor_lomba === "100m gaya bebas" ? "selected" : "" }}>100m Gaya Bebas</option>
                            <option value="200m gaya bebas" {{ $record->nomor_lomba === "200m gaya bebas" ? "selected" : "" }}>200m Gaya Bebas</option>
                            <option value="400m gaya bebas" {{ $record->nomor_lomba === "400m gaya bebas" ? "selected" : "" }}>400m Gaya Bebas</option>
                            <option value="800m gaya bebas" {{ $record->nomor_lomba === "800m gaya bebas" ? "selected" : "" }}>800m Gaya Bebas</option>
                            <option value="1500m gaya bebas" {{ $record->nomor_lomba === "1500m gaya bebas" ? "selected" : "" }}>1500m Gaya Bebas</option>
                            <option value="25m fins gaya kupu-kupu" {{ $record->nomor_lomba === "25m fins gaya kupu-kupu" ? "selected" : "" }}>25m Fins Gaya Kupu-Kupu</option>
                            <option value="25m gaya kupu-kupu" {{ $record->nomor_lomba === "25m gaya kupu-kupu" ? "selected" : "" }}>25m Gaya Kupu-Kupu</option>
                            <option value="50m gaya kupu-kupu" {{ $record->nomor_lomba === "50m gaya kupu-kupu" ? "selected" : "" }}>50m Gaya Kupu-Kupu</option>
                            <option value="100m gaya kupu-kupu" {{ $record->nomor_lomba === "100m gaya kupu-kupu" ? "selected" : "" }}>100m Gaya Kupu-Kupu</option>
                            <option value="200m gaya kupu-kupu" {{ $record->nomor_lomba === "200m gaya kupu-kupu" ? "selected" : "" }}>200m Gaya Kupu-Kupu</option>
                            <option value="25m gaya punggung" {{ $record->nomor_lomba === "25m gaya punggung" ? "selected" : "" }}>25m Gaya Punggung</option>
                            <option value="50m gaya punggung" {{ $record->nomor_lomba === "50m gaya punggung" ? "selected" : "" }}>50m Gaya Punggung</option>
                            <option value="100m gaya punggung" {{ $record->nomor_lomba === "100m gaya punggung" ? "selected" : "" }}>100m Gaya Punggung</option>
                            <option value="200m gaya punggung" {{ $record->nomor_lomba === "200m gaya punggung" ? "selected" : "" }}>200m Gaya Punggung</option>
                            <option value="25m gaya dada" {{ $record->nomor_lomba === "25m gaya dada" ? "selected" : "" }}>25m Gaya Dada</option>
                            <option value="50m gaya dada" {{ $record->nomor_lomba === "50m gaya dada" ? "selected" : "" }}>50m Gaya Dada</option>
                            <option value="100m gaya dada" {{ $record->nomor_lomba === "100m gaya dada" ? "selected" : "" }}>100m Gaya Dada</option>
                            <option value="200m gaya dada" {{ $record->nomor_lomba === "200m gaya dada" ? "selected" : "" }}>200m Gaya Dada</option>
                            <option value="200m gaya ganti" {{ $record->nomor_lomba === "200m gaya ganti" ? "selected" : "" }}>200m Gaya Ganti</option>
                            <option value="400m gaya ganti" {{ $record->nomor_lomba === "400m gaya ganti" ? "selected" : "" }}>400m Gaya Ganti</option>
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