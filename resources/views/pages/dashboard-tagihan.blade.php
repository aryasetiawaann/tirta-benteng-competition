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
                        {{-- INI BUAT PENGUMUMAN --}}
                        <h4 style="color: green">Informasi: Untuk pembayaran silahkan kontak melalui <a href="tel:+6281311384000">081311384000</a> - April</h4>
                    </div>
                </div>
            </div>
        </div>
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
                        @php $counter = 1; @endphp
                            @foreach ($atlets as $atlet)
                                @foreach ($atlet->acara as $acara)
                                @if($acara->pivot->status_pembayaran == "Menunggu")
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td>{{ $atlet->name }}</td>
                                    <td>{{ $acara->kompetisi->nama }}</td>
                                    <td>{{ $acara->nomor_lomba }} - {{ $acara->nama }}</td>
                                    <td><span class="status bayar">Rp{{ number_format($acara->harga, 2, ',', '.') }}</span></td>
                                    <td>
                                        <div class="actions">
                                            {{-- Hapus Style buat unable lagi --}}
                                            <button onclick="payButton(this)" data-id="{{ $acara->pivot->id }}" data-harga="{{ $acara->harga }}" class="button-gap pay-button" data-tooltip="Bayar" style='display:none;'><i class='bx bx-xs bxs-credit-card'></i></button>
                                            <form action="{{ route('dashboard.tagihan.destroy', $acara->pivot->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <a onclick="return confirm('Apakah kamu yakin ingin menghapus? ')"><button class="button-red button-gap" data-tooltip="Hapus Tagihan"><i class='bx bx-xs bxs-trash' ></i></button></a>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            @endforeach
                        @endif
                    </tbody>
                </table>
                {{-- Hapus Style buat unable lagi --}}
                <div class="total-price" style='display:none;'>
                    @if ($totalHarga != 0)
                        <p><b>Total: </b>Rp{{ number_format($totalHarga, 2, ',', '.') }}</p>
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
            var paymentId = element.getAttribute('data-id');
            var paymentPrice = element.getAttribute('data-harga');

            fetch("{{ route('dashboard.tagihan.generateSnapToken') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ payment_id: paymentId , payment_price: paymentPrice})
            })
            .then(response => response.json())
            .then(data => {
                if(data.snap_token){
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            window.location.href = "/dashboard/riwayat-pembayaran";
                        },
                        onPending: function(result){
                        },
                        onError: function(result){
                        }
                    });
                } else {
                    alert('Gagal mendapatkan token pembayaran.');
                }
            })
            .catch(error => {
                // console.log('Payment ID:', paymentId);
                // console.log('Payment Price:', paymentPrice);
                // console.error('Error:', error);
                alert('Harap untuk cek pesan masuk pada email anda.');
            });
        }

        function payAll() {
            window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
              window.location.href = "/dashboard/riwayat-pembayaran";
            },
            onPending: function(result){
              /* You may add your own implementation here */
            },
            onError: function(result){
              /* You may add your own implementation here */
            }
          });
        }
    </script>

@endsection