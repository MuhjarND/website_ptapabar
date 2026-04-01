@extends('layouts.admin')
@section('title', 'Edit Kategori Post')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit Kategori: {{ $postCategory->name }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.post-categories.update', $postCategory) }}">
            @csrf
            @method('PUT')
            @include('admin.post-categories.partials.form', ['postCategory' => $postCategory])
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.post-categories.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
