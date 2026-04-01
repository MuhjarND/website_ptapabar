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
            <div class="form-group">
                <label>Tipe</label>
                <select name="type" id="gallery-type" class="form-control" style="max-width:200px;" onchange="toggleType()">
                    <option value="image" {{ old('type', $gallery->type) == 'image' ? 'selected' : '' }}>Foto</option>
                    <option value="video" {{ old('type', $gallery->type) == 'video' ? 'selected' : '' }}>Video (YouTube)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kategori Foto/Video</label>
                <select name="gallery_category_id" class="form-control" style="max-width:320px;" required>
                    <option value="">Pilih kategori galeri</option>
                    @foreach($galleryCategories as $galleryCategory)
                        <option value="{{ $galleryCategory->id }}" {{ (string) old('gallery_category_id', $gallery->gallery_category_id) === (string) $galleryCategory->id ? 'selected' : '' }}>
                            {{ $galleryCategory->name }}{{ !$galleryCategory->is_active ? ' (Nonaktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                <small style="color:#999;font-size:12px;">Kelola kategori melalui master data kategori galeri.</small>
            </div>
            <div class="form-group" id="file-group">
                <label>File Foto</label>
                @if($gallery->file)
                    <div style="margin-bottom:8px;"><img src="{{ asset('storage/'.$gallery->file) }}" style="max-height:100px;border-radius:6px;"></div>
                @endif
                <input type="file" name="file" class="form-control" accept="image/*">
                <small style="color:#999;font-size:12px;">Kosongkan jika tidak ingin mengganti</small>
            </div>
            <div class="form-group" id="video-group" style="display:none;">
                <label>URL Video YouTube</label>
                <input type="url" name="video_url" class="form-control" value="{{ old('video_url', $gallery->video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
            </div>
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
                    <input type="checkbox" name="is_active" {{ $gallery->is_active ? 'checked' : '' }}> Aktif
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
