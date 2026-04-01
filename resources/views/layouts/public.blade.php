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
</head>
<body>
    @include('public.partials.site-chrome')

    @yield('content')

    @include('public.partials.footer')
    @include('public.partials.accessibility-widgets')

    <script src="{{ asset('assets/js/public-layout.js') }}"></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
