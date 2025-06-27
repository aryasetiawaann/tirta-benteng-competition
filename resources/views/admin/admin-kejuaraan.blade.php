@extends('admin.admin-dashboard-layout')
@section('content')
    <div class="main-content">
        @if (session('success'))
            <x-success-list>
                <x-success-item>{{ session('success') }}</x-success-item>
            </x-success-list>
        @endif
        @if (session('error'))
            <x-error-list>
                <x-error-item>{{ session('error') }}</x-error-item>
            </x-error-list>
        @endif
        @if ($errors->any())
            <x-error-list>
                @foreach ($errors->all() as $error)
                    <x-error-item>{{ $error }}</x-error-item>
                @endforeach
            </x-error-list>
        @endif
        <section class="all-container all-card w100">
            <header class="divider flex">
                <h1>Kejuaraan</h1>
            </header>

            <form action="{{ route('admin.kejuaraan.store') }}" method="POST" enctype="multipart/form-data"
                style="margin-top: 1rem;">
                @csrf

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="kompetisi_id">Pilih Kompetisi:</label><br>
                    <select name="kompetisi_id" id="kompetisi_id" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Kompetisi --</option>
                        @foreach ($kompetisiList as $kompetisi)
                            <option value="{{ $kompetisi->id }}">{{ $kompetisi->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="file">Upload File Excel:</label><br>
                    <input type="file" id="file" name="file" accept=".xlsx,.xls" required>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">Import</button>
            </form>

        </section>

        <section class="all-container all-card w100">
            <form action="{{ route('admin.kejuaraan.input-doc') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="kompetisi_id">Pilih Kompetisi:</label><br>
                    <select name="kompetisi_id" id="kompetisi_id" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Kompetisi --</option>
                        @foreach ($kompetisiList as $kompetisi)
                            <option value="{{ $kompetisi->id }}">{{ $kompetisi->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="jenis_dokumen">Jenis Dokumen:</label>
                    <select name="jenis_dokumen" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Jenis --</option>
                        <option value="sertifikat">Sertifikat</option>
                        <option value="surat_keterangan">Surat Keterangan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dokumen">Upload File PDF:</label>
                    <input type="file" name="dokumen[]" class="form-control" accept="application/pdf" multiple required>
                </div>

                <button type="submit" class="btn btn-primary">Upload Dokumen</button>
            </form>
        </section>

        <section class="all-container all-card w100" style="margin-top: 2rem;">
            <header class="divider flex">
                <h2>Daftar Pemenang</h2>
            </header>

            <form action="{{ route('admin.kejuaraan') }}" method="GET" style="margin-bottom: 1rem;">
                <label for="filter_kompetisi">Filter berdasarkan Kompetisi:</label>
                <select name="filter_kompetisi" id="filter_kompetisi" onchange="this.form.submit()" class="form-control"
                    style="width: 250px;">
                    <option value="">-- Semua Kompetisi --</option>
                    @foreach ($kompetisiList as $kompetisi)
                        <option value="{{ $kompetisi->id }}"
                            {{ request('filter_kompetisi') == $kompetisi->id ? 'selected' : '' }}>
                            {{ $kompetisi->nama }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if ($pemenangList->count())
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kelompok Umur</th>
                                <th>Acara</th>
                                <th>Nama Atlet</th>
                                <th>Klub</th>
                                <th>Rank</th>
                                <th>Nomor Lomba</th>
                                <th>Sertifikat</th>
                                <th>SK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pemenangList as $pemenang)
                                <tr>
                                    <td>{{ $pemenang->kelompok_umur }}</td>
                                    <td>{{ $pemenang->acara->nomor_lomba }}</td>
                                    <td>{{ $pemenang->nama }}</td>
                                    <td>{{ $pemenang->club }}</td>
                                    <td>{{ $pemenang->rank }}</td>
                                    <td>{{ $pemenang->nomor_lomba }}</td>
                                    <td>{{ $pemenang->certificate->filename ?? '-' }}</td>
                                    <td>{{ $pemenang->letter->filename ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ✅ Pagination --}}
                <div style="margin-top: 1rem;">
                    {{ $pemenangList->withQueryString()->links() }}
                </div>
            @else
                <p>Tidak ada data pemenang.</p>
            @endif
        </section>
    </div>
@endsection
