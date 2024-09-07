<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Laravel') }} </title>
    
    <!-- MIDTRANS -->
    <script type="text/javascript"
      src="https://app.midtrans.com/snap/snap.js"
      data-client-key={{ config('midtrans.client_key') }}></script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->

    <link rel="icon" href="{{ asset('assets/img/LogoWebHD.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <!-- ICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.js" integrity="sha512-8Z5++K1rB3U+USaLKG6oO8uWWBhdYsM3hmdirnOEWp8h2B1aOikj5zBzlXs8QOrvY9OxEnD2QDkbSKKpfqcIWw==" crossorigin="anonymous"></script>
    @vite(['resources/css/sidebar.css', 'resources/css/dashboard.css',
           'resources/js/sidebar.js', 'resources/js/dashboard.js', 'resources/css/profile.css'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Inter", sans-serif;
        }

        body, html, .overlay {
            min-height: 100%;
        }

        body {
            display: flex;
            background-color: #eee;
        }

        body .sidebar, .menu-btn{
            background-color: #fff;
        }

        body .main-content .all-card{
            background-color: #fff;
        }

        .w100 {
            width: calc(100% - 40px);
        }

        .w50 {
            width: calc(50% - 40px);
        }

        .w768 {
            max-width: 768px;
        }

        .mtopbot {
            margin: 10px 0 10px 0;
        }

        .smaller {
            font-size: smaller
        }

        @media (max-width: 450px) {
            .main-content, .top-container, .bottom-container {
                font-size: 0.9em;
            }

            button {
                font-size: 0.8em;
            }
        }
    </style>
    @yield('style')
</head>
<body>
    @include('components.dashboard-sidebar')
    @yield('content')
</body>
</html>
