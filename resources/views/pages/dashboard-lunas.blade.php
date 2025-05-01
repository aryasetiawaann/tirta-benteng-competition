@extends('layouts.dashboard-layout')
@section('title', 'Daftar Pembayaran')

@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon dark-green">
                        <i class="icon ph-bold ph-wallet"></i>
                    </div>
                    <div class="card-content">
                        <h1>Riwayat Pembayaran</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-warning" style="font-size: 18px; margin-bottom: 0px;">
            <strong>Perhatian! Jika sudah melakukan pembayaran tidak dapat melakukan refund dan mengganti nomor acara. Dimohon di cek kembali sebelum melakukan pembayaran</strong>
        </div>

        <div class="bottom-container">
            <section class="all-container all-card w100 community-card">
                <header class="divider flex community-card-header">
                    <div class="community-card-icon-container">
                        <i class='bx bxl-whatsapp community-card-icon'></i>
                        <div class="community-card-text">
                            <h2>Yuk, Gabung Grup Peserta!</h2>
                            <p>Dapatkan informasi terbaru terkait perlombaan.</p>
                        </div>
                    </div>
                    <a href="https://chat.whatsapp.com/Bf8Cqva3vYgI04hsEA5RwW" target="_blank" class="button community-card-button">
                        Join Grup WhatsApp
                    </a>
                </header>
            </section>

            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Riwayat Pembayaran</h1>
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
                        riwayat
                    </label>
                    <input type="text" id="search" placeholder="Cari...">
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>Metode</th>
                                    <th>Detail</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>                            
                                @if ($pembayaran->isEmpty())
                                <tr><td colspan="5" style="text-align:center;">Belum ada riwayat pembayaran</td></tr>
                                @else
                                @foreach ($pembayaran as $bayar)
                                    <tr>
                                        <td>{{ $loop->iteration}}</td>
                                        <td>{{ $bayar->midtrans_order_id }}</td>
                                        <td>{{ $bayar->metode_pembayaran }}</td>
                                        <td>
                                            
                                            @foreach ($bayar->groupedPeserta as $atletName => $pesertaList)
                                                <b>{{ $atletName }}</b> <br> 
                                                @foreach ($pesertaList as $peserta)
                                                    - {{ $peserta->getAcara->nomor_lomba }} {{ $peserta->getAcara->nama }} 
                                                    {{ $peserta->getAcara->grup }} <br>
                                                @endforeach
                                                <br>
                                            @endforeach
                                        </td>
                                        <td><span class="status bayar">Rp.{{ number_format($bayar->total_harga, 2, ',', '.') }}</span></td>
                                        @if ($bayar->status == 'Menunggu')
                                            <td>
                                                <span class="status" style="background-color: rgb(248, 164, 9)">{{ $bayar->status }}</span>
                                            </td>
                                            <td><button class="payButton" data-token={{ $bayar->snap_token }}>Bayar</button></td>
                                        @elseif ($bayar->status == 'Berhasil')
                                            <td><span class="status bayar" >{{ $bayar->status }}</span></td>
                                        @elseif ($bayar->status == 'Gagal')
                                            <td><span class="status" style="background-color: rgb(224, 17, 17)">{{ $bayar->status }}</span></td>
                                        @elseif ($bayar->status == 'Kedaluarsa')
                                            <td><span class="status" style="background-color: rgb(224, 17, 17)" >{{ $bayar->status }}</span></td>
                                        @endif
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
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
        document.addEventListener("DOMContentLoaded", function() {
            const payButtons = document.querySelectorAll(".payButton");

            payButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const snapToken = this.getAttribute("data-token");

                    if (!snapToken) {
                        alert("Token pembayaran tidak tersedia!");
                        return;
                    }

                    snap.pay(snapToken, {
                        onSuccess: function(result) {
                            // Redirect ke halaman sukses setelah pembayaran berhasil
                            window.location.href = "/dashboard/riwayat-pembayaran";
                        },
                        onPending: function(result) {
                            // Redirect ke halaman menunggu jika pembayaran masih pending
                            window.location.href = "/dashboard/riwayat-pembayaran";
                        },
                        onError: function(result) {
                            // Redirect ke halaman gagal jika ada kesalahan saat pembayaran
                            window.location.href = "/dashboard/riwayat-pembayaran";
                        },
                        onClose: function() {
                            // Jika pengguna menutup tanpa menyelesaikan pembayaran, arahkan ke halaman tertentu
                            window.location.href = "/dashboard/riwayat-pembayaran";
                        }
                    });
                });
            });
        });
    </script>
@endsection