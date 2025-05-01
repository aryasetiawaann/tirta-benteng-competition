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
        <div class="alert alert-warning" style="font-size: 18px; margin-bottom: 0px;">
            <strong>Perhatian! Jika sudah melakukan pembayaran tidak dapat melakukan refund dan mengganti nomor acara. Dimohon di cek kembali sebelum melakukan pembayaran</strong>
        </div>

        <div class="bottom-container">
            <section class="all-container all-card w100 community-card">
                <header class="divider flex community-card-header">
                    <div class="community-card-icon-container">
                        <i class='bx bxl-whatsapp community-card-icon'></i>
                        <div class="community-card-text">
                            <h2>Punya Pertanyaan?</h2>
                            <p>Gabung grup WhatsApp kami untuk bertanya atau berdiskusi dengan peserta lain.</p>
                        </div>
                    </div>
                    <a href="https://chat.whatsapp.com/Bf8Cqva3vYgI04hsEA5RwW" target="_blank" class="button community-card-button">
                        Tanya di WhatsApp
                    </a>
                </header>
            </section>

            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Tagihan</h1>
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
                        tagihan
                    </label>
                    <input type="text" id="search" placeholder="Cari...">
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="" id="select_all_id"></th>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Kompetisi</th>
                                    <th>Nomor Lomba</th>
                                    <th>Jumlah Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($pesertas->isEmpty())
                                    <tr><td colspan="6" style="text-align:center;">Belum ada tagihan</td></tr>
                                @else
                                    @foreach ($pesertas as $peserta)
                                        <tr>
                                            <td><input type="checkbox" name="ids" class="checkbox_ids" value="{{ $peserta->id }}" data-harga={{ $peserta->getAcara->harga }}></td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $peserta->getAtlet->name }}</td>
                                            <td>{{ $peserta->getAcara->kompetisi->nama }}</td>
                                            <td>{{ $peserta->getAcara->nomor_lomba }} - {{ $peserta->getAcara->nama }}</td>
                                            <td><span class="status bayar">Rp{{ number_format($peserta->getAcara->harga, 2, ',', '.') }}</span></td>
                                            <td>
                                                <div class="actions">
                                                    <form action="{{ route('dashboard.tagihan.destroy', $peserta->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <a onclick="return confirm('Apakah kamu yakin ingin menghapus? ')"><button class="button-red button-gap" data-tooltip="Hapus Tagihan"><i class='bx bx-xs bxs-trash' ></i></button></a>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="total-price">
                        <p><b>Total Pembayaran: </b><span id="total_harga">Rp.0,00</span></p>
                        <button class="pay-all-button" id="payButton" style="display: none">Bayar</button>
                    </div>
                    <div class="pagination">
                        <button class="prev" disabled>Sebelumnya</button>
                        <div class="page-numbers"></div>
                        <button class="next" disabled>Selanjutnya</button>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectAllCheckbox = document.getElementById("select_all_id");
            const checkboxes = document.querySelectorAll(".checkbox_ids");
            const totalHargaElement = document.getElementById("total_harga");
            const payButton = document.getElementById("payButton");

            selectAllCheckbox.addEventListener("click", function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                    updateTotalHarga();
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", updateTotalHarga);
            });

            payButton.addEventListener("click", function(){
                let selectedIds = [];
                let checkedCheckboxes = document.querySelectorAll(".checkbox_ids:checked");

                checkedCheckboxes.forEach(checkbox => {
                    selectedIds.push(checkbox.value);
                });

                if (selectedIds.length === 0) {
                    alert("Atlets Ids is Empty");
                    return;
                }

                fetch("{{ route('dashboard.tagihan.generateSnapToken') }}" ,{
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ peserta_ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.snap_token) {
                        snap.pay(data.snap_token, {
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
                    } else {
                        alert("Gagal mendapatkan token pembayaran.");
                    }
                })
                .catch(error => console.error("Error:", error));
            });


            function updateTotalHarga() {
                let total = 0;
                let checkedCheckboxes = document.querySelectorAll(".checkbox_ids:checked");
                let totalAtlet = checkedCheckboxes.length;

                checkedCheckboxes.forEach(checkbox => {
                    total += parseFloat(checkbox.getAttribute("data-harga"));
                });

                totalHargaElement.textContent = total.toLocaleString("id-ID", { minimumFractionDigits: 2 });

                // Tampilkan tombol bayar jika ada atlet yang dipilih
                if (totalAtlet > 0) {
                    payButton.style.display = "block";
                    payButton.textContent = `Bayar (${totalAtlet})`; // Tambahkan jumlah atlet ke tombol
                } else {
                    payButton.style.display = "none";
                }
            }
        });

    </script>

@endsection