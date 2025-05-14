<!DOCTYPE html>
<html>

<head>
    <title>Laporan Verifikasi Atlet</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            /* sama seperti contoh */
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #1f2937;
            /* warna heading gelap */
            font-size: 22px;
            margin-bottom: 20px;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #4b5563;
            /* abu-abu gelap */
            line-height: 1.6;
        }

        .highlight {
            color: #1d4ed8;
            /* biru cerah */
            font-weight: bold;
        }

        .success {
            color: #10b981;
            /* hijau untuk status sukses */
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #9ca3af;
            text-align: center;
        }

        .emoji {
            font-size: 18px;
        }

        .link{
            color: #1d4ed8;
            /* biru cerah */
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>Laporan Verifikasi Atlet</h2>

        @if ($jumlah > 0)
            <p>Ada <span class="highlight">{{ $jumlah }}</span> atlet yang menunggu untuk diverifikasi.</p>
            <p>Segera lakukan pengecekan agar proses dapat berjalan lancar. 💪</p>
            <a class="link" href={{route('admin.verifikasi')}}>Klik disini untuk menuju website</a>
        @else
            <p class="success">Hari ini tidak ada atlet yang menunggu verifikasi. <span class="emoji">🎉</span></p>
            <p>Silakan lanjutkan aktivitas lainnya. Semangat terus!</p>
        @endif

        <div class="footer">
            – Dikirim oleh Sistem Otomatis
        </div>
    </div>
</body>

</html>
