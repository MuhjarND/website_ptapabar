<div class="form-group">
    <label>Judul Agenda Pimpinan</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $agenda->title ?? '') }}" required>
</div>
<div class="d-flex gap-2" style="flex-wrap:wrap;">
    <div class="form-group" style="min-width:220px;flex:1;">
        <label>Jenis Kegiatan Pimpinan</label>
        <select name="agenda_type" class="form-control">
            @foreach($agendaTypes as $value => $label)
                <option value="{{ $value }}" {{ old('agenda_type', $agenda->agenda_type ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group" style="min-width:160px;">
        <label>Urutan</label>
        <input type="number" name="order" class="form-control" value="{{ old('order', $agenda->order ?? 0) }}">
    </div>
</div>
<div class="form-group">
    <label>Ringkasan Kegiatan Pimpinan</label>
    <textarea name="summary" class="form-control" rows="3">{{ old('summary', $agenda->summary ?? '') }}</textarea>
</div>
<div class="form-group">
    <label>Deskripsi Kegiatan Pimpinan</label>
    <textarea name="description" class="form-control rich-editor" rows="8">{{ old('description', $agenda->description ?? '') }}</textarea>
</div>
<div class="d-flex gap-2" style="flex-wrap:wrap;">
    <div class="form-group" style="min-width:220px;flex:1;">
        <label>Lokasi</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $agenda->location ?? '') }}">
    </div>
    <div class="form-group" style="min-width:220px;flex:1;">
        <label>Penyelenggara</label>
        <input type="text" name="organizer" class="form-control" value="{{ old('organizer', $agenda->organizer ?? '') }}">
    </div>
</div>
<div class="d-flex gap-2" style="flex-wrap:wrap;">
    <div class="form-group" style="min-width:220px;flex:1;">
        <label>Mulai</label>
        <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at', optional(optional($agenda ?? null)->start_at)->format('Y-m-d\\TH:i')) }}" required>
    </div>
    <div class="form-group" style="min-width:220px;flex:1;">
        <label>Selesai</label>
        <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at', optional(optional($agenda ?? null)->end_at)->format('Y-m-d\\TH:i')) }}">
    </div>
</div>
<div class="form-group">
    <label class="checkbox-label">
        <input type="checkbox" name="is_active" {{ old('is_active', $agenda->is_active ?? true) ? 'checked' : '' }}> Aktif
    </label>
</div>
<div class="d-flex gap-2" style="margin-top:24px;">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    <a href="{{ route('admin.agendas.index') }}" class="btn" style="background:#e9ecef;">Batal</a>
</div>
