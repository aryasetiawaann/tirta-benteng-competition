@extends('admin.admin-dashboard-layout')

@section('content')
    <div class="main-content">
        <div class="top-container">
            <div class="top-card all-card flex">
                <div class="card-left">
                    <div class="card-icon dark-green">
                        <i class="icon ph-bold ph-wallet"></i>
                    </div>
                    <div class="card-content">
                        <h1>Pembayaran</h1>

                    </div>  
                </div>
            </div>
        </div>

        <div class="bottom-container">

            <section class="all-container all-card w100">
                <header class="divider flex">
                    <h1>Daftar Pembayaran</h1>
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
                        Pembayaran
                    </label>
                    <input type="text" id="search" placeholder="Cari...">
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="" id="select_all_id"></th>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Club</th>
                                    <th>Email</th>
                                    <th>Kompetisi</th>
                                    <th>Nomor Lomba</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($pesertas->isEmpty())
                                    <tr><td colspan="7" style="text-align:center;">Belum ada pembayaran yang tersedia</td></tr>
                                @else
                                    @foreach ($pesertas as $peserta)
                                        <tr>
                                            <td><input type="checkbox" name="ids" class="checkbox_ids" value="{{ $peserta->id }}"></td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $peserta->getAtlet->name }}</td>
                                            <td>{{ $peserta->getAtlet->user->club }}</td>
                                            <td>{{ $peserta->getAtlet->user->email }}</td>
                                            <td>{{ $peserta->getAcara->kompetisi->nama }}</td>
                                            <td>{{ $peserta->getAcara->nomor_lomba }} - {{ $peserta->getAcara->nama }}</td>
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
                        <button class="pay-all-button" id="updateButton" style="display: none">Selesai</button>
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
            const updateButton = document.getElementById("updateButton");

            selectAllCheckbox.addEventListener("click", function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                    updateTotalAtlet();
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", updateTotalAtlet);
            });

            updateButton.addEventListener("click", function(){
                let selectedIds = [];
                let checkedCheckboxes = document.querySelectorAll(".checkbox_ids:checked");

                checkedCheckboxes.forEach(checkbox => {
                    selectedIds.push(checkbox.value);
                });

                if (selectedIds.length === 0) {
                    alert("Atlets Ids is Empty");
                    return;
                }

                const confirmUpdate = confirm("Apakah Anda yakin ingin mengubah status pembayaran peserta yang dipilih?");
                if (!confirmUpdate) {
                    return; // Batal update jika user tidak menyetujui
                }

                fetch("{{ route('admin.payment.update') }}" ,{
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ peserta_ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); // jika ingin refresh data setelah update
                })
                .catch(error => console.error("Error:", error));
            });


            function updateTotalAtlet() {
                let checkedCheckboxes = document.querySelectorAll(".checkbox_ids:checked");
                let totalAtlet = checkedCheckboxes.length;


                // Tampilkan tombol bayar jika ada atlet yang dipilih
                if (totalAtlet > 0) {
                    updateButton.style.display = "block";
                    updateButton.textContent = `Selesai (${totalAtlet})`; // Tambahkan jumlah atlet ke tombol
                } else {
                    updateButton.style.display = "none";
                }
            }
        });

    </script>

@endsection