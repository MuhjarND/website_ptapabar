<?php

namespace Tests\Feature;

use App\Post;
use App\PostCategory;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PostCategoryManagementTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_create_and_update_post_category()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.post-categories.store'), [
            'name' => 'Rapat Internal',
            'order' => 2,
            'is_active' => '1',
        ])->assertRedirect(route('admin.post-categories.index'));

        $category = PostCategory::where('name', 'Rapat Internal')->firstOrFail();

        $this->assertDatabaseHas('post_categories', [
            'id' => $category->id,
            'slug' => 'rapat-internal',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.post-categories.update', $category), [
            'name' => 'Rapat Pimpinan',
            'order' => 1,
        ])->assertRedirect(route('admin.post-categories.index'));

        $this->assertDatabaseHas('post_categories', [
            'id' => $category->id,
            'name' => 'Rapat Pimpinan',
            'slug' => 'rapat-pimpinan',
            'is_active' => false,
        ]);
    }

    public function test_category_in_use_cannot_be_deleted()
    {
        $admin = $this->makeAdmin();
        $author = $this->makeAuthor();
        $category = PostCategory::firstOrCreate(
            ['slug' => 'sosialisasi'],
            ['name' => 'Sosialisasi', 'order' => 0, 'is_active' => true]
        );

        Post::create([
            'title' => 'Post Uji',
            'slug' => 'post-uji',
            'content' => 'Konten',
            'excerpt' => 'Ringkasan',
            'category' => 'berita',
            'post_category_id' => $category->id,
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        $this->actingAs($admin)->delete(route('admin.post-categories.destroy', $category))
            ->assertRedirect(route('admin.post-categories.index'));

        $this->assertDatabaseHas('post_categories', ['id' => $category->id]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-kategori@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    protected function makeAuthor()
    {
        return User::create([
            'name' => 'Author',
            'email' => 'author-kategori@example.com',
            'password' => bcrypt('password'),
            'role' => 'author',
        ]);
    }
}
