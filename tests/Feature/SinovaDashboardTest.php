<?php

namespace Tests\Feature;

use App\CaseStatistic;
use App\CourtHearing;
use App\Page;
use App\Setting;
use App\Support\PublicNavigation;
use App\SurveyIndex;
use App\TransparencyDocument;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class SinovaDashboardTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_admin_gets_all_reporting_codes_with_links_only_for_available_public_evidence(): void
    {
        $draf = Page::create(['slug' => 'wilayah-yurisdiksi', 'title' => 'Wilayah', 'content' => 'Konten draf', 'is_active' => false]);
        Page::create(['slug' => 'visi-dan-misi', 'title' => 'Visi dan Misi', 'content' => 'Konten halaman ini sedang dalam proses pembaruan.', 'is_active' => true]);
        $plan = Page::create(['slug' => 'rencana-dan-kinerja', 'title' => 'Rencana Aksi', 'menu_group' => 'transparansi', 'is_active' => true]);
        $document = new TransparencyDocument([
            'page_id' => $plan->id, 'title' => 'Dokumen uji', 'year' => now()->year - 1,
            'source' => 'Arsip uji', 'external_url' => 'https://example.test/arsip.pdf',
        ]);
        $document->published_at = now();
        $document->save();
        CourtHearing::create(['hearing_date' => '2026-11-01', 'title' => 'Agenda terbit', 'is_published' => true]);
        CourtHearing::create(['hearing_date' => '2026-12-01', 'title' => 'Agenda draf', 'is_published' => false]);
        CaseStatistic::create(['period' => '2025-01-01', 'is_published' => true, 'opening' => 0, 'received' => 1, 'decided' => 0, 'closing' => 1, 'source' => 'Rekap uji']);
        config(['sinova.public_url' => 'https://portal.example.test/webaru/', 'court.verdicts_url' => 'https://putusan3.mahkamahagung.go.id/']);

        $response = $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
            ->get(route('admin.sinova.index'))->assertOk()
            ->assertSee('41 butir pelaporan')
            ->assertSee('12/35 tautan tersedia')
            ->assertSee('Tidak berlaku untuk PTA')
            ->assertSee('Dinilai via API')
            ->assertSee('Validitas Tautan (Broken Links)')
            ->assertSee('Halaman belum aktif.')
            ->assertSee('Isi halaman atau terbitkan dokumen')
            ->assertSee('Atur COURT_VERDICTS_URL')
            ->assertSee('Belum ada dokumen Rencana Aksi tahun ' . now()->year)
            ->assertDontSee('Belum ada jadwal sidang banding yang terbit.')
            ->assertDontSee('Belum ada statistik perkara yang terbit.')
            ->assertSee('data-sinova-code="A.1.4.4"', false)
            ->assertSee('Lengkapi nilai, periode, dan tautan laporan')
            ->assertSee('assets/js/admin-sinova.js')
            ->assertSee(PublicNavigation::SIPP_BANDING_URL, false)
            ->assertSee('https://portal.example.test/webaru/jadwal-sidang-banding?tanggal=2026-11-01', false)
            ->assertSee('https://portal.example.test/webaru/statistik-perkara?tahun=2025', false)
            ->assertSee('https://portal.example.test/webaru/#statistik-web', false)
            ->assertDontSee('tanggal=2026-12-01', false)
            ->assertDontSee('value="https://portal.example.test/webaru/halaman/' . $draf->slug, false)
            ->assertDontSee('value="https://portal.example.test/webaru/halaman/visi-dan-misi', false)
            ->assertDontSee('Workflow Publikasi');

        $this->assertSame(41, substr_count($response->getContent(), 'data-sinova-code='));
        $this->assertSame(2, substr_count($response->getContent(), 'value="https://portal.example.test/webaru/halaman/rencana-dan-kinerja"'));
        $this->assertSame(12, substr_count($response->getContent(), 'data-sinova-copy'));
    }

    public function test_survey_evidence_reports_missing_types_and_excludes_drafts_and_unsafe_links(): void
    {
        $base = ['label' => 'Baik', 'value' => 89.5, 'period' => 'Triwulan III 2026', 'is_active' => true];
        SurveyIndex::create(['title' => 'Indeks Kepuasan Masyarakat', 'report_url' => 'https://example.test/skm.pdf'] + $base);
        SurveyIndex::create(['title' => 'SPAK internal', 'report_url' => 'https://example.test/draf.pdf', 'is_active' => false] + $base);
        SurveyIndex::create(['title' => 'SPKL', 'report_url' => 'javascript:alert(1)'] + $base);

        $this->get(route('surveys.public'))->assertOk()
            ->assertSee('Triwulan III 2026')->assertSee('89.5')->assertSee('https://example.test/skm.pdf')
            ->assertDontSee('SPAK internal')->assertDontSee('javascript:alert(1)', false);

        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
            ->get(route('admin.sinova.index'))->assertOk()
            ->assertSee('value="' . route('surveys.public') . '"', false)
            ->assertSee('Lengkapi nilai, periode, dan tautan laporan: Survei Persepsi Anti Korupsi, Survei Persepsi Kualitas Layanan.');

        SurveyIndex::create(['title' => 'SPAK', 'report_url' => 'https://example.test/spak.pdf'] + $base);
        SurveyIndex::create(['title' => 'Survei Persepsi Kualitas Pelayanan', 'report_url' => 'https://example.test/spkl.pdf'] + $base);
        $this->get(route('admin.sinova.index'))->assertOk()
            ->assertDontSee('Lengkapi nilai, periode, dan tautan laporan:');
    }

    public function test_official_service_and_profile_fallbacks_do_not_need_seeders(): void
    {
        Setting::set('address', Setting::LEGACY_OFFICE_ADDRESS);

        $this->get(route('court-ecourt.index'))->assertOk()
            ->assertSee('e-Filing')->assertSee(PublicNavigation::ECOURT_URL);
        $this->get(route('court-verdicts.index'))->assertOk()
            ->assertSee('pengadilan/profil/pengadilan/pta-papua-barat.html', false);
        $this->get(route('page.show', 'ecourt'))->assertRedirect(route('court-ecourt.index'));
        $this->get(route('page.show', 'direktori-putusan'))->assertRedirect(route('court-verdicts.index'));
        $this->get(route('page.show', 'panggilan-ghaib'))->assertOk()->assertSee('tingkat pertama');
        $this->get(route('page.show', 'wilayah-yurisdiksi'))->assertOk()->assertSee('Pengadilan Agama Kaimana')
            ->assertSee('Kabupaten Tambrauw')->assertSee('Kabupaten Maybrat')
            ->assertSee('VUtDL1lPOXMvVFhN-1.pdf')->assertSee('Buka peta wilayah yurisdiksi');
        $this->get(route('page.show', 'fungsi-tugas-dan-yuridiksi-pengadilan'))->assertOk()->assertSee('Tugas Pokok');
        $this->get(route('page.show', 'standar-dan-maklumat-pelayanan'))->assertOk()
            ->assertSee('Kebijakan-Standar-Pelayanan.pdf')->assertSee('MAKLUMAT-PELAYANAN.png');
        $this->get(route('page.show', 'alamat-dan-kontak'))->assertOk()
            ->assertSee(Setting::OFFICE_ADDRESS)->assertDontSee(Setting::LEGACY_OFFICE_ADDRESS)->assertSee('Gedung Pengadilan');

        Page::create(['title' => 'Alamat', 'slug' => 'alamat-dan-kontak', 'content' => 'Konten yang dikelola admin', 'is_active' => true]);
        $this->get(route('page.show', 'alamat-dan-kontak'))->assertOk()->assertSee('Konten yang dikelola admin');
    }

    public function test_author_has_no_access_and_unsafe_url_configuration_is_not_exported(): void
    {
        $route = route('admin.sinova.index');
        $this->get($route)->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => User::ROLE_AUTHOR]))
            ->get($route)->assertRedirect(route('admin.dashboard'));

        config([
            'sinova.public_url' => 'https://user:secret@portal.example.test/',
            'court.verdicts_url' => 'https://putusan3.mahkamahagung.go.id@other.example.test/koleksi/',
        ]);
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
            ->get($route)->assertOk()
            ->assertSee(route('home') . '#statistik-web', false)
            ->assertDontSee('portal.example.test')
            ->assertDontSee('other.example.test');
        $this->get(route('home'))->assertOk()->assertSee('id="statistik-web"', false);

        $collectionUrl = 'https://putusan3.mahkamahagung.go.id/direktori/koleksi-uji.html';
        config(['court.verdicts_url' => $collectionUrl]);
        $this->get($route)->assertOk()->assertSee('value="' . $collectionUrl . '"', false);
    }
}
