
{{-- INI TEMPLATE BUAT PDF YAAA JANGAN DIHAPUS --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Table</title>
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Amaranth:ital,wght@0,400;0,700;1,400;1,700&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        *{
            margin: 0;
            text-transform: uppercase;
            font-family: "Montserrat", sans-serif;
        }

        body {
            margin: 0 80px;
        }

        .top-table{
            background-color: #008DDA;
            margin-bottom: 0;
            color: white;
            display: flex;
            align-items: center;
            padding: 5px;
            font-size: 13px;
            text-transform: uppercase;
        }

        .top-table .no-acara{
            margin-right: 20px;
            width: min-content;
            text-align: center;
            font-weight: bold
        }

        .top-table .nama-acara {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            font-family: monospace;
        }
        th {
            background-color: #f2f2f2;
        }
        .ser {
            width: 5%;
        }
        .grup, .lint {
            width: 5%;
            text-align: center;
        }
        .nama, .asal {
            width: 30%;
        }
        .qtf, .hasil {
            width: 15%;
            text-align: center;
        }

        .container {
            margin-top: 250px;
        }

        header {
            display: table;
        }

        nav {
            padding: 0 90px; 
            padding-top: 15px;
            display: table-row;
        }
        
        .logo-kiri {
            display: table-cell;
            width: 15%;
            text-align: center;
            
        }
        
        .logo-kiri img {
           width: 100px;
        }

        .center{
            display: table-cell;
            width: 25%;
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
            display: table-cell;
            width: 15%;
            text-align: center;
        }

        .logo-kanan img {
           width: 100px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo-kiri">
                <img src="{{ public_path('assets/img/logo.png') }}" alt="Logo Club" >
            </div>
            <div class="center">
                <p>KEJUARAAN RENANG TIRTA BENTENG SWIMMING FUN COMPETITION KOLAM RENANG TIRTA ARYA KAMUNING YONIF AK/203</p>
                <p>01 JUNI 2024</p>
                <hr>
            </div>
            <div class="logo-kanan">
                <img src="{{ public_path('assets/img/logo.png') }}" alt="Logo Partner" >
            </div>
        </nav>
    </header>
    <div class="container">

        <div class="top-table">
            <p class="no-acara">ACARA 101</p>
            <p class="nama-acara">25 Meter papan gaya dada ku vb putra</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="ser">SER</th>
                    <th class="grup">GRUP</th>
                    <th class="lint">LINT</th>
                    <th class="nama">NAMA</th>
                    <th class="asal">ASAL SEKOLAH / KLUB</th>
                    <th class="qtf">QTF</th>
                    <th class="hasil">HASIL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="9" class="ser">1</td>
                    <td rowspan="4" class="grup">A</td>
                    <td class="lint">1</td>
                    <td class="nama">MUHAMMAD ABRISHAN KALID KARIM</td>
                    <td class="asal">SAHABAT AQUATIC CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">2</td>
                    <td class="nama">DYLANDA RASA RIKMANAN</td>
                    <td class="asal">ANDESIT.SC</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">3</td>
                    <td class="nama">ATALLAH NAUFAL TRI HARTANTO</td>
                    <td class="asal">CSC (CAPTAIN SWIMMING CLUB)</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">4</td>
                    <td class="nama">NICHOLAS JAYDEN DJONAES</td>
                    <td class="asal">SAHABAT AQUATIC CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td rowspan="5" class="grup">B</td>
                    <td class="lint">1</td>
                    <td class="nama">YUSRIFKY MUIZDIN ANWAR</td>
                    <td class="asal">CSC (CAPTAIN SWIMMING CLUB)</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">2</td>
                    <td class="nama">NAGRADA TOGAR</td>
                    <td class="asal">DOLPHIN SWIMMING CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">3</td>
                    <td class="nama">IRA NASYA ALFARIZQI</td>
                    <td class="asal">ELSA NAUSTION SWIMMING SCHOOL</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">4</td>
                    <td class="nama">PRINCE ALEXANDRE GABRIEL KAMBEY</td>
                    <td class="asal">LIV CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">5</td>
                    <td class="nama">FAHREZA</td>
                    <td class="asal">PANSER SWIMMING CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td rowspan="9" class="ser">1</td>
                    <td rowspan="4" class="grup">A</td>
                    <td class="lint">1</td>
                    <td class="nama">MUHAMMAD ABRISHAN KALID KARIM</td>
                    <td class="asal">SAHABAT AQUATIC CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">2</td>
                    <td class="nama">DYLANDA RASA RIKMANAN</td>
                    <td class="asal">ANDESIT.SC</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">3</td>
                    <td class="nama">ATALLAH NAUFAL TRI HARTANTO</td>
                    <td class="asal">CSC (CAPTAIN SWIMMING CLUB)</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">4</td>
                    <td class="nama">NICHOLAS JAYDEN DJONAES</td>
                    <td class="asal">SAHABAT AQUATIC CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td rowspan="5" class="grup">B</td>
                    <td class="lint">1</td>
                    <td class="nama">YUSRIFKY MUIZDIN ANWAR</td>
                    <td class="asal">CSC (CAPTAIN SWIMMING CLUB)</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">2</td>
                    <td class="nama">NAGRADA TOGAR</td>
                    <td class="asal">DOLPHIN SWIMMING CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">3</td>
                    <td class="nama">IRA NASYA ALFARIZQI</td>
                    <td class="asal">ELSA NAUSTION SWIMMING SCHOOL</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">4</td>
                    <td class="nama">PRINCE ALEXANDRE GABRIEL KAMBEY</td>
                    <td class="asal">LIV CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
                <tr>
                    <td class="lint">5</td>
                    <td class="nama">FAHREZA</td>
                    <td class="asal">PANSER SWIMMING CLUB</td>
                    <td class="qtf"></td>
                    <td class="hasil"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
