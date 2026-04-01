<div class="form-group">
    <label>Judul Artikel</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $article->title ?? '') }}" required>
</div>
<div class="form-group">
    <label>Kategori Tinta Peradilan</label>
    <select name="article_category" class="form-control" required>
        @foreach($categoryOptions as $value => $label)
            <option value="{{ $value }}" {{ old('article_category', $article->article_category ?? \App\Article::CATEGORY_LAIN_LAIN) === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Ringkasan</label>
    <textarea name="excerpt" class="form-control" rows="3" placeholder="Kosongkan jika ingin dibuat otomatis dari isi artikel">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
    <small style="color:#999;font-size:12px;">Ringkasan ini akan tampil di homepage.</small>
</div>
<div class="form-group">
    <label>Isi Artikel / Karya Tulis</label>
    <textarea name="content" class="form-control rich-editor">{{ old('content', $article->content ?? '') }}</textarea>
    <small style="color:#999;font-size:12px;">Boleh dikosongkan jika artikel hanya berupa file PDF atau link PDF.</small>
</div>
<div class="form-group">
    <label>Upload PDF</label>
    <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
    @if(!empty($article->pdf_file))
        <div style="margin-top:10px;">
            <a href="{{ asset('storage/' . $article->pdf_file) }}" target="_blank" class="btn btn-sm btn-success">
                <i class="fas fa-file-pdf"></i> Lihat PDF Saat Ini
            </a>
        </div>
    @endif
</div>
<div class="form-group">
    <label>Atau Link PDF</label>
    <input type="url" name="pdf_url" class="form-control" value="{{ old('pdf_url', $article->pdf_url ?? '') }}" placeholder="https://.../dokumen.pdf">
    <small style="color:#999;font-size:12px;">Jika upload PDF diisi, link PDF akan diabaikan.</small>
</div>
<div class="form-group">
    <label>Urutan</label>
    <input type="number" name="order" class="form-control" value="{{ old('order', $article->order ?? 0) }}" style="max-width:120px;">
</div>
<div class="form-group">
    <label class="checkbox-label">
        <input type="checkbox" name="is_active" {{ old('is_active', $article->is_active ?? true) ? 'checked' : '' }}> Aktif
    </label>
</div>
<div class="d-flex gap-2" style="margin-top:24px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    <a href="{{ route('admin.articles.index') }}" class="btn" style="background:#e9ecef">Batal</a>
</div>
