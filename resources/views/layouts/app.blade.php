<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CV Atmobrass Jaya') — Atmobrass</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    @include('partials.navbar')
    <main style="padding-top:64px;">
        @include('partials.flash')
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>