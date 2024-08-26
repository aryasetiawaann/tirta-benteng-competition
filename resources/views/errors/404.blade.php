<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            text-align: center;
            color: #4b4b4b
        }
        h1 {
            font-size: 5em;
            margin: 0;
        }
        p {
            font-size: 1.5em;
            margin: 0;
        }
        a {
            margin-top: 1em;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <h1>404</h1>
    <p>Oops! Halaman yang anda cari tidak ada.</p>
    <a href="{{ url('/') }}">Balik ke Homepage</a>
</body>
</html>
