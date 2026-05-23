@extends('admin.admin-dashboard-layout')

@section('content')
<div class="main-content">

    @if ($errors->any())
        <x-error-list>
            @foreach ($errors->all() as $error)
                <x-error-item>{{ $error }}</x-error-item>
            @endforeach
        </x-error-list>
    @endif

    {{-- Result summary shown after successful import --}}
    @if (session('import_result'))
        @php $r = session('import_result'); @endphp
        <section class="all-container all-card w100" style="margin-bottom: 1.5rem;">
            <header class="divider">
                <h1>Hasil Import</h1>
            </header>
            <div style="padding: 1rem;">
                <ul>
                    <li><strong>Akun Klub:</strong> {{ $r['user_email'] }}
                        @if($r['user_created'])
                            <span style="color:#155724;">(akun baru dibuat)</span>
                            — <strong>Password:</strong>
                            <code style="background:#f8f9fa;padding:2px 6px;border-radius:3px;font-size:1em;">{{ $r['user_password'] }}</code>
                            <em style="color:#721c24;"> — catat dan bagikan ke klub, tidak akan ditampilkan lagi</em>
                        @else
                            <span style="color:#0c5460;">(akun sudah ada, digunakan)</span>
                        @endif
                    </li>
                    <li>Atlet baru: <strong>{{ $r['athletes_new'] }}</strong></li>
                    <li>Atlet digunakan kembali: <strong>{{ $r['athletes_reused'] }}</strong></li>
                    <li>Pendaftaran dibuat: <strong>{{ $r['registrations'] }}</strong></li>
                    <li>Total biaya: <strong>Rp {{ number_format($r['pembayaran_total'], 0, ',', '.') }}</strong></li>
                </ul>
                @if (!empty($r['errors']))
                    <hr>
                    <h3 style="color:#721c24;">Peringatan / Dilewati:</h3>
                    <ul>
                        @foreach($r['errors'] as $err)
                            <li style="color:#721c24;">{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    @endif

    {{-- Upload form --}}
    <section class="all-container all-card w100">
        <header class="divider">
            <h1>Import Atlet dari XLSX</h1>
        </header>
        <form action="{{ route('admin.import.atlet') }}" method="POST" enctype="multipart/form-data"
              style="padding: 1rem;">
            @csrf

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="kompetisi_id"><strong>Kompetisi</strong></label>
                <select name="kompetisi_id" id="kompetisi_id" class="form-control" required>
                    <option value="">-- Pilih Kompetisi --</option>
                    @foreach($kompetisis as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="file"><strong>File XLSX</strong></label>
                <input type="file" name="file" id="file" class="form-control" accept=".xlsx" required>
                <small class="form-text text-muted">Format: template resmi dengan sheet Info Klub, Referensi, dan Input Atlet.</small>
            </div>

            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </section>

</div>
@endsection
