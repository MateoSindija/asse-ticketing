<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">



<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="
        https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js
        "></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @stack('head')

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/sass/modals.scss'])
</head>

<body>
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>
</body>

</html>
