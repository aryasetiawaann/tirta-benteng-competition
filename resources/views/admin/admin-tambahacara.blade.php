@extends('admin.admin-dashboard-layout')
@section('content')
<div class="main-content">

    <div>
        <h2>Peringatan!!</h2>
        <p>Menghapus kompetisi yang sedang berjalan atau sudah selesai akan menghapus seluruh data peserta dan semua history kompetisi pada pengguna akan terhapus.</p>
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

    @foreach ($kompetisi as $kompe)  
    {{-- Buat jadi card --}}
    <div> 
        <h2>{{ $kompe->nama }}</h2>
        <hr>
        <div>
            @if(now() > $kompe->waktu_kompetisi)
                <p>Status: <span class="status tutup smaller">Selesai</span></p>
            @elseif (now() >= $kompe->tutup_pendaftaran)
                <p>Status: <span class="status buka smaller">Berjalan</span></p>
            @elseif (now() >= $kompe->buka_pendaftaran && now() < $kompe->tutup_pendaftaran)
                <p>Status: <span class="status buka smaller">Registrasi</span></p>
            @else
                <p>Status: <span class="status buka smaller">Belum dibuka</span></p>
            @endif

            <p>Kategori: {{ $kompe->kategori }}</p>
            <p>Open Registration : {{ \Carbon\Carbon::parse($kompe->buka_pendaftaran)->format('d M Y') }}</p>
            <p>Closed Registration : {{ \Carbon\Carbon::parse($kompe->tutup_pendaftaran)->format('d M Y') }}</p>
            <p>Tech Meeting : {{ $kompe->waktu_techmeeting? \Carbon\Carbon::parse($kompe->waktu_techmeeting)->format('d M Y') : '-' }}</p>
            <p>Tanggal Kompetisi : {{ \Carbon\Carbon::parse($kompe->waktu_kompetisi)->format('d M Y') }}</p>
            <p>Lokasi : {{ $kompe->lokasi }}</p>
            <p>{{ $kompe->deskripsi }}</p>
        </div>
        <div>
            <a href="{{ route('dashboard.admin.listacara', $kompe->id) }}">
                <button>Lihat Acara</button>
            </a>
            <a href="{{ route('dashboard.admin.editkompetisi', $kompe->id) }}">
                <button>Edit</button>
            </a>
            <form action="{{ route('dashboard.admin.kompetisi.destroy', $kompe->id) }}" method="post">
                @csrf
                @method('delete')
                <button class="button-red button-gap" onclick="return confirm('Apakah kamu yakin ingin menghapus? ')">
                    <i class='bx bx-xs bxs-trash'></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection