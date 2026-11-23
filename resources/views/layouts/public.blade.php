<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $publicSiteSettings['site_description'] }}">
    <title>@yield('title', 'Beranda') - {{ $publicSiteSettings['site_name'] }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/public-site.css') }}" rel="stylesheet">
    @stack('styles')
    <link href="{{ asset('assets/css/public-usability.css') }}" rel="stylesheet">
</head>
<body class="@yield('body-class')">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    @include('public.partials.site-chrome')

    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    @include('public.partials.footer')
    @include('public.partials.accessibility-widgets')

    <script src="{{ asset('assets/js/public-layout.js') }}"></script>
    <script src="{{ asset('assets/js/public-usability.js') }}" defer></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
