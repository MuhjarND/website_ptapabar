@extends('layouts.public')
@section('title', 'Hasil Survei Pelayanan Masyarakat')

@section('content')
<div class="section court-section">
    <div class="container">
        <article class="page-content">
            <h1>Hasil Survei Pelayanan Masyarakat</h1>
            <div class="content-body">
                @if($surveys->isEmpty())
                    <p>Belum ada hasil survei yang dipublikasikan.</p>
                @else
                    <div class="court-table" tabindex="0" role="region" aria-label="Hasil survei pelayanan">
                        <table>
                            <caption>Nilai dan laporan survei menurut periode</caption>
                            <thead>
                                <tr><th scope="col">Jenis Survei</th><th scope="col">Periode</th><th scope="col">Nilai</th><th scope="col">Laporan</th></tr>
                            </thead>
                            <tbody>
                                @foreach($surveys as $survey)
                                    @php($reportUrl = $survey->public_report_url)
                                    <tr>
                                        <th scope="row">{{ $survey->title }}</th>
                                        <td>{{ $survey->period ?: 'Belum dicantumkan' }}</td>
                                        <td>{{ $survey->display_value }} ({{ $survey->label }})</td>
                                        <td>
                                            @if($reportUrl)
                                                <a href="{{ $reportUrl }}" aria-label="Laporan {{ $survey->title }} {{ $survey->period }}">Lihat laporan</a>
                                            @else
                                                Belum tersedia
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </article>
    </div>
</div>
@endsection
