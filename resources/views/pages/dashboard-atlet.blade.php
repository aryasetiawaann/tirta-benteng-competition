@extends('layouts.dashboard-layout')
@section('title', 'Atlet Saya')
@section('content')
@include('components.daftar-atlet-overlay')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-icon">
                    <i class='bx bxs-user' ></i>
                </div>
                <div class="card-content">
                    <p>Jumlah Atlet</p>
                    <h1>{{  $atlets_count }}</h1>
                </div>
            </div>
        </div>
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
        <div class="bottom-container">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Atlet</h1>
                    <a id="openOverlay"><button>+ Tambah</button></a>
                </header>
                <div class="table-container">
                    <label for="entries">Tampilkan
                        <select id="entries" name="entries">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select> 
                        entri
                    </label>
                    <input type="text" id="search" placeholder="Cari...">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Tanggal Lahir</th>
                                <th>Umur</th>
                                <th>Jenis Kelamin</th>
                                <th>Track Record</th>
                                <th>Kelengkapan Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ( $atlets_count > 0)   
                                @foreach ($atlets as $key => $atlet) 
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $atlet->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($atlet->umur)->format('d M Y') }}</td>
                                    <td>{{ now()->diffInYears(\Carbon\Carbon::parse($atlet->umur)) }}</td>
                                    <td>{{ $atlet->jenis_kelamin }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.track-record.index', $atlet->id) }}">
                                            <button>Logo Show</button>
                                        </a>
                                    </td>

                                    @if ($atlet->dokumen != NULL)
                                        <td>Lengkap</td>  
                                    @else
                                        <td>Tidak Lengkap</td>
                                    @endif
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('dashboard.atlet.edit', $atlet->id) }}">
                                                <button class="button-gap" data-tooltip="Edit Atlet"><i class='bx bx-xs bx-edit'></i></button>
                                            </a>
                                            @if ($atlet->dokumen != NULL)
                                                <a href="{{ route('dashboard.atlet.dokumen.download', $atlet->id) }}">
                                                    <button class="button-gap button-green" data-tooltip="Unduh Dokumen"><i class='bx bx-xs bx-file'></i></button>
                                                </a>
                                                <form action="{{ route('dashboard.atlet.dokumen.delete', $atlet->id) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="button-red button-gap" data-tooltip="Hapus Dokumen" onclick="return confirm('Apakah kamu yakin ingin menghapus dokumen ini? ')">
                                                        <i class='bx bx-xs bx-file'></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('dashboard.atlet.destroy', $atlet->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button class="button-red button-gap" data-tooltip="Hapus Atlet"onclick="return confirm('Apakah kamu yakin ingin menghapus atlet ini? ')">
                                                    <i class='bx bx-xs bx-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="7" style="text-align:center;">Belum ada data</td></tr>
                            @endif 
                        </tbody>
                    </table>
                    <div class="pagination">
                        <button class="prev" disabled>Sebelumnya</button>
                        <div class="page-numbers"></div>
                        <button class="next" disabled>Selanjutnya</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection