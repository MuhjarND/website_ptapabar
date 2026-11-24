@if($paginator->hasPages())
    <nav class="public-pagination" aria-label="Paginasi">
        <p class="public-pagination-status">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</p>
        <ul>
            <li>
                @if($paginator->onFirstPage())
                    <span class="public-pagination-control" aria-disabled="true">&lsaquo; Sebelumnya</span>
                @else
                    <a class="public-pagination-control" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo; Sebelumnya</a>
                @endif
            </li>

            @foreach($elements as $element)
                @if(is_string($element))
                    <li class="public-pagination-page public-pagination-ellipsis" aria-hidden="true">{{ $element }}</li>
                @else
                    @foreach($element as $page => $url)
                        <li class="public-pagination-page">
                            @if($page === $paginator->currentPage())
                                <span class="public-pagination-control" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="public-pagination-control" href="{{ $url }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if($paginator->hasMorePages())
                    <a class="public-pagination-control" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &rsaquo;</a>
                @else
                    <span class="public-pagination-control" aria-disabled="true">Berikutnya &rsaquo;</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
