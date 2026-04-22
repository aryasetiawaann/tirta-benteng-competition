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

    {{-- Bulk Action Toolbar --}}
    <form id="bulk-form" method="POST" action="">
        @csrf
        <div id="bulk-toolbar" style="display:none; margin-bottom:12px; padding:10px 14px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; display:none; align-items:center; gap:10px; flex-wrap:wrap;">
            <span id="selected-count" style="font-weight:600; margin-right:4px;">0 atlet dipilih</span>
            <button type="submit"
                    formaction="{{ route('admin.dashboard.bulk.verified') }}"
                    onclick="return confirmBulk('Apakah kamu yakin ingin memverifikasi atlet-atlet yang dipilih?')"
                    class="button-green button-gap">
                <i class='bx bx-xs bx-check'></i> Verifikasi Terpilih
            </button>
            <button type="submit"
                    formaction="{{ route('admin.dashboard.bulk.flagged') }}"
                    onclick="return confirmBulk('Apakah kamu yakin ingin menandai atlet-atlet yang dipilih?')"
                    class="button-orange button-gap">
                <i class='bx bx-xs bxs-flag-alt'></i> Tandai Terpilih
            </button>
            <button type="button" onclick="clearSelection()" style="margin-left:auto;" class="button-gap">
                Batal Pilih
            </button>
        </div>

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
                            <th><input type="checkbox" id="select-all" title="Pilih Semua"></th>
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
                      @forelse ($notVerAtlets as $atlet)
                        <tr>
                          <td>
                            <input type="checkbox" name="selected_atlets[]" value="{{ $atlet->id }}" class="atlet-checkbox">
                          </td>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $atlet->name }}</td>
                          <td>{{ $atlet->user->phone ?? 'Tidak Ada' }}</td>
                          <td>{{ $atlet->user->club ?? 'Tidak Ada' }}</td>
                          <td>{{ $atlet->jenis_kelamin }}</td>
                          <td>{{ \Carbon\Carbon::parse($atlet->umur)->format('d M Y') }}</td>
                          <td>
                            <a href="{{ route('dashboard.atlet.dokumen.view', $atlet->id) }}" target="_blank" rel="noopener noreferrer">
                              <button type="button" class="button-gap" data-tooltip="Lihat Dokumen">
                                <i class='bx bx-xs bx-show'></i>
                              </button>
                            </a>
                          </td>
                          <td>
                            <div class="actions">
                              <form action="{{ route('admin.dashboard.verified', $atlet->id) }}" method="post">
                                  @csrf
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
                          <td colspan="9" style="text-align:center;">Belum ada atlet dengan dokumen</td>
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
    </form>
</section>

</div>

<script>
    const selectAll   = document.getElementById('select-all');
    const toolbar     = document.getElementById('bulk-toolbar');
    const countLabel  = document.getElementById('selected-count');

    function getCheckboxes() {
        return document.querySelectorAll('.atlet-checkbox');
    }

    function updateToolbar() {
        const checked = document.querySelectorAll('.atlet-checkbox:checked');
        const count   = checked.length;
        if (count > 0) {
            toolbar.style.display = 'flex';
            countLabel.textContent = count + ' atlet dipilih';
        } else {
            toolbar.style.display = 'none';
        }
        selectAll.indeterminate = count > 0 && count < getCheckboxes().length;
        selectAll.checked       = count > 0 && count === getCheckboxes().length;
    }

    selectAll.addEventListener('change', function () {
        getCheckboxes().forEach(cb => cb.checked = this.checked);
        updateToolbar();
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('atlet-checkbox')) {
            updateToolbar();
        }
    });

    function clearSelection() {
        getCheckboxes().forEach(cb => cb.checked = false);
        selectAll.checked       = false;
        selectAll.indeterminate = false;
        toolbar.style.display   = 'none';
    }

    function confirmBulk(msg) {
        const count = document.querySelectorAll('.atlet-checkbox:checked').length;
        if (count === 0) {
            alert('Pilih minimal satu atlet terlebih dahulu.');
            return false;
        }
        return confirm(msg);
    }
</script>
@endsection
