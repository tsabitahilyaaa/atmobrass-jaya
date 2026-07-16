<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Atmobrass</title>
    <link rel="icon" href="{{ asset('images/logo/logo.png') }}" />
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen" style="overflow-x:hidden;">
    @include('partials.admin-sidebar')
    <div class="md:ml-64 pt-14 md:pt-0" style="min-height:100vh;">
        <div class="p-4 sm:p-8">
            @include('partials.flash')
            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>
</html>