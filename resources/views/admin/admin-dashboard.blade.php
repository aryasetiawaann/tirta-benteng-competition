@extends('admin.admin-dashboard-layout')
@section('content')
@include('components.upload-file-hasil')
<div class="main-content">

    <div class="admin-container">
        <div class="card100">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Welcome! {{ auth()->user()->name }}</h2>
                </header>
                <section>
                    <p>{{ \Carbon\Carbon::now()->format('l') }}, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                </section>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div style="color: green;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Menampilkan Pesan Error -->
    @if (session('error'))
        <div style="color: red;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Menampilkan Validasi Error -->
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-container">
        <div class="download card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Download Buku Acara</h2>
                </header>
                <section>
                    <div>
                        <h4 style="margin-bottom: 10px">EXCEL</h4>
                        @if ($kompetisi->count() > 0)
                            <hr>
                            @foreach ($kompetisi as $kompe)
                            <div>
                                <h4 style="margin-top: 10px">{{ $kompe->nama }}</h4>
                                <a href="{{ route('dashboard.admin.excel.download', $kompe->id) }}">
                                    <button ton class="button-blue"><i class='bx bx-download'></i></button>
                                </a>
                            </div>
                            @endforeach
                        @else
                            <h4>Belum ada kompetisi</h4>
                        @endif
                    </div>
                </section>
            </div>
        </div>
        <div class="upload card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Upload Hasil Kompetisi</h2>
                </header>
                <section>
                    <div>
                        <h4 style="margin-bottom: 10px">PDF</h4>
                        <button class="button-blue" id="openOverlay"><i class='bx bx-upload'></i></button>
                        @if ($kompetisi_file->count() > 0 )
                            <hr>
                            @foreach ($kompetisi_file as $kompe)
                            {{-- biar bisa di scroll y overflownya di auto in aja biar gak begitu panjang ke bawah --}}
                                <div>
                                    <h3>{{ $kompe->nama }}</h3>
                                    <a href="{{ route('dashboard.admin.file.edit', $kompe->id) }}">
                                        <button class="button-blue"><i class='bx bx-edit'></i></button>
                                    </a>
                                    <a href="{{ route('dashboard.admin.file.download', $kompe->id) }}">
                                        <button class="button-green"><i class='bx bx-download'></i></button>
                                    </a>
                                    <form action="{{ route('dashboard.admin.file.delete', $kompe->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button class="button-red button-gap" onclick="return confirm('Apakah anda yakin ingin menghapus file ini?')">
                                            <i class='bx bx-xs bxs-trash'></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>
        </div>
        <div class="download card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Logo Kompetisi</h2>
                </header>
                <section>
                    <div class="logo-section">
                        @if ($kompetisi->count() > 0)
                            @foreach ($kompetisi as $kompe)
                                @if ($kompe->logo->count() > 0)
                                    @foreach ($kompe->logo as $logo)
                                        <div class="logo-item">
                                            <img src="{{ asset($logo->name) }}" alt="logo" class="logo-img">
                                            <p class="logo-name">{{ $kompe->nama }}</p>
                                            <form action="{{ route('dashboard.admin.kompetisi.logo.delete', $logo->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button class="button-red button-gap" onclick="return confirm('Apakah anda yakin ingin menghapus gambar ini?')">
                                                    <i class='bx bx-xs bxs-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                @else
                                    <p>Belum ada logo</p>
                                @endif
                            @endforeach
                        @else
                            <h4>Belum ada kompetisi</h4>
                        @endif
                    </div>
                </section>
            </div>
        </div>
        <div class="download card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Detail Harga Kompetisi</h2>
                </header>
                <section>
                    <div>
                        @if ($kompetisi->count() > 0)
                            @foreach ($kompetisi as $kompe)
                            <div>
                                <h3>{{ $kompe->nama }}</h3>
                                    @if ($kompe->harga->count() > 0)
                                        @foreach ($kompe->harga as $harga)
                                            {{-- dibuat jadi card --}}
                                            <div >
                                                <div>
                                                    <h3>{{ $harga->judul }}</h3>
                                                    <p>Rp.{{ number_format($harga->harga, 2, ',', '.') }}</p>
                                                    <div>
                                                        {!! $harga->deskripsi !!}
                                                    </div>
                                                </div>
                                                <div>
                                                    <form action="{{ route('dashboard.admin.kompetisi.detail-harga.delete', $harga->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="button-red button-gap" onclick="return confirm('Apakah anda yakin ingin menghapus ini?')">
                                                            <i class='bx bx-xs bxs-trash'></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            {{--  --}}
                                        @endforeach
                                    @else
                                    <p>Belum ada detail harga</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <h4>Belum ada kompetisi</h4>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
    
</div>
@endsection