@section('scripts')
<style>
    #drop-zone.dragover { border-color:#c8a951; background:#fef7e0; }
</style>
<script>
var dt = new DataTransfer();
function submitPhotoDelete(actionUrl) {
    if (!confirm('Hapus file ini?')) {
        return;
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = actionUrl;
    form.style.display = 'none';

    var tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(tokenInput);

    var methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);

    document.body.appendChild(form);
    form.submit();
}

function syncAnnouncementCategoryVisibility() {
    var categorySelect = document.getElementById('post-category-select');
    var newsCategoryGroup = document.getElementById('news-category-group');
    var newsCategorySelect = document.getElementById('news-category-select');
    var newsScopeGroup = document.getElementById('news-scope-group');
    var newsScopeSelect = document.getElementById('news-scope-select');
    var announcementGroup = document.getElementById('announcement-category-group');
    var announcementSelect = document.getElementById('announcement-category-select');

    if (!categorySelect || !newsCategoryGroup || !newsCategorySelect || !newsScopeGroup || !newsScopeSelect || !announcementGroup || !announcementSelect) {
        return;
    }

    var isNews = categorySelect.value === 'berita';
    var isAnnouncement = categorySelect.value === 'pengumuman';

    newsCategoryGroup.style.display = isNews ? 'block' : 'none';
    newsScopeGroup.style.display = isNews ? 'block' : 'none';
    announcementGroup.style.display = isAnnouncement ? 'block' : 'none';

    if (!isNews) {
        newsCategorySelect.value = '';
        newsScopeSelect.value = '';
    }

    if (!isAnnouncement) {
        announcementSelect.value = '';
    }
}

var postCategorySelect = document.getElementById('post-category-select');
if (postCategorySelect) {
    postCategorySelect.addEventListener('change', syncAnnouncementCategoryVisibility);
    syncAnnouncementCategoryVisibility();
}

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
                    '<div style="padding:6px 8px;font-size:11px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + file.name + '</div>' +
                    '<button type="button" onclick="removeFile(' + index + ')" style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:rgba(220,53,69,0.9);color:#fff;border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-times"></i></button>';
                preview.appendChild(div);
            } else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    div.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:110px;object-fit:cover;">' +
                        '<div style="padding:6px 8px;font-size:11px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + file.name + '</div>' +
                        '<button type="button" onclick="removeFile(' + index + ')" style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:rgba(220,53,69,0.9);color:#fff;border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-times"></i></button>';
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
        if (i !== index) {
            newDt.items.add(dt.files[i]);
        }
    }
    dt = newDt;
    document.getElementById('photo-input').files = dt.files;
    renderPreviews();
}
var dz = document.getElementById('drop-zone');
['dragenter', 'dragover'].forEach(function(ev) {
    dz.addEventListener(ev, function(e) {
        e.preventDefault();
        dz.classList.add('dragover');
    });
});
['dragleave', 'drop'].forEach(function(ev) {
    dz.addEventListener(ev, function(e) {
        e.preventDefault();
        dz.classList.remove('dragover');
    });
});
dz.addEventListener('drop', function(e) {
    var input = document.getElementById('photo-input');
    for (var i = 0; i < e.dataTransfer.files.length; i++) {
        var file = e.dataTransfer.files[i];
        if (file.type.startsWith('image/') || file.type === 'application/pdf') {
            dt.items.add(file);
        }
    }
    input.files = dt.files;
    renderPreviews();
});
</script>
@endsection
