<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
     <!-- ICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.js" integrity="sha512-8Z5++K1rB3U+USaLKG6oO8uWWBhdYsM3hmdirnOEWp8h2B1aOikj5zBzlXs8QOrvY9OxEnD2QDkbSKKpfqcIWw==" crossorigin="anonymous"></script>
    @vite(['resources/css/sidebar.css', 'resources/css/dashboard.css', 'resources/js/sidebar.js'])
    <style>
        body {
            display: flex;
            background-color: #eee;
        }
    </style>

    <title>Dashboard</title>
</head>
<body>
        @include('components.dashboard-sidebar')
        <div class="main-content">
            <div class="top-container">
                <!-- Cards Content -->
                <div class="top-card">
                    <div class="card-icon">
                        <i class="bx bxs-grid-alt"></i>
                    </div>
                    <div class="card-content">
                        <p>Ini card</p>
                        <h1>100</h1>
                    </div>
                </div>
            <div class="top-card">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <p>Ini card</p>
                    <h1>100</h1>
                </div>
            </div>
            <div class="top-card">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <p>Ini card</p>
                    <h1>100</h1>
                </div>
            </div>
            <div class="top-card">
                <div class="card-icon">
                    <i class="bx bxs-grid-alt"></i>
                </div>
                <div class="card-content">
                    <p>Ini card</p>
                    <h1>100</h1>
                </div>
            </div>
        </div>
        <div class="bottom-container">
                <div class="left-container">
                    <div class="table-container">
                        <div class="table-header">
                            <h1>Daftar Peserta</h1>
                        </div>
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
                <div class="right-container">
                    <div class="activity-container">
                        <div class="activity-header">
                            <h1>Aktivitas terbaru</h1>
                        </div>
                        <div class="activity-content">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut in porro dolorum. Minus quasi beatae nam eius neque illo quaerat, tempora, porro debitis libero sunt voluptate ratione harum facere temporibus.</p> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>