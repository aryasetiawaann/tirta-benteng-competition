<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Header Print</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amaranth:ital,wght@0,400;0,700;1,400;1,700&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        *{
            margin: 0;
            text-transform: uppercase;
            font-family: "Montserrat", sans-serif;
        }

        nav {
            padding: 0 60px; 
            padding-top: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-kiri {
            width: 10%;
        }

        .center{
            width: 55%;
            font-weight: bold;
            text-align: center;
        }

        .center hr{
            margin: 0 auto;
            margin-top: 15px;
            width: 25%;
            border: 1.5px solid grey;
        }

        .logo-kanan {
            width: 10%;
        }

    </style>
</head>
<body>
    <nav>
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Club" class="logo-kiri">
        <div class="center">
            <p>KEJUARAAN RENANG TIRTA BENTENG SWIMMING FUN COMPETITION KOLAM RENANG TIRTA ARYA KAMUNING YONIF AK/203</p>
            <p>01 JUNI 2024</p>
            <hr>
        </div>
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Partner" class="logo-kanan">
    </nav>
</body>
</html>