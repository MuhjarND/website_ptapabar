@php($type = old('type', $gallery->type ?? 'image'))
<div class="form-group">
    <label for="gallery-type">Tipe Konten</label>
    <select name="type" id="gallery-type" class="form-control">
        @foreach(\App\Gallery::typeOptions() as $value => $label)
            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="form-group" id="gallery-category-group">
    <label for="gallery-category">Kategori Foto/Video</label>
    <select name="gallery_category_id" id="gallery-category" class="form-control">
        <option value="">Pilih kategori galeri</option>
        @foreach($galleryCategories as $galleryCategory)
            <option value="{{ $galleryCategory->id }}" @selected((string) old('gallery_category_id', $gallery->gallery_category_id ?? null) === (string) $galleryCategory->id)>
                {{ $galleryCategory->name }}{{ !$galleryCategory->is_active ? ' (Nonaktif)' : '' }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-group" id="gallery-file-group">
    <label for="gallery-file">File Gambar</label>
    @if(!empty($gallery->file))
        <div style="margin-bottom:8px">
            <img src="{{ asset('storage/' . $gallery->file) }}" alt="Gambar saat ini" style="max-height:100px;border-radius:6px">
        </div>
    @endif
    <input type="file" name="file" id="gallery-file" class="form-control" accept="image/*">
    <small>Maksimal 5 MB. Untuk banner, dianjurkan gambar lanskap.</small>
</div>
<div class="form-group" id="gallery-video-group">
    <label for="gallery-video-url">Tautan Video YouTube</label>
    <input type="url" name="video_url" id="gallery-video-url" class="form-control" value="{{ old('video_url', $gallery->video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=...">
    <small>Cukup tempel tautan YouTube, tanpa unggah file video.</small>
</div>

@section('scripts')
<script>
    const galleryType = document.getElementById('gallery-type');
    const galleryCategory = document.getElementById('gallery-category');
    const galleryFile = document.getElementById('gallery-file');
    const galleryVideo = document.getElementById('gallery-video-url');

    function updateGalleryFields() {
        const video = galleryType.value === 'video';
        const archiveItem = video || galleryType.value === 'image';
        document.getElementById('gallery-category-group').hidden = !archiveItem;
        document.getElementById('gallery-file-group').hidden = video;
        document.getElementById('gallery-video-group').hidden = !video;
        galleryCategory.disabled = !archiveItem;
        galleryCategory.required = archiveItem;
        galleryFile.disabled = video;
        galleryVideo.disabled = !video;
        galleryVideo.required = video;
    }

    galleryType.addEventListener('change', updateGalleryFields);
    updateGalleryFields();
</script>
@endsection
