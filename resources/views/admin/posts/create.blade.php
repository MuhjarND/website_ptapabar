@extends('layouts.admin')
@section('title', 'Tambah Post')
@section('content')
<div class="card">
    <div class="card-header"><h2>Tambah Post Baru</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="category" class="form-control" style="max-width:250px">
                    <option value="berita" {{ old('category') == 'berita' ? 'selected' : '' }}>Berita</option>
                    <option value="pengumuman" {{ old('category') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                </select>
            </div>
            <div class="form-group">
                <label>Gambar Utama</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Konten</label>
                <textarea name="content" class="form-control rich-editor" rows="12">{{ old('content') }}</textarea>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_published" checked> Publikasikan
                </label>
            </div>

            <!-- Dokumentasi Foto & PDF -->
            <div class="card" style="margin-top:24px;border:2px dashed #d1d5db;">
                <div class="card-header" style="background:#f8f9fa;">
                    <h2><i class="fas fa-folder-open" style="color:#c8a951;margin-right:8px;"></i> Dokumentasi Foto & PDF</h2>
                </div>
                <div class="card-body">
                    <div id="drop-zone" style="border:2px dashed #cbd5e0;border-radius:12px;padding:40px;text-align:center;cursor:pointer;transition:0.2s;background:#fafbfc;" onclick="document.getElementById('photo-input').click()">
                        <i class="fas fa-cloud-upload-alt" style="font-size:36px;color:#c8a951;margin-bottom:12px;display:block;"></i>
                        <p style="font-size:14px;font-weight:600;color:#333;margin-bottom:4px;">Klik atau seret file ke sini</p>
                        <p style="font-size:12px;color:#999;">JPG, PNG, PDF — maks 10MB per file</p>
                        <input type="file" name="photos[]" id="photo-input" multiple accept="image/*,.pdf,application/pdf" style="display:none;" onchange="previewFiles(this)">
                    </div>
                    <div id="photo-preview" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;margin-top:16px;"></div>
                </div>
            </div>

            <div class="d-flex gap-2" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.posts.index') }}" class="btn" style="background:#e9ecef">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<style>
    #drop-zone.dragover { border-color:#c8a951; background:#fef7e0; }
</style>
<script>
var dt = new DataTransfer();
function previewFiles(input) {
    for (var i = 0; i < input.files.length; i++) {
        dt.items.add(input.files[i]);
    }
    input.files = dt.files;
    renderPreviews();
}
function renderPreviews() {
    var preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    for (var i = 0; i < dt.files.length; i++) {
        (function(index, file) {
            var div = document.createElement('div');
            div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1px solid #e8ecf0;background:#f8f9fa;';
            var isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
            if (isPdf) {
                div.innerHTML = '<div style="width:100%;height:110px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg,#dc3545,#c82333);">' +
                    '<i class="fas fa-file-pdf" style="font-size:36px;color:#fff;margin-bottom:6px;"></i>' +
                    '<span style="font-size:10px;color:rgba(255,255,255,0.8);font-weight:600;">PDF</span></div>' +
                    '<div style="padding:6px 8px;font-size:11px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'+file.name+'</div>' +
                    '<button type="button" onclick="removeFile('+index+')" style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:rgba(220,53,69,0.9);color:#fff;border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-times"></i></button>';
                preview.appendChild(div);
            } else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    div.innerHTML = '<img src="'+e.target.result+'" style="width:100%;height:110px;object-fit:cover;">' +
                        '<div style="padding:6px 8px;font-size:11px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'+file.name+'</div>' +
                        '<button type="button" onclick="removeFile('+index+')" style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:rgba(220,53,69,0.9);color:#fff;border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-times"></i></button>';
                };
                reader.readAsDataURL(file);
                preview.appendChild(div);
            }
        })(i, dt.files[i]);
    }
}
function removeFile(index) {
    var newDt = new DataTransfer();
    for (var i = 0; i < dt.files.length; i++) {
        if (i !== index) newDt.items.add(dt.files[i]);
    }
    dt = newDt;
    document.getElementById('photo-input').files = dt.files;
    renderPreviews();
}
// Drag and drop
var dz = document.getElementById('drop-zone');
['dragenter','dragover'].forEach(function(ev) { dz.addEventListener(ev, function(e) { e.preventDefault(); dz.classList.add('dragover'); }); });
['dragleave','drop'].forEach(function(ev) { dz.addEventListener(ev, function(e) { e.preventDefault(); dz.classList.remove('dragover'); }); });
dz.addEventListener('drop', function(e) {
    var input = document.getElementById('photo-input');
    for (var i = 0; i < e.dataTransfer.files.length; i++) {
        var f = e.dataTransfer.files[i];
        if (f.type.startsWith('image/') || f.type === 'application/pdf') dt.items.add(f);
    }
    input.files = dt.files;
    renderPreviews();
});
</script>
@endsection
