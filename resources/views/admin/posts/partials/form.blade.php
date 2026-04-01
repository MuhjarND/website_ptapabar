@php
    $currentCategory = old('category', $fixedCategory ?? optional($post)->category ?? 'berita');
@endphp

<div class="form-group">
    <label>Judul</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', optional($post)->title) }}" required>
</div>
<div class="d-flex gap-2" style="align-items:flex-start;flex-wrap:wrap;">
    @if($fixedCategory)
    <div class="form-group" style="flex:1;min-width:220px;">
        <label>Jenis Publikasi</label>
        <input type="hidden" name="category" value="{{ $fixedCategory }}">
        <div class="form-control" style="display:flex;align-items:center;background:#f8fbf8;">
            <span class="badge {{ $fixedCategory === 'berita' ? 'badge-info' : 'badge-warning' }}">{{ ucfirst($fixedCategory) }}</span>
        </div>
    </div>
    @else
    <div class="form-group" style="flex:1;min-width:220px;">
        <label>Jenis Publikasi</label>
        <select name="category" class="form-control" id="post-category-select">
            <option value="berita" {{ old('category', optional($post)->category) == 'berita' ? 'selected' : '' }}>Berita</option>
            <option value="pengumuman" {{ old('category', optional($post)->category) == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
        </select>
    </div>
    @endif
    @if($fixedCategory !== 'pengumuman')
    <div
        class="form-group"
        id="news-category-group"
        style="flex:1;min-width:220px;display:{{ $currentCategory !== 'pengumuman' ? 'block' : 'none' }};"
    >
        <label>Kategori Berita</label>
        <select name="post_category_id" class="form-control" id="news-category-select">
            <option value="">Pilih kategori berita</option>
            @foreach($postCategories as $postCategory)
                <option value="{{ $postCategory->id }}" {{ (string) old('post_category_id', optional($post)->post_category_id) === (string) $postCategory->id ? 'selected' : '' }}>
                    {{ $postCategory->name }}{{ !$postCategory->is_active ? ' (Nonaktif)' : '' }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Field ini hanya digunakan jika jenis publikasi adalah berita.</small>
    </div>
    @if(!auth()->user()->isAuthorSatker())
        <div
            class="form-group"
            id="news-scope-group"
            style="flex:1;min-width:220px;display:{{ $currentCategory !== 'pengumuman' ? 'block' : 'none' }};"
        >
            <label>Kelompok Berita</label>
            <select name="news_scope" class="form-control" id="news-scope-select">
                <option value="">Pilih kelompok berita</option>
                @foreach(\App\Post::newsScopeOptions() as $value => $label)
                    <option value="{{ $value }}" {{ old('news_scope', optional($post)->news_scope) === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Pilih apakah berita ini masuk Berita Terkini atau Berita Peradilan Agama Papua Barat.</small>
        </div>
    @else
        <div class="form-group" style="flex:1;min-width:220px;">
            <label>Kelompok Berita</label>
            <input type="hidden" name="news_scope" value="{{ \App\Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT }}">
            <div class="form-control" style="display:flex;align-items:center;background:#f8fbf8;">
                <span class="badge badge-info">{{ \App\Post::newsScopeOptions()[\App\Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT] }}</span>
            </div>
            <small class="text-muted">Author satker hanya dapat membuat berita Peradilan Agama Papua Barat.</small>
        </div>
    @endif
    @endif
</div>
@if($fixedCategory !== 'berita')
<div
    class="form-group"
    id="announcement-category-group"
    style="display:{{ $currentCategory === 'pengumuman' ? 'block' : 'none' }};"
>
    <label>Kategori Pengumuman</label>
    <select name="announcement_category" class="form-control" id="announcement-category-select">
        <option value="">Pilih kategori pengumuman</option>
        @foreach(\App\Post::announcementCategoryOptions() as $value => $label)
            <option value="{{ $value }}" {{ old('announcement_category', optional($post)->announcement_category) === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">Field ini hanya digunakan jika jenis publikasi adalah pengumuman.</small>
</div>
@endif
<div class="form-group">
    <label>Gambar Utama</label>
    @if(optional($post)->image)
        <div style="margin-bottom:8px"><img src="{{ asset('storage/' . $post->image) }}" style="max-height:100px;border-radius:6px"></div>
    @endif
    <input type="file" name="image" class="form-control" accept="image/*">
</div>
<div class="form-group">
    <label>Konten</label>
    <textarea name="content" class="form-control rich-editor" rows="12">{{ old('content', optional($post)->content) }}</textarea>
</div>
<div class="form-group">
    <label class="checkbox-label">
        <input type="checkbox" name="is_published" {{ old('is_published', optional($post)->is_published ?? true) ? 'checked' : '' }}> Publikasikan
    </label>
</div>

<div class="card" style="margin-top:24px;border:2px dashed #d1d5db;">
    <div class="card-header" style="background:#f8f9fa;">
        <h2><i class="fas fa-folder-open" style="color:#c8a951;margin-right:8px;"></i> Dokumentasi Foto & PDF</h2>
    </div>
    <div class="card-body">
        @if(!empty($post) && $post->photos->count() > 0)
            <p style="font-size:13px;font-weight:600;color:#333;margin-bottom:10px;">File yang sudah diupload ({{ $post->photos->count() }})</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;margin-bottom:20px;">
                @foreach($post->photos as $photo)
                    <div style="position:relative;border-radius:8px;overflow:hidden;border:1px solid #e8ecf0;">
                        @if(Str::endsWith(strtolower($photo->image), '.pdf'))
                            <div style="width:100%;height:110px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg,#dc3545,#c82333);">
                                <i class="fas fa-file-pdf" style="font-size:32px;color:#fff;margin-bottom:6px;"></i>
                                <span style="font-size:10px;color:rgba(255,255,255,0.8);font-weight:600;">PDF</span>
                            </div>
                            <div style="padding:6px 8px;font-size:11px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ basename($photo->image) }}</div>
                        @else
                            <img src="{{ asset('storage/' . $photo->image) }}" style="width:100%;height:110px;object-fit:cover;">
                            @if($photo->caption)
                                <div style="padding:6px 8px;font-size:11px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $photo->caption }}</div>
                            @endif
                        @endif
                        <button
                            type="button"
                            onclick="submitPhotoDelete('{{ route('admin.posts.photo.destroy', $photo->id) }}')"
                            style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:rgba(220,53,69,0.9);color:#fff;border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <p style="font-size:13px;color:#666;margin-bottom:12px;">Tambah file baru:</p>
        <div id="drop-zone" style="border:2px dashed #cbd5e0;border-radius:12px;padding:32px;text-align:center;cursor:pointer;transition:0.2s;background:#fafbfc;" onclick="document.getElementById('photo-input').click()">
            <i class="fas fa-cloud-upload-alt" style="font-size:28px;color:#c8a951;margin-bottom:8px;display:block;"></i>
            <p style="font-size:13px;font-weight:600;color:#333;margin-bottom:4px;">Klik atau seret file ke sini</p>
            <p style="font-size:12px;color:#999;">JPG, PNG, PDF - maks 10MB per file</p>
            <input type="file" name="photos[]" id="photo-input" multiple accept="image/*,.pdf,application/pdf" style="display:none;" onchange="previewFiles(this)">
        </div>
        <div id="photo-preview" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;margin-top:12px;"></div>
    </div>
</div>
