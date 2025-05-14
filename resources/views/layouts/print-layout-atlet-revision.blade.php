<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar Atlet Perlu Revisi Dokumen</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table, th, td {
            border: 1px solid #333;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 8px;
            text-align: center;
        }

        td {
            padding: 8px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>

    <h1>Daftar Atlet</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Club</th>
                <th>Email</th>
                <th>No. HP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atlets as $atlet)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $atlet->name }}</td>
                    <td>{{ $atlet->user->club }}</td>
                    <td>{{ $atlet->user->email }}</td>
                    <td>{{ $atlet->user->phone ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
    </div>

</body>
</html>
