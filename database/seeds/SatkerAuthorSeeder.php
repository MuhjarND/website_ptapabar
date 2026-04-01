<?php

use App\User;
use Illuminate\Database\Seeder;

class SatkerAuthorSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'PA Manokwari', 'email' => 'pa.manokwari@pta-papuabarat.go.id'],
            ['name' => 'PA Sorong', 'email' => 'pa.sorong@pta-papuabarat.go.id'],
            ['name' => 'PA Kaimana', 'email' => 'pa.kaimana@pta-papuabarat.go.id'],
            ['name' => 'PA Fak-Fak', 'email' => 'pa.fakfak@pta-papuabarat.go.id'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => bcrypt('password'),
                    'role' => User::ROLE_AUTHOR_SATKER,
                ]
            );
        }
    }
}
