<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>HomeGrowR</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css')}}">
        <script defer src="{{ mix('js/app.js') }}"></script>


    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>
    </body>
</html>
