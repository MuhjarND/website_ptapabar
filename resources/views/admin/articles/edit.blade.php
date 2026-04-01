@extends('layouts.admin')
@section('title', 'Edit Artikel')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit Artikel</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.articles.partials.form', ['article' => $article])
        </form>
    </div>
</div>
@endsection
