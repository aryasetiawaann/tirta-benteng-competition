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
                <h1>Upload Pemenang Kejuaraan</h1>
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
                    <input type="file" id="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                </div>

                <button type="submit" class="btn btn-primary">Import</button>
            </form>

        </section>

        <section class="all-container all-card w100">
            <header class="divider flex">
                <h2>Upload Keterangan Juara</h2>
            </header>

            <form action="{{ route('admin.kejuaraan.input-doc') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
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

            <form action="{{ route('admin.kejuaraan') }}" method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                <div>
                    <label for="filter_kompetisi" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Kompetisi:</label>
                    <select name="filter_kompetisi" id="filter_kompetisi" class="form-control" style="width: 250px;">
                        <option value="">-- Semua Kompetisi --</option>
                        @foreach ($kompetisiList as $kompetisi)
                            <option value="{{ $kompetisi->id }}"
                                {{ request('filter_kompetisi') == $kompetisi->id ? 'selected' : '' }}>
                                {{ $kompetisi->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_dokumen" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Status Dokumen:</label>
                    <select name="filter_dokumen" id="filter_dokumen" class="form-control" style="width: 200px;">
                        <option value="">-- Semua Status --</option>
                        <option value="has_sertifikat" {{ request('filter_dokumen') == 'has_sertifikat' ? 'selected' : '' }}>Sudah Punya Sertifikat</option>
                        <option value="no_sertifikat" {{ request('filter_dokumen') == 'no_sertifikat' ? 'selected' : '' }}>Belum Punya Sertifikat</option>
                        <option value="has_sk" {{ request('filter_dokumen') == 'has_sk' ? 'selected' : '' }}>Sudah Punya SK</option>
                        <option value="no_sk" {{ request('filter_dokumen') == 'no_sk' ? 'selected' : '' }}>Belum Punya SK</option>
                        <option value="has_both" {{ request('filter_dokumen') == 'has_both' ? 'selected' : '' }}>Lengkap (Sertifikat & SK)</option>
                        <option value="no_both" {{ request('filter_dokumen') == 'no_both' ? 'selected' : '' }}>Belum Punya Keduanya</option>
                    </select>
                </div>

                <div>
                    <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Cari:</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Nama, Klub, atau Kode" value="{{ request('search') }}" style="width: 250px;">
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request()->hasAny(['filter_kompetisi', 'filter_dokumen', 'search']))
                        <a href="{{ route('admin.kejuaraan') }}" class="btn" style="background-color: #6c757d; color: white; text-decoration: none;">Reset</a>
                    @endif
                </div>
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
