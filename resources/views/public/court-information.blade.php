@extends('layouts.public')
@php($title = [
    'hearings' => 'Jadwal Sidang Banding',
    'statistics' => 'Statistik Perkara Banding',
    'verdicts' => 'Direktori Putusan',
    'ecourt' => 'e-Court Mahkamah Agung',
    'contact' => 'Alamat dan Kontak Pengadilan',
    'duties' => 'Tugas Pokok dan Fungsi Pengadilan',
    'jurisdiction' => 'Wilayah Yurisdiksi',
    'service-standards' => 'Standar dan Maklumat Pelayanan',
    'summons' => 'Panggilan Ghaib',
][$type])
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

            @if($type === 'ecourt')
                <p>e-Court adalah layanan Mahkamah Agung Republik Indonesia untuk administrasi perkara dan persidangan secara elektronik.</p>
                <ul>
                    <li><strong>e-Filing:</strong> pendaftaran perkara dan pengiriman berkas secara elektronik.</li>
                    <li><strong>e-Payment:</strong> taksiran dan pembayaran panjar biaya perkara.</li>
                    <li><strong>e-Summons:</strong> panggilan dan pemberitahuan melalui sarana elektronik.</li>
                    <li><strong>e-Litigation:</strong> persidangan dan pertukaran dokumen secara elektronik.</li>
                </ul>
                <p><a href="{{ \App\Support\PublicNavigation::ECOURT_URL }}">Buka aplikasi resmi e-Court Mahkamah Agung</a>.</p>
            @elseif($type === 'contact')
                <figure>
                    <img width="640" src="{{ \App\Support\WordPressContent::imageUrl('2026/11/WhatsApp-Image-2026-11-01-at-11.55.19-AM.jpeg') }}" alt="Gedung Pengadilan Tinggi Agama Papua Barat" loading="lazy">
                    <figcaption>Gedung Pengadilan Tinggi Agama Papua Barat</figcaption>
                </figure>
                <p><strong>Alamat:</strong> {{ \App\Setting::officeAddress($contact['address']) }}</p>
                <p><strong>Telepon:</strong> <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['phone']) }}">{{ $contact['phone'] }}</a></p>
                <p><strong>Email:</strong> <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></p>
                <p><strong>Website resmi:</strong> <a href="https://pta-papuabarat.go.id/">pta-papuabarat.go.id</a></p>
            @elseif($type === 'duties')
                <h2>Tugas pokok</h2>
                <p>Pengadilan Tinggi Agama Papua Barat memeriksa dan memutus perkara peradilan agama pada tingkat banding serta sengketa kewenangan mengadili antar-Pengadilan Agama di wilayah hukumnya pada tingkat pertama dan terakhir.</p>
                <h2>Fungsi</h2>
                <ul>
                    <li>Memberikan pelayanan teknis dan administrasi perkara tingkat banding.</li>
                    <li>Melakukan pembinaan dan pengawasan terhadap Pengadilan Agama di wilayah hukumnya.</li>
                    <li>Memberikan pertimbangan hukum Islam kepada instansi pemerintah di wilayah hukumnya apabila diminta.</li>
                    <li>Menyelenggarakan administrasi umum dan layanan pendukung peradilan.</li>
                </ul>
                <p>Dasar hukum: Pasal 51, 52, dan 53 Undang-Undang Nomor 7 Tahun 1989 tentang Peradilan Agama beserta perubahannya.</p>
            @elseif($type === 'jurisdiction')
                <p>Wilayah kerja PTA Papua Barat mencakup 12 kabupaten dan 1 kota yang dilayani oleh empat Pengadilan Agama di Papua Barat dan Papua Barat Daya.</p>
                <dl>
                    <dt><strong>Pengadilan Agama Manokwari</strong></dt>
                    <dd>Kabupaten Manokwari, Kabupaten Manokwari Selatan, Kabupaten Pegunungan Arfak, Kabupaten Teluk Bintuni, Kabupaten Teluk Wondama, dan Kabupaten Tambrauw.</dd>
                    <dt><strong>Pengadilan Agama Sorong</strong></dt>
                    <dd>Kota Sorong, Kabupaten Sorong, Kabupaten Sorong Selatan, Kabupaten Raja Ampat, dan Kabupaten Maybrat.</dd>
                    <dt><strong>Pengadilan Agama Fakfak</strong></dt>
                    <dd>Kabupaten Fakfak.</dd>
                    <dt><strong>Pengadilan Agama Kaimana</strong></dt>
                    <dd>Kabupaten Kaimana.</dd>
                </dl>
                <p><a href="https://jdih.mahkamahagung.go.id/storage/uploads/produk_hukum/2025/2025sk-kma28/KMA/SK.OT1.1/II/2025.pdf">Dasar penetapan wilayah hukum (PDF)</a>.</p>
                <p><a href="{{ \App\Support\WordPressContent::fileUrl('2024/05/LKJIP_3300_526718_PENGADILAN-TINGGI-AGAMA-PAPUA-BARAT_2024__VUtDL1lPOXMvVFhN-1.pdf') }}">Buka peta wilayah yurisdiksi pada halaman 3 LKjIP 2023 (PDF)</a>.</p>
            @elseif($type === 'service-standards')
                <h2>Standar Pelayanan</h2>
                <p><a href="{{ \App\Support\WordPressContent::fileUrl('2025/11/6.1.a.-Kebijakan-Standar-Pelayanan.pdf') }}">Baca Kebijakan Standar Pelayanan PTA Papua Barat (PDF)</a>.</p>
                <h2>Maklumat Pelayanan</h2>
                <figure>
                    <a href="{{ \App\Support\WordPressContent::imageUrl('2025/11/SELAMAT-DATANG-DAN-MAKLUMAT-PELAYANAN.png') }}">
                        <img width="640" src="{{ \App\Support\WordPressContent::imageUrl('2025/11/SELAMAT-DATANG-DAN-MAKLUMAT-PELAYANAN.png') }}" alt="Buka gambar Maklumat Pelayanan PTA Papua Barat dalam ukuran penuh" loading="lazy">
                    </a>
                </figure>
            @elseif($type === 'summons')
                <p>Panggilan ghaib merupakan pemanggilan pihak yang alamatnya tidak diketahui dalam perkara di pengadilan tingkat pertama. Pengadilan Tinggi Agama Papua Barat tidak menerbitkan panggilan ghaib untuk perkara tingkat banding.</p>
                <p>Untuk pengumuman dan informasi perkara, hubungi Pengadilan Agama tingkat pertama yang menangani perkara tersebut.</p>
            @elseif($type === 'verdicts')
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
