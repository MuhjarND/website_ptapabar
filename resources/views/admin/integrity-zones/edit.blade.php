@extends('layouts.admin')
@section('title', 'Edit Eviden Zona Integritas')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit: {{ $integrityZone->title }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.integrity-zones.update', $integrityZone) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Area</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $integrityZone->title) }}" required>
            </div>
            <div class="form-group">
                <label>Poster Eviden</label>
                <div style="margin-bottom:10px;">
                    <img src="{{ asset('storage/' . $integrityZone->image) }}" alt="{{ $integrityZone->title }}" style="width:120px;height:156px;object-fit:cover;border-radius:12px;border:1px solid #dce8df;">
                </div>
                <input type="file" name="image" class="form-control" accept="image/*">
                <small style="color:#999;font-size:12px;">Kosongkan jika tidak ingin mengganti poster.</small>
            </div>
            <div class="form-group">
                <label>Link Eviden</label>
                <input type="url" name="url" class="form-control" value="{{ old('url', $integrityZone->url) }}" required>
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', $integrityZone->order) }}" style="max-width:120px;">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" {{ $integrityZone->is_active ? 'checked' : '' }}> Aktif
                </label>
            </div>
            <div class="d-flex gap-2" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.integrity-zones.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
