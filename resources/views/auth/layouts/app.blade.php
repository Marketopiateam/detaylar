<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detaylar</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('uploads') }}/store/{{ config('settingConfig.config_icon_image') }}"
        type="image/png">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Extra details for Live View on GitHub Pages -->

    <!-- Icons -->
    <link href="{{ asset('assets') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- otrixweb CSS -->
    <link type="text/css" href="{{ asset('assets') }}/css/otrixweb.css?v=1.0.0" rel="stylesheet">
</head>

<body class="{{ $class ?? '' }}" style="background:#f7f7f7">

    <div class="main-content" style="background:#f7f7f7">
        @yield('content')
    </div>

    <script src="{{ asset('assets') }}/vendor/jquery/dist/jquery.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    @stack('js')

    <!-- otrixweb JS -->
    <script src="{{ asset('assets') }}/js/otrixweb.js?v=1.0.0"></script>
</body>

</html>
