<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-profile-panel">
                <div class="footer-profile-copy">
                    <h4>{{ $publicSiteSettings['site_name'] }}</h4>
                    <p>{{ $publicSiteSettings['address'] }}</p>
                    <p><a href="{{ route('page.show', 'alamat-dan-kontak') }}">Alamat dan kontak lengkap</a></p>
                    <div class="footer-contact-list">
                        <div class="footer-contact-item">
                            <i class="fas fa-phone"></i>
                            <span>{{ $publicSiteSettings['phone'] }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $publicSiteSettings['email'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="footer-profile-map">
                    <h4>Lokasi Kantor</h4>
                    <div class="footer-map">
                        <iframe src="https://www.google.com/maps?q={{ rawurlencode($publicSiteSettings['address'] ?: $publicSiteSettings['site_name']) }}&amp;output=embed" title="Peta lokasi kantor PTA Papua Barat" allowfullscreen loading="lazy"></iframe>
                    </div>
                </div>
            </div>

            <div class="footer-links-panel">
                <h4>Tautan Cepat</h4>
                <ul>
                    @forelse(($publicFooterLinks[\App\FooterLink::SECTION_QUICK] ?? collect()) as $link)
                        <li><a href="{{ $link->url }}" @if(\Illuminate\Support\Str::startsWith($link->url, 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ $link->title }}</a></li>
                    @empty
                        <li><span class="text-muted">Belum ada tautan.</span></li>
                    @endforelse
                </ul>
            </div>

            <div class="footer-links-panel">
                <h4>PA Sewilayah Hukum</h4>
                <ul>
                    @forelse(($publicFooterLinks[\App\FooterLink::SECTION_RELIGIOUS_COURTS] ?? collect()) as $link)
                        <li><a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->title }}</a></li>
                    @empty
                        <li><span class="text-muted">Belum ada tautan.</span></li>
                    @endforelse
                </ul>
            </div>

            <div class="footer-links-panel">
                <h4>Tautan Eksternal</h4>
                <ul>
                    @forelse(($publicFooterLinks[\App\FooterLink::SECTION_EXTERNAL] ?? collect()) as $link)
                        <li><a href="{{ \App\Support\PublicNavigation::serviceUrl($link->url) }}" target="_blank" rel="noopener noreferrer">{{ $link->title }}</a></li>
                    @empty
                        <li><span class="text-muted">Belum ada tautan.</span></li>
                    @endforelse
                </ul>
            </div>

            @if(!empty($publicSiteSettings['instagram_url']) || !empty($publicSiteSettings['facebook_url']) || !empty($publicSiteSettings['youtube_url']))
                <div class="footer-links-panel footer-social-panel">
                    <h4>Sosial Media</h4>
                    <div class="footer-social-links">
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
                </div>
            @endif

            <div class="footer-statistics-panel" id="statistik-web">
                <div class="footer-statistics-head">
                    <div class="footer-statistics-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <h4>STATISTIK WEB</h4>
                        <p>Ringkasan kunjungan situs PTA Papua Barat</p>
                    </div>
                </div>

                <div class="footer-statistics-body">
                    <div class="stats-summary-grid">
                        <div class="stats-summary-card">
                            <span>Hari ini</span>
                            <strong>{{ number_format($publicVisitorStats['today']) }}</strong>
                        </div>
                        <div class="stats-summary-card">
                            <span>Minggu ini</span>
                            <strong>{{ number_format($publicVisitorStats['week']) }}</strong>
                        </div>
                        <div class="stats-summary-card">
                            <span>Bulan ini</span>
                            <strong>{{ number_format($publicVisitorStats['month']) }}</strong>
                        </div>
                        <div class="stats-summary-card">
                            <span>Total</span>
                            <strong>{{ number_format($publicVisitorStats['total']) }}</strong>
                        </div>
                    </div>

                    <div class="stats-detail-grid">
                        <div class="stats-block">
                            <h5><i class="fas fa-mobile-alt"></i> Perangkat</h5>
                            <div class="stats-row">
                                <span>Mobile</span>
                                <strong>{{ number_format($publicVisitorStats['devices']['mobile']) }}</strong>
                            </div>
                            <div class="stats-row">
                                <span>Desktop</span>
                                <strong>{{ number_format($publicVisitorStats['devices']['desktop']) }}</strong>
                            </div>
                            <div class="stats-row">
                                <span>Tablet</span>
                                <strong>{{ number_format($publicVisitorStats['devices']['tablet']) }}</strong>
                            </div>
                            <div class="stats-row">
                                <span>Unknown</span>
                                <strong>{{ number_format($publicVisitorStats['devices']['unknown']) }}</strong>
                            </div>
                        </div>

                        <div class="stats-block">
                            <h5><i class="fas fa-map-marker-alt"></i> Negara Teratas</h5>
                            @forelse($publicVisitorStats['countries'] as $country)
                                <div class="stats-row">
                                    <span>{{ $country['flag'] }} {{ $country['name'] }}</span>
                                    <strong>{{ number_format($country['total']) }}</strong>
                                </div>
                            @empty
                                <div class="stats-row">
                                    <span><i class="fas fa-globe-asia"></i> Unknown</span>
                                    <strong>0</strong>
                                </div>
                            @endforelse
                        </div>

                        <div class="stats-online-card">
                            <span><i class="fas fa-user"></i> Pengunjung Online</span>
                            <strong>{{ number_format($publicVisitorStats['online']) }}</strong>
                            <p class="stats-online-meta">Online: {{ number_format($publicVisitorStats['online']) }}</p>
                            <p>Jumlah sesi aktif dalam beberapa menit terakhir.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; {{ date('Y') }} Pengadilan Tinggi Agama Papua Barat. Hak Cipta Dilindungi.
        </div>
    </div>
</footer>
