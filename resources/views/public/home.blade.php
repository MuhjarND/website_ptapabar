@extends('layouts.public')
@section('title', 'Beranda')

@push('styles')
<link href="{{ asset('assets/css/public-home.css') }}" rel="stylesheet">
@endpush

@section('content')
    @include('public.partials.home.hero-slider')
    @include('public.partials.home.welcome-strip')
    @include('public.partials.home.quick-links')
    @include('public.partials.home.announcements')
    @include('public.partials.home.latest-news')
    @include('public.partials.home.religious-court-news')
    @include('public.partials.home.gallery-showcase')
    @include('public.partials.home.integrity-zones')
    @include('public.partials.home.surveys')
    @include('public.partials.home.applications')
@endsection

@push('scripts')
<script src="{{ asset('assets/js/public-home.js') }}"></script>
@endpush
