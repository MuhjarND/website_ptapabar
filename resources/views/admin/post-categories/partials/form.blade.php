<div class="form-group">
    <label>Nama Kategori</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', optional($postCategory)->name) }}" required>
</div>
<div class="form-group" style="max-width:220px;">
    <label>Urutan</label>
    <input type="number" min="0" name="order" class="form-control" value="{{ old('order', optional($postCategory)->order ?? 0) }}">
</div>
<div class="form-group">
    <label class="checkbox-label">
        <input type="checkbox" name="is_active" {{ old('is_active', optional($postCategory)->is_active ?? true) ? 'checked' : '' }}> Aktif
    </label>
</div>
