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

<div id="lava">

    <dashboard></dashboard>

</div>

<script>
    window.config = @json(Lava::getActivePanel()->getConfig())
</script>
<script>
    window.user = @json(\Illuminate\Support\Facades\Auth::user() ?? [])
</script>

<script src="{{ mix('app.js', 'lava') }}"></script>

<script>
    window.Lava = new CreateLavaApp();
</script>

@foreach( Lava::getActivePanel()->getScripts() as $script )
    <script src="{{ \Illuminate\Support\Str::of($script)->startsWith(['http', 'https']) ? $script : asset('lava/' . $script) }}"></script>
@endforeach

<script>

    console.log('config ===> ', window.config)
    console.log('user ===> ', window.user)
    window.Lava.start();

</script>

</body>
</html>
