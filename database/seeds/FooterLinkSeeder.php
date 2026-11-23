<?php

use App\FooterLink;
use Illuminate\Database\Seeder;

class FooterLinkSeeder extends Seeder
{
    public function run()
    {
        FooterLink::where('title', 'Dokumen')->delete();

        $links = [
            [FooterLink::SECTION_QUICK, 'Beranda', route('home', [], false)],
            [FooterLink::SECTION_QUICK, 'Berita', route('berita.index', [], false)],
            [FooterLink::SECTION_QUICK, 'Pengumuman', route('pengumuman.index', [], false)],
            [FooterLink::SECTION_RELIGIOUS_COURTS, 'PA Manokwari', 'https://www.pa-manokwari.go.id'],
            [FooterLink::SECTION_RELIGIOUS_COURTS, 'PA Sorong', 'https://www.pa-sorong.go.id'],
            [FooterLink::SECTION_RELIGIOUS_COURTS, 'PA Kaimana', 'https://pa-kaimana.go.id'],
            [FooterLink::SECTION_RELIGIOUS_COURTS, 'PA Fakfak', 'https://www.pa-fakfak.go.id'],
            [FooterLink::SECTION_EXTERNAL, 'Mahkamah Agung RI', 'https://www.mahkamahagung.go.id'],
            [FooterLink::SECTION_EXTERNAL, 'Badilag', 'https://badilag.mahkamahagung.go.id'],
            [FooterLink::SECTION_EXTERNAL, 'SIPP', 'https://sipp.mahkamahagung.go.id'],
        ];

        foreach ($links as $index => $link) {
            FooterLink::updateOrCreate(
                ['section' => $link[0], 'title' => $link[1]],
                [
                    'url' => $link[2],
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
