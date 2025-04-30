@extends('admin.admin-dashboard-layout')
@section('style')
<style>
    .download .w100,
    .upload .w100, {
        width: calc(100% - 40px);
        height: calc(100% - 40px);
    }

    .admin-container section {
    overflow: auto;
    max-height: 500px;
}
</style>
@endsection
@section('content')
@include('components.upload-file-hasil')
<div class="main-content">
    
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

    <div class="admin-container">
        <div class="download card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Download Buku Acara</h2>
                </header>
                <section>
                    <div class="download-section">
                        @if ($kompetisi->count() > 0)
                            @foreach ($kompetisi as $kompe)
                                <div class="download-item">
                                    <a href="{{ route('dashboard.admin.excel.download', $kompe->id) }}">
                                        <button class="button-blue download-button"><i class='bx bx-download'></i></button>
                                    </a>
                                    <h4>{{ $kompe->nama }}</h4>
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
                    <div class="download-item">
                        <button class="button-green" id="openOverlay"><i class='bx bx-upload'></i></button>
                        <h4 style="margin-bottom: 10px">PDF</h4>
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
                                <div style="margin-bottom: 20px;">
                                    <h3 style="margin: 10px 10px 10px 0;">{{ $kompe->nama }}</h3>
                                    @if ($kompe->harga->count() > 0)
                                        @foreach ($kompe->harga as $harga)
                                            <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
                                                <div class="info">
                                                    <h4><strong>Judul:</strong> {{ $harga->judul }}</h4>
                                                    <p><strong>Harga:</strong> Rp.{{ number_format($harga->harga, 2, ',', '.') }}</p>
                                                    <div>
                                                        <strong>Deskripsi:</strong>
                                                        <p>{!! $harga->deskripsi !!}</p>
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

        <div class="download card">
            <div class="all-container all-card w100">
                <header class="flex divider">
                    <h2>Unduh Dokumen Peserta</h2>
                </header>
                <section>
                    <div class="download-section">
                        @if ($kompetisi->count() > 0)
                            @foreach ($kompetisi as $kompe)
                                <div class="download-item">
                                    <a href="{{ route('dashboard.admin.dokumen.download', $kompe->id) }}">
                                        <button class="button-blue download-button"><i class='bx bx-download'></i></button>
                                    </a>
                                    <h4>{{ $kompe->nama }}</h4>
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

    <div>
        <h2>Daftar Verifikasi Dokumen Peserta</h2>
        <div>
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Dokumen</th>
                    <th></th>
                </tr>
                @foreach ($notVerAtlets as $atlet)
                    <tr>
                        <td>{{ $loop->iteration}}</td>
                        <td>{{ $atlet->name}}</td>
                        <td>{{ \Carbon\Carbon::parse($atlet->umur)->format('d M Y') }}</td>
                        <td>{{ $atlet->jenis_kelamin }}</td>
                        <td>
                            <a href="{{ route('dashboard.atlet.dokumen.view', $atlet->id) }}">
                            <button class="button-gap button-green" data-tooltip="Lihat Dokumen">
                                <i class='bx bx-xs bx-download'></i>
                            </button>
                            </a>
                        </td>
                        <td>
                            <div class="actions">
                                <form action="{{ route('admin.dashboard.verified', $atlet->id) }}" method="post">
                                    @csrf
                                    @method('post')
                                    <a onclick="return confirm('Apakah kamu yakin ingin menerima atlet ini? ')"><button class="button-green button-gap" data-tooltip="Terima Atlet"><i class='bx bx-xs bxs-check' ></i></button></a>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach

            </table>
        </div>
    </div>
</div>
@endsection
