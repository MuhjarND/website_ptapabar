@extends('layouts.admin')
@php($title = $type === 'hearings' ? 'Jadwal Sidang Banding' : 'Statistik Perkara')
@section('title', $title)
@section('content')
<div class="card">
    <div class="card-header"><h2>{{ $record->exists ? 'Edit' : 'Tambah' }} {{ $title }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ $record->exists ? route('admin.court-information.update', [$type, $record->id]) : route('admin.court-information.store', $type) }}">
            @csrf
            @if($record->exists)
                @method('PUT')
            @endif
            @if($type === 'hearings')
                <p>Masukkan informasi yang telah disetujui Kepaniteraan untuk publik. Jangan cantumkan nama pihak atau informasi perkara yang dikecualikan.</p>
                <div class="form-group">
                    <label for="hearing_date">Tanggal sidang</label>
                    <input class="form-control" id="hearing_date" name="hearing_date" type="date" value="{{ old('hearing_date', optional($record->hearing_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label for="hearing_time">Jam WIT (opsional)</label>
                    <input class="form-control" id="hearing_time" name="hearing_time" type="time" value="{{ old('hearing_time', substr((string) $record->hearing_time, 0, 5)) }}">
                </div>
                <div class="form-group">
                    <label for="title">Keterangan publik</label>
                    <input class="form-control" id="title" name="title" maxlength="160" value="{{ old('title', $record->title) }}" required>
                </div>
                <div class="form-group">
                    <label for="location">Lokasi / ruang (opsional)</label>
                    <input class="form-control" id="location" name="location" maxlength="160" value="{{ old('location', $record->location) }}">
                </div>
            @else
                <div class="form-group">
                    <label for="month">Bulan laporan</label>
                    <input class="form-control" id="month" name="month" type="month" value="{{ old('month', optional($record->period)->format('Y-m')) }}" required>
                </div>
                @foreach(['opening' => 'Sisa awal bulan', 'received' => 'Perkara masuk', 'decided' => 'Perkara putus', 'closing' => 'Sisa akhir bulan'] as $field => $label)
                    <div class="form-group">
                        <label for="{{ $field }}">{{ $label }}</label>
                        <input class="form-control" id="{{ $field }}" name="{{ $field }}" type="number" min="0" max="2147483647" value="{{ old($field, $record->{$field}) }}" required>
                    </div>
                @endforeach
                <div class="form-group">
                    <label for="source">Sumber laporan (catatan internal)</label>
                    <input class="form-control" id="source" name="source" maxlength="160" value="{{ old('source', $record->source) }}" required>
                </div>
            @endif
            <div class="form-group">
                <input type="hidden" name="is_published" value="0">
                <label><input type="checkbox" name="is_published" value="1" {{ old('is_published', false) ? 'checked' : '' }}> Terbitkan setelah data ditinjau</label>
                @if($record->is_published)
                    <p>Data saat ini terbit. Centang kembali jika perubahan juga siap ditayangkan.</p>
                @endif
            </div>
            <button class="btn btn-gold" type="submit">Simpan Data</button>
            <a class="btn btn-primary" href="{{ route('admin.court-information.index', $type) }}">Kembali</a>
        </form>
    </div>
</div>
@endsection
