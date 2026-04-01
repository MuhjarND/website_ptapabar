<?php

namespace Tests\Feature;

use App\Post;
use App\PostCategory;
use App\PostPhoto;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PostAuthorizationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_author_only_sees_their_own_posts()
    {
        $author = $this->makeUser('author1@example.com', 'author');
        $otherAuthor = $this->makeUser('author2@example.com', 'author');

        $ownPost = $this->makePost($author, 'Post Milik Saya');
        $otherPost = $this->makePost($otherAuthor, 'Post Orang Lain');

        $response = $this->actingAs($author)->get(route('admin.posts.index'));

        $response->assertOk();
        $response->assertSee($ownPost->title);
        $response->assertDontSee($otherPost->title);
    }

    public function test_admin_can_open_create_post_form()
    {
        $admin = $this->makeUser('admin-create-post@example.com', 'admin');

        $response = $this->actingAs($admin)->get(route('admin.posts.create'));

        $response->assertOk();
        $response->assertSeeText('Tambah Post Baru');
        $response->assertSeeText('Dokumentasi Foto');
    }

    public function test_admin_can_open_separated_berita_and_pengumuman_pages()
    {
        $admin = $this->makeUser('admin-separated-pages@example.com', 'admin');

        $this->actingAs($admin)
            ->get(route('admin.berita.index'))
            ->assertOk()
            ->assertSeeText('Daftar Berita')
            ->assertSeeText('Tambah Berita');

        $this->actingAs($admin)
            ->get(route('admin.pengumuman.index'))
            ->assertOk()
            ->assertSeeText('Daftar Pengumuman')
            ->assertSeeText('Tambah Pengumuman');

        $this->actingAs($admin)
            ->get(route('admin.berita.create'))
            ->assertOk()
            ->assertSeeText('Tambah Berita Baru')
            ->assertDontSeeText('Kategori Pengumuman');

        $this->actingAs($admin)
            ->get(route('admin.pengumuman.create'))
            ->assertOk()
            ->assertSeeText('Tambah Pengumuman Baru')
            ->assertDontSeeText('Kategori Berita');
    }

    public function test_separated_post_pages_only_show_their_own_content_type()
    {
        $admin = $this->makeUser('admin-separated-list@example.com', 'admin');
        $berita = $this->makePost($admin, 'Berita Internal');
        $pengumuman = $this->makePost($admin, 'Pengumuman Internal', 'pengumuman');

        $this->actingAs($admin)
            ->get(route('admin.berita.index'))
            ->assertOk()
            ->assertSee($berita->title)
            ->assertDontSee($pengumuman->title);

        $this->actingAs($admin)
            ->get(route('admin.pengumuman.index'))
            ->assertOk()
            ->assertSee($pengumuman->title)
            ->assertDontSee($berita->title);
    }

    public function test_edit_form_uses_button_based_photo_delete_action()
    {
        $admin = $this->makeUser('admin-edit-post@example.com', 'admin');
        $post = $this->makePost($admin, 'Post Dengan Foto');
        $photo = PostPhoto::create([
            'post_id' => $post->id,
            'image' => 'post-photos/foto.png',
            'caption' => 'Foto',
            'order' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.posts.edit', $post));

        $response->assertOk();
        $response->assertSee("submitPhotoDelete('" . route('admin.posts.photo.destroy', $photo->id) . "')", false);
    }

    public function test_author_cannot_manage_other_authors_post_and_attachments()
    {
        $author = $this->makeUser('author1@example.com', 'author');
        $otherAuthor = $this->makeUser('author2@example.com', 'author');
        $otherPost = $this->makePost($otherAuthor, 'Post Orang Lain');
        $photo = PostPhoto::create([
            'post_id' => $otherPost->id,
            'image' => 'post-photos/bukti.png',
            'caption' => 'Bukti',
            'order' => 0,
        ]);

        $this->actingAs($author)
            ->get(route('admin.posts.edit', $otherPost))
            ->assertStatus(403);

        $this->actingAs($author)
            ->put(route('admin.posts.update', $otherPost), [
                'title' => 'Tidak Boleh',
                'category' => 'berita',
            ])
            ->assertStatus(403);

        $this->actingAs($author)
            ->delete(route('admin.posts.destroy', $otherPost))
            ->assertStatus(403);

        $this->actingAs($author)
            ->delete(route('admin.posts.photo.destroy', $photo->id))
            ->assertStatus(403);
    }

    public function test_admin_can_manage_any_post()
    {
        $admin = $this->makeUser('admin@example.com', 'admin');
        $author = $this->makeUser('author@example.com', 'author');
        $post = $this->makePost($author, 'Post Admin Target');

        $response = $this->actingAs($admin)->delete(route('admin.posts.destroy', $post));

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_post_can_store_additional_master_category()
    {
        $admin = $this->makeUser('admin-master@example.com', 'admin');
        $postCategory = PostCategory::create([
            'name' => 'Rakor Khusus',
            'slug' => 'rakor-khusus',
            'order' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Post Dengan Kategori Master',
            'content' => 'Konten post',
            'category' => 'berita',
            'post_category_id' => $postCategory->id,
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', [
            'title' => 'Post Dengan Kategori Master',
            'post_category_id' => $postCategory->id,
        ]);
    }

    public function test_pengumuman_requires_and_stores_announcement_category()
    {
        $admin = $this->makeUser('admin-announcement@example.com', 'admin');

        $this->actingAs($admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Pengumuman Tanpa Kategori',
                'content' => 'Konten pengumuman',
                'category' => 'pengumuman',
                'is_published' => '1',
            ])
            ->assertSessionHasErrors('announcement_category');

        $response = $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Pengumuman Dengan Kategori',
            'content' => 'Konten pengumuman',
            'category' => 'pengumuman',
            'announcement_category' => Post::ANNOUNCEMENT_CATEGORY_BADILAG,
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', [
            'title' => 'Pengumuman Dengan Kategori',
            'category' => 'pengumuman',
            'announcement_category' => Post::ANNOUNCEMENT_CATEGORY_BADILAG,
        ]);
    }

    public function test_author_satker_only_manages_peradilan_agama_papua_barat_news()
    {
        $satker = $this->makeUser('satker@example.com', User::ROLE_AUTHOR_SATKER);
        $otherAuthor = $this->makeUser('author-normal@example.com', 'author');

        $peradilanNews = $this->makePost(
            $satker,
            'Berita PA Papua Barat',
            'berita',
            Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT
        );

        $terkiniNews = $this->makePost(
            $satker,
            'Berita Terkini Satker',
            'berita',
            Post::NEWS_SCOPE_TERKINI
        );

        $announcement = $this->makePost($otherAuthor, 'Pengumuman Normal', 'pengumuman');

        $this->actingAs($satker)
            ->get(route('admin.berita.index'))
            ->assertOk()
            ->assertSee($peradilanNews->title)
            ->assertDontSee($terkiniNews->title);

        $this->actingAs($satker)
            ->get(route('admin.pengumuman.index'))
            ->assertStatus(403);

        $response = $this->actingAs($satker)->post(route('admin.berita.store'), [
            'title' => 'Berita Dari Satker',
            'content' => 'Konten',
            'category' => 'berita',
            'news_scope' => Post::NEWS_SCOPE_TERKINI,
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.berita.index'));
        $this->assertDatabaseHas('posts', [
            'title' => 'Berita Dari Satker',
            'category' => 'berita',
            'news_scope' => Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT,
            'user_id' => $satker->id,
        ]);

        $this->actingAs($satker)
            ->delete(route('admin.pengumuman.destroy', $announcement))
            ->assertStatus(403);
    }

    public function test_post_slug_is_generated_uniquely_for_long_titles()
    {
        $admin = $this->makeUser('admin-long-slug@example.com', 'admin');
        $title = str_repeat('PTA Papua Barat Hadiri Wisuda Purnabakti KPTA Bengkulu ', 4);

        $this->actingAs($admin)->post(route('admin.berita.store'), [
            'title' => $title,
            'content' => 'Konten pertama',
            'category' => 'berita',
            'news_scope' => Post::NEWS_SCOPE_TERKINI,
            'is_published' => '1',
        ])->assertRedirect(route('admin.berita.index'));

        $this->actingAs($admin)->post(route('admin.berita.store'), [
            'title' => $title,
            'content' => 'Konten kedua',
            'category' => 'berita',
            'news_scope' => Post::NEWS_SCOPE_TERKINI,
            'is_published' => '1',
        ])->assertRedirect(route('admin.berita.index'));

        $posts = Post::where('title', $title)->orderBy('id')->get();

        $this->assertCount(2, $posts);
        $this->assertNotSame($posts[0]->slug, $posts[1]->slug);
    }

    protected function makeUser($email, $role)
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => $role,
        ]);
    }

    protected function makePost(User $user, $title, $category = 'berita', $newsScope = null)
    {
        return Post::create([
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . uniqid(),
            'content' => 'Konten',
            'excerpt' => 'Ringkasan',
            'category' => $category,
            'news_scope' => $category === 'berita' ? ($newsScope ?: Post::NEWS_SCOPE_TERKINI) : null,
            'is_published' => true,
            'user_id' => $user->id,
        ]);
    }
}
