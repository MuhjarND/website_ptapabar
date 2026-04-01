@extends('layouts.admin')
@section('title', 'Tambah Eviden Zona Integritas')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Eviden Zona Integritas</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.integrity-zones.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Nama Area</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Area 1" required>
            </div>
            <div class="form-group">
                <label>Poster Eviden</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
                <small style="color:#999;font-size:12px;">Upload poster area zona integritas.</small>
            </div>
            <div class="form-group">
                <label>Link Eviden</label>
                <input type="url" name="url" class="form-control" value="{{ old('url') }}" placeholder="https://..." required>
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
                <a href="{{ route('admin.integrity-zones.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
