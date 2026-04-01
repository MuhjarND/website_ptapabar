@extends('layouts.admin')
@section('title', $pageTitle)
@section('content')
<div class="card">
    <div class="card-header"><h2>{{ $formHeading }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ $fixedCategory === 'berita' ? route('admin.berita.update', $post) : ($fixedCategory === 'pengumuman' ? route('admin.pengumuman.update', $post) : route('admin.posts.update', $post)) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.posts.partials.form', ['post' => $post])
            <div class="d-flex gap-2" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ $backRoute }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@include('admin.posts.partials.file-upload-script')
