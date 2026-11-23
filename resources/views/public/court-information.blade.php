@extends('layouts.public')
@php($title = ['hearings' => 'Jadwal Sidang Banding', 'statistics' => 'Statistik Perkara Banding', 'verdicts' => 'Direktori Putusan'][$type])
@section('title', $title)
@section('content')
<div class="section court-section">
<div class="container">
    <article class="page-content">
        <h1>{{ $title }}</h1>
        <div class="content-body">
            @if($type === 'hearings')
                <form method="GET" class="court-filter">
                    <label for="tanggal">Tanggal sidang</label>
                    <input id="tanggal" type="date" name="tanggal" value="{{ $date }}" required>
                    <button type="submit">Tampilkan</button>
                </form>
            @elseif($type === 'statistics')
                <form method="GET" class="court-filter">
                    <label for="tahun">Tahun laporan</label>
                    <input id="tahun" type="number" name="tahun" value="{{ $year }}" min="1900" max="2100" required>
                    <button type="submit">Tampilkan</button>
                </form>
            @endif

            @if($type === 'verdicts')
                @if($verdictsUrl)
                    <p><a href="{{ $verdictsUrl }}">Buka koleksi putusan PTA Papua Barat</a>.</p>
                @else
                    <p>Buka <a href="https://putusan3.mahkamahagung.go.id/direktori/">Direktori Putusan Mahkamah Agung</a>, lalu pilih Pengadilan Tinggi Agama Papua Barat pada filter pengadilan.</p>
                @endif
            @elseif($rows->isEmpty())
                <p>{{ $type === 'hearings' ? 'Belum ada jadwal sidang yang dipublikasikan untuk tanggal ini.' : 'Belum ada laporan perkara yang dipublikasikan untuk tahun ini.' }}</p>
            @else
                <div class="court-table" tabindex="0" role="region" aria-label="{{ $title }}">
                    <table>
                        <caption>{{ $type === 'hearings' ? 'Jadwal dalam Waktu Indonesia Timur (WIT)' : 'Rekap bulanan tahun ' . $year }}</caption>
                        @if($type === 'hearings')
                            <thead><tr><th scope="col">Jam</th><th scope="col">Keterangan</th><th scope="col">Lokasi</th></tr></thead>
                            <tbody>
                            @foreach($rows as $row)
                                <tr><td>{{ $row->hearing_time ? substr($row->hearing_time, 0, 5) : 'Belum diumumkan' }}</td><td>{{ $row->title }}</td><td>{{ $row->location ?: '-' }}</td></tr>
                            @endforeach
                            </tbody>
                        @else
                            <thead><tr><th scope="col">Bulan</th><th scope="col">Sisa awal</th><th scope="col">Masuk</th><th scope="col">Putus</th><th scope="col">Sisa akhir</th></tr></thead>
                            <tbody>
                            @foreach($rows as $row)
                                <tr><th scope="row">{{ $row->period->locale('id')->isoFormat('MMMM YYYY') }}</th><td>{{ $row->opening }}</td><td>{{ $row->received }}</td><td>{{ $row->decided }}</td><td>{{ $row->closing }}</td></tr>
                            @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            @endif
        </div>
    </article>
</div>
</div>
@endsection
