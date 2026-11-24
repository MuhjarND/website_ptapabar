<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-profile-panel">
                @php($mapQuery = rawurlencode($publicSiteSettings['address'] ?: $publicSiteSettings['site_name']))
                <h4>{{ $publicSiteSettings['site_name'] }}</h4>
                <p>{{ $publicSiteSettings['address'] }}</p>
                <iframe
                    class="footer-map"
                    src="https://www.google.com/maps?q={{ $mapQuery }}&amp;output=embed"
                    title="Peta lokasi kantor PTA Papua Barat"
                    loading="lazy"
                    allowfullscreen
                ></iframe>
                <a class="footer-location-link" href="https://www.google.com/maps/search/?api=1&amp;query={{ $mapQuery }}" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i> Buka Lokasi di Google Maps
                </a>
                <div class="footer-contact-list">
                    <div class="footer-contact-item">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                        <span>{{ $publicSiteSettings['phone'] }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <span>{{ $publicSiteSettings['email'] }}</span>
                    </div>
                </div>
                <p><a href="{{ route('page.show', 'alamat-dan-kontak') }}">Alamat dan kontak lengkap</a></p>
            </div>

            <div class="footer-navigation">
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
            </div>

            <div class="footer-statistics" id="statistik-web">
                <h4>Statistik Pengunjung</h4>
                <dl>
                    <div><dt>Hari Ini</dt><dd>{{ number_format($publicVisitorStats['today'], 0, ',', '.') }}</dd></div>
                    <div><dt>Kemarin</dt><dd>{{ number_format($publicVisitorStats['yesterday'], 0, ',', '.') }}</dd></div>
                    <div><dt>Total</dt><dd>{{ number_format($publicVisitorStats['total'], 0, ',', '.') }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; {{ date('Y') }} Pengadilan Tinggi Agama Papua Barat. Hak Cipta Dilindungi.
        </div>
    </div>
</footer>
