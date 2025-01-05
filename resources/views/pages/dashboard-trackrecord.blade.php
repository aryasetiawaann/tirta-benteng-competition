@extends('layouts.dashboard-layout')
@section('title', 'Track Record')
@section('content')
@include('components.daftar-trackrecord-overlay')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon">
                        <i class='bx bxs-user' ></i>
                    </div>
                    <div class="card-content">
                        <h1>Track Record {{ $atlet->name }}</h1>
                    </div>
                </div>
            </div>
        </div>
        <nav class="breadcrumb">
            <ul>
                <li>Atlet Saya</li>
                <li><a href="{{ route('dashboard.atlet.index') }}">Daftar Atlet</a></li>
                <li><a href="{{ route('dashboard.track-record.index', $atlet->id) }}">Track Record - {{ $atlet->name }}</a></li>
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
        <div class="bottom-container">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Track Record</h1>
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
                                <th>Kompetisi</th>
                                <th>Nomor Lomba</th>
                                <th>Durasi Renang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ( $records->count() > 0)  
                                @foreach ($records as $key => $record) 
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $record->kompetisi }}</td>
                                    <td class="capitalize">{{ $record->nomor_lomba }}</td>
                                    <td>
                                        <span class="status registration">
                                            {{ sprintf('%02d:%02d.%02d', 
                                                floor($record->time / 60),  // Menit
                                                floor(fmod($record->time, 60)),  // Detik
                                                round(($record->time - floor($record->time)) * 100)  // Milidetik
                                            ) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('dashboard.track-record.edit', $record->id) }}">
                                                <button class="button-gap"><i class='bx bx-xs bx-edit'></i></button>
                                            </a>
                            
                                            <form action="{{ route('dashboard.track-record.destroy', $record->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button class="button-red button-gap" onclick="return confirm('Apakah kamu yakin ingin menghapus track record ini? ')">
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