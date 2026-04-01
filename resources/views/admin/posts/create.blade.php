@extends('layouts.admin')
@section('title', $pageTitle)
@section('content')
<div class="card">
    <div class="card-header"><h2>{{ $formHeading }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ $storeRoute }}" enctype="multipart/form-data">
            @csrf
            @include('admin.posts.partials.form', ['post' => null])
            <div class="d-flex gap-2" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ $backRoute }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@include('admin.posts.partials.file-upload-script')
