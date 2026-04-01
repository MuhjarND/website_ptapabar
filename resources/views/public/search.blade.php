@extends('layouts.public')
@section('title', 'Hasil Pencarian')

@push('styles')
<link href="{{ asset('assets/css/public-content.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="content-shell">
        <div class="page-content">
            <h1>Hasil Pencarian</h1>
            <p class="search-intro">
                Menampilkan hasil untuk: <strong>"{{ $q }}"</strong>
            </p>

            @include('public.partials.search.page-results')
            @include('public.partials.search.post-results')
        </div>
    </div>
</div>
@endsection
