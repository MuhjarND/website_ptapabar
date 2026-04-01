<!-- Top Bar -->
<div class="topbar">
    <div class="container">
        <div><i class="fas fa-map-marker-alt"></i> {{ $publicSiteSettings['address'] }}</div>
        <div class="topbar-links">
            <a href="mailto:{{ $publicSiteSettings['email'] }}"><i class="fas fa-envelope"></i> {{ $publicSiteSettings['email'] }}</a>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $publicSiteSettings['phone']) }}"><i class="fas fa-phone"></i> {{ $publicSiteSettings['phone'] }}</a>
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
                <h2>{{ $publicSiteSettings['site_name'] }}</h2>
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
            @foreach($publicNavigation as $group)
                @if($group['pages']->count() > 0)
                <li>
                    <a href="#">{{ $group['label'] }} <i class="fas fa-chevron-down" style="font-size:10px;"></i></a>
                    <div class="dropdown-menu">
                        @foreach($group['pages'] as $menuPage)
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
            <li class="nav-search-item">
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
            </li>
        </ul>
    </div>
</nav>
