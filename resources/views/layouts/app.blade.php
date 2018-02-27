<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">

    <script type="text/javascript" src="{{ mix('js/moment.min.js') }}"></script>

</head>
<body class="mdc-typography">

    <noscript style="position: absolute; width: 100%;   text-align: center;">
        <h3>Javascript is required to run this application. Please enable it in your browser.</h3>
    </noscript>

    <div id="app"></div>

    <!-- Scripts -->
    <script src="{{ mix('js/index.js') }}"></script>

</body>
</html>
