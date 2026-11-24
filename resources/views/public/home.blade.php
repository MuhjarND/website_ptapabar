@extends('layouts.public')
@section('title', 'Beranda')
@section('body-class', 'home-pa-layout')

@push('styles')
<link href="{{ asset('assets/css/public-home.css') }}?v={{ filemtime(public_path('assets/css/public-home.css')) }}" rel="stylesheet">
<link href="{{ asset('assets/css/public-pa-layout.css') }}?v={{ filemtime(public_path('assets/css/public-pa-layout.css')) }}" rel="stylesheet">
@endpush

@section('content')
    @if(!empty($homePopup['image']))
        <dialog class="home-image-popup" id="homeImagePopup" aria-label="Informasi PTA Papua Barat">
            <form method="dialog">
                <button type="submit" aria-label="Tutup pengumuman" autofocus>&times;</button>
            </form>
            <img src="{{ \App\Support\HomeImage::url($homePopup['image'], 960) }}" alt="{{ $homePopup['alt'] ?? 'Informasi PTA Papua Barat' }}" decoding="async">
        </dialog>
    @endif
    @include('public.partials.home.hero-slider')
    @include('public.partials.home.welcome-strip')
    @include('public.partials.home.quick-links')
    <div class="home-publications">
        <div class="container home-publications-board" data-home-publications>
            @include('public.partials.home.latest-news')
            @include('public.partials.home.announcements', ['publicationPart' => 'announcement'])
        </div>
        <div class="container home-publications-board" data-home-publications>
            @include('public.partials.home.religious-court-news')
            @include('public.partials.home.announcements', ['publicationPart' => 'article'])
        </div>
        @include('public.partials.home.agendas')
    </div>
    @include('public.partials.home.gallery-showcase')
    @include('public.partials.home.integrity-zones')
    @include('public.partials.home.surveys')
    @include('public.partials.home.applications')
@endsection

@push('scripts')
<script src="{{ asset('assets/js/public-home.js') }}?v={{ filemtime(public_path('assets/js/public-home.js')) }}"></script>
@endpush
