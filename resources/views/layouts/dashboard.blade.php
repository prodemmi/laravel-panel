<?php

    session()->push('auth_must_redirect', Route::current()->getName());

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ Lava::getActivePanel()->getRTL() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ Lava::getActivePanel()->name }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('app.css', 'lava') }}">

    {{-- animatecss --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    @foreach( Lava::getActivePanel()->getStyles() as $styles )
        <link rel="stylesheet"
              href="{{ \Illuminate\Support\Str::of($styles)->startsWith(['http', 'https']) ? $styles : asset('lava/' . $styles) }}">
    @endforeach

</head>
<body>

<div id="app">

    <dashboard></dashboard>

</div>

@php($panel = Lava::getActivePanel())

<script>
    window.baseUrl = @json($panel->getBaseUrl())
</script>

<script>
    window.debug = @json($panel->getDebug())
</script>

<script>
    window.user = @json(\Illuminate\Support\Facades\Auth::user() ?? [])
</script>

<script>
    window.license = @json($panel->getLicense());
</script>

<script src="{{ mix('app.js', 'lava') }}"></script>

@foreach( $panel->getScripts() as $script )
    <script src="{{ \Illuminate\Support\Str::of($script)->startsWith('http') ? $script : asset('lava/' . $script) }}"></script>
@endforeach

<script>

    window.Lava = new CreateLavaApp();

</script>

</body>
</html>
