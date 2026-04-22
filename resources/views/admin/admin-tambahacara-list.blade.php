@extends('admin.admin-dashboard-layout')
@section('style')
    <style>
        p {
            margin-bottom: 5px;
        }

        .grid {
            grid-template-columns: 1fr 1fr 1fr;
        }

        @media screen and (max-width: 1280px) {
            .grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media screen and (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .button-container {
            margin: 20px 20px 20px 0;
        }

        .card-hidden {
            display: none !important;
        }

        #filter-empty {
            display: none;
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-size: 15px;
        }

        .filter-wrapper {
            position: relative;
            margin-left: auto;
        }

        .filter-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            z-index: 100;
            min-width: 260px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
            padding: 16px;
        }

        .filter-dropdown.open {
            display: block;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 12px;
        }

        .filter-group:last-of-type {
            margin-bottom: 16px;
        }

        .filter-group label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .filter-group select {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            background: #fff;
        }

        .filter-dropdown-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        #filter-active-dot {
            display: none;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            position: absolute;
            top: -2px;
            right: -2px;
        }

        .filter-wrapper button {
            margin-left: 0;
        }
    </style>
@endsection

@section('content')
    @include('components.tambah-acara-overlay')

    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon">
                        <i class="bx bxs-grid-alt"></i>
                    </div>
                    <div class="card-content">
                        <h1>{{$nama_kompetisi}}</h1>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <x-success-list>
                <x-success-item>{{ session('success') }}</x-success-item>
            </x-success-list>
        @endif

        <!-- Menampilkan Pesan Error -->
        @if (session('error'))
            <x-error-list>
                <x-error-item>{{ session('error') }}</x-error-item>
            </x-error-list>
        @endif

        <!-- Menampilkan Validasi Error -->
        @if ($errors->any())
            <x-error-list>
                @foreach ($errors->all() as $error)
                    <x-error-item>{{ $error }}</x-error-item>
                @endforeach
            </x-error-list>
        @endif

        <nav class="breadcrumb">
            <ul>
                <li><a href="{{ route('dashboard.admin.acara') }}">List Kompetisi</a></li>
                <li><a href="{{ route('dashboard.admin.listacara', $id_kompetisi) }}">{{ $nama_kompetisi }}</a></li>
            </ul>
        </nav>

        <div class="button-container">
            <button id="openOverlay">Tambah</button>

            @if($acara->count() > 0)
                <div class="filter-wrapper">
                    <button type="button" id="btn-filter-toggle" onclick="toggleFilterDropdown()" style="position:relative;">
                        <i class='bx bx-xs bx-filter-alt'></i> Filter
                        <span id="filter-active-dot"></span>
                    </button>
                    <div class="filter-dropdown" id="filter-dropdown">
                        <div class="filter-group">
                            <label for="filter-kategori">Kategori</label>
                            <select id="filter-kategori">
                                <option value="">Semua</option>
                                @foreach($acara->pluck('kategori')->unique()->sort()->values() as $kat)
                                    <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="filter-grup">Grup</label>
                            <select id="filter-grup">
                                <option value="">Semua</option>
                                @foreach($acara->pluck('grup')->unique()->sort()->values() as $grp)
                                    <option value="{{ $grp }}">{{ $grp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-dropdown-footer">
                            <button type="button" onclick="resetFilters()">Reset</button>
                            <button type="button" onclick="toggleFilterDropdown()">Tutup</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="bottom-container grid" id="acara-grid">
            <p id="filter-empty">Tidak ada acara yang sesuai filter.</p>
            @foreach ($acara as $ac)
                <section class="all-container all-card" data-nomor="{{ $ac->nomor_lomba }}" data-kategori="{{ $ac->kategori }}"
                    data-grup="{{ $ac->grup }}">
                    <header class="flex divider">
                        <h2>
                            {{ strtoupper($ac->nomor_lomba) }} - {{ strtoupper($ac->nama) }}
                            @if($ac->kategori == 'Wanita')
                                PUTRI
                            @elseif($ac->kategori == 'Pria')
                                PUTRA
                            @elseif($ac->kategori == 'Campuran')
                                CAMPURAN
                            @else
                                {{ strtoupper($ac->kategori) }}
                            @endif
                            - KU {{ strtoupper($ac->grup) }}
                        </h2>
                    </header>
                    <div class="info">
                        <h3 class="mtopbot">
                            Harga: <span class="status harga smaller">Rp.{{ number_format($ac->harga, 2, ',', '.') }}</span>
                        </h3>
                        <p>Kuota: {{ $ac->peserta->count() }} / {{$ac->kuota}}</p>
                        <p>Nomor Grup: {{ $ac->grup }}</p>
                        <p>Tahun: {{ $ac->max_umur != null ? $ac->min_umur . ' - ' . $ac->max_umur : $ac->min_umur }}</p>

                    </div>
                    <div class="actions">
                        <a href="{{ route('dashboard.admin.editacara', $ac->id) }}">
                            <button>Edit</button>
                        </a>
                        <form action="{{ route('dashboard.admin.acara.destroy', $ac->id) }}" method="post">
                            @csrf
                            @method('delete')
                            <button class="button-red"
                                onclick="return confirm('-- PERINGATAN!! --\nMenghapus acara yang sedang berjalan atau sudah selesai akan menghapus seluruh data peserta dan semua history kompetisi pada pengguna akan terhapus.')">
                                <i class='bx bx-xs bxs-trash'></i>
                            </button>
                        </form>
                    </div>
                </section>
            @endforeach
        </div>
    </div>

    <script>
        const filterKategori = document.getElementById('filter-kategori');
        const filterGrup = document.getElementById('filter-grup');
        const emptyMsg = document.getElementById('filter-empty');
        const dropdown = document.getElementById('filter-dropdown');
        const activeDot = document.getElementById('filter-active-dot');

        function toggleFilterDropdown() {
            if (dropdown) dropdown.classList.toggle('open');
        }

        function applyFilters() {
            const kategori = filterKategori ? filterKategori.value : '';
            const grup = filterGrup ? filterGrup.value : '';

            const cards = document.querySelectorAll('#acara-grid .all-card');
            let visible = 0;

            cards.forEach(card => {
                const match = (!kategori || card.dataset.kategori === kategori)
                    && (!grup || card.dataset.grup === grup);
                card.classList.toggle('card-hidden', !match);
                if (match) visible++;
            });

            if (emptyMsg) emptyMsg.style.display = visible === 0 ? 'block' : 'none';
            if (activeDot) activeDot.style.display = (kategori || grup) ? 'block' : 'none';
        }

        function resetFilters() {
            if (filterKategori) filterKategori.value = '';
            if (filterGrup) filterGrup.value = '';
            applyFilters();
        }

        [filterKategori, filterGrup].forEach(el => {
            if (el) el.addEventListener('change', applyFilters);
        });

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function (e) {
            const wrapper = document.querySelector('.filter-wrapper');
            if (wrapper && !wrapper.contains(e.target) && dropdown) {
                dropdown.classList.remove('open');
            }
        });
    </script>
@endsection