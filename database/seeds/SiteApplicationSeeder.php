<?php

use App\SiteApplication;
use Illuminate\Database\Seeder;

class SiteApplicationSeeder extends Seeder
{
    public function run()
    {
        $applications = [
            [
                'title' => 'Portal Inovasi PTA',
                'description' => 'Pusat informasi inovasi layanan digital yang dikembangkan di lingkungan PTA Papua Barat.',
                'url' => 'https://example.com/portal-inovasi-pta',
                'group_type' => SiteApplication::GROUP_PTA_INOVASI,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Antrian PTSP Online',
                'description' => 'Pendaftaran antrean layanan terpadu satu pintu untuk mempermudah pelayanan masyarakat.',
                'url' => 'https://example.com/antrian-ptsp',
                'group_type' => SiteApplication::GROUP_PTA_INOVASI,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Monitoring Kinerja PTA',
                'description' => 'Dashboard internal untuk memantau capaian kinerja, tindak lanjut, dan target unit kerja.',
                'url' => 'https://example.com/monitoring-kinerja',
                'group_type' => SiteApplication::GROUP_PTA_INOVASI,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Arsip Digital PTA',
                'description' => 'Pengelolaan dokumen digital, notulensi, dan arsip kegiatan institusi secara terpusat.',
                'url' => 'https://example.com/arsip-digital',
                'group_type' => SiteApplication::GROUP_PTA_INOVASI,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'e-Court',
                'description' => 'Layanan administrasi perkara elektronik Mahkamah Agung Republik Indonesia.',
                'url' => 'https://ecourt.mahkamahagung.go.id',
                'group_type' => SiteApplication::GROUP_MAHKAMAH_AGUNG,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'SIPP',
                'description' => 'Sistem Informasi Penelusuran Perkara yang digunakan di lingkungan peradilan Indonesia.',
                'url' => 'https://sipp.mahkamahagung.go.id',
                'group_type' => SiteApplication::GROUP_MAHKAMAH_AGUNG,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'SIWAS',
                'description' => 'Sistem Informasi Pengawasan untuk penyampaian pengaduan dan tindak lanjut pengawasan.',
                'url' => 'https://siwas.mahkamahagung.go.id',
                'group_type' => SiteApplication::GROUP_MAHKAMAH_AGUNG,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Direktori Putusan',
                'description' => 'Akses publik ke arsip putusan pengadilan dan Mahkamah Agung secara daring.',
                'url' => 'https://putusan3.mahkamahagung.go.id',
                'group_type' => SiteApplication::GROUP_MAHKAMAH_AGUNG,
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($applications as $application) {
            SiteApplication::updateOrCreate(
                ['title' => $application['title']],
                $application
            );
        }
    }
}
