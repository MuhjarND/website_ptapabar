<div class="form-group">
    <label for="period">Periode survei</label>
    <input id="period" name="period" maxlength="80" class="form-control" value="{{ old('period', optional($reportSurvey)->period) }}" placeholder="Contoh: Triwulan III 2026">
</div>
<div class="form-group">
    <label for="report_url">Tautan laporan survei (HTTPS)</label>
    <input id="report_url" name="report_url" type="url" maxlength="2048" class="form-control" value="{{ old('report_url', optional($reportSurvey)->report_url) }}">
    <small>Untuk bukti SINOVA, isi nilai, periode, dan tautan laporan publik untuk SKM, SPAK, dan Survei Persepsi Kualitas Layanan.</small>
</div>
