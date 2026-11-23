<?php

namespace Tests\Feature;

use App\CaseStatistic;
use App\Page;
use App\ServiceRequest;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourtReadinessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        $this->artisan('migrate', ['--path' => [
            'database/migrations/2014_10_12_000000_create_users_table.php',
            'database/migrations/2024_01_01_000001_create_pages_table.php',
            'database/migrations/2024_01_01_000004_create_settings_table.php',
            'database/migrations/2024_01_01_000007_create_survey_indices_table.php',
            'database/migrations/2026_04_02_000008_create_service_requests_table.php',
            'database/migrations/2026_11_19_000002_create_court_information_tables.php',
            'database/migrations/2026_11_19_000003_add_report_to_survey_indices.php',
        ]])->assertExitCode(0);
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(User::ROLE_AUTHOR);
        });
    }

    public function test_admin_can_publish_schedule_and_public_only_sees_selected_date()
    {
        $url = route('admin.court-information.store', 'hearings');
        $this->actingAs($this->user(User::ROLE_AUTHOR_SATKER))
            ->post($url)->assertRedirect(route('admin.dashboard'));
        $this->actingAs($this->user(User::ROLE_ADMIN));
        $this->get(route('admin.court-information.create', 'hearings'))->assertOk();
        $this->post($url, [
            'hearing_date' => '2026-11-19', 'hearing_time' => '09:00', 'title' => 'Sidang contoh',
            'location' => 'Ruang uji', 'is_published' => '1',
        ])->assertRedirect(route('admin.court-information.index', 'hearings'));
        $this->post($url, [
            'hearing_date' => '2026-11-19', 'title' => 'Sidang draf',
        ])->assertRedirect();
        $this->get('/jadwal-sidang-banding?tanggal=2026-11-19')->assertOk()
            ->assertSee('Sidang contoh')->assertSee('09:00')->assertDontSee('Sidang draf');
        $this->get('/jadwal-sidang-banding?tanggal=2026-11-20')->assertOk()
            ->assertDontSee('Sidang contoh');
        $this->get('/halaman/jadwal-sidang?tanggal=2026-11-19')
            ->assertRedirect('/jadwal-sidang-banding?tanggal=2026-11-19');
        $this->get('/admin/informasi-perkara/invalid')->assertNotFound();
    }

    public function test_monthly_report_validates_period_and_keeps_private_source_out_of_public_view()
    {
        $url = route('admin.court-information.store', 'statistics');
        $this->actingAs($this->user(User::ROLE_ADMIN));
        $this->get(route('admin.court-information.create', 'statistics'))->assertOk();
        $data = ['month' => '2026-10', 'opening' => 4, 'received' => 8, 'decided' => 10, 'closing' => 2,
            'source' => 'Rekap internal pengujian', 'is_published' => '1'];
        $this->post($url, $data)->assertRedirect(route('admin.court-information.index', 'statistics'));
        $this->post($url, $data)->assertSessionHasErrors('month');
        $this->get('/statistik-perkara?tahun=2026')->assertOk()->assertSee('Oktober 2026')
            ->assertSee('Sisa awal')->assertSee('Sisa akhir')->assertDontSee($data['source']);
        $this->get('/statistik-perkara?tahun=2025')->assertOk()->assertDontSee('Oktober 2026');
        $id = CaseStatistic::firstOrFail()->id;
        $this->put(route('admin.court-information.update', ['statistics', $id]), array_replace($data, ['is_published' => '0']))
            ->assertRedirect(route('admin.court-information.index', 'statistics'));
        $this->get('/statistik-perkara?tahun=2026')->assertOk()->assertDontSee('Oktober 2026');
    }

    public function test_deployment_redirects_are_opt_in_and_use_only_explicit_targets()
    {
        $this->get('/direktori-putusan')->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertSee('Direktori Putusan Mahkamah Agung');
        $this->get('/renstra')->assertNotFound();
        config(['site-deployment.wordpress_redirects' => true]);
        $this->get('/renstra')->assertNotFound();
        Page::create(['title' => 'Renstra', 'slug' => 'renstra', 'is_active' => true, 'menu_group' => 'transparansi']);
        $this->get('/renstra')->assertStatus(301)->assertRedirect(route('page.show', 'renstra'));
        $this->get('/wp-content/uploads/file.pdf')->assertNotFound();
        config(['site-deployment.enforce_https' => true, 'app.url' => 'https://preview.example.test']);
        $this->get('/jadwal-sidang-banding?tanggal=2026-11-19')
            ->assertStatus(308)->assertRedirect('https://preview.example.test/jadwal-sidang-banding?tanggal=2026-11-19');
    }

    public function test_service_attachments_are_private_and_admin_can_download()
    {
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        $this->post(route('service-request.submit'), [
            'request_type' => ServiceRequest::TYPE_PENGADUAN,
            'name' => 'Penguji', 'email' => 'uji@example.test', 'subject' => 'Uji', 'message' => 'Pesan pengujian',
            'attachment' => UploadedFile::fake()->create('dokumen.pdf', 1, 'application/pdf'),
        ])->assertRedirect(route('service-request.form'));
        $request = ServiceRequest::firstOrFail();
        Storage::disk('local')->assertExists($request->attachment);
        Storage::disk('public')->assertMissing($request->attachment);
        $link = route('admin.service-requests.download', $request);
        $this->get($link)->assertRedirect(route('login'));
        $this->actingAs($this->user(User::ROLE_ADMIN))->get($link)->assertOk();
    }

    public function test_survey_report_requires_a_period_and_https_link()
    {
        $this->actingAs($this->user(User::ROLE_ADMIN));
        $url = route('admin.surveys.store');
        $data = ['title' => 'Survei uji', 'value' => 95.05, 'label' => 'Baik'];
        $this->post($url, $data + ['report_url' => 'https://example.test/laporan.pdf'])->assertSessionHasErrors('period');
        $this->post($url, $data + ['period' => 'Triwulan III 2026', 'report_url' => 'http://example.test/laporan.pdf'])
            ->assertSessionHasErrors('report_url');
        $this->post($url, $data + ['period' => 'Triwulan III 2026', 'report_url' => 'https://example.test/laporan.pdf'])
            ->assertRedirect(route('admin.surveys.index'));
        $this->assertDatabaseHas('survey_indices', ['period' => 'Triwulan III 2026', 'report_url' => 'https://example.test/laporan.pdf']);
    }

    private function user($role)
    {
        return User::create([
            'name' => 'Penguji',
            'email' => $role . '@example.test',
            'password' => bcrypt('testing-password'),
            'role' => $role,
        ]);
    }
}
