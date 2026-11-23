<?php

namespace Tests\Feature;

use App\CaseStatistic;
use App\CourtHearing;
use App\Page;
use App\Support\PublicNavigation;
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
            'page_id' => $plan->id, 'title' => 'Dokumen uji', 'year' => 2026,
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
            ->assertSee('40 butir pelaporan')
            ->assertSee('7/34 tautan tersedia')
            ->assertSee('Tidak berlaku untuk PTA')
            ->assertSee('Dinilai via API')
            ->assertSee('Validitas Tautan (Broken Links)')
            ->assertSee('Halaman belum aktif.')
            ->assertSee('Isi halaman atau terbitkan dokumen')
            ->assertSee('Atur COURT_VERDICTS_URL')
            ->assertSee('assets/js/admin-sinova.js')
            ->assertSee(PublicNavigation::SIPP_BANDING_URL, false)
            ->assertSee('https://portal.example.test/webaru/jadwal-sidang-banding?tanggal=2026-11-01', false)
            ->assertSee('https://portal.example.test/webaru/statistik-perkara?tahun=2025', false)
            ->assertSee('https://portal.example.test/webaru/#statistik-web', false)
            ->assertDontSee('tanggal=2026-12-01', false)
            ->assertDontSee('value="https://portal.example.test/webaru/halaman/' . $draf->slug, false)
            ->assertDontSee('value="https://portal.example.test/webaru/halaman/visi-dan-misi', false)
            ->assertDontSee('Workflow Publikasi');

        $this->assertSame(40, substr_count($response->getContent(), 'data-sinova-code='));
        $this->assertSame(2, substr_count($response->getContent(), 'value="https://portal.example.test/webaru/halaman/rencana-dan-kinerja"'));
        $this->assertSame(7, substr_count($response->getContent(), 'data-sinova-copy'));
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
