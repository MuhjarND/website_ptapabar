@extends('layouts.admin')
@section('title', 'Tambah Link Cepat')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Link Cepat</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.quicklinks.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Judul *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="cth: Direktori Putusan">
            </div>
            <div class="form-group">
                <label>Deskripsi (opsional)</label>
                <textarea name="description" class="form-control" rows="2" placeholder="cth: Arsip putusan Mahkamah Agung dan pengadilan di bawahnya.">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label>Ikon / Logo (gambar)</label>
                <input type="file" name="icon" class="form-control" accept="image/*">
                <small style="color:#999;">Upload ikon/logo. Disarankan format PNG transparan, ukuran 64x64px atau lebih.</small>
            </div>
            <div class="form-group">
                <label>URL / Link Tujuan</label>
                <input type="text" name="url" class="form-control" value="{{ old('url') }}" placeholder="cth: https://putusan3.mahkamahagung.go.id">
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
                <a href="{{ route('admin.quicklinks.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
