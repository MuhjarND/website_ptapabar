@extends('layouts.admin')
@section('title', 'Tambah Galeri')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Item Galeri</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.galleries.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="form-group">
                <label>Tipe</label>
                <select name="type" id="gallery-type" class="form-control" style="max-width:200px;" onchange="toggleType()">
                    <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Foto</option>
                    <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video (YouTube)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kategori Foto/Video</label>
                <select name="gallery_category_id" class="form-control" style="max-width:320px;" required>
                    <option value="">Pilih kategori galeri</option>
                    @foreach($galleryCategories as $galleryCategory)
                        <option value="{{ $galleryCategory->id }}" {{ (string) old('gallery_category_id') === (string) $galleryCategory->id ? 'selected' : '' }}>
                            {{ $galleryCategory->name }}
                        </option>
                    @endforeach
                </select>
                <small style="color:#999;font-size:12px;">Kelola kategori melalui master data kategori galeri.</small>
            </div>
            <div class="form-group" id="file-group">
                <label>File Foto</label>
                <input type="file" name="file" class="form-control" accept="image/*">
            </div>
            <div class="form-group" id="video-group" style="display:none;">
                <label>URL Video YouTube</label>
                <input type="url" name="video_url" class="form-control" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=...">
                <small style="color:#999;font-size:12px;">Paste URL YouTube lengkap</small>
            </div>
            <div class="form-group">
                <label>Deskripsi (opsional)</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" style="max-width:100px;">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" checked> Aktif
                </label>
            </div>
            <div class="d-flex gap-2" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.galleries.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
function toggleType() {
    var type = document.getElementById('gallery-type').value;
    document.getElementById('file-group').style.display = type === 'image' ? 'block' : 'none';
    document.getElementById('video-group').style.display = type === 'video' ? 'block' : 'none';
}
toggleType();
</script>
@endsection
