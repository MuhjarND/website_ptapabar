@if($imageFiles->count() > 0 || $pdfFiles->count() > 0)
    <div class="page-content post-documentation">
        <div class="post-documentation-header">
            <h2>
                <i class="fas fa-folder-open"></i> Dokumentasi
                <span>{{ $imageFiles->count() + $pdfFiles->count() }}</span>
            </h2>
            <a href="javascript:void(0)" onclick="downloadAllFiles()" class="post-download-all">
                <i class="fas fa-download"></i> Unduh Semua
            </a>
        </div>

        @if($imageFiles->count() > 0)
            <div class="tiled-gallery">
                @foreach($imageFiles as $index => $photo)
                    <div class="tile-item tile-{{ $index % 6 }}" onclick="openLightbox({{ $index }})">
                        <img src="{{ asset('storage/' . $photo->image) }}" alt="{{ $photo->caption ?? 'Foto ' . ($index + 1) }}" loading="lazy">
                        <div class="tile-overlay">
                            <div class="tile-actions">
                                @if($photo->caption)
                                    <span class="tile-caption">{{ $photo->caption }}</span>
                                @endif
                                <a href="{{ asset('storage/' . $photo->image) }}" download class="tile-dl file-dl" onclick="event.stopPropagation();" title="Unduh foto">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($pdfFiles->count() > 0)
            <div class="pdf-list {{ $imageFiles->count() > 0 ? 'pdf-list-spaced' : '' }}">
                @foreach($pdfFiles as $pdf)
                    <a href="{{ asset('storage/' . $pdf->image) }}" target="_blank" class="pdf-card file-dl" rel="noopener noreferrer">
                        <div class="pdf-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="pdf-info">
                            <span class="pdf-name">{{ $pdf->caption ?? basename($pdf->image) }}</span>
                            <span class="pdf-meta">Dokumen PDF - Klik untuk membuka</span>
                        </div>
                        <div class="pdf-action" title="Unduh PDF">
                            <i class="fas fa-download"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @if($imageFiles->count() > 0)
        <div id="lightbox" class="post-lightbox">
            <div class="lb-backdrop" onclick="closeLightbox()"></div>
            <button type="button" class="lb-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
            <button type="button" class="lb-nav lb-prev" onclick="event.stopPropagation(); navigateLB(-1)"><i class="fas fa-chevron-left"></i></button>
            <button type="button" class="lb-nav lb-next" onclick="event.stopPropagation(); navigateLB(1)"><i class="fas fa-chevron-right"></i></button>
            <div class="lb-content" onclick="event.stopPropagation()">
                <img id="lb-img" src="" alt="Dokumentasi foto">
                <div class="lb-footer">
                    <span id="lb-caption"></span>
                    <div class="lb-footer-actions">
                        <span id="lb-counter"></span>
                        <a id="lb-download" href="" download class="lb-dl-btn"><i class="fas fa-download"></i> Unduh</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
