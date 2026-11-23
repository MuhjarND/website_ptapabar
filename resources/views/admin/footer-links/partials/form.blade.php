<div class="form-group">
    <label>Bagian Footer</label>
    <select name="section" class="form-control" style="max-width:280px;">
        @foreach($sectionOptions as $value => $label)
            <option value="{{ $value }}" {{ old('section', $footerLink->section ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Judul</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $footerLink->title ?? '') }}" required>
</div>
<div class="form-group">
    <label>URL / Path</label>
    <input type="text" name="url" class="form-control" value="{{ old('url', $footerLink->url ?? '') }}" placeholder="https://... atau /berita" required>
</div>
<div class="form-group">
    <label>Urutan</label>
    <input type="number" name="order" class="form-control" value="{{ old('order', $footerLink->order ?? 0) }}" style="max-width:120px;">
</div>
<div class="form-group">
    <label class="checkbox-label">
        <input type="checkbox" name="is_active" {{ old('is_active', $footerLink->is_active ?? true) ? 'checked' : '' }}> Aktif
    </label>
</div>
<div class="d-flex gap-2" style="margin-top:24px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    <a href="{{ route('admin.footer-links.index') }}" class="btn" style="background:#e9ecef">Batal</a>
</div>
