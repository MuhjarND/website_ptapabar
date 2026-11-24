<!-- Top Bar -->
<div class="topbar">
    <div class="container">
        <div><i class="fas fa-map-marker-alt"></i> {{ $publicSiteSettings['address'] }}</div>
        <div class="topbar-links">
            <a href="mailto:{{ $publicSiteSettings['email'] }}"><i class="fas fa-envelope"></i> {{ $publicSiteSettings['email'] }}</a>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $publicSiteSettings['phone']) }}"><i class="fas fa-phone"></i> {{ $publicSiteSettings['phone'] }}</a>
            @if(!empty($publicSiteSettings['instagram_url']) || !empty($publicSiteSettings['facebook_url']) || !empty($publicSiteSettings['youtube_url']))
                <div class="topbar-social-links">
                    @if(!empty($publicSiteSettings['instagram_url']))
                        <a href="{{ $publicSiteSettings['instagram_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    @endif
                    @if(!empty($publicSiteSettings['facebook_url']))
                        <a href="{{ $publicSiteSettings['facebook_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if(!empty($publicSiteSettings['youtube_url']))
                        <a href="{{ $publicSiteSettings['youtube_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-main">
            <a href="{{ route('home') }}" class="header-identity" aria-label="Beranda {{ $publicSiteSettings['site_name'] }}">
                <div class="header-logo"><img src="{{ asset('logo-pta-160.webp') }}" width="160" height="199" alt="Logo PTA Papua Barat"></div>
                <div class="header-text">
                    <h1>Mahkamah Agung Republik Indonesia</h1>
                    <h2>{{ $publicSiteSettings['site_name'] }}</h2>
                    <p>Modern, Akuntabel, Jujur, Unggul</p>
                </div>
            </a>
            <div class="header-utility-links" aria-label="Layanan utama">
                <a href="{{ route('home') }}#zona-integritas" class="header-utility-link">
                    <i class="fas fa-award"></i>
                    <span><strong>Zona Integritas</strong><small>Menuju WBK / WBBM</small></span>
                </a>
                <a href="https://cctv.badilag.net/display/satker/86a6f4a5d2011e7377a09309ed4f605d" class="header-utility-link" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    <span><strong>CCTV</strong><small>Pantau CCTV</small></span>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Navigation -->
@php
    $pageSlug = request()->routeIs('page.show') ? request()->route('slug') : null;
    $pageTrail = $pageSlug === null ? [] : [...($breadcrumbs ?? collect())->pluck('slug')->all(), $pageSlug];
@endphp
<nav class="navbar" aria-label="Navigasi utama">
    <div class="container nav-container">
        <button type="button" class="mobile-toggle" aria-expanded="false" aria-controls="site-navigation" data-mobile-nav>
            <i class="fas fa-bars" aria-hidden="true"></i><span>Menu</span>
        </button>
        <ul class="nav-list" id="site-navigation">
            <li><a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif><i class="fas fa-home"></i> Beranda</a></li>
            @foreach($publicNavigation as $group)
                @if($group['pages']->isNotEmpty() || $group['links'])
                @php
                    $groupIsCurrent = ($group['active'] ?? false)
                        || collect($group['links'])->contains(fn ($link) => $link['current'] !== null)
                        || $group['pages']->whereIn('slug', $pageTrail)->isNotEmpty();
                @endphp
                <li>
                    <details @class(['site-nav-group', 'is-current' => $groupIsCurrent])>
                    <summary>{{ $group['label'] }} <i class="fas fa-chevron-down" aria-hidden="true"></i></summary>
                    <div class="dropdown-menu">
                        @foreach($group['links'] as $label => $link)
                            <a href="{{ $link['url'] }}" @if($link['current']) aria-current="{{ $link['current'] }}" @endif>{{ $label }}</a>
                        @endforeach
                        @foreach($group['pages'] as $menuPage)
                            @php $children = $menuPage->activeChildren; @endphp
                            @if($children->count() > 0)
                                <details @class(['site-nav-subgroup', 'is-current' => in_array($menuPage->slug, $pageTrail, true)])>
                                    <summary>{{ $menuPage->title }}</summary>
                                    <div class="site-nav-children">
                                        <a href="{{ route('page.show', $menuPage->slug) }}" @if($menuPage->slug === $pageSlug) aria-current="page" @endif>Ikhtisar {{ $menuPage->title }}</a>
                                        @foreach($children as $child)
                                            <a href="{{ route('page.show', $child->slug) }}" @if($child->slug === $pageSlug) aria-current="page" @endif>{{ $child->title }}</a>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <a href="{{ route('page.show', $menuPage->slug) }}" @if($menuPage->slug === $pageSlug) aria-current="page" @endif>{{ $menuPage->title }}</a>
                            @endif
                        @endforeach
                    </div>
                    </details>
                </li>
                @endif
            @endforeach
            <li class="nav-search-item">
                <details @class(['nav-search', 'site-nav-group', 'is-current' => request()->routeIs('search')])>
                    <summary class="nav-search-btn"><i class="fas fa-search" aria-hidden="true"></i><span>Cari</span></summary>
                    <div class="search-dropdown" id="searchDrop">
                        <form action="{{ route('search') }}" method="GET">
                            <input type="search" name="q" aria-label="Cari berita atau halaman" placeholder="Cari berita, halaman..." required>
                            <button type="submit"><i class="fas fa-search"></i> Cari</button>
                        </form>
                    </div>
                </details>
            </li>
        </ul>
    </div>
</nav>
