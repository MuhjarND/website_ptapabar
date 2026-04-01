<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_create_and_update_user()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Editor Baru',
            'email' => 'editor@example.com',
            'role' => 'author',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'editor@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $user->password));

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Editor Update',
            'email' => 'editor@example.com',
            'role' => 'admin',
            'password' => 'newsecret',
            'password_confirmation' => 'newsecret',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Editor Update', $user->name);
        $this->assertSame('admin', $user->role);
        $this->assertTrue(Hash::check('newsecret', $user->password));
    }

    public function test_admin_can_create_author_satker_user()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'PA Manokwari',
            'email' => 'satker@example.com',
            'role' => User::ROLE_AUTHOR_SATKER,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'satker@example.com',
            'role' => User::ROLE_AUTHOR_SATKER,
        ]);
    }

    public function test_admin_cannot_delete_current_logged_in_user()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-user@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
