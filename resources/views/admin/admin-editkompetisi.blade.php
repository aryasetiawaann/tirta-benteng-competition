@extends('admin.admin-dashboard-layout')
@section('content')
    <div class="main-content">
        <div class="all-container all-card w100">
            <header class="flex divider">
                <h2>Edit {{ $kompetisi->nama }}</h2>
            </header>
            <section>
                <form class="edit-container" method="POST"
                    action="{{ route('dashboard.admin.updatekompetisi', $kompetisi->id) }}">
                    @csrf
                    @method('put')

                    <label for="nama">Nama Kompetisi *</label>
                    <input type="text" id="nama" name="nama" placeholder="Nama Kompetisi"
                        value="{{ $kompetisi->nama }}">
                    <label for="kategori">Kategori *</label>
                    <select id="kategori" name="kategori">
                        <option value="fun" {{ $kompetisi->kategori === 'Fun' ? 'selected' : '' }}>Fun</option>
                        <option value="resmi" {{ $kompetisi->kategori === 'Resmi' ? 'selected' : '' }}>Resmi</option>
                    </select>
                    <label for="openreg">Open Registrasi *</label>
                    <input type="date" id="openreg" name="openreg" placeholder="Open Registrasi"
                        value="{{ $kompetisi->buka_pendaftaran }}">
                    <label for="closereg">Close Registrasi *</label>
                    <input type="date" id="closereg" name="closereg" placeholder="Close Registrasi"
                        value="{{ $kompetisi->tutup_pendaftaran }}">
                    <label for="techmeet">Technical Meeting</label>
                    <input type="date" id="techmeet" name="techmeet" placeholder="Technical Meeting"
                        value="{{ $kompetisi->waktu_techmeeting }}">
                    <label for="datekompe">Tanggal Kompetisi *</label>
                    <input type="date" id="datekompe" name="datekompe" value="{{ $kompetisi->waktu_kompetisi }}">
                    <label for="lokasi">Lokasi *</label>
                    <input type="text" id="lokasi" name="lokasi" placeholder="Lokasi"
                        value="{{ $kompetisi->lokasi }}">
                    <label>
                        <input type="checkbox" id="has_pricing" name="has_pricing" value="1"
                            {{ $kompetisi->has_pricing ? 'checked' : '' }}> Aktifkan Harga Paket
                    </label>

                    <div id="pricing_container"
                        style="display: {{ $kompetisi->has_pricing ? 'block' : 'none' }}; margin-top: 10px;">
                        <input type="number" id="max_participation" name="max_participation"
                            placeholder="Maksimal acara yang diikuti"
                            value="{{ old('max_participation', $kompetisi->max_participation) }}">

                        <input type="number" id="additional_price" name="additional_price"
                            placeholder="Biaya tambah Nomor Acara"
                            value="{{ old('additional_price', $kompetisi->additional_price) }}">


                        <div id="pricing_wrapper">
                            @php
                                $pricings = $kompetisi->pricings ?? [];
                            @endphp

                            @foreach ($pricings as $index => $pricing)
                                <div class="pricing-group">
                                    <p>Paket {{ $index + 1 }}</p>
                                    <input type="number" name="pricings[{{ $index }}][event_amount]"
                                        value="{{ $pricing->event_amount }}" placeholder="Maksimal Jumlah Acara"
                                        class="form-control" required>
                                    <input type="number" name="pricings[{{ $index }}][harga]"
                                        value="{{ $pricing->harga }}" placeholder="Jumlah Harga" class="form-control"
                                        required>
                                </div>
                            @endforeach

                            @if (!$kompetisi->has_pricing)
                                <div class="pricing-group">
                                    <p>Paket 1</p>
                                    <input type="number" name="pricings[0][event_amount]"
                                        placeholder="Maksimal Jumlah Acara (contoh: 3)" class="form-control">
                                    <input type="number" name="pricings[0][harga]" placeholder="Jumlah Harga"
                                        class="form-control">
                                </div>
                            @endif
                        </div>

                        <button type="button" onclick="addPricing()">Tambah Paket +</button>
                    </div>

                    <label for="deskripsi">Deskripsi</label>
                    <input id="deskripsi" type="hidden" name="deskripsi" value="{{ $kompetisi->deskripsi }}">
                    <trix-editor input="deskripsi" style="height:200px;"></trix-editor>
                    <input type="hidden" name="id" value="{{ $kompetisi->id }}">
                    <div class="flex center">
                        <button type="submit" class="submit-button">Simpan</button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        let pricingIndex = 1;

        function addPricing() {
            const wrapper = document.getElementById('pricing_wrapper');

            const div = document.createElement('div');
            div.classList.add('pricing-group');

            div.innerHTML = `
            <p style="margin-top: 10px;">Paket ${pricingIndex + 1}</p>
            <input type="number" name="pricings[${pricingIndex}][event_amount]" placeholder="Maksimal Jumlah Acara (contoh: 3)" class="form-control" >
            <input type="number" name="pricings[${pricingIndex}][harga]" placeholder="Jumlah Harga" class="form-control" >
        `;

            wrapper.appendChild(div);
            pricingIndex++;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('has_pricing');
            const pricingContainer = document.getElementById('pricing_container');


            checkbox.addEventListener('change', function() {
                pricingContainer.style.display = this.checked ? 'block' : 'none';
            });
        });
    </script>
@endsection
