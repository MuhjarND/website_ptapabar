<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class PostWorkflowTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_author_submission_is_forced_to_review_or_draft()
    {
        $author = User::create([
            'name' => 'Author Workflow',
            'email' => 'author-workflow@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_AUTHOR,
        ]);

        $this->actingAs($author)->post(route('admin.berita.store'), [
            'title' => 'Berita Review',
            'content' => 'Konten review',
            'category' => 'berita',
            'workflow_status' => Post::STATUS_PUBLISHED,
            'is_published' => '1',
        ])->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Berita Review',
            'workflow_status' => Post::STATUS_DRAFT,
            'is_published' => false,
        ]);

        $this->actingAs($author)->post(route('admin.berita.store'), [
            'title' => 'Berita Minta Review',
            'content' => 'Konten review',
            'category' => 'berita',
            'workflow_status' => Post::STATUS_REVIEW,
        ])->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Berita Minta Review',
            'workflow_status' => Post::STATUS_REVIEW,
            'is_published' => false,
        ]);
    }

    public function test_admin_can_publish_post_with_workflow_status()
    {
        $admin = User::create([
            'name' => 'Admin Workflow',
            'email' => 'admin-workflow@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)->post(route('admin.berita.store'), [
            'title' => 'Berita Publish Workflow',
            'content' => 'Konten publish',
            'category' => 'berita',
            'workflow_status' => Post::STATUS_PUBLISHED,
        ])->assertRedirect(route('admin.berita.index'));

        $post = Post::where('title', 'Berita Publish Workflow')->firstOrFail();

        $this->assertSame(Post::STATUS_PUBLISHED, $post->workflow_status);
        $this->assertTrue($post->is_published);
        $this->assertNotNull($post->published_at);
    }
}
