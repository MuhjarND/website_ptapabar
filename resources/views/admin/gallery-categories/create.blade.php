@extends('layouts.admin')
@section('title', 'Tambah Kategori Galeri')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Kategori Galeri</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gallery-categories.store') }}">
            @csrf
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" style="max-width:120px;">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" checked> Aktif
                </label>
            </div>
            <div class="d-flex gap-2" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.gallery-categories.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
