<?php

namespace Tests\Feature;

use App\Post;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class WordPressLeadershipNewsTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_website_reads_published_pimpinan_posts_directly_without_importing_them(): void
    {
        $this->travelTo(now('Asia/Jayapura')->setDate(2026, 11, 24));
        config(['database.connections.wordpress' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => 'legacy_']]);
        $schema = Schema::connection('wordpress');
        $schema->create('posts', function (Blueprint $table) {
            $table->id('ID');
            $table->string('post_title');
            $table->dateTime('post_date');
            $table->text('post_excerpt')->default('');
            $table->text('post_content')->default('');
            $table->string('post_type')->default('post');
            $table->string('post_status')->default('publish');
            $table->string('post_password')->default('');
            $table->unsignedBigInteger('post_parent')->default(0);
        });
        $schema->create('terms', function (Blueprint $table) {
            $table->id('term_id');
            $table->string('slug');
        });
        $schema->create('term_taxonomy', function (Blueprint $table) {
            $table->id('term_taxonomy_id');
            $table->unsignedBigInteger('term_id');
            $table->string('taxonomy');
        });
        $schema->create('term_relationships', function (Blueprint $table) {
            $table->unsignedBigInteger('object_id');
            $table->unsignedBigInteger('term_taxonomy_id');
        });
        $schema->create('postmeta', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->string('meta_key');
            $table->string('meta_value');
        });

        $wp = DB::connection('wordpress');
        $wp->table('terms')->insert([['term_id' => 21, 'slug' => 'agenda-pimpinan'], ['term_id' => 11, 'slug' => 'berita']]);
        $wp->table('term_taxonomy')->insert([
            ['term_taxonomy_id' => 31, 'term_id' => 21, 'taxonomy' => 'category'],
            ['term_taxonomy_id' => 41, 'term_id' => 11, 'taxonomy' => 'category'],
        ]);
        foreach ([
            ['ID' => 1, 'post_title' => 'Kunjungan Pimpinan WP', 'post_content' => '<p>Isi berita WordPress</p><script>janganTampil()</script>'],
            ['ID' => 2, 'post_title' => 'Agenda WP Draf', 'post_status' => 'draft'],
            ['ID' => 3, 'post_title' => 'Berita WP Lain'],
            ['ID' => 4, 'post_title' => 'Agenda WP Terkunci', 'post_password' => 'test'],
            ['ID' => 5, 'post_title' => 'Foto kegiatan', 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_parent' => 1],
        ] as $post) {
            $wp->table('posts')->insert($post + ['post_date' => '2026-10-10 09:00:00']);
        }
        foreach ([1 => 31, 2 => 31, 3 => 41, 4 => 31] as $id => $category) {
            $wp->table('term_relationships')->insert(['object_id' => $id, 'term_taxonomy_id' => $category]);
        }
        $wp->table('postmeta')->insert([
            ['post_id' => 1, 'meta_key' => '_thumbnail_id', 'meta_value' => '5'],
            ['post_id' => 5, 'meta_key' => '_wp_attached_file', 'meta_value' => '2026/10/dokumentasi.jpg'],
        ]);

        $detail = route('agenda.wordpress.show', ['id' => 1]);
        $archive = route('berita.index', ['kategori' => 'agenda-pimpinan']);
        $this->get(route('home'))->assertOk()->assertSee('Kunjungan Pimpinan WP')->assertSee($detail, false)
            ->assertDontSee('Agenda WP Draf')->assertDontSee('Agenda WP Terkunci')->assertDontSee('Berita WP Lain');
        $this->get($archive)->assertOk()->assertSee('Kunjungan Pimpinan WP')->assertDontSee('Berita WP Lain');
        $this->get($detail)->assertOk()->assertSee('Isi berita WordPress')->assertSee('2026/10/dokumentasi.jpg', false)
            ->assertDontSee('janganTampil');
        foreach ([2, 3, 4] as $id) {
            $this->get(route('agenda.wordpress.show', ['id' => $id]))->assertNotFound();
        }

        $wp->table('posts')->where('ID', 1)->update(['post_title' => 'Judul WP Diperbarui']);
        $this->get(route('home'))->assertOk()->assertSee('Judul WP Diperbarui')->assertDontSee('Kunjungan Pimpinan WP');
        $this->assertSame(0, Post::count());
    }
}
