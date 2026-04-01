@extends('layouts.admin')
@section('title', 'Tambah Artikel')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Artikel</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.articles.partials.form')
        </form>
    </div>
</div>
@endsection
