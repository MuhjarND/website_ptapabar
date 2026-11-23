<?php

use App\FloatingIcon;
use App\SiteApplication;
use Illuminate\Database\Seeder;

class FloatingIconSeeder extends Seeder
{
    public function run()
    {
        SiteApplication::whereRaw('LOWER(title) = ?', ['buku tamu'])->delete();

        FloatingIcon::updateOrCreate(
            ['title' => 'Buku Tamu'],
            [
                'text' => 'Ingin berkunjung ke PTA, lihat ketersediaan pegawai',
                'icon' => 'floating-icons/buku-tamu-default.svg',
                'url' => 'https://bukutamu.pta-papuabarat.go.id/employees',
                'position' => \App\FloatingIcon::POSITION_BOTTOM_RIGHT,
                'order' => 1,
                'is_active' => true,
            ]
        );
    }
}
