<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    @vite(['resources/css/dashboard.css'])
</head>
<body>

    <div class="sidebar">
        <div class="top">
            <div class="logo">
                <i class="bx bx-swim"></i>
                <span>Lomba Renang</span>
            </div>
            <i class="bx bx-menu" id="btn"></i>
        </div>
        <div class="user">
            <img src="{{ asset('assets/img/Burger.png') }}" alt="profile" class="user-img">
            <div>
                <p class="bold">John Doe</p>
                <p>User</p>
            </div>
        </div>
        <ul>
            <li>
                <a href="#"> 
                    <i class="bx bxs-grid-alt"></i>
                    <span class="nav-item">Dashboard</span>
                </a>
                <span class="tooltip">Dashboard</span>
            </li>
            <li>
                <a href="#"> 
                    <i class='bx bx-body'></i>
                    <span class="nav-item">Atlet Saya</span>
                </a>
                <span class="tooltip">Atlet Saya</span>
            </li>
            <li class="dropdown-btn">
                <a href="#"> 
                    <i class="bx bxs-trophy"></i>
                    <span class="nav-item">Kompetisi</span>
                    <i class="bx bx-chevron-down"></i>
                </a>
                <div class="dropdown-container">
                    <a href="#">Daftar Kompetisi</a>
                    <a href="#">Kompetisi Saya</a>
                </div>
                <span class="tooltip">Kompetisi</span>
            </li>
            <li>
                <a href="#"> 
                    <i class="bx bxs-wallet"></i>
                    <span class="nav-item">Pembayaran</span>
                </a>
                <span class="tooltip">Pembayaran</span>
            </li>
            <li>
                <a href="#"> 
                    <i class="bx bx-book-content"></i>
                    <span class="nav-item">Unduhan</span>
                </a>
                <span class="tooltip">Unduhan</span>
            </li>
            <li>
                <a href="#"> 
                    <i class="bx bx-log-out"></i>
                    <span class="nav-item">Logout</span>
                </a>
                <span class="tooltip">Logout</span>
            </li>                                                
        </ul>
    </div>

    <div class="main-content">
        <div class="container">
            <h1>Daftar Peserta</h1>
            <div class="table-container">
                <label for="entries">Tampilkan
                    <select id="entries" name="entries">
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
                            <th>Status Peserta</th>
                            <th>Status Kompetisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Arya</td>
                            <td>Gadjah Mada Swimming Competition 2024</td>
                            <td>120 - 50m Gaya Dada Putra</td>
                            <td><span class="status waiting">Menunggu</span></td>
                            <td><span class="status registration">Registrasi</span></td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
                <div class="pagination">
                    <button class="prev">Sebelumnya</button>
                    <span class="current-page">1</span>
                    <button class="next">Selanjutnya</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let btn = document.querySelector("#btn");
        let sidebar = document.querySelector(".sidebar");
        let dropdown = document.querySelectorAll(".dropdown-btn");

        btn.onclick = function() {
            sidebar.classList.toggle("active");
        };

        dropdown.forEach(button => {
            button.addEventListener("click", function() {
                this.classList.toggle("active");
                let dropdownContent = this.querySelector(".dropdown-container");
                if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }
            });
        });
    </script>
</body>
</html>
