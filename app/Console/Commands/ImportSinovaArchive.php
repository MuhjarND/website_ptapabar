<?php

namespace App\Console\Commands;

use App\Page;
use App\SurveyIndex;
use App\TransparencyDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ImportSinovaArchive extends Command
{
    protected $signature = 'sinova:import-archive {directory : Folder hasil ekstrak PDF}
        {--dry-run : Cek seluruh PDF tanpa mengubah data}
        {--publish : Terbitkan dokumen baru yang berasal dari arsip publik}';

    protected $description = 'Impor salinan PDF resmi SINOVA ke Dokumen Transparansi';

    public function handle(): int
    {
        $archive = config('sinova-archive.documents');
        $directory = rtrim($this->argument('directory'), '/\\') . DIRECTORY_SEPARATOR;
        $invalid = [];

        foreach ($archive as $file => $entry) {
            $path = $directory . $file;
            if (!is_file($path) || !is_readable($path) || hash_file('sha256', $path) !== $entry['sha256']) {
                $invalid[] = $file;
            }
        }

        if ($invalid) {
            $this->error('Tidak diimpor. Berkas hilang atau isinya berbeda: ' . implode(', ', $invalid));
            return self::FAILURE;
        }

        $this->info(count($archive) . ' PDF cocok dengan manifest.');
        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        if (!Schema::hasTable('transparency_documents')) {
            $this->error('Tabel transparency_documents belum tersedia. Jalankan migrasi modul dokumen terlebih dahulu.');
            return self::FAILURE;
        }

        $survey = config('sinova-archive.survey');
        if ($survey && $this->option('publish') && (!Schema::hasTable('survey_indices')
            || !Schema::hasColumn('survey_indices', 'report_url')
            || parse_url(config('app.url'), PHP_URL_SCHEME) !== 'https')) {
            $this->error('Untuk menerbitkan survei, pastikan skema survei sudah lengkap dan APP_URL memakai HTTPS.');
            return self::FAILURE;
        }

        $pages = Page::whereIn('slug', array_keys(config('sinova-archive.pages')))->get()->keyBy('slug');
        foreach ($pages as $page) {
            if (!$page->is_active || $page->menu_group !== TransparencyDocument::PAGE_GROUP) {
                $this->error("Halaman {$page->slug} tidak aktif atau bukan kelompok Transparansi. Tidak ada perubahan.");
                return self::FAILURE;
            }
        }

        $disk = Storage::disk('local');
        foreach ($archive as $file => $entry) {
            $path = TransparencyDocument::STORAGE_DIRECTORY . '/arsip-sinova/' . $file;
            if ($disk->exists($path) && hash_file('sha256', $disk->path($path)) !== $entry['sha256']) {
                $this->error("PDF tujuan {$file} berbeda. Berkas lama tidak ditimpa.");
                return self::FAILURE;
            }
        }

        $added = 0;
        foreach ($archive as $file => $entry) {
            $slug = $entry['page'];
            $page = $pages[$slug] ??= Page::firstOrCreate(['slug' => $slug], [
                'title' => config('sinova-archive.pages')[$slug],
                'menu_group' => TransparencyDocument::PAGE_GROUP,
                'is_active' => true,
            ]);
            $path = TransparencyDocument::STORAGE_DIRECTORY . '/arsip-sinova/' . $file;
            $document = TransparencyDocument::where('pdf_path', $path)
                ->orWhere(fn ($query) => $query->where('page_id', $page->id)->where('source', $entry['source']))
                ->first();
            if ($document && $document->pdf_path !== $path) {
                continue;
            }
            if (!$disk->exists($path)) {
                $stream = fopen($directory . $file, 'rb');
                if (!$stream) {
                    $this->error("Tidak dapat membuka {$file}.");
                    return self::FAILURE;
                }
                try {
                    $stored = $disk->put($path, $stream);
                } finally {
                    fclose($stream);
                }
                if (!$stored) {
                    $this->error("Tidak dapat menyimpan {$file}. Periksa izin tulis storage.");
                    return self::FAILURE;
                }
            }

            if (!$document) {
                $document = new TransparencyDocument([
                    'page_id' => $page->id,
                    'pdf_path' => $path,
                    'title' => $entry['title'],
                    'year' => $entry['year'],
                    'period' => $entry['period'] ?? null,
                    'version' => $entry['version'] ?? null,
                    'budget_code' => $entry['budget_code'] ?? null,
                    'source' => $entry['source'],
                ]);
                $document->published_at = $this->option('publish') ? now() : null;
                $document->save();
                $this->line($this->documentUrl($document));
                $added++;
            }

            if (($survey['file'] ?? null) === $file && $this->option('publish') && $document->published_at) {
                $this->importSurvey($survey, $document);
            }
        }

        $this->info($added . ' dokumen baru; entri arsip yang sudah ada tidak diubah.');
        return self::SUCCESS;
    }

    private function importSurvey(array $survey, TransparencyDocument $document): void
    {
        $existing = SurveyIndex::where('period', $survey['period'])->get()->map->evidenceType();

        foreach ($survey['values'] as $title => $value) {
            if (!$existing->contains($title)) {
                SurveyIndex::create([
                    'title' => $title,
                    'icon' => 'fas fa-chart-bar',
                    'value' => $value,
                    'label' => 'Arsip Survei Badilag',
                    'period' => $survey['period'],
                    'report_url' => $this->documentUrl($document),
                    'order' => 99,
                    'is_active' => true,
                ]);
            }
        }
    }

    private function documentUrl(TransparencyDocument $document): string
    {
        return rtrim(config('app.url'), '/') . route('transparency-documents.download', $document, false);
    }
}
