@extends('layouts.admin')
@section('title', 'Dashboard SINOVA')
@section('styles')
    <style>
        .sinova-intro { margin-bottom: 20px; }
        .sinova-intro p { margin-top: 6px; line-height: 1.55; color: #405b4f; }
        .sinova-jump { margin-top: 14px; }
        .sinova-jump summary { cursor: pointer; font-weight: 600; color: #0d5c2f; }
        .sinova-jump ul { display: flex; flex-wrap: wrap; gap: 8px 18px; list-style: none; margin: 10px 0; }
        .sinova-jump a { font-size: 13px; color: #0d5c2f; }
        .sinova-group { scroll-margin-top: 105px; }
        .sinova-group .card-header { gap: 12px; flex-wrap: wrap; }
        .sinova-table { overflow-x: auto; }
        .sinova-table table { table-layout: fixed; }
        .sinova-table th, .sinova-table td { vertical-align: top; }
        .sinova-table th:first-child { width: 96px; }
        .sinova-table th:last-child { width: 47%; }
        .sinova-description, .sinova-copy-status { margin-top: 5px; font-size: 13px; color: #4b6258; line-height: 1.45; }
        .sinova-url { margin: 7px 0; font-size: 12px; padding: 7px 9px; }
        .sinova-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .sinova-preview { border-left: 3px solid #b8860b; padding-left: 10px; }
        @media (max-width: 850px) { .sinova-table table { min-width: 550px; } }
    </style>
@endsection
@section('content')
    @php
        $rows = collect($report)->collapse();
        $linkRows = $rows->where('type', 'URL Sistem');
        $ready = $linkRows->whereNotNull('url')->count();
    @endphp
    <div class="sinova-intro">
        <h2>Tautan Pelaporan SINOVA</h2>
        <p><strong>{{ $ready }}/{{ $linkRows->count() }} tautan tersedia</strong> dari {{ $rows->count() }} butir pelaporan.</p>
        <p>Gunakan <strong>Salin URL</strong> untuk menempelkan tautan ke SINOVA. Periksa isi, periode, dan akses tanpa login sebelum mengirim laporan. Ini bukan penilaian resmi SINOVA.</p>
        @unless($hasHttpsBase)
            <p class="sinova-preview">Tautan situs saat ini menggunakan HTTP. Jika ini pratinjau lokal, gunakan alamat HTTPS situs publik saat pelaporan.</p>
        @endunless
        <details class="sinova-jump">
            <summary>Lompat ke kelompok</summary>
            <ul>
                @foreach(array_keys($report) as $group)
                    <li><a href="#sinova-group-{{ $loop->iteration }}">{{ $group }}</a></li>
                @endforeach
            </ul>
        </details>
    </div>

    @foreach($report as $group => $items)
        @php
            $groupId = 'sinova-group-' . $loop->iteration;
            $groupLinks = collect($items)->where('type', 'URL Sistem');
        @endphp
        <section class="card sinova-group" id="{{ $groupId }}" aria-labelledby="{{ $groupId }}-heading">
            <div class="card-header">
                <h2 id="{{ $groupId }}-heading">{{ $group }}</h2>
                <span class="badge badge-info">
                    @if($groupLinks->isNotEmpty())
                        {{ $groupLinks->whereNotNull('url')->count() }}/{{ $groupLinks->count() }} tautan
                    @else
                        {{ count($items) }} butir
                    @endif
                </span>
            </div>
            <div class="sinova-table">
                <table>
                    <thead>
                        <tr><th scope="col">Kode</th><th scope="col">Butir Pelaporan</th><th scope="col">Tautan / Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($items as $code => $item)
                            @php($inputId = 'url-' . str_replace('.', '-', $code))
                            <tr data-sinova-code="{{ $code }}">
                                <th scope="row">{{ $code }}</th>
                                <td>
                                    <strong>{{ $item['title'] }}</strong>
                                    <p class="sinova-description">{{ $item['description'] }}</p>
                                </td>
                                <td>
                                    <span class="badge {{ $item['url'] && !$item['note'] ? 'badge-success' : ($item['type'] === 'URL Sistem' ? 'badge-warning' : 'badge-secondary') }}">{{ $item['type'] }}</span>
                                    @if($item['url'])
                                        <input class="form-control sinova-url" type="text" readonly id="{{ $inputId }}" value="{{ $item['url'] }}" aria-label="URL {{ $code }} {{ $item['title'] }}">
                                    @endif
                                    @if($item['note'])
                                        <p class="sinova-description">{{ $item['note'] }}</p>
                                    @endif
                                    <div class="sinova-actions">
                                        @if($item['url'])
                                            <button type="button" class="btn btn-primary btn-sm" data-sinova-copy aria-controls="{{ $inputId }}" aria-label="Salin URL {{ $code }} {{ $item['title'] }}">
                                                <i class="fas fa-copy" aria-hidden="true"></i> Salin URL
                                            </button>
                                            <a href="{{ $item['url'] }}" class="btn btn-sm" target="_blank" rel="noopener noreferrer" aria-label="Buka {{ $code }} {{ $item['title'] }}">Buka</a>
                                        @endif
                                        @if($item['manageUrl'])
                                            <a href="{{ $item['manageUrl'] }}" class="btn btn-sm">{{ $item['url'] ? 'Kelola' : 'Lengkapi' }}</a>
                                        @endif
                                    </div>
                                    @if($item['url'])
                                        <p class="sinova-copy-status" role="status" data-copy-status></p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/admin-sinova.js') }}" defer></script>
@endsection
