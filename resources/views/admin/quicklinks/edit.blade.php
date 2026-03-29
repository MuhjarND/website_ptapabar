@extends('layouts.admin')
@section('title', 'Edit Link Cepat')
@section('content')
<div class="card">
    <div class="card-header"><h2>Edit: {{ $quicklink->title }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.quicklinks.update', $quicklink) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Judul *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $quicklink->title) }}" required>
            </div>
            <div class="form-group">
                <label>Deskripsi (opsional)</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $quicklink->description) }}</textarea>
            </div>
            <div class="form-group">
                <label>Ikon / Logo</label>
                @if($quicklink->icon)
                    <div style="margin-bottom:8px"><img src="{{ asset('storage/'.$quicklink->icon) }}" style="max-height:50px;border-radius:6px;background:#f5f5f5;padding:4px;"></div>
                @endif
                <input type="file" name="icon" class="form-control" accept="image/*">
                <small style="color:#999;">Biarkan kosong jika tidak diubah.</small>
            </div>
            <div class="form-group">
                <label>URL / Link Tujuan</label>
                <input type="text" name="url" class="form-control" value="{{ old('url', $quicklink->url) }}">
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', $quicklink->order) }}" style="max-width:120px">
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_active" {{ $quicklink->is_active ? 'checked' : '' }}> Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.quicklinks.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
