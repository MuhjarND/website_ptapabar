<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $publicSiteSettings['site_description'] }}">
    <title>@yield('title', 'Beranda') - {{ $publicSiteSettings['site_name'] }}</title>
    <link rel="icon" type="image/webp" href="{{ asset('logo-pta-160.webp') }}">
    @stack('preloads')
    <link href="{{ asset('assets/css/public-site.css') }}?v={{ filemtime(public_path('assets/css/public-site.css')) }}" rel="stylesheet">
    @stack('styles')
    <link href="{{ asset('assets/css/public-usability.css') }}?v={{ filemtime(public_path('assets/css/public-usability.css')) }}" rel="stylesheet">
    @php($iconStyles = asset('assets/vendor/fontawesome-6.4.0/css/all.min.css'))
    <link href="{{ $iconStyles }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ $iconStyles }}" rel="stylesheet"></noscript>
</head>
<body class="@yield('body-class')">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    @include('public.partials.site-chrome')

    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    @include('public.partials.footer')
    @include('public.partials.accessibility-widgets')

    <script src="{{ asset('assets/js/public-layout.js') }}?v={{ filemtime(public_path('assets/js/public-layout.js')) }}" defer></script>
    <script src="{{ asset('assets/js/public-usability.js') }}?v={{ filemtime(public_path('assets/js/public-usability.js')) }}" defer></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
