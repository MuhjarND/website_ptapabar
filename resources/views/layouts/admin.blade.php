<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - PTA Papua Barat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f4f1; color: #1a1a2e; display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 260px; background: linear-gradient(180deg, #062d17 0%, #0d5c2f 100%); color: #fff; position: fixed; top: 0; left: 0; height: 100vh; z-index: 100; overflow-y: auto; transition: 0.3s; }
        .sidebar-brand { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; gap: 12px; }
        .sidebar-brand .brand-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .sidebar-brand .brand-icon img { width: 100%; height: 100%; object-fit: contain; }
        .sidebar-brand span { font-size: 14px; font-weight: 600; line-height: 1.3; }
        .sidebar-nav { padding: 16px 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; transition: 0.2s; border-left: 3px solid transparent; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.08); color: #fff; border-left-color: #c8a951; }
        .sidebar-nav a i { width: 20px; text-align: center; font-size: 15px; }
        .sidebar-nav .nav-label { padding: 20px 20px 8px; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.35); font-weight: 600; }

        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; min-height: 100vh; }
        .topbar { background: #fff; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 50; }
        .topbar h1 { font-size: 18px; font-weight: 600; color: #0d5c2f; }
        .topbar .user-info { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #555; }
        .topbar .user-info .avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #c8a951, #e6c964); display: flex; align-items: center; justify-content: center; color: #062d17; font-weight: 700; font-size: 14px; }
        .content-area { padding: 32px; }

        /* Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); border: 1px solid #dce8df; }
        .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 16px; }
        .stat-card .stat-icon.blue { background: #e6f4ea; color: #0d5c2f; }
        .stat-card .stat-icon.gold { background: #fef7e0; color: #c8a951; }
        .stat-card .stat-icon.green { background: #e6f4ea; color: #1a7a42; }
        .stat-card .stat-icon.purple { background: #f0faf3; color: #0d5c2f; }
        .stat-card .stat-number { font-size: 28px; font-weight: 700; color: #0d5c2f; }
        .stat-card .stat-label { font-size: 13px; color: #666; margin-top: 4px; }

        /* Table */
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); border: 1px solid #dce8df; margin-bottom: 24px; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #dce8df; display: flex; align-items: center; justify-content: space-between; }
        .card-header h2 { font-size: 16px; font-weight: 600; color: #0d5c2f; }
        .card-body { padding: 24px; }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; padding: 12px 16px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #555; font-weight: 600; background: #f0faf3; border-bottom: 2px solid #dce8df; }
        table td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #f0f4f1; color: #333; }
        table tr:hover td { background: #f8fbf8; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer; text-decoration: none; transition: 0.2s; font-family: inherit; }
        .btn-primary { background: linear-gradient(135deg, #0d5c2f, #1a7a42); color: #fff; }
        .btn-primary:hover { background: linear-gradient(135deg, #1a7a42, #28a05a); transform: translateY(-1px); }
        .btn-gold { background: linear-gradient(135deg, #c8a951, #e6c964); color: #062d17; }
        .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(200,169,81,0.3); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #1a7a42; color: #fff; }
        .btn-warning { background: #ffc107; color: #333; }

        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; transition: 0.2s; background: #fff; }
        .form-control:focus { outline: none; border-color: #0d5c2f; box-shadow: 0 0 0 3px rgba(13,92,47,0.1); }
        textarea.form-control { min-height: 120px; resize: vertical; }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 8px center; background-repeat: no-repeat; background-size: 20px; }

        /* Alerts */
        .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #e6f4ea; color: #1a7a42; }
        .badge-secondary { background: #f0f2f5; color: #666; }
        .badge-info { background: #e6f4ea; color: #0d5c2f; }
        .badge-warning { background: #fef7e0; color: #b8860b; }

        .text-muted { color: #999; font-size: 12px; }
        .text-right { text-align: right; }
        .mb-0 { margin-bottom: 0; }
        .d-flex { display: flex; }
        .gap-2 { gap: 8px; }
        .pagination { display: flex; gap: 4px; justify-content: center; padding: 16px 0; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; }
        .pagination a { color: #0d5c2f; background: #f0f4f1; }
        .pagination a:hover { background: #0d5c2f; color: #fff; }
        .pagination .active span { background: #0d5c2f; color: #fff; }

        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; }
        .checkbox-label input[type="checkbox"] { width: 18px; height: 18px; }

        .logout-form { display: inline; }
        .logout-form button { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; font-size: 14px; font-family: inherit; display: flex; align-items: center; gap: 12px; padding: 12px 20px; width: 100%; border-left: 3px solid transparent; transition: 0.2s; }
        .logout-form button:hover { background: rgba(255,255,255,0.08); color: #fff; }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><img src="{{ asset('logo_pta.png') }}" alt="Logo PTA"></div>
            <span>Panel Admin<br>PTA Papua Barat</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.pages.index') }}" class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Halaman
            </a>
            @endif
            <a href="{{ route('admin.berita.index') }}" class="{{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Berita
            </a>
            @if(auth()->user()->canManageAnnouncements())
            <a href="{{ route('admin.pengumuman.index') }}" class="{{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i> Pengumuman
            </a>
            @endif
            @if(auth()->user()->isAdmin())
            <div class="nav-label">Master Data</div>
            <a href="{{ route('admin.post-categories.index') }}" class="{{ request()->routeIs('admin.post-categories.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Kategori Post
            </a>
            <a href="{{ route('admin.gallery-categories.index') }}" class="{{ request()->routeIs('admin.gallery-categories.*') ? 'active' : '' }}">
                <i class="fas fa-photo-film"></i> Kategori Galeri
            </a>
            <a href="{{ route('admin.site-applications.index') }}" class="{{ request()->routeIs('admin.site-applications.*') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Aplikasi
            </a>
            <a href="{{ route('admin.articles.index') }}" class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i> Artikel
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> User
            </a>

            <div class="nav-label">Konten Pendukung</div>
            <a href="{{ route('admin.sliders.index') }}" class="{{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                <i class="fas fa-images"></i> Slider
            </a>
            <a href="{{ route('admin.surveys.index') }}" class="{{ request()->routeIs('admin.surveys.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Survei & Indeks
            </a>
            <a href="{{ route('admin.quicklinks.index') }}" class="{{ request()->routeIs('admin.quicklinks.*') ? 'active' : '' }}">
                <i class="fas fa-external-link-square-alt"></i> Link Cepat
            </a>
            <a href="{{ route('admin.galleries.index') }}" class="{{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                <i class="fas fa-photo-video"></i> Foto & Video
            </a>
            <a href="{{ route('admin.integrity-zones.index') }}" class="{{ request()->routeIs('admin.integrity-zones.*') ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i> Zona Integritas
            </a>
            @endif

            <div class="nav-label">Pengaturan</div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Pengaturan Situs
            </a>
            @endif
            <a href="{{ route('home') }}" target="_blank">
                <i class="fas fa-external-link-alt"></i> Lihat Situs
            </a>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit"><i class="fas fa-sign-out-alt"></i> Keluar</button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1>@yield('title', 'Dashboard')</h1>
            <div class="user-info">
                <span>{{ auth()->user()->name }}</span>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </div>
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <div><i class="fas fa-exclamation-triangle"></i>
                        @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                    </div>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        document.querySelectorAll('.rich-editor').forEach(function(el) {
            CKEDITOR.replace(el.getAttribute('name'), {
                versionCheck: false,
                height: 400,
                filebrowserUploadUrl: '{{ route("admin.ckeditor.upload", ["_token" => csrf_token()]) }}',
                filebrowserUploadMethod: 'form',
                toolbarGroups: [
                    { name: 'document', groups: ['mode'] },
                    { name: 'clipboard', groups: ['clipboard', 'undo'] },
                    { name: 'editing', groups: ['find', 'selection'] },
                    '/',
                    { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                    { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
                    { name: 'links' },
                    { name: 'insert' },
                    '/',
                    { name: 'styles' },
                    { name: 'colors' },
                    { name: 'tools' }
                ],
                removeButtons: 'Save,NewPage,ExportPdf,Preview,Print,Templates',
                removePlugins: 'easyimage',
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
