<?php

namespace Tests\Feature;

use App\Agenda;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class AgendaManagementTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_admin_can_create_and_update_agenda()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.agendas.store'), [
            'title' => 'Rapat Koordinasi PTA',
            'agenda_type' => Agenda::TYPE_KEGIATAN,
            'summary' => 'Agenda rapat koordinasi.',
            'description' => 'Deskripsi agenda rapat koordinasi.',
            'location' => 'Ruang Aula',
            'organizer' => 'Sekretariat PTA',
            'start_at' => '2026-04-10 09:00:00',
            'end_at' => '2026-04-10 11:00:00',
            'order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.agendas.index'));

        $agenda = Agenda::where('title', 'Rapat Koordinasi PTA')->firstOrFail();

        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'agenda_type' => Agenda::TYPE_KEGIATAN,
            'location' => 'Ruang Aula',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.agendas.update', $agenda), [
            'title' => 'Kalender Sidang Mingguan',
            'agenda_type' => Agenda::TYPE_KALENDER_SIDANG,
            'summary' => 'Jadwal sidang minggu berjalan.',
            'description' => 'Jadwal sidang diperbarui.',
            'location' => 'Ruang Sidang Utama',
            'organizer' => 'Kepaniteraan',
            'start_at' => '2026-04-12 08:00:00',
            'end_at' => '2026-04-12 12:00:00',
            'order' => 2,
        ])->assertRedirect(route('admin.agendas.index'));

        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'title' => 'Kalender Sidang Mingguan',
            'agenda_type' => Agenda::TYPE_KALENDER_SIDANG,
            'is_active' => false,
        ]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin Agenda',
            'email' => 'admin-agenda@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
