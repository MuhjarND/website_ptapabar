@extends('layouts.public')
@section('title', 'Beranda')
@section('body-class', 'home-pa-layout')

@push('styles')
<link href="{{ asset('assets/css/public-home.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/public-pa-layout.css') }}" rel="stylesheet">
@if($homeLayout !== null)
<link href="{{ asset('assets/css/public-home-layout.css') }}" rel="stylesheet">
@endif
@endpush

@section('content')
    @if(!empty($homePopup['image']))
        <dialog class="home-image-popup" id="homeImagePopup" aria-label="Informasi PTA Papua Barat">
            <form method="dialog">
                <button type="submit" aria-label="Tutup pengumuman" autofocus>&times;</button>
            </form>
            <img src="{{ asset('storage/' . $homePopup['image']) }}" alt="{{ $homePopup['alt'] ?? 'Informasi PTA Papua Barat' }}">
        </dialog>
    @endif
    @if($homeLayout !== null)
        @include('public.partials.home.layout-grid')
    @else
        @include('public.partials.home.hero-slider')
        @include('public.partials.home.welcome-strip')
        @include('public.partials.home.quick-links')
        <div class="home-publications">
            <div class="container home-publications-board" data-home-publications>
                @include('public.partials.home.latest-news', ['publicationPanel' => true])
                @include('public.partials.home.announcements', ['publicationPart' => 'announcement', 'publicationPanel' => true])
            </div>
            <div class="container home-publications-board" data-home-publications>
                @include('public.partials.home.religious-court-news', ['publicationPanel' => true])
                @include('public.partials.home.announcements', ['publicationPart' => 'article', 'publicationPanel' => true])
            </div>
        </div>
        @include('public.partials.home.agendas')
        @include('public.partials.home.gallery-showcase')
        @include('public.partials.home.integrity-zones')
        @include('public.partials.home.surveys')
        @include('public.partials.home.applications')
    @endif
@endsection

@push('scripts')
<script src="{{ asset('assets/js/public-home.js') }}"></script>
@endpush
