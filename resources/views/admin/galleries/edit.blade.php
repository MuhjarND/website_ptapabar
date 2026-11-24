@extends('layouts.admin')
@section('title', 'Edit Galeri')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit: {{ Str::limit($gallery->title, 50) }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.galleries.update', $gallery) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $gallery->title) }}" required>
            </div>
            @include('admin.galleries.media-fields')
            <div class="form-group">
                <label>Deskripsi (opsional)</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $gallery->description) }}</textarea>
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', $gallery->order) }}" style="max-width:100px;">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" @checked($gallery->is_active)> Aktif
                </label>
            </div>
            <div class="d-flex gap-2" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.galleries.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
