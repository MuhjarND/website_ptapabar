<?php

namespace Tests\Feature;

use App\Agenda;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class PublicAgendaFlowTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_public_agenda_archive_can_be_filtered_by_type()
    {
        $user = User::create([
            'name' => 'Admin Agenda Pimpinan',
            'email' => 'admin-agenda-publik@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        Agenda::create([
            'title' => 'Agenda Pimpinan PTA',
            'slug' => 'agenda-pimpinan-pta',
            'agenda_type' => Agenda::TYPE_AGENDA_PIMPINAN,
            'summary' => 'Agenda pimpinan.',
            'start_at' => '2026-04-15 09:00:00',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        Agenda::create([
            'title' => 'Kalender Sidang Pekanan',
            'slug' => 'kalender-sidang-pekanan',
            'agenda_type' => Agenda::TYPE_KALENDER_SIDANG,
            'summary' => 'Kalender sidang.',
            'start_at' => '2026-04-16 08:00:00',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $this->get(route('agenda.index', ['agenda_type' => Agenda::TYPE_KALENDER_SIDANG]))
            ->assertOk()
            ->assertSee('Kalender Sidang Pekanan')
            ->assertDontSee('Agenda Pimpinan PTA');
    }
}
