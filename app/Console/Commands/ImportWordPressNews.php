<?php

namespace App\Console\Commands;

use App\Article;
use App\Post;
use App\PostCategory;
use App\Slider;
use App\Support\WordPressContent;
use App\Support\WordPressDump;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class ImportWordPressNews extends Command
{
    protected $signature = 'wordpress:import-news {dump : Path to the phpMyAdmin SQL export}
                            {--all : Include documentation, announcements, articles and the configured slider}
                            {--apply : Write to Laravel instead of previewing}
                            {--expect-database= : Exact Laravel database name required with --apply}';
    protected $description = 'Pratinjau atau impor satu kali arsip publik WordPress PTA Papua Barat';

    public function handle(): int
    {
        $file = $this->argument('dump');
        $apply = $this->option('apply');
        $all = $this->option('all');
        $sliderId = $all ? config('wordpress.slider_id') : 0;

        if (!is_file($file) || !is_readable($file)) {
            $this->error('File dump WordPress tidak dapat dibaca.');
            return 1;
        }

        try {
            $categories = collect();
            if ($apply) {
                if (!$this->option('expect-database') || DB::connection()->getDatabaseName() !== $this->option('expect-database')) {
                    throw new RuntimeException('Nama database tujuan tidak cocok dengan --expect-database. Tidak ada perubahan.');
                }
                if (!Schema::hasColumn('posts', 'wordpress_id') || !Schema::hasColumn('posts', 'wordpress_cover_path')
                    || ($all && (!Schema::hasColumns('articles', ['wordpress_id', 'wordpress_cover_path'])
                        || !Schema::hasColumn('post_photos', 'wordpress_path')))
                    || ($sliderId && !Schema::hasColumns('sliders', ['wordpress_id', 'wordpress_cover_path']))) {
                    throw new RuntimeException('Jalankan migrasi metadata WordPress sebelum impor.');
                }
                $categories = PostCategory::pluck('id', 'slug');
            }

            $reader = new WordPressDump($file, config('wordpress.dump_prefix', 'wp_'));
            [$publications, $thumbnails, $files] = $this->metadata($reader, $all);
            [$attachments, $parents] = $all ? $this->attachmentIndex($reader, $files) : [[], []];
            $counts = array_fill_keys(['berita', 'pengumuman', 'artikel', 'slider', 'konten baru', 'rujukan dokumentasi', 'dokumentasi baru'], 0);
            $run = function () use ($reader, $publications, $thumbnails, $files, $attachments, $parents, $categories, $sliderId, $all, $apply, &$counts) {
                $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jayapura'));
                foreach ($reader->rows(['posts']) as [, $row]) {
                    $id = (int) $row['ID'];
                    if (!isset($publications[$id]) || $row['post_type'] !== 'post'
                        || $row['post_status'] !== 'publish' || $row['post_password'] !== '') {
                        continue;
                    }

                    $title = Post::sanitizeExcerpt($row['post_title'], 252);
                    $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $row['post_date'], $now->getTimezone());
                    if ($title === '' || !$date || $date->format('Y-m-d H:i:s') !== $row['post_date']
                        || $date > $now || $date->format('Y') < '1971' || $date->format('Y') > '2037') {
                        continue;
                    }

                    [$html, $firstImage, $pdf] = WordPressContent::clean($row['post_content']);
                    $cover = WordPressContent::imagePath($files[$thumbnails[$id] ?? 0] ?? '') ?? $firstImage;
                    [$type, $terms] = $publications[$id];
                    $documentation = $all && $type !== 'artikel'
                        ? $this->documentation($row['post_content'], $attachments, $parents[$id] ?? [], $cover)
                        : [];
                    if (Post::sanitizeExcerpt($html) === '' && !$cover && !$pdf && !$documentation) {
                        continue;
                    }

                    $counts[$type]++;
                    $counts['rujukan dokumentasi'] += count($documentation);
                    if (!$apply) {
                        continue;
                    }

                    $data = [
                        'title' => $title,
                        'content' => $html,
                        'excerpt' => Post::sanitizeExcerpt($row['post_excerpt'] ?: $html, 200),
                    ];
                    $slugSource = Str::slug(rawurldecode($row['post_name'])) ?: $title;
                    if ($type === 'artikel') {
                        $article = Article::where('wordpress_id', $id)->first() ?? new Article(['image' => '']);
                        if (!$article->exists) {
                            $article->wordpress_cover_path = $cover;
                        }
                        $counts['konten baru'] += $this->write($article, $id, $slugSource, $row['post_date'], $data + [
                            'pdf_url' => $pdf,
                            'article_category' => $this->agencyCategory($terms),
                            'is_active' => true,
                        ]);
                    } else {
                        $post = Post::where('wordpress_id', $id)->first() ?? new Post;
                        if (!$post->exists) {
                            $post->wordpress_cover_path = $cover;
                        }
                        $counts['konten baru'] += $this->write($post, $id, $slugSource, $row['post_date'], $data + [
                            'category' => $type,
                            'post_category_id' => $type === 'berita' ? $this->mappedCategory($terms, $categories) : null,
                            'news_scope' => $type === 'berita' ? Post::NEWS_SCOPE_TERKINI : null,
                            'announcement_category' => $type === 'pengumuman' ? $this->agencyCategory($terms) : null,
                            'published_at' => $row['post_date'],
                            'workflow_status' => Post::STATUS_PUBLISHED,
                            'is_published' => true,
                        ]);
                        if ($documentation) {
                            $counts['dokumentasi baru'] += $this->saveDocumentation($post, $documentation);
                        }
                    }
                }

                if ($sliderId) {
                    $this->importSlides($reader, $sliderId, $now, $apply, $counts);
                }
            };
            $apply ? DB::transaction($run) : $run();
        } catch (QueryException $exception) {
            $this->error('Koneksi atau skema database tidak dapat dibaca/ditulis. Periksa konfigurasi dan izin; tidak ada perubahan dari impor yang gagal.');
            return 1;
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());
            return 1;
        }

        $this->info($apply ? 'Impor selesai.' : 'Pratinjau saja; tidak ada perubahan database.');
        $this->table(array_keys($counts), [array_values($counts)]);

        return 0;
    }

    private function metadata(WordPressDump $reader, bool $all): array
    {
        $terms = $taxonomies = $parents = $relationships = $thumbnails = $files = [];
        foreach ($reader->rows(['terms', 'term_taxonomy', 'term_relationships', 'postmeta']) as [$table, $row]) {
            if ($table === 'terms') {
                $terms[(int) $row['term_id']] = $row['slug'];
            } elseif ($table === 'term_taxonomy' && $row['taxonomy'] === 'category') {
                $term = (int) $row['term_id'];
                $taxonomies[(int) $row['term_taxonomy_id']] = $term;
                $parents[$term] = (int) $row['parent'];
            } elseif ($table === 'term_relationships') {
                $relationships[(int) $row['object_id']][] = (int) $row['term_taxonomy_id'];
            } elseif ($table === 'postmeta' && $row['meta_key'] === '_thumbnail_id') {
                $thumbnails[(int) $row['post_id']] = (int) $row['meta_value'];
            } elseif ($table === 'postmeta' && $row['meta_key'] === '_wp_attached_file') {
                $files[(int) $row['post_id']] = $row['meta_value'];
            }
        }

        $required = $all ? ['berita', 'artikel', 'pengumuman'] : ['berita'];
        if (array_diff($required, array_intersect_key($terms, $parents))) {
            throw new RuntimeException('Kategori WordPress yang diperlukan tidak ditemukan. Tidak ada perubahan.');
        }

        $branches = [];
        foreach ($taxonomies as $taxonomy => $term) {
            $seen = [];
            while (isset($terms[$term]) && !isset($seen[$term])) {
                $branches[$taxonomy][] = $terms[$term];
                $seen[$term] = true;
                $term = $parents[$term] ?? 0;
            }
        }

        $publications = [];
        foreach ($relationships as $id => $references) {
            $tagged = [];
            foreach ($references as $reference) {
                $tagged = array_merge($tagged, $branches[$reference] ?? []);
            }
            if (in_array('agenda-pimpinan', $tagged, true)) {
                continue;
            }
            foreach (['pengumuman', 'artikel', 'berita'] as $type) {
                if (in_array($type, $tagged, true)) {
                    if ($all || $type === 'berita') {
                        $publications[$id] = [$type, $tagged];
                    }
                    break;
                }
            }
        }

        return [$publications, $thumbnails, $files];
    }

    private function attachmentIndex(WordPressDump $reader, array $files): array
    {
        $attachments = $parents = [];
        foreach ($reader->rows(['posts']) as [, $row]) {
            if ($row['post_type'] !== 'attachment' || !in_array($row['post_status'], ['inherit', 'publish'], true)
                || $row['post_password'] !== '') {
                continue;
            }

            $id = (int) $row['ID'];
            $path = WordPressContent::filePath($files[$id] ?? $row['guid'] ?? '');
            if ($path) {
                $attachments[$id] = [$path, $row['post_excerpt']];
                $parent = (int) ($row['post_parent'] ?? 0);
                if ($parent) {
                    $parents[$parent][] = $id;
                }
            }
        }

        return [$attachments, $parents];
    }

    private function documentation(string $html, array $attachments, array $relatedIds, ?string $cover): array
    {
        [$inline, $referencedIds] = WordPressContent::documentationReferences($html);
        $files = $originals = [];
        $originalKey = fn (string $path) => preg_replace('/(?:-scaled)?-\d+x\d+(?=\.[^.]+$)|-scaled(?=\.[^.]+$)/i', '', $path);
        foreach (array_unique(array_merge($relatedIds, $referencedIds)) as $id) {
            if (isset($attachments[$id])) {
                [$path, $caption] = $attachments[$id];
                $files[$path] = $caption;
                $originals[$originalKey($path)] ??= $path;
            }
        }
        foreach ($inline as $path => $caption) {
            $path = $originals[$originalKey($path)] ?? $path;
            $files[$path] = ($files[$path] ?? '') ?: $caption;
        }
        if ($cover && !array_filter(array_keys($files), fn ($path) => WordPressContent::imagePath($path))) {
            $files[$cover] = '';
        }

        return $files;
    }

    private function saveDocumentation(Post $post, array $files): int
    {
        $existing = $post->photos()->whereNotNull('wordpress_path')->pluck('wordpress_path')->flip();
        $order = (int) $post->photos()->max('order');
        $rows = [];
        foreach ($files as $path => $caption) {
            if (!$existing->has($path)) {
                $rows[] = [
                    'image' => '',
                    'wordpress_path' => $path,
                    'caption' => Post::sanitizeExcerpt($caption, 240) ?: null,
                    'order' => ++$order,
                ];
            }
        }

        return $post->photos()->createMany($rows)->count();
    }

    private function write(Post|Article $entry, int $id, string $slug, string $date, array $data): int
    {
        if ($entry->exists) {
            return 0;
        }
        $entry->slug = $entry::generateUniqueSlug($slug);
        $entry->wordpress_id = $id;
        $entry->created_at = $date;
        $entry->fill($data);
        $entry->save();

        return 1;
    }

    private function importSlides(WordPressDump $reader, int $sliderId, DateTimeImmutable $now, bool $apply, array &$counts): void
    {
        $status = null;
        foreach ($reader->rows(['nextend2_smartslider3_sliders']) as [, $row]) {
            if ((int) $row['id'] === $sliderId) {
                $status = $row['slider_status'];
            }
        }
        if ($status === null) {
            throw new RuntimeException('Proyek Smart Slider yang dikonfigurasi tidak ditemukan.');
        }

        foreach ($reader->rows(['nextend2_smartslider3_slides']) as [, $row]) {
            $time = $now->format('Y-m-d H:i:s');
            $start = $row['publish_up'];
            $end = $row['publish_down'];
            if ($status !== 'published' || (int) $row['slider'] !== $sliderId || !(int) $row['published'] || (int) $row['generator_id']
                || ($start && $start > $time) || ($end && !in_array($end, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'], true) && $end < $time)) {
                continue;
            }

            $params = json_decode($row['params'] ?: '{}', true);
            $path = WordPressContent::imagePath($params['backgroundImage'] ?? '')
                ?? WordPressContent::imagePath($row['thumbnail'] ?? '');
            if (!$path) {
                continue;
            }
            $counts['slider']++;
            if (!$apply) {
                continue;
            }

            $id = (int) $row['id'];
            $slide = Slider::where('wordpress_id', $id)->first() ?? new Slider(['image' => '']);
            if ($slide->exists) {
                continue;
            }
            $slide->wordpress_id = $id;
            $slide->wordpress_cover_path = $path;
            $slide->fill([
                'title' => Post::sanitizeExcerpt(($params['backgroundAlt'] ?? '') ?: $row['title'], 252),
                'description' => Post::sanitizeExcerpt($row['description'], 252),
                'order' => 1000 + ((int) $row['first'] ? 0 : (int) $row['ordering'] + 1),
                'is_active' => true,
            ]);
            $slide->save();
            $counts['konten baru']++;
        }
    }

    private function agencyCategory(array $terms): string
    {
        foreach ($terms as $term) {
            $term = $term === 'pta-papuabarat' ? 'pta-papua-barat' : $term;
            if (isset(Article::categoryOptions()[$term])) {
                return $term;
            }
        }

        return Article::CATEGORY_LAIN_LAIN;
    }

    private function mappedCategory(array $terms, Collection $categories): ?int
    {
        foreach ($terms as $term) {
            $term = $term === 'bimbingan-teknis' ? 'bimtek' : $term;
            if ($categories->has($term)) {
                return $categories->get($term);
            }
        }

        return null;
    }
}
