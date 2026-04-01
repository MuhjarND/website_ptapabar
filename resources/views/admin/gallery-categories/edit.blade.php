@extends('layouts.admin')
@section('title', 'Edit Kategori Galeri')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit: {{ $galleryCategory->name }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gallery-categories.update', $galleryCategory) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $galleryCategory->name) }}" required>
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', $galleryCategory->order) }}" style="max-width:120px;">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" {{ old('is_active', $galleryCategory->is_active) ? 'checked' : '' }}> Aktif
                </label>
            </div>
            <div class="d-flex gap-2" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.gallery-categories.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
