<div class="form-group">
    <label>Judul *</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', optional($siteApplication)->title) }}" required>
</div>
<div class="form-group">
    <label>Grup Aplikasi *</label>
    <select name="group_type" class="form-control" required style="max-width:320px;">
        @foreach($groupOptions as $value => $label)
            <option value="{{ $value }}" {{ old('group_type', optional($siteApplication)->group_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Deskripsi (opsional)</label>
    <textarea name="description" class="form-control" rows="2">{{ old('description', optional($siteApplication)->description) }}</textarea>
</div>
<div class="form-group">
    <label>Ikon / Logo (gambar)</label>
    @if(optional($siteApplication)->icon)
        <div style="margin-bottom:8px"><img src="{{ asset('storage/' . $siteApplication->icon) }}" style="max-height:50px;border-radius:6px;background:#f5f5f5;padding:4px;"></div>
    @endif
    <input type="file" name="icon" class="form-control" accept="image/*">
    <small style="color:#999;">Disarankan PNG transparan atau SVG yang sudah dikonversi ke gambar.</small>
</div>
<div class="form-group">
    <label>URL / Link Tujuan</label>
    <input type="text" name="url" class="form-control" value="{{ old('url', optional($siteApplication)->url) }}" placeholder="https://">
</div>
<div class="form-group" style="max-width:120px;">
    <label>Urutan</label>
    <input type="number" name="order" class="form-control" min="0" value="{{ old('order', optional($siteApplication)->order ?? 0) }}">
</div>
<div class="form-group">
    <label class="checkbox-label"><input type="checkbox" name="is_active" {{ old('is_active', optional($siteApplication)->is_active ?? true) ? 'checked' : '' }}> Aktif</label>
</div>
