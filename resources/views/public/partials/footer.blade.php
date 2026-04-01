<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-profile-panel">
                <div class="footer-profile-copy">
                    <h4>{{ $publicSiteSettings['site_name'] }}</h4>
                    <p>{{ $publicSiteSettings['address'] }}</p>
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
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.682!2d134.0816!3d-0.8613!2m3!1f0!2f0!3f0!2m3!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d540a6e252a3c1d%3A0x3b7b0e86f5a7b84e!2sManokwari%2C+Papua+Barat!5e0!3m2!1sid!2sid!4v1" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>

            <div class="footer-links-panel">
                <h4>Tautan Cepat</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('berita.index') }}">Berita</a></li>
                    <li><a href="{{ route('pengumuman.index') }}">Pengumuman</a></li>
                </ul>
            </div>

            <div class="footer-links-panel">
                <h4>Tautan Eksternal</h4>
                <ul>
                    <li><a href="https://www.mahkamahagung.go.id" target="_blank">Mahkamah Agung RI</a></li>
                    <li><a href="https://badilag.mahkamahagung.go.id" target="_blank">Badilag</a></li>
                    <li><a href="https://sipp.mahkamahagung.go.id" target="_blank">SIPP</a></li>
                </ul>
            </div>

            <div class="footer-statistics-panel">
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
