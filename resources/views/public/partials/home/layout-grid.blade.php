<div class="home-layout-grid">
    @foreach($homeLayout as $block)
        @php($type = $block['type'])
        <div class="home-layout-block {{ $block['w'] <= 7 ? 'home-layout-narrow' : '' }}"
            data-home-block="{{ $type }}"
            style="--block-column: {{ $block['x'] }}; --block-row: {{ $block['y'] }}; --block-width: {{ $block['w'] }}; --block-height: {{ $block['h'] }}">
            @if(in_array($type, ['announcement', 'article'], true))
                @include('public.partials.home.announcements', [
                    'publicationPart' => $type,
                    'publicationPanel' => $block['w'] <= 4,
                ])
            @elseif(isset(\App\Support\HomeLayout::BLOCKS[$type]))
                @include('public.partials.home.' . $type, [
                    'publicationPanel' => in_array($type, ['latest-news', 'religious-court-news'], true) && $block['w'] <= 8,
                ])
            @elseif($type === 'banner')
                <figure class="home-layout-banner">
                    @if(!empty($block['link']))
                        <a href="{{ $block['link'] }}">
                    @endif
                    <img src="{{ $block['image'] }}" alt="{{ $block['title'] }}" loading="lazy">
                    @if(!empty($block['link']))
                        </a>
                    @endif
                </figure>
            @elseif($type === 'text' || $type === 'search')
                <section class="home-layout-custom">
                    @if(!empty($block['title']) || $type === 'search')
                        <h2>{{ ($block['title'] ?? '') ?: 'Pencarian Situs' }}</h2>
                    @endif
                    @if($type === 'text')
                        <p>{{ $block['content'] }}</p>
                    @else
                        <form action="{{ route('search') }}" method="GET" role="search">
                            <input type="search" name="q" placeholder="Kata kunci" aria-label="Kata kunci pencarian" required>
                            <button type="submit">Cari</button>
                        </form>
                    @endif
                </section>
            @endif
        </div>
    @endforeach
</div>
