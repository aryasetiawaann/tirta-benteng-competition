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
        <h1>Verifikasi Dokumen Atlet</h1>
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
                        <th>No Telepon</th>
                        <th>Nama Club</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th>Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  @php $counter = 1; @endphp
                  @forelse ($notVerAtlets as $atlet)
                    <tr>
                      <td>{{ $counter++ }}</td>
                      <td>{{ $atlet->name }}</td>
                      <td>{{ $atlet->user->phone ?? 'Tidak Ada' }}</td>
                      <td>{{ $atlet->user->club ?? 'Tidak Ada' }}</td>
                      <td>{{ $atlet->jenis_kelamin }}</td>
                      <td>{{ \Carbon\Carbon::parse($atlet->umur)->format('d M Y') }}</td>
                      <td>
                        <a href="{{ route('dashboard.atlet.dokumen.view', $atlet->id) }}" target="_blank" rel="noopener noreferrer">
                          <button class="button-gap" data-tooltip="Lihat Dokumen">
                            <i class='bx bx-xs bx-show'></i>
                          </button>
                        </a>
                      </td>
                      <td>
                        <div class="actions">
                          <form action="{{ route('admin.dashboard.verified', $atlet->id) }}" method="post">
                              @csrf
                              @method('post')
                              <button class="button-green button-gap" data-tooltip="Terima Atlet" onclick="return confirm('Apakah kamu yakin ingin menerima atlet ini?')">
                                <i class='bx bx-xs bx-check'></i>
                              </button>
                              <button class="button-orange button-gap" data-tooltip="Tandai Atlet" formaction="{{ route('admin.dashboard.flagged', $atlet->id) }}" onclick="return confirm('Apakah kamu yakin ingin menandai atlet ini?')">
                                <i class='bx bx-xs bxs-flag-alt'></i>
                              </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" style="text-align:center;">Belum ada atlet dengan dokumen</td>
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

<section class="all-container all-card w100">
    <header class="divider flex">
        <h1>Atlet Butuh Revisi Dokumen</h1>
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
                        <th>No Telepon</th>
                        <th>Nama Club</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th>Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  @php $counter = 1; @endphp
                  @forelse ($flagAtlets as $atlet)
                    <tr>
                      <td>{{ $counter++ }}</td>
                      <td>{{ $atlet->name }}</td>
                      <td>{{ $atlet->user->phone ?? 'Tidak Ada' }}</td>
                      <td>{{ $atlet->user->club ?? 'Tidak Ada' }}</td>
                      <td>{{ $atlet->jenis_kelamin }}</td>
                      <td>{{ \Carbon\Carbon::parse($atlet->umur)->format('d M Y') }}</td>
                      <td>
                        <a href="{{ route('dashboard.atlet.dokumen.view', $atlet->id) }}" target="_blank" rel="noopener noreferrer">
                          <button class="button-gap" data-tooltip="Lihat Dokumen">
                            <i class='bx bx-xs bx-show'></i>
                          </button>
                        </a>
                      </td>
                      <td>
                        <div class="actions">
                          <form action="{{ route('admin.dashboard.verified', $atlet->id) }}" method="post">
                            @csrf
                            @method('post')
                            <button class="button-green button-gap" data-tooltip="Terima Atlet" onclick="return confirm('Apakah kamu yakin ingin menerima atlet ini?')">
                              <i class='bx bx-xs bx-check'></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" style="text-align:center;">Belum ada atlet dengan dokumen</td>
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