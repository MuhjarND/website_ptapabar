@extends('layouts.public')
@section('title', 'Agenda Pimpinan')

@push('styles')
<link href="{{ asset('assets/css/public-content.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="section section-white">
    <div class="container">
        <div class="content-shell-wide">
            <div class="section-header">
                <h1 class="section-title">Agenda Pimpinan</h1>
            </div>
            <div class="archive-filter-bar">
                <a href="{{ route('agenda.index') }}" class="archive-filter-button {{ $activeAgendaType ? '' : 'is-active' }}">Semua</a>
                @foreach($agendaTypes as $value => $label)
                    <a href="{{ route('agenda.index', ['agenda_type' => $value]) }}" class="archive-filter-button {{ $activeAgendaType === $value ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="agenda-public-grid">
                @forelse($agendas as $agenda)
                    <article class="agenda-public-card">
                        <div class="agenda-public-date">
                            <span>{{ $agenda->start_at->format('d') }}</span>
                            <small>{{ $agenda->start_at->translatedFormat('M Y') }}</small>
                        </div>
                        <div class="agenda-public-copy">
                            <div class="section-category-pill announcement-category-pill">{{ $agenda->agenda_type_label }}</div>
                            <h3>{{ $agenda->title }}</h3>
                            @if($agenda->summary)
                                <p>{{ $agenda->summary }}</p>
                            @elseif($agenda->description)
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($agenda->description), 160) }}</p>
                            @endif
                            <div class="agenda-meta-list">
                                <span><i class="fas fa-clock"></i> {{ $agenda->start_at->format('d M Y H:i') }}@if($agenda->end_at) - {{ $agenda->end_at->format('d M Y H:i') }}@endif</span>
                                @if($agenda->location)
                                    <span><i class="fas fa-map-marker-alt"></i> {{ $agenda->location }}</span>
                                @endif
                                @if($agenda->organizer)
                                    <span><i class="fas fa-user-tie"></i> {{ $agenda->organizer }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="archive-empty"><i class="fas fa-calendar-alt"></i>Belum ada agenda pimpinan.</div>
                @endforelse
            </div>

            {{ $agendas->withQueryString()->onEachSide(1)->links('public.partials.pagination') }}
        </div>
    </div>
</section>
@endsection
