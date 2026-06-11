@extends('admin.admin-dashboard-layout')
@section('style')
<style>
    .pagination a { text-decoration: none; }
    .pagination a button { cursor: pointer; }
</style>
@endsection
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
        <h1>List Atlet</h1>
    </header>
    <div class="table-container">
        <label for="entries">Tampilkan
            <select id="entries" onchange="changePerPage(this.value)">
                @foreach([5, 10, 25, 50, 100] as $opt)
                    <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
            atlet
        </label>
        <input type="text" id="search" placeholder="Cari..." value="{{ $search }}">
        <div class="table-scroll" data-server-paginated>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tanggal Lahir</th>
                        <th>Umur</th>
                        <th>Jenis Kelamin</th>
                        <th>Nama Club</th>
                        <th>Email Akun</th>
                        <th>Verified?</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  @forelse ($atlets as $atlet)
                    <tr>
                      <td>{{ ($atlets->currentPage() - 1) * $atlets->perPage() + $loop->iteration }}</td>
                      <td>{{ $atlet->name }}</td>
                      <td>{{ \Carbon\Carbon::parse($atlet->umur)->format('d M Y') }}</td>
                      <td>{{ now()->diffInYears(\Carbon\Carbon::parse($atlet->umur)) }}</td>
                      <td>{{ $atlet->jenis_kelamin }}</td>
                      <td>{{ $atlet->user->club ?? 'Tidak Ada' }}</td>
                      <td>{{ $atlet->user->email ?? 'Tidak Ada' }}</td>
                      <td>
                        @if($atlet->is_verified == 'verified')
                            Terverifikasi
                        @elseif($atlet->is_verified == 'not verified')
                            Belum Diverifikasi
                        @elseif($atlet->is_verified == 'need revision')
                            Butuh Revisi
                        @else
                            -
                        @endif
                      </td>
                      <td>
                        <div class="actions">
                            @if($atlet->dokumen)
                            <a href="{{ route('dashboard.atlet.dokumen.view', $atlet->id) }}" target="_blank" rel="noopener noreferrer">
                              <button class="button-gap" data-tooltip="Lihat Dokumen">
                                <i class='bx bx-xs bx-show'></i>
                              </button>
                            </a>
                          @endif
                          <a href="{{ route('admin.atlet.edit', $atlet->id) }}">
                              <button class="button-gap" data-tooltip="Edit Atlet">
                                  <i class='bx bx-xs bx-edit'></i>
                              </button>
                          </a>
                        </div>
                        <div class="actions">
                          <form action="{{ route('dashboard.atlet.destroy', $atlet->id) }}" method="post">
                            @csrf
                            @method('delete')
                            <button class="button-red button-gap" data-tooltip="Hapus Atlet" onclick="return confirm('Apakah kamu yakin ingin menghapus atlet ini? ')">
                                <i class='bx bx-xs bx-trash'></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" style="text-align:center;">Belum ada atlet terdaftar</td>
                    </tr>
                  @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">
            @if($atlets->onFirstPage())
                <button class="prev" disabled>Sebelumnya</button>
            @else
                <a href="{{ $atlets->previousPageUrl() }}"><button class="prev">Sebelumnya</button></a>
            @endif
            <div class="page-numbers">
                @for($i = max(1, $atlets->currentPage() - 2); $i <= min($atlets->lastPage(), $atlets->currentPage() + 2); $i++)
                    <a href="{{ $atlets->url($i) }}">
                        <span class="page-number {{ $i == $atlets->currentPage() ? 'current' : '' }}">{{ $i }}</span>
                    </a>
                @endfor
            </div>
            @if($atlets->hasMorePages())
                <a href="{{ $atlets->nextPageUrl() }}"><button class="next">Selanjutnya</button></a>
            @else
                <button class="next" disabled>Selanjutnya</button>
            @endif
        </div>
    </div>
</section>

</div>

<script>
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const val = this.value;
        searchTimeout = setTimeout(function () {
            const params = new URLSearchParams(window.location.search);
            if (val) params.set('search', val);
            else params.delete('search');
            params.delete('page');
            window.location.href = window.location.pathname + '?' + params.toString();
        }, 500);
    });

    function changePerPage(value) {
        const params = new URLSearchParams(window.location.search);
        params.set('perPage', value);
        params.delete('page');
        window.location.href = window.location.pathname + '?' + params.toString();
    }
</script>
@endsection
