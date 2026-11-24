<?php

namespace Tests\Feature;

use App\Page;
use App\TransparencyDocument;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransparencyDocumentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Isolate these feature tests from existing MySQL schemas and data.
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        $this->artisan('migrate', ['--path' => [
            'database/migrations/2014_10_12_000000_create_users_table.php',
            'database/migrations/2024_01_01_000001_create_pages_table.php',
            'database/migrations/2026_11_19_000001_create_transparency_documents_table.php',
        ]])->assertExitCode(0);
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(User::ROLE_AUTHOR);
        });
    }

    public function test_admin_can_publish_private_pdf_in_renstra_then_withdraw_it()
    {
        Storage::fake('local');
        $renstra = $this->page('Renstra');
        $dipa = $this->page('DIPA');
        $admin = $this->user();
        $index = route('admin.transparency-documents.index');
        $pdf = UploadedFile::fake()->create('renstra.pdf', 50, 'application/pdf');

        $this->actingAs($admin)->get(route('admin.transparency-documents.create'))
            ->assertOk()->assertSee('Renstra')->assertSee('DIPA');
        $this->post(route('admin.transparency-documents.store'), $this->fields($renstra) + ['pdf_file' => $pdf])
            ->assertRedirect($index);

        $document = TransparencyDocument::firstOrFail();
        $pageUrl = route('page.show', $renstra->slug);
        $download = route('transparency-documents.download', $document);
        $this->get($index)->assertOk()->assertSee('Dokumen Uji Renstra')->assertSee('Draf');
        Storage::disk('local')->assertExists($document->pdf_path);
        $this->assertNull($document->published_at);
        $this->get(route('admin.transparency-documents.download', $document))->assertOk();
        $this->get(route('admin.transparency-documents.edit', $document))->assertOk();
        $this->get($pageUrl)->assertOk()->assertDontSee('Dokumen Uji Renstra');
        $this->get($download)->assertNotFound();

        $this->from($index)->post(route('admin.transparency-documents.publish', $document))
            ->assertRedirect($index);
        $this->assertNotNull($document->fresh()->published_at);
        Auth::logout();
        $this->get($pageUrl)->assertOk()->assertSee('Dokumen Uji Renstra')->assertSee('2025-2029');
        $this->get(route('page.show', $dipa->slug))->assertOk()->assertDontSee('Dokumen Uji Renstra');
        Storage::disk('local')->put($document->pdf_path, '%PDF-1.4 test');
        $this->get($download)->assertOk()->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename=dokumen-uji-renstra.pdf');
        $this->get($download, ['Range' => 'bytes=0-4'])->assertStatus(206)
            ->assertHeader('Content-Range', 'bytes 0-4/13');
        $this->get(route('admin.transparency-documents.download', $document))->assertRedirect(route('login'));

        $this->actingAs($admin)->from($index)->post(route('admin.transparency-documents.unpublish', $document))
            ->assertRedirect($index);
        $this->get($download)->assertNotFound();
        $this->get($pageUrl)->assertDontSee('Dokumen Uji Renstra');
        Storage::disk('local')->assertExists($document->pdf_path);
    }

    public function test_missing_pdf_cannot_be_published_and_replacement_is_saved_as_draft()
    {
        Storage::fake('local');
        $page = $this->page('Renstra');
        $fields = $this->fields($page);
        $document = TransparencyDocument::create($fields + ['pdf_path' => 'transparency-documents/missing.pdf']);
        $index = route('admin.transparency-documents.index');
        $this->actingAs($this->user())->from($index)
            ->post(route('admin.transparency-documents.publish', $document))->assertSessionHas('error');
        $this->assertNull($document->fresh()->published_at);

        $this->put(route('admin.transparency-documents.update', $document), $fields + [
            'pdf_file' => UploadedFile::fake()->create('baru.pdf', 1, 'application/pdf'),
        ])->assertRedirect($index);
        $document->refresh();
        Storage::disk('local')->assertExists($document->pdf_path);
        $this->from($index)->post(route('admin.transparency-documents.publish', $document))
            ->assertSessionHas('success');
        $this->assertNotNull($document->fresh()->published_at);

        $this->put(route('admin.transparency-documents.update', $document), $fields + [
            'external_url' => 'https://example.test/revisi.pdf',
        ])->assertRedirect($index);
        Storage::disk('local')->assertMissing($document->pdf_path);
        $this->assertNull($document->fresh()->published_at);
    }

    public function test_dipa_external_link_is_only_listed_after_publication_and_edit_goes_back_to_draft()
    {
        $dipa = $this->page('DIPA');
        $index = route('admin.transparency-documents.index');
        $pageUrl = route('page.show', $dipa->slug);
        $pdfUrl = 'https://example.test/dipa.pdf';
        $fields = array_replace($this->fields($dipa), [
            'title' => 'Dokumen Uji DIPA', 'period' => 'Semester I 2026', 'budget_code' => 'DIPA 01',
        ]);

        $this->actingAs($this->user())->post(route('admin.transparency-documents.store'), $fields + ['external_url' => $pdfUrl])
            ->assertRedirect($index);

        $document = TransparencyDocument::firstOrFail();
        $this->get($pageUrl)->assertDontSee($pdfUrl);
        $this->from($index)->post(route('admin.transparency-documents.publish', $document))->assertRedirect($index);
        $this->get($pageUrl)->assertSee($pdfUrl)->assertSee('DIPA 01')->assertSee('Semester I 2026');
        $this->get(route('transparency-documents.download', $document))->assertNotFound();

        $this->put(route('admin.transparency-documents.update', $document), $fields)->assertRedirect($index);
        $this->assertSame($pdfUrl, $document->fresh()->external_url);
        $this->assertNull($document->fresh()->published_at);
        $this->get($pageUrl)->assertDontSee($pdfUrl);
    }

    public function test_documents_require_one_valid_source_and_a_transparency_page()
    {
        $renstra = $this->page('Renstra');
        $other = $this->page('Profil', 'tentang-pengadilan');
        $source = 'https://example.test/dokumen.pdf';
        $store = route('admin.transparency-documents.store');
        $this->actingAs($this->user());

        $this->post($store, $this->fields($renstra))->assertSessionHasErrors('pdf_file');
        $this->post($store, $this->fields($renstra) + ['external_url' => 'http://example.test/dokumen.pdf'])
            ->assertSessionHasErrors('external_url');
        $this->post($store, $this->fields($other) + ['external_url' => $source])
            ->assertSessionHasErrors('page_id');
        $this->post($store, $this->fields($renstra) + ['pdf_file' => UploadedFile::fake()->create('contoh.txt', 1, 'text/plain')])
            ->assertSessionHasErrors('pdf_file');
        $this->post($store, $this->fields($renstra) + [
            'pdf_file' => UploadedFile::fake()->create('contoh.pdf', 1, 'application/pdf'),
            'external_url' => $source,
        ])->assertSessionHasErrors('pdf_file');
        $this->assertSame(0, TransparencyDocument::count());
    }

    public function test_author_cannot_manage_documents_and_inactive_page_hides_pdf()
    {
        Storage::fake('local');
        $page = $this->page('Renstra');
        $document = TransparencyDocument::create($this->fields($page) + ['pdf_path' => 'transparency-documents/renstra.pdf']);
        Storage::disk('local')->put($document->pdf_path, '%PDF-1.4');
        $this->actingAs($this->user(User::ROLE_AUTHOR_SATKER));
        $this->get(route('admin.transparency-documents.index'))->assertRedirect(route('admin.dashboard'));
        $this->post(route('admin.transparency-documents.publish', $document))->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.transparency-documents.download', $document))->assertRedirect(route('admin.dashboard'));

        $document->published_at = now();
        $document->save();
        $page->update(['is_active' => false]);
        $this->get(route('transparency-documents.download', $document))->assertNotFound();

        $page->delete();
        $this->assertNull($document->fresh()->page_id);
        Storage::disk('local')->assertExists($document->pdf_path);
    }

    private function user($role = User::ROLE_ADMIN)
    {
        return User::create([
            'name' => 'Penguji Dokumen',
            'email' => $role . '@example.test',
            'password' => bcrypt('testing-password'),
            'role' => $role,
        ]);
    }

    private function page($title, $menuGroup = TransparencyDocument::PAGE_GROUP)
    {
        return Page::create([
            'title' => $title,
            'slug' => strtolower($title),
            'content' => '<p>Informasi resmi.</p>',
            'menu_group' => $menuGroup,
            'is_active' => true,
        ]);
    }

    private function fields(Page $page)
    {
        return [
            'page_id' => $page->id,
            'title' => 'Dokumen Uji Renstra',
            'year' => 2025,
            'period' => '2025-2029',
            'version' => 'Reviu',
            'source' => 'Bagian Perencanaan',
        ];
    }
}
