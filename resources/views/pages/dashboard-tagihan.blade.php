@extends('layouts.dashboard-layout')
@section('title', 'Tagihan')

@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon dark-green">
                        <i class="icon ph-bold ph-wallet"></i>
                    </div>
                    <div class="card-content">
                        <h1>Tagihan</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="nav-page nav-card">
            <p>
                <a href="#">#</a> / 
            </p>
        </div> -->
        <div class="bottom-container">
            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Tagihan Belum Dibayarkan</h1>
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
                            <th>Nama</th>
                            <th>Kompetisi</th>
                            <th>Nomor Lomba</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($atlets->isEmpty())
                            <tr><td colspan="7" style="text-align:center;">Belum ada data</td></tr>
                        @else
                            @foreach ($atlets as $atlet)
                                @foreach ($atlet->acara as $acara)
                                        <tr>
                                            <td>{{ $counter = isset($counter) ? $counter + 1 : 1 }}</td>
                                            <td>{{ $atlet->name }}</td>
                                            <td>{{ $acara->kompetisi->nama }}</td>
                                            <td>{{ $acara->nomor_lomba }} - {{ $acara->nama }}</td>
                                            <td><span class="status bayar">Rp.{{ number_format($acara->harga, 2, ',', '.') }}</span></td>
                                            <td style="display:flex">
                                                <button onclick="payButton(this)" data-token="{{ $acara->pivot->snap_token }}" data-id="{{ $acara->pivot->id }}" class="button-gap pay-button"><i class='bx bx-xs bxs-credit-card'></i></button>
                                                <form action="{{ route('dashboard.tagihan.destroy', $acara->pivot->id) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <a onclick="return confirm('Apakah kamu yakin ingin menghapus? ')"><button class="button-red button-gap"><i class='bx bx-xs bxs-trash' ></i></button></a>
                                                </form>
                                            </td>
                                        </tr>
                                @endforeach
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div class="total-price">
                    @if ($totalHarga != 0)
                        <p><b>Total: </b>Rp.{{ number_format($totalHarga, 2, ',', '.') }}</p>
                        <button class="pay-all-button" onclick="payAll()">Bayar Semua</button>
                    @endif
                </div>
                <div class="pagination">
                    <button class="prev" disabled>Sebelumnya</button>
                    <div class="page-numbers"></div>
                    <button class="next" disabled>Selanjutnya</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        
        function payButton(element) {
            var transactionToken = element.getAttribute('data-token');
            var transactionId = element.getAttribute('data-id');

            window.snap.pay(transactionToken, {
            onSuccess: function(result){
              /* You may add your own implementation here */
              window.location.href = "/dashboard/tagihan/" + transactionId;
            },
            onPending: function(result){
              /* You may add your own implementation here */
              alert("Menunggu pembayaran"); console.log(result);
            },
            onError: function(result){
              /* You may add your own implementation here */
              alert("Pembayaran gagal!"); console.log(result);
            }
          });
        }

        function payAll() {
            window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
              /* You may add your own implementation here */
              window.location.href = "/dashboard/tagihan/bayar-semua";
            },
            onPending: function(result){
              /* You may add your own implementation here */
              alert("Menunggu pembayaran"); console.log(result);
            },
            onError: function(result){
              /* You may add your own implementation here */
              alert("Pembayaran gagal!"); console.log(result);
            }
          });
        }
    </script>

@endsection