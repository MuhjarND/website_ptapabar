<section class="transparency-documents" aria-labelledby="transparency-documents-title">
    <h2 id="transparency-documents-title">Dokumen {{ $page->title }}</h2>
    <ul>
        @foreach($documents as $document)
            <li>
                <a href="{{ $document->external_url ?: route('transparency-documents.download', $document) }}">
                    {{ $document->title }}{{ $document->pdf_path ? ' (PDF)' : '' }}
                </a>
                <span class="transparency-document-meta">
                    {{ $document->period ?: $document->year }}
                    @if($document->budget_code) / {{ $document->budget_code }} @endif
                    @if($document->version) / {{ $document->version }} @endif
                </span>
            </li>
        @endforeach
    </ul>
</section>
