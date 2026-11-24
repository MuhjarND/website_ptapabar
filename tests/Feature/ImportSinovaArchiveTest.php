<?php

namespace Tests\Feature;

use App\Page;
use App\SurveyIndex;
use App\TransparencyDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class ImportSinovaArchiveTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_verified_import_creates_public_local_pdfs_and_never_republishes_withdrawn_records(): void
    {
        $disk = Storage::fake('local');
        $pdf = '%PDF-1.4 fixture';
        config([
            'app.url' => 'https://portal.example.test',
            'sinova-archive.pages' => ['renstra' => 'Renstra'],
            'sinova-archive.documents' => ['report.pdf' => [
                'sha256' => hash('sha256', $pdf), 'page' => 'renstra', 'title' => 'Dokumen Uji', 'year' => 2025,
                'source' => 'https://example.test/report.pdf',
            ]],
            'sinova-archive.survey' => ['file' => 'report.pdf', 'period' => 'Triwulan II 2025', 'values' => [
                'Survei Kepuasan Masyarakat' => 3.71,
            ]],
        ]);
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme('https');
        $args = ['directory' => $disk->path('incoming'), '--publish' => true];

        $disk->put('incoming/report.pdf', 'Not the expected PDF');
        $this->artisan('sinova:import-archive', $args)->assertExitCode(1);
        $this->assertSame(0, Page::count());

        $disk->put('incoming/report.pdf', $pdf);
        $this->artisan('sinova:import-archive', $args + ['--dry-run' => true])->assertExitCode(0);
        $this->assertSame(0, TransparencyDocument::count());

        $this->artisan('sinova:import-archive', $args)->assertExitCode(0);
        $document = TransparencyDocument::firstOrFail();
        $this->assertNotNull($document->published_at);
        $disk->assertExists($document->pdf_path);
        $file = route('transparency-documents.download', $document);
        $this->get(route('page.show', 'renstra'))->assertOk()->assertSee($file, false);
        $this->get($file)->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertSame($file, SurveyIndex::sole()->report_url);
        $this->assertSame($file, SurveyIndex::sole()->public_report_url);

        $document->forceFill(['published_at' => null])->save();
        $this->artisan('sinova:import-archive', $args)->assertExitCode(0);
        $this->assertSame(1, TransparencyDocument::count());
        $this->assertSame(1, SurveyIndex::count());
        $this->assertNull(SurveyIndex::sole()->public_report_url);
        $this->get($file)->assertNotFound();
    }
}
