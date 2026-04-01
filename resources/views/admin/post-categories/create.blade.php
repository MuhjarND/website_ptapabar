@extends('layouts.admin')
@section('title', 'Tambah Kategori Post')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Kategori Post</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.post-categories.store') }}">
            @csrf
            @include('admin.post-categories.partials.form', ['postCategory' => null])
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.post-categories.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
