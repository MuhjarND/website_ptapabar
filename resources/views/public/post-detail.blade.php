@extends('layouts.public')
@section('title', $post->title)

@push('styles')
<link href="{{ asset('assets/css/public-post-detail.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="post-detail-shell">
        @include('public.partials.post-detail.article')
        @include('public.partials.post-detail.documentation')
        @include('public.partials.post-detail.related-posts')
    </div>
</div>
@endsection

@push('scripts')
    @if($imageFiles->count() > 0)
        <script id="postDetailPhotos" type="application/json">@json($lightboxPhotos)</script>
    @endif
    <script src="{{ asset('assets/js/public-post-detail.js') }}"></script>
@endpush
