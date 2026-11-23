<?php

namespace Tests\Feature;

use App\Mail\ServiceRequestSubmitted;
use App\ServiceRequest;
use App\Setting;
use App\User;
use Tests\MigratesTestDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceRequestFlowTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_public_user_can_submit_service_request_and_email_is_sent()
    {
        Storage::fake('local');
        Mail::fake();
        Setting::set('email', 'layanan@pta-papuabarat.go.id');

        $this->get(route('service-request.form'))
            ->assertOk()
            ->assertSee('Pengaduan');

        $this->post(route('service-request.submit'), [
            'request_type' => ServiceRequest::TYPE_PENGADUAN,
            'name' => 'Pemohon Publik',
            'email' => 'pemohon@example.com',
            'phone' => '08123456789',
            'subject' => 'Usulan layanan',
            'message' => 'Mohon peningkatan layanan informasi.',
            'attachment' => UploadedFile::fake()->create('lampiran.pdf', 240, 'application/pdf'),
        ])->assertRedirect(route('service-request.form'));

        $serviceRequest = ServiceRequest::where('email', 'pemohon@example.com')->firstOrFail();

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'request_type' => ServiceRequest::TYPE_PENGADUAN,
            'status' => ServiceRequest::STATUS_BARU,
            'emailed_to' => 'layanan@pta-papuabarat.go.id',
        ]);

        Storage::disk('local')->assertExists($serviceRequest->attachment);
        $this->get(route('admin.service-requests.download', $serviceRequest))->assertRedirect(route('login'));
        Mail::assertSent(ServiceRequestSubmitted::class);
    }

    public function test_admin_can_update_service_request_status()
    {
        $admin = User::create([
            'name' => 'Admin Layanan',
            'email' => 'admin-layanan@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $serviceRequest = ServiceRequest::create([
            'request_type' => ServiceRequest::TYPE_PERMOHONAN_INFORMASI,
            'name' => 'Pemohon Informasi',
            'email' => 'informasi@example.com',
            'subject' => 'Permintaan salinan dokumen',
            'message' => 'Mohon salinan dokumen publik.',
            'status' => ServiceRequest::STATUS_BARU,
        ]);

        $this->actingAs($admin)->put(route('admin.service-requests.update', $serviceRequest), [
            'status' => ServiceRequest::STATUS_DITINDAKLANJUTI,
            'admin_notes' => 'Sedang diproses oleh petugas informasi.',
        ])->assertRedirect(route('admin.service-requests.show', $serviceRequest));

        $this->assertDatabaseHas('service_requests', [
            'id' => $serviceRequest->id,
            'status' => ServiceRequest::STATUS_DITINDAKLANJUTI,
            'handled_by' => $admin->id,
        ]);
    }
}
