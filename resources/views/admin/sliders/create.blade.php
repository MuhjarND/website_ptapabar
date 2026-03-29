@extends('layouts.admin')
@section('title', 'Tambah Slider')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Slider Baru</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Judul (opsional)</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label>Deskripsi (opsional)</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}">
            </div>
            <div class="form-group">
                <label>Gambar Slider *</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>
            <div class="form-group">
                <label>Posisi Teks</label>
                <select name="text_position" class="form-control" style="max-width:250px">
                    <option value="bottom-left" {{ old('text_position') == 'bottom-left' ? 'selected' : '' }}>Bawah Kiri</option>
                    <option value="bottom-center" {{ old('text_position') == 'bottom-center' ? 'selected' : '' }}>Bawah Tengah</option>
                    <option value="center-left" {{ old('text_position') == 'center-left' ? 'selected' : '' }}>Tengah Kiri</option>
                    <option value="center-center" {{ old('text_position') == 'center-center' ? 'selected' : '' }}>Tengah</option>
                    <option value="top-left" {{ old('text_position') == 'top-left' ? 'selected' : '' }}>Atas Kiri</option>
                </select>
                <small style="color:#999;">Posisi teks overlay pada slider</small>
            </div>
            <div class="form-group">
                <label>Link (opsional)</label>
                <input type="text" name="link" class="form-control" value="{{ old('link') }}">
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" style="max-width:120px">
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_active" checked> Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.sliders.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
