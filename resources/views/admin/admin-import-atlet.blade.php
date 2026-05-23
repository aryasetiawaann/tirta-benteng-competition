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
        <section class="all-container all-card w100" style="margin-bottom: 1.5rem; overflow: visible;">
            <header class="divider">
                <h1>Hasil Import</h1>
            </header>
            <div style="padding: 1rem;">
                <table style="border-collapse:collapse;margin-bottom:0.75rem;border:none; overflow-x: auto;">
                    <tr>
                        <td style="padding:4px 12px 4px 0;white-space:nowrap;border:none;"><strong>Email</strong></td>
                        <td style="border:none;"><input type="text" readonly value="{{ $r['user_email'] }}"
                                onclick="this.select()"
                                style="width:280px;padding:4px 8px;border:1px solid #ced4da;border-radius:4px;background:#f8f9fa;cursor:text;">
                            @if($r['user_created'])
                                <span style="color:#155724;margin-left:6px;">(akun baru dibuat)</span>
                            @else
                                <span style="color:#0c5460;margin-left:6px;">(akun sudah ada)</span>
                            @endif
                        </td>
                    </tr>
                    @if($r['user_created'])
                    <tr>
                        <td style="padding:4px 12px 4px 0;white-space:nowrap;border:none;"><strong>Password</strong></td>
                        <td style="border:none;"><input type="text" readonly value="{{ $r['user_password'] }}"
                                onclick="this.select()"
                                style="width:280px;padding:4px 8px;border:1px solid #ffc107;border-radius:4px;background:#fff3cd;cursor:text;font-family:monospace;">
                            <em style="color:#721c24;margin-left:6px;font-size:0.9em;">catat — tidak ditampilkan lagi</em>
                        </td>
                    </tr>
                    @endif
                    <tr><td style="padding:4px 12px 4px 0;border:none;"><strong>Atlet baru</strong></td><td>{{$r['athletes_new'] }}</td></tr>
                    <tr><td style="padding:4px 12px 4px 0;border:none;"><strong>Atlet digunakan kembali</strong></td><td>{{$r['athletes_reused'] }}</td></tr>
                    <tr><td style="padding:4px 12px 4px 0;border:none;"><strong>Pendaftaran dibuat</strong></td><td>{{$r['registrations'] }}</td></tr>
                    <tr><td style="padding:4px 12px 4px 0;border:none;"><strong>Total biaya</strong></td><td>Rp {{ number_format($r['pembayaran_total'], 0, ',', '.') }}</td></tr>
                </table>
                @if (!empty($r['registered_athletes']))
                    <hr>
                    <h3>Detail Pendaftaran</h3>
                    <div style="overflow-x:auto;">
                    <table class="table" style="border-collapse:collapse;margin-top:0.5rem;white-space:nowrap;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:8px;border:1px solid #dee2e6;text-align:left;">No</th>
                                <th style="padding:8px;border:1px solid #dee2e6;text-align:left;">Nama Atlet</th>
                                <th style="padding:8px;border:1px solid #dee2e6;text-align:left;">Nomor Lomba Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($r['registered_athletes'] as $idx => $a)
                            <tr>
                                <td style="padding:8px;border:1px solid #dee2e6;">{{ $idx + 1 }}</td>
                                <td style="padding:8px;border:1px solid #dee2e6;">{{ $a['name'] }}</td>
                                <td style="padding:8px;border:1px solid #dee2e6;">
                                    @foreach($a['events'] as $event)
                                        <span style="display:inline-block;background:#e9ecef;padding:2px 8px;border-radius:4px;margin:2px;font-size:0.9em;">{{ $event }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @endif
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
