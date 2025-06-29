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
        <h1>List Atlet</h1>
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
            atlet
        </label>
        <input type="text" id="search" placeholder="Cari...">
        <div class="table-scroll">
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
                      <td>{{ $loop->iteration }}</td>
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
                        </a>
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
            <button class="prev" disabled>Sebelumnya</button>
            <div class="page-numbers"></div>
            <button class="next" disabled>Selanjutnya</button>
        </div>
    </div>
</section>

</div>
@endsection