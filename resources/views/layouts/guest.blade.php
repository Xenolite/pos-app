<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body{
            margin: 0;
            overflow: hidden;
            font-family: 'Figtree', sans-serif;
        }

        .right-side{
            background-image: url('https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=1400');
            background-size: cover;
            background-position: center;
            height: 100vh;
            position: relative;
        }

        .overlay{
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.25);
            top: 0;
            left: 0;
        }

        .welcome-text{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            z-index: 2;
        }

        .logo-text{
            position: absolute;
            bottom: 20px;
            right: 30px;
            color: white;
            z-index: 2;
        }

        @media(max-width: 768px){
            .right-side{
                display: none;
            }
        }
    </style>
</head>

<body>

{{ $slot }}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>