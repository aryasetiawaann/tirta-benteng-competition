
{{-- INI TEMPLATE BUAT PDF YAAA JANGAN DIHAPUS --}}
<html>
    <head>
        <style>
            /** Define the margins of your page **/
            @page {
                margin: 130px 25px 160px 25px;
            }

            header {
                position: fixed;
                top: -140px;
                left: 0px;
                right: 0px;
                height: 130px;

                /** Extra personal styles **/
                text-align: center;
            }

            header p {
                text-align: right;
                font-size: 12px;
                text-transform: uppercase;
                margin-top: 60px;
                margin-right: 80px;
                margin-bottom: 3px;
            }

            header h1 {
                text-transform: capitalize;
                font-size: 13px;
                width: 350px;
                margin: 0 auto;
            }

            footer {
                position: fixed; 
                bottom: -160px; 
                left: 0px; 
                right: 0px;
                height: 150px; 

                /** Extra personal styles **/
                text-align: center;
            }

            .table-footer{
            margin: 0 auto;
            }

            .table-footer tr td {
                width: 200px;
                text-align: center;
            }

            .table-footer tr img {
                width: 70px;
            }

            main {
                font-size: 12px;
            }

            main h2 {
                font-size: 12px;
            }

            main table {
                width: 100%;
                text-align: left;
            }

            .garis {
                border-top: 2px solid black;
            }

            .grup {
                width: 10%;
                text-align: center;
            }
            
            table tbody .grup {
                border: 1px solid black;
            }

            .line {
                width: 10%;
                text-align: center;
            }

            .name, .club {
                width: 30%;
                text-align: left;
            }


            .age {
                width: 10%;
                text-align: center;
            }

            .record, .finals, .place {
                width: 20%;
                text-align: right;
            }

            .record {
                text-align: left;
            }

        </style>
    </head>
    <body>
        <header>
            <p>TIRTA BENTENG SWIMMING CLUB - {{$time}}</p>
            <h1>{{$kompetisi->nama}} - {{ \Carbon\Carbon::parse($kompetisi->waktu_kompetisi)->format('d/m/Y') }} Meet Program</h1>
        </header>

        <footer>
            <table class="table-footer">
                <tr>
                    @if ($kompetisi->logo->count() > 0)
                        @foreach ($kompetisi->logo as $logo)  
                            <td>
                                <img src="{{ public_path($logo->name) }}" alt="logo">
                            </td>
                        @endforeach
                    @else
                        <td>
                            <img src="{{ public_path('assets/img/logo.png') }}" alt="logo">
                        </td>
                    @endif
                </tr>
            </table>
        </footer>

        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            @foreach ($acaras as $acara)
                <h2>Event {{ $acara->nomor_lomba }} {{ $acara->nama }} {{ $acara->grup }}</h2>
                <table>
                    <thead>
                        <tr class="head">
                            <th class="grup">Group</th>
                            <th class="line">Lane</th>
                            <th class="name">Name</th>
                            <th class="age">Age</th>
                            <th class="club">Club</th>
                            <th class="record">Best Record</th>
                            <th class="finals">Finals</th>
                            <th class="place">Place</th>
                        </tr>
                    </thead>
                    <tr>
                        <td colspan="8" class="garis"></td>
                    </tr>
                    <tbody>
                            @foreach($acara->heats as $heatIndex => $heat)
                            <tr>
                                <td style="text-align: left;" colspan="8"><h4>Heat {{ $heatIndex + 1 }} of {{ count($acara->heats) }} Timed Finals</h4></td>
                            </tr>
                            @foreach($heat as $groupKey => $group)
                                @foreach($group as $key => $participant)
                                @if ($participant)
                                    
                                    <tr>
                                        @if (($groupKey + 1) % 2 != 0)
                                            @if ($key == 0)
                                                <td rowspan="4" class="grup">A</td>
                                            @endif
                                        @else
                                            @if ($key == 0)
                                                <td rowspan="4" class="grup">B</td>
                                            @endif
                                        @endif
                                        <td class="line">{{ $key+1 }}</td>
                                        <td class="name">{{ $participant['name'] }}</td>
                                        <td class="age">{{ now()->diffInYears(\Carbon\Carbon::parse($participant['umur'])) }}</td>
                                        <td class="club">{{ $participant['club']}}</td>
                                        @if ($participant['track_record'] == 999)
                                            <td class="record">NT</td>
                                        @else
                                            <td class="record">{{ sprintf('%02d:%02d.%02d', 
                                                floor($participant['track_record'] / 60),  // Menit
                                                floor(fmod($participant['track_record'], 60)),  // Detik
                                                ceil(($participant['track_record'] - floor($participant['track_record'])) * 100)  // Milidetik
                                            ) }}</td>
                                        @endif
                                        <td class="finals">____________</td>
                                        <td class="place">____________</td>
                                    </tr>
                                @else
                                    <tr>
                                        @if (($groupKey + 1) % 2 != 0)
                                            @if ($key == 0)
                                                <td rowspan="4" class="grup">A</td>
                                            @endif
                                        @else
                                            @if ($key == 0)
                                                <td rowspan="4" class="grup">B</td>
                                            @endif
                                        @endif
                                        <td class="line">{{ $key+1 }}</td>
                                        <td class="name">&nbsp;</td>
                                        <td class="age">&nbsp;</td>
                                        <td class="club">&nbsp;</td>
                                        <td class="record">&nbsp;</td>
                                        <td class="finals">&nbsp;</td>
                                        <td class="place">&nbsp;</td>
                                    </tr>
                                @endif
                                @endforeach
                            @endforeach
                            @endforeach
                    </tbody>
                </table>
            @endforeach
            <table>

            </table>
        </main>
    </body>
</html>
