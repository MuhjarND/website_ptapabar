<div class="form-group">
    <label>Nama Ikon</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $floatingIcon->title ?? '') }}" required>
</div>
<div class="form-group">
    <label>Teks</label>
    <input type="text" name="text" class="form-control" value="{{ old('text', $floatingIcon->text ?? '') }}" placeholder="Opsional, tampil di sebelah kiri ikon">
</div>
<div class="form-group">
    <label>Ikon</label>
    <input type="file" name="icon" class="form-control" accept="image/*" {{ isset($floatingIcon) ? '' : 'required' }}>
    @if(!empty($floatingIcon->icon))
        <div style="margin-top:10px;">
            <img src="{{ asset('storage/' . $floatingIcon->icon) }}" alt="{{ $floatingIcon->title }}" style="width:64px;height:64px;object-fit:contain;border-radius:16px;border:1px solid #dce8df;background:#f8fbf8;padding:6px;">
        </div>
    @endif
</div>
<div class="form-group">
    <label>Link</label>
    <input type="url" name="url" class="form-control" value="{{ old('url', $floatingIcon->url ?? '') }}" placeholder="https://..." required>
</div>
<div class="form-group">
    <label>Posisi Tampil</label>
    <select name="position" class="form-control" style="max-width:280px;">
        @foreach($positionOptions as $value => $label)
            <option value="{{ $value }}" {{ old('position', $floatingIcon->position ?? \App\FloatingIcon::POSITION_BOTTOM_RIGHT) === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Urutan</label>
    <input type="number" name="order" class="form-control" value="{{ old('order', $floatingIcon->order ?? 0) }}" style="max-width:120px;">
</div>
<div class="form-group">
    <label class="checkbox-label">
        <input type="checkbox" name="is_active" {{ old('is_active', $floatingIcon->is_active ?? true) ? 'checked' : '' }}> Aktif
    </label>
</div>
<div class="d-flex gap-2" style="margin-top:24px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    <a href="{{ route('admin.floating-icons.index') }}" class="btn" style="background:#e9ecef">Batal</a>
</div>
