<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Website Resmi Pengadilan Tinggi Agama Papua Barat - Mahkamah Agung Republik Indonesia">
    <title>@yield('title', 'Beranda') - PTA Papua Barat</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --green: #0d5c2f;
            --green-light: #1a7a42;
            --green-dark: #083d1f;
            --green-darker: #062d17;
            --gold: #c8a951;
            --gold-light: #e6c964;
            --gold-dark: #a88b3a;
            --bg: #f5f7f5;
            --white: #ffffff;
            --text: #2d3748;
            --text-light: #718096;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text); background: var(--bg); }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; height: auto; }

        /* ===== TOP BAR ===== */
        .topbar { background: var(--green-darker); color: rgba(255,255,255,0.8); font-size: 12px; padding: 8px 0; }
        .topbar .container { display: flex; justify-content: space-between; align-items: center; }
        .topbar a { color: rgba(255,255,255,0.8); transition: color 0.2s; }
        .topbar a:hover { color: var(--gold-light); }
        .topbar .topbar-links { display: flex; gap: 16px; align-items: center; }
        .topbar .topbar-links i { margin-right: 4px; }

        /* ===== HEADER ===== */
        .header { background: var(--white); box-shadow: var(--shadow); position: relative; z-index: 100; }
        .header-main { display: flex; align-items: center; padding: 16px 0; gap: 16px; }
        .header-logo {
            width: 64px; height: 64px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
        }
        .header-logo img { width: 100%; height: 100%; object-fit: contain; }
        .header-text h1 { font-size: 13px; font-weight: 600; color: var(--text-light); text-transform: uppercase; letter-spacing: 1px; }
        .header-text h2 { font-size: 22px; font-weight: 800; color: var(--green); line-height: 1.2; }

        /* ===== NAVIGATION ===== */
        .navbar {
            background: linear-gradient(135deg, var(--green) 0%, var(--green-light) 100%);
            position: sticky; top: 0; z-index: 999;
            box-shadow: 0 4px 20px rgba(13,92,47,0.3);
        }
        .nav-container { display:flex;align-items:center;justify-content:space-between; }
        .nav-list { display: flex; list-style: none; margin: 0; padding: 0; flex: 1; }
        .nav-list > li { position: relative; }
        .nav-list > li > a {
            display: flex; align-items: center; gap: 6px; padding: 14px 18px;
            color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 500;
            transition: 0.2s; white-space: nowrap; border-bottom: 3px solid transparent;
        }
        .nav-list > li > a:hover, .nav-list > li:hover > a {
            background: rgba(255,255,255,0.1); color: var(--gold-light);
            border-bottom-color: var(--gold);
        }

        /* Search */
        .nav-search { position: relative; }
        .nav-search-btn {
            background: rgba(255,255,255,0.15); border: none; color: #fff;
            width: 40px; height: 40px; border-radius: 50%; cursor: pointer;
            font-size: 14px; transition: 0.2s; display: flex; align-items: center; justify-content: center;
        }
        .nav-search-btn:hover { background: rgba(255,255,255,0.25); }
        .search-dropdown {
            display: none; position: absolute; top: 100%; right: 0;
            background: var(--white); border-radius: 0 0 12px 12px;
            box-shadow: var(--shadow-lg); padding: 16px; width: 320px;
            border-top: 3px solid var(--gold); animation: slideDown 0.2s ease;
        }
        .search-dropdown.open { display: block; }
        .search-dropdown input {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border);
            border-radius: 8px; font-size: 14px; font-family: inherit;
        }
        .search-dropdown input:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(13,92,47,0.1); }
        .search-dropdown button {
            width: 100%; padding: 10px; background: var(--green); color: #fff;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; margin-top: 8px; font-family: inherit; transition: 0.2s;
        }
        .search-dropdown button:hover { background: var(--green-light); }

        /* Dropdown */
        .dropdown-menu {
            display: none; position: absolute; top: 100%; left: 0;
            background: var(--white); min-width: 280px; box-shadow: var(--shadow-lg);
            border-radius: 0 0 8px 8px; border-top: 3px solid var(--gold); z-index: 1000;
            animation: slideDown 0.2s ease;
        }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .nav-list > li:hover > .dropdown-menu { display: block; }
        .dropdown-menu a {
            display: block; padding: 10px 20px; font-size: 13px; color: var(--text);
            transition: 0.15s; border-left: 3px solid transparent;
        }
        .dropdown-menu a:hover { background: #f0faf3; color: var(--green); border-left-color: var(--gold); padding-left: 24px; }
        .dropdown-menu .has-submenu { position: relative; }
        .dropdown-menu .has-submenu > a::after { content: '›'; float: right; font-size: 16px; color: #999; }
        .dropdown-menu .submenu {
            display: none; position: absolute; left: 100%; top: 0;
            background: var(--white); min-width: 260px; box-shadow: var(--shadow-lg);
            border-radius: 0 8px 8px 0; border-top: 3px solid var(--gold);
        }
        .dropdown-menu .has-submenu:hover > .submenu { display: block; }

        .mobile-toggle { display: none; background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; padding: 14px 18px; }

        /* ===== CONTAINER ===== */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* ===== HERO SLIDER ===== */
        .hero-slider { position: relative; height: 460px; overflow: hidden; background: var(--green-dark); }
        .hero-slider .slide {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0; transition: opacity 0.8s ease;
        }
        .hero-slider .slide.active { opacity: 1; }
        .hero-slider .slide img { width: 100%; height: 100%; object-fit: cover; }
        .hero-slider .slide-overlay {
            position: absolute; padding: 40px;
            max-width: 600px;
        }
        .hero-slider .slide-overlay.pos-bottom-left { bottom: 0; left: 0; background: linear-gradient(45deg, rgba(6,45,23,0.9) 0%, transparent 100%); right: 0; max-width: 100%; }
        .hero-slider .slide-overlay.pos-bottom-center { bottom: 0; left: 0; right: 0; text-align: center; background: linear-gradient(transparent, rgba(6,45,23,0.85)); max-width: 100%; }
        .hero-slider .slide-overlay.pos-center-left { top: 50%; left: 0; transform: translateY(-50%); }
        .hero-slider .slide-overlay.pos-center-center { top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
        .hero-slider .slide-overlay.pos-top-left { top: 0; left: 0; background: linear-gradient(180deg, rgba(6,45,23,0.85) 0%, transparent 100%); right: 0; max-width: 100%; }
        .hero-slider .slide-overlay h3 { color: #fff; font-size: 32px; font-weight: 800; line-height: 1.2; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
        .hero-slider .slide-overlay p { color: rgba(255,255,255,0.85); font-size: 15px; margin-top: 10px; line-height: 1.6; text-shadow: 0 1px 4px rgba(0,0,0,0.3); }
        .hero-slider .slider-nav { position: absolute; bottom: 24px; right: 40px; display: flex; gap: 8px; z-index: 10; }
        .hero-slider .slider-nav button {
            width: 44px; height: 44px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.15); color: #fff; cursor: pointer;
            font-size: 16px; transition: 0.2s; backdrop-filter: blur(4px);
        }
        .hero-slider .slider-nav button:hover { background: var(--gold); border-color: var(--gold); color: var(--green-dark); }
        .slider-dots { position: absolute; bottom: 28px; left: 40px; display: flex; gap: 8px; z-index: 10; }
        .slider-dots .dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; transition: 0.3s; }
        .slider-dots .dot.active { background: var(--gold); width: 28px; border-radius: 5px; }

        /* ===== WELCOME STRIP ===== */
        .welcome-strip { background: linear-gradient(135deg, var(--green-dark), var(--green)); padding: 20px 0; text-align: center; }
        .welcome-strip h3 { color: #fff; font-size: 16px; font-weight: 700; letter-spacing: 0.5px; }
        .welcome-strip h3 i { color: var(--gold); }
        .welcome-strip p { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 4px; }

        /* ===== QUICK LINKS ===== */
        .quick-links { background: #f0f4f1; padding: 32px 0; border-bottom: 1px solid var(--border); }
        .quick-links-grid { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .quick-link-card {
            background: var(--white); border-radius: 14px; padding: 24px 20px;
            display: flex; align-items: flex-start; gap: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid var(--border);
            transition: 0.3s; flex: 1; min-width: 200px; max-width: 240px;
        }
        .quick-link-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); border-color: var(--green); }
        .quick-link-icon {
            width: 48px; height: 48px; flex-shrink: 0; border-radius: 12px;
            border: 2px solid var(--green); display: flex; align-items: center;
            justify-content: center; overflow: hidden; background: #f8faf8;
        }
        .quick-link-icon img { width: 28px; height: 28px; object-fit: contain; }
        .quick-link-icon i { font-size: 20px; color: var(--green); }
        .quick-link-info h4 { font-size: 13px; font-weight: 700; color: #1a3320; line-height: 1.3; margin-bottom: 4px; text-transform: uppercase; }
        .quick-link-info p { font-size: 11px; color: var(--text-light); line-height: 1.5; }

        /* ===== SECTION ===== */
        .section { padding: 60px 0; }
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
        .section-title { font-size: 24px; font-weight: 700; color: var(--green); position: relative; padding-bottom: 12px; }
        .section-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background: linear-gradient(90deg, var(--gold), var(--gold-light)); border-radius: 2px; }
        .section-link { color: var(--green); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: 0.2s; }
        .section-link:hover { color: var(--gold-dark); }

        /* ===== POST CARDS ===== */
        .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px; }
        .post-card { background: var(--white); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); transition: 0.3s; border: 1px solid var(--border); }
        .post-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
        .post-card .card-img { height: 200px; background: linear-gradient(135deg, var(--green), var(--green-light)); overflow: hidden; position: relative; }
        .post-card .card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
        .post-card:hover .card-img img { transform: scale(1.05); }
        .post-card .card-img .category-badge { position: absolute; top: 12px; left: 12px; background: var(--gold); color: var(--green-dark); font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
        .post-card .card-body { padding: 20px; }
        .post-card .card-date { font-size: 12px; color: var(--text-light); display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
        .post-card .card-title { font-size: 16px; font-weight: 600; color: #1a3320; line-height: 1.4; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .post-card .card-excerpt { font-size: 13px; color: var(--text-light); line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

        /* ===== PENGUMUMAN LIST ===== */
        .pengumuman-list { list-style: none; }
        .pengumuman-item { display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--border); transition: 0.2s; }
        .pengumuman-item:hover { padding-left: 8px; }
        .pengumuman-item:last-child { border-bottom: none; }
        .pengumuman-item .date-box {
            flex-shrink: 0; width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--green), var(--green-light));
            border-radius: 10px; display: flex; flex-direction: column;
            align-items: center; justify-content: center; color: #fff;
        }
        .pengumuman-item .date-box .day { font-size: 20px; font-weight: 700; line-height: 1; }
        .pengumuman-item .date-box .month { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .pengumuman-item .item-content h4 { font-size: 14px; font-weight: 600; color: var(--green); line-height: 1.4; margin-bottom: 4px; }
        .pengumuman-item .item-content p { font-size: 12px; color: var(--text-light); }

        /* ===== SURVEY SECTION ===== */
        .survey-section {
            background: linear-gradient(135deg, var(--green-dark), var(--green));
            padding: 60px 0; position: relative; overflow: hidden;
        }
        .survey-section::before {
            content: ''; position: absolute; top: -50px; right: -50px;
            width: 200px; height: 200px; border-radius: 50%;
            background: rgba(200,169,81,0.08);
        }
        .survey-section::after {
            content: ''; position: absolute; bottom: -80px; left: -80px;
            width: 300px; height: 300px; border-radius: 50%;
            background: rgba(255,255,255,0.03);
        }
        .survey-header { text-align: center; margin-bottom: 40px; position: relative; z-index: 1; }
        .survey-header h2 { color: #fff; font-size: 28px; font-weight: 800; }
        .survey-header p { color: rgba(255,255,255,0.7); font-size: 14px; margin-top: 8px; max-width: 500px; margin-left: auto; margin-right: auto; }
        .survey-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; position: relative; z-index: 1; }
        .survey-card {
            background: #0a4a26;
            border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;
            padding: 36px 24px; text-align: center; transition: 0.3s;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .survey-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,0.25); border-color: rgba(200,169,81,0.3); }
        .survey-card .survey-icon { font-size: 36px; color: var(--gold); margin-bottom: 18px; }
        .survey-card .survey-title { color: #fff; font-size: 14px; font-weight: 600; margin-bottom: 18px; letter-spacing: 0.3px; }
        .survey-card .survey-value { color: #fff; font-size: 48px; font-weight: 800; line-height: 1; margin-bottom: 14px; }
        .survey-card .survey-label {
            display: inline-block; padding: 5px 18px; border-radius: 20px;
            background: var(--green-light); color: #fff; font-size: 12px; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.15);
        }

        /* ===== SIDEBAR ===== */
        .page-layout { display: grid; grid-template-columns: 280px 1fr; gap: 32px; padding: 40px 0; }
        .page-sidebar .sidebar-menu { background: var(--white); border-radius: 12px; box-shadow: var(--shadow); border: 1px solid var(--border); overflow: hidden; }
        .page-sidebar .sidebar-title { background: linear-gradient(135deg, var(--green), var(--green-light)); color: #fff; padding: 16px 20px; font-size: 14px; font-weight: 600; }
        .page-sidebar .sidebar-menu a { display: block; padding: 10px 20px; font-size: 13px; color: var(--text); border-bottom: 1px solid #f0f2f5; transition: 0.15s; border-left: 3px solid transparent; }
        .page-sidebar .sidebar-menu a:hover, .page-sidebar .sidebar-menu a.active { background: #f0faf3; color: var(--green); border-left-color: var(--gold); }

        /* ===== PAGE CONTENT ===== */
        .page-content { background: var(--white); border-radius: 12px; padding: 36px; box-shadow: var(--shadow); border: 1px solid var(--border); }
        .page-content h1 { font-size: 24px; font-weight: 700; color: var(--green); margin-bottom: 8px; }
        .page-content .breadcrumb { font-size: 12px; color: var(--text-light); margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border); }
        .page-content .breadcrumb a { color: var(--gold-dark); }
        .page-content .breadcrumb a:hover { text-decoration: underline; }
        .page-content .content-body { font-size: 15px; line-height: 1.8; color: var(--text); }
        .page-content .content-body p { margin-bottom: 16px; }
        .page-content .content-body h2, .page-content .content-body h3 { color: var(--green); margin: 24px 0 12px; }
        .page-content .content-body ul, .page-content .content-body ol { margin: 12px 0; padding-left: 24px; }
        .page-content .content-body li { margin-bottom: 6px; }
        .page-content .content-body table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .page-content .content-body table th, .page-content .content-body table td { padding: 10px 14px; border: 1px solid var(--border); font-size: 13px; }
        .page-content .content-body table th { background: var(--green); color: #fff; font-weight: 600; }

        /* ===== FOOTER ===== */
        .footer { background: var(--green-darker); color: rgba(255,255,255,0.8); padding: 48px 0 0; }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1.5fr; gap: 32px; padding-bottom: 40px; }
        .footer h4 { color: var(--gold); font-size: 14px; font-weight: 600; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 1px; }
        .footer p, .footer a { font-size: 13px; line-height: 1.7; }
        .footer a { color: rgba(255,255,255,0.7); transition: 0.2s; }
        .footer a:hover { color: var(--gold-light); }
        .footer ul { list-style: none; }
        .footer ul li { margin-bottom: 8px; }
        .footer ul li a { display: flex; align-items: center; gap: 8px; }
        .footer ul li a::before { content: '›'; color: var(--gold); font-weight: 700; }
        .footer-map { border-radius: 10px; overflow: hidden; border: 2px solid rgba(255,255,255,0.1); }
        .footer-map iframe { width: 100%; height: 200px; border: 0; }
        .footer-stats {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 20px 0;
            display: flex; align-items: center; justify-content: center; gap: 40px;
        }
        .footer-stats .stat-item { text-align: center; }
        .footer-stats .stat-item .stat-num { font-size: 22px; font-weight: 700; color: var(--gold); }
        .footer-stats .stat-item .stat-lbl { font-size: 11px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.5px; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 20px 0; text-align: center; font-size: 12px; color: rgba(255,255,255,0.5);
        }

        /* ===== LOGIN OVERRIDE ===== */
        .login-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--green-darker), var(--green)); }
        .login-box { background: var(--white); border-radius: 16px; padding: 48px; width: 100%; max-width: 440px; box-shadow: var(--shadow-lg); }
        .login-box h2 { text-align: center; color: var(--green); margin-bottom: 32px; font-size: 20px; }
        .login-box .form-group { margin-bottom: 20px; }
        .login-box label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; }
        .login-box input[type="text"], .login-box input[type="email"], .login-box input[type="password"] { width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; }
        .login-box input:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(13,92,47,0.1); }
        .login-box .btn-login { width: 100%; padding: 14px; background: linear-gradient(135deg, var(--green), var(--green-light)); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; transition: 0.2s; }
        .login-box .btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,92,47,0.3); }

        /* ===== PAGINATION ===== */
        .pub-pagination { display: flex; gap: 6px; justify-content: center; padding: 32px 0; }
        .pub-pagination a, .pub-pagination span { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; }
        .pub-pagination a { background: var(--white); color: var(--green); border: 1px solid var(--border); transition: 0.2s; }
        .pub-pagination a:hover { background: var(--green); color: #fff; }
        .pub-pagination .active span { background: var(--green); color: #fff; border-radius: 8px; }

        /* ===== SCROLL ANIMATIONS ===== */
        .fade-up { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .fade-up.visible { opacity: 1; transform: translateY(0); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .hero-slider { height: 300px; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .page-layout { grid-template-columns: 1fr; }
            .posts-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
        }
        @media (max-width: 768px) {
            .topbar .container { flex-direction: column; gap: 4px; }
            .mobile-toggle { display: block; }
            .nav-list {
                display: none; flex-direction: column; position: absolute;
                top: 100%; left: 0; right: 0; background: var(--green);
                box-shadow: var(--shadow-lg); max-height: 80vh; overflow-y: auto;
            }
            .nav-list.open { display: flex; }
            .nav-list > li > a { border-bottom: 1px solid rgba(255,255,255,0.05); }
            .dropdown-menu { position: static; box-shadow: none; border-radius: 0; border-top: none; background: #e8f5ec; }
            .dropdown-menu .submenu { position: static; box-shadow: none; border-radius: 0; border-top: none; }
            .hero-slider { height: 220px; }
            .hero-slider .slide-overlay { padding: 20px; }
            .hero-slider .slide-overlay h3 { font-size: 18px; }
            .posts-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-stats { flex-wrap: wrap; gap: 20px; }
            .nav-search { margin: 0 12px; }
            .custom-cursor, .cursor-ring { display: none !important; }
        }

        /* ===== CUSTOM CURSOR ===== */
        .custom-cursor {
            position: fixed; top: 0; left: 0; z-index: 99999;
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--gold); pointer-events: none;
            transform: translate(-50%, -50%);
            transition: width 0.2s, height 0.2s, background 0.2s, box-shadow 0.2s;
            box-shadow: 0 0 12px rgba(200,169,81,0.5), 0 0 24px rgba(200,169,81,0.2);
        }
        .custom-cursor.hover {
            width: 16px; height: 16px;
            background: var(--green-light);
            box-shadow: 0 0 18px rgba(26,122,66,0.5), 0 0 36px rgba(26,122,66,0.2);
        }
        .custom-cursor.click {
            width: 8px; height: 8px;
            background: #fff;
            box-shadow: 0 0 20px rgba(255,255,255,0.6);
        }
        .cursor-ring {
            position: fixed; top: 0; left: 0; z-index: 99998;
            width: 36px; height: 36px; border-radius: 50%;
            border: 1.5px solid rgba(200,169,81,0.35);
            pointer-events: none; transform: translate(-50%, -50%);
            transition: width 0.25s ease-out, height 0.25s ease-out, border-color 0.25s, opacity 0.25s;
        }
        .cursor-ring.hover {
            width: 50px; height: 50px;
            border-color: rgba(26,122,66,0.4);
        }
        .cursor-ring.click {
            width: 28px; height: 28px;
            border-color: rgba(200,169,81,0.6);
        }
        .cursor-sparkle {
            position: fixed; z-index: 99997; pointer-events: none;
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--gold-light);
            animation: sparkle-fade 0.6s ease-out forwards;
        }
        @keyframes sparkle-fade {
            0% { opacity: 1; transform: translate(-50%,-50%) scale(1); }
            100% { opacity: 0; transform: translate(-50%,-50%) scale(0); }
        }
        .cursor-trail {
            position: fixed; z-index: 99996; pointer-events: none;
            width: 4px; height: 4px; border-radius: 50%;
            background: rgba(200,169,81,0.3);
            animation: trail-fade 0.4s ease-out forwards;
        }
        @keyframes trail-fade {
            0% { opacity: 0.5; transform: translate(-50%,-50%) scale(1); }
            100% { opacity: 0; transform: translate(-50%,-50%) scale(0.3); }
        }

        /* Hide default cursor on desktop */
        @media (min-width: 769px) {
            *, *::before, *::after { cursor: none !important; }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="topbar">
        <div class="container">
            <div><i class="fas fa-map-marker-alt"></i> Jl. Brawijaya, Manokwari Timur, Papua Barat</div>
            <div class="topbar-links">
                <a href="ptapapuabarat@gmail.com"><i class="fas fa-envelope"></i> ptapapuabarat@gmail.com</a>
                <a href="#"><i class="fas fa-phone"></i> 0811 4088 3744</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-main">
                <div class="header-logo"><img src="{{ asset('logo_pta.png') }}" alt="Logo PTA Papua Barat"></div>
                <div class="header-text">
                    <h1>Mahkamah Agung Republik Indonesia</h1>
                    <h2>Pengadilan Tinggi Agama Papua Barat</h2>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container">
            <button class="mobile-toggle" onclick="document.querySelector('.nav-list').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-list">
                <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Beranda</a></li>
                @php
                    $menuGroups = [
                        'tentang-pengadilan' => 'Tentang Pengadilan',
                        'informasi-umum' => 'Informasi Umum',
                        'informasi-hukum' => 'Informasi Hukum',
                        'transparansi' => 'Transparansi',
                        'peraturan-kebijakan' => 'Peraturan & Kebijakan',
                        'informasi' => 'Informasi',
                    ];
                @endphp
                @foreach($menuGroups as $groupSlug => $groupName)
                    @php $topLevelPages = \App\Page::where('menu_group', $groupSlug)->whereNull('parent_id')->where('is_active', true)->orderBy('order')->get(); @endphp
                    @if($topLevelPages->count() > 0)
                    <li>
                        <a href="#">{{ $groupName }} <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
                        <div class="dropdown-menu">
                            @foreach($topLevelPages as $menuPage)
                                @php $children = $menuPage->activeChildren; @endphp
                                @if($children->count() > 0)
                                    <div class="has-submenu">
                                        <a href="{{ route('page.show', $menuPage->slug) }}">{{ $menuPage->title }}</a>
                                        <div class="submenu">
                                            @foreach($children as $child)
                                                <a href="{{ route('page.show', $child->slug) }}">{{ $child->title }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ route('page.show', $menuPage->slug) }}">{{ $menuPage->title }}</a>
                                @endif
                            @endforeach
                        </div>
                    </li>
                    @endif
                @endforeach
                <li><a href="{{ route('pengumuman.index') }}">Pengumuman</a></li>
            </ul>
            <!-- Search -->
            <div class="nav-search">
                <button class="nav-search-btn" onclick="document.getElementById('searchDrop').classList.toggle('open')">
                    <i class="fas fa-search"></i>
                </button>
                <div class="search-dropdown" id="searchDrop">
                    <form action="{{ route('search') }}" method="GET">
                        <input type="text" name="q" placeholder="Cari berita, halaman..." required autofocus>
                        <button type="submit"><i class="fas fa-search"></i> Cari</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h4>Pengadilan Tinggi Agama Papua Barat</h4>
                    <p>Jl. Brawijaya, Kelurahan Manokwari Timur, Distrik Manokwari, Provinsi Papua Barat. Kode POS 98311</p>
                    <p style="margin-top:12px;">
                        <i class="fas fa-phone" style="color:var(--gold);margin-right:6px;"></i> 0811 4088 3744<br>
                        <i class="fas fa-envelope" style="color:var(--gold);margin-right:6px;"></i> ptapapuabarat@gmail.com
                    </p>
                </div>
                <div>
                    <h4>Tautan Cepat</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        <li><a href="{{ route('berita.index') }}">Berita</a></li>
                        <li><a href="{{ route('pengumuman.index') }}">Pengumuman</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Tautan Eksternal</h4>
                    <ul>
                        <li><a href="https://www.mahkamahagung.go.id" target="_blank">Mahkamah Agung RI</a></li>
                        <li><a href="https://badilag.mahkamahagung.go.id" target="_blank">Badilag</a></li>
                        <li><a href="https://sipp.mahkamahagung.go.id" target="_blank">SIPP</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Lokasi Kantor</h4>
                    <div class="footer-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.682!2d134.0816!3d-0.8613!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d540a6e252a3c1d%3A0x3b7b0e86f5a7b84e!2sManokwari%2C+Papua+Barat!5e0!3m2!1sid!2sid!4v1" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
            <!-- Visitor Stats -->
            <div class="footer-stats">
                @php
                    $todayCount = \App\VisitorCount::where('visit_date', now()->toDateString())->value('count') ?? 0;
                    $totalCount = \App\VisitorCount::sum('count');
                @endphp
                <div class="stat-item">
                    <div class="stat-num">{{ number_format($todayCount) }}</div>
                    <div class="stat-lbl">Hari Ini</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">{{ number_format($totalCount) }}</div>
                    <div class="stat-lbl">Total Pengunjung</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num"><i class="fas fa-circle" style="color:#34d399;font-size:10px;"></i> Online</div>
                    <div class="stat-lbl">Status Server</div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Pengadilan Tinggi Agama Papua Barat. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    <!-- Scroll Animation Observer -->
    <script>
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.fade-up').forEach(function(el) { observer.observe(el); });
    </script>

    <!-- Custom Cursor -->
    <div class="custom-cursor" id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>
    <script>
    (function() {
        if (window.innerWidth <= 768 || 'ontouchstart' in window) return;

        var dot = document.getElementById('cursorDot');
        var ring = document.getElementById('cursorRing');
        var mouseX = 0, mouseY = 0, ringX = 0, ringY = 0;
        var trailCounter = 0;

        document.addEventListener('mousemove', function(e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
            dot.style.left = mouseX + 'px';
            dot.style.top = mouseY + 'px';

            // Trail particles every 3rd move
            trailCounter++;
            if (trailCounter % 3 === 0) {
                var trail = document.createElement('div');
                trail.className = 'cursor-trail';
                trail.style.left = mouseX + 'px';
                trail.style.top = mouseY + 'px';
                document.body.appendChild(trail);
                setTimeout(function() { trail.remove(); }, 400);
            }
        });

        // Smooth ring follow
        function animateRing() {
            ringX += (mouseX - ringX) * 0.15;
            ringY += (mouseY - ringY) * 0.15;
            ring.style.left = ringX + 'px';
            ring.style.top = ringY + 'px';
            requestAnimationFrame(animateRing);
        }
        animateRing();

        // Hover detection
        document.addEventListener('mouseover', function(e) {
            var t = e.target;
            if (t.matches('a, button, input, textarea, select, [onclick], .quick-link-card, .post-card, .survey-card, .nav-search-btn, .slider-dots .dot')) {
                dot.classList.add('hover');
                ring.classList.add('hover');
            }
        });
        document.addEventListener('mouseout', function(e) {
            var t = e.target;
            if (t.matches('a, button, input, textarea, select, [onclick], .quick-link-card, .post-card, .survey-card, .nav-search-btn, .slider-dots .dot')) {
                dot.classList.remove('hover');
                ring.classList.remove('hover');
            }
        });

        // Click sparkle effect
        document.addEventListener('mousedown', function(e) {
            dot.classList.add('click');
            ring.classList.add('click');
            // Create sparkle particles
            for (var i = 0; i < 6; i++) {
                var spark = document.createElement('div');
                spark.className = 'cursor-sparkle';
                var angle = (Math.PI * 2 / 6) * i;
                var dist = 18 + Math.random() * 14;
                spark.style.left = (e.clientX + Math.cos(angle) * dist) + 'px';
                spark.style.top = (e.clientY + Math.sin(angle) * dist) + 'px';
                spark.style.width = (3 + Math.random() * 4) + 'px';
                spark.style.height = spark.style.width;
                document.body.appendChild(spark);
                setTimeout(function() { spark.remove(); }, 600);
            }
        });
        document.addEventListener('mouseup', function() {
            dot.classList.remove('click');
            ring.classList.remove('click');
        });

        // Hide cursor when leaving window
        document.addEventListener('mouseleave', function() {
            dot.style.opacity = '0';
            ring.style.opacity = '0';
        });
        document.addEventListener('mouseenter', function() {
            dot.style.opacity = '1';
            ring.style.opacity = '1';
        });
    })();
    </script>
    <!-- Text-to-Speech Widget -->
    <div id="tts-widget" class="tts-widget">
        <button id="tts-toggle" class="tts-toggle" title="Text-to-Speech">
            <i class="fas fa-volume-up" id="tts-icon"></i>
        </button>
        <div id="tts-panel" class="tts-panel" style="display:none;">
            <div class="tts-header">
                <span><i class="fas fa-headphones"></i> Baca Halaman</span>
                <button id="tts-close" class="tts-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="tts-controls">
                <button id="tts-play" class="tts-btn tts-btn-play" title="Play"><i class="fas fa-play"></i></button>
                <button id="tts-pause" class="tts-btn" title="Pause" style="display:none;"><i class="fas fa-pause"></i></button>
                <button id="tts-stop" class="tts-btn" title="Stop"><i class="fas fa-stop"></i></button>
                <div class="tts-speed">
                    <label>Kecepatan</label>
                    <input type="range" id="tts-rate" min="0.5" max="2" step="0.1" value="1">
                    <span id="tts-rate-val">1x</span>
                </div>
            </div>
            <div id="tts-status" class="tts-status">Siap membaca...</div>
        </div>
    </div>

    <style>
    .tts-widget { position: fixed; bottom: 24px; right: 24px; z-index: 9000; }
    .tts-toggle {
        width: 52px; height: 52px; border-radius: 50%;
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: #fff; border: none; cursor: pointer;
        font-size: 20px; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 20px rgba(13,92,47,0.4);
        transition: 0.3s; position: relative;
    }
    .tts-toggle:hover { transform: scale(1.1); box-shadow: 0 6px 28px rgba(13,92,47,0.5); }
    .tts-toggle.speaking { animation: tts-pulse 1.5s infinite; }
    @keyframes tts-pulse {
        0%, 100% { box-shadow: 0 4px 20px rgba(13,92,47,0.4); }
        50% { box-shadow: 0 4px 20px rgba(13,92,47,0.4), 0 0 0 12px rgba(13,92,47,0.15); }
    }
    .tts-panel {
        position: absolute; bottom: 62px; right: 0;
        width: 280px; background: #fff;
        border-radius: 14px; box-shadow: 0 8px 40px rgba(0,0,0,0.15);
        overflow: hidden; animation: tts-slideUp 0.25s ease;
    }
    @keyframes tts-slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .tts-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: #fff; font-size: 13px; font-weight: 600;
    }
    .tts-header i { margin-right: 6px; }
    .tts-close { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; font-size: 14px; padding: 4px; transition: 0.2s; }
    .tts-close:hover { color: #fff; }
    .tts-controls { display: flex; align-items: center; gap: 8px; padding: 14px 16px; }
    .tts-btn {
        width: 36px; height: 36px; border-radius: 50%;
        border: 1px solid #e0e0e0; background: #f8f9fa;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        font-size: 13px; color: #555; transition: 0.2s;
    }
    .tts-btn:hover { background: var(--green); color: #fff; border-color: var(--green); }
    .tts-btn-play { background: var(--green); color: #fff; border-color: var(--green); }
    .tts-speed { flex: 1; display: flex; flex-direction: column; gap: 2px; margin-left: 4px; }
    .tts-speed label { font-size: 10px; color: #999; font-weight: 600; text-transform: uppercase; }
    .tts-speed input[type=range] { width: 100%; height: 4px; accent-color: var(--green); }
    #tts-rate-val { font-size: 11px; color: var(--green); font-weight: 700; }
    .tts-status { padding: 8px 16px 12px; font-size: 12px; color: #888; border-top: 1px solid #f0f0f0; }

    @media (max-width: 768px) {
        .tts-widget { bottom: 16px; right: 16px; }
        .tts-toggle { width: 46px; height: 46px; font-size: 18px; }
        .tts-panel { width: 260px; }
    }
    </style>

    <script>
    (function() {
        if (!('speechSynthesis' in window)) {
            var w = document.getElementById('tts-widget');
            if (w) w.style.display = 'none';
            return;
        }

        var synth = window.speechSynthesis;
        var toggleBtn = document.getElementById('tts-toggle');
        var panel = document.getElementById('tts-panel');
        var playBtn = document.getElementById('tts-play');
        var pauseBtn = document.getElementById('tts-pause');
        var stopBtn = document.getElementById('tts-stop');
        var closeBtn = document.getElementById('tts-close');
        var rateInput = document.getElementById('tts-rate');
        var rateVal = document.getElementById('tts-rate-val');
        var statusEl = document.getElementById('tts-status');
        var ttsIcon = document.getElementById('tts-icon');
        var panelOpen = false;
        var utterance = null;
        var isSpeaking = false;
        var isPaused = false;

        function getPageText() {
            var selectors = ['.content-body', '.page-content', '.hero-content', '.posts-grid'];
            var text = '';
            for (var i = 0; i < selectors.length; i++) {
                var els = document.querySelectorAll(selectors[i]);
                els.forEach(function(el) { text += el.innerText + ' '; });
            }
            text = text.replace(/\s+/g, ' ').trim();
            return text || '';
        }

        function startSpeaking() {
            synth.cancel();
            var text = getPageText();
            if (!text) {
                statusEl.textContent = 'Tidak ada teks untuk dibaca.';
                return;
            }
            utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = parseFloat(rateInput.value);

            // Try to find Indonesian voice
            var voices = synth.getVoices();
            for (var i = 0; i < voices.length; i++) {
                if (voices[i].lang.startsWith('id')) { utterance.voice = voices[i]; break; }
            }

            utterance.onstart = function() {
                isSpeaking = true; isPaused = false;
                playBtn.style.display = 'none';
                pauseBtn.style.display = 'flex';
                toggleBtn.classList.add('speaking');
                ttsIcon.className = 'fas fa-volume-up';
                statusEl.textContent = 'Sedang membaca...';
            };
            utterance.onend = function() {
                isSpeaking = false; isPaused = false;
                playBtn.style.display = 'flex';
                pauseBtn.style.display = 'none';
                toggleBtn.classList.remove('speaking');
                statusEl.textContent = 'Selesai.';
            };
            utterance.onerror = function() {
                isSpeaking = false;
                playBtn.style.display = 'flex';
                pauseBtn.style.display = 'none';
                toggleBtn.classList.remove('speaking');
                statusEl.textContent = 'Terjadi kesalahan.';
            };

            synth.speak(utterance);
        }

        function togglePanel() {
            panelOpen = !panelOpen;
            panel.style.display = panelOpen ? 'block' : 'none';
        }

        toggleBtn.addEventListener('click', function() {
            if (!panelOpen) {
                togglePanel();
                startSpeaking();
            } else {
                if (isSpeaking && !isPaused) {
                    synth.pause();
                    isPaused = true;
                    playBtn.style.display = 'flex';
                    pauseBtn.style.display = 'none';
                    toggleBtn.classList.remove('speaking');
                    statusEl.textContent = 'Dijeda.';
                } else if (isPaused) {
                    synth.resume();
                    isPaused = false;
                    playBtn.style.display = 'none';
                    pauseBtn.style.display = 'flex';
                    toggleBtn.classList.add('speaking');
                    statusEl.textContent = 'Sedang membaca...';
                } else {
                    startSpeaking();
                }
            }
        });

        playBtn.addEventListener('click', function() {
            if (isPaused) {
                synth.resume();
                isPaused = false;
                playBtn.style.display = 'none';
                pauseBtn.style.display = 'flex';
                toggleBtn.classList.add('speaking');
                statusEl.textContent = 'Sedang membaca...';
            } else {
                startSpeaking();
            }
        });

        pauseBtn.addEventListener('click', function() {
            synth.pause();
            isPaused = true;
            playBtn.style.display = 'flex';
            pauseBtn.style.display = 'none';
            toggleBtn.classList.remove('speaking');
            statusEl.textContent = 'Dijeda.';
        });

        stopBtn.addEventListener('click', function() {
            synth.cancel();
            isSpeaking = false; isPaused = false;
            playBtn.style.display = 'flex';
            pauseBtn.style.display = 'none';
            toggleBtn.classList.remove('speaking');
            statusEl.textContent = 'Dihentikan.';
        });

        closeBtn.addEventListener('click', function() {
            synth.cancel();
            isSpeaking = false; isPaused = false;
            playBtn.style.display = 'flex';
            pauseBtn.style.display = 'none';
            toggleBtn.classList.remove('speaking');
            panelOpen = false;
            panel.style.display = 'none';
            statusEl.textContent = 'Siap membaca...';
        });

        rateInput.addEventListener('input', function() {
            rateVal.textContent = this.value + 'x';
            if (isSpeaking) {
                var wasPlaying = !isPaused;
                synth.cancel();
                if (wasPlaying) startSpeaking();
            }
        });

        // Preload voices
        if (synth.getVoices().length === 0) {
            synth.addEventListener('voiceschanged', function() {});
        }
    })();
    </script>

    @yield('scripts')
</body>
</html>
