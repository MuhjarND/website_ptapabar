@extends('layouts.public')
@section('title', $article->title)

@push('styles')
<link href="{{ asset('assets/css/public-content.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="section section-white">
    <div class="container">
        <div class="page-content" style="max-width:980px;margin:0 auto;">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Beranda</a> / <span>Artikel</span>
            </div>
            <h1>{{ $article->title }}</h1>
            <div class="card-date" style="margin-bottom:20px;"><i class="far fa-calendar"></i> {{ $article->created_at->format('d M Y') }}</div>
            @if($article->pdf_href)
                <div style="margin-bottom:24px;">
                    <a href="{{ $article->pdf_href }}" target="_blank" class="section-link">Buka PDF <i class="fas fa-arrow-right"></i></a>
                </div>
            @endif
            <div class="content-body">{!! $article->content !!}</div>
        </div>
    </div>
</section>
@endsection
