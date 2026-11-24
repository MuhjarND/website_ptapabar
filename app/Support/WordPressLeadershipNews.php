<?php

namespace App\Support;

use App\Post;
use App\PostPhoto;
use Closure;
use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDOException;

class WordPressLeadershipNews
{
    public function latest(int $limit): ?Collection
    {
        return $this->read(fn ($db) => $this->posts($db, $this->query($db)->limit($limit)->get()));
    }

    public function paginate(int $perPage): ?LengthAwarePaginator
    {
        return $this->read(function ($db) use ($perPage) {
            $page = $this->query($db)->paginate($perPage);
            return $page->setCollection($this->posts($db, $page->getCollection()));
        });
    }

    public function find(int $id): ?Post
    {
        return $this->read(function ($db) use ($id) {
            $row = $this->query($db)->where('p.ID', $id)->first();
            if (!$row) {
                return null;
            }

            $post = $this->posts($db, collect([$row]))->first();
            [$files] = WordPressContent::documentationReferences($row->post_content);
            $attachments = $db->table('posts as a')
                ->join('postmeta as m', 'm.post_id', '=', 'a.ID')
                ->where('a.post_parent', $id)->where('a.post_type', 'attachment')
                ->where('a.post_status', 'inherit')->where('m.meta_key', '_wp_attached_file')
                ->pluck('m.meta_value');

            foreach ($attachments as $attachment) {
                if ($path = WordPressContent::filePath($attachment)) {
                    $files[$path] ??= '';
                }
            }

            return $post->setRelation('photos', collect($files)->map(fn ($caption, $path) => new PostPhoto([
                'wordpress_path' => $path,
                'caption' => $caption,
            ]))->values());
        });
    }

    private function query(Connection $db)
    {
        return $db->table('posts as p')
            ->select('p.ID', 'p.post_title', 'p.post_date', 'p.post_excerpt', 'p.post_content')
            ->where('p.post_type', 'post')->where('p.post_status', 'publish')
            ->where('p.post_password', '')
            ->whereBetween('p.post_date', ['1971-01-01 00:00:00', now('Asia/Jayapura')->format('Y-m-d H:i:s')])
            ->whereExists(function ($query) {
                $query->from('term_relationships as r')
                    ->join('term_taxonomy as tt', 'tt.term_taxonomy_id', '=', 'r.term_taxonomy_id')
                    ->join('terms as t', 't.term_id', '=', 'tt.term_id')
                    ->whereColumn('r.object_id', 'p.ID')->where('tt.taxonomy', 'category')
                    ->whereIn('t.slug', ['pimpinan', 'agenda-pimpinan']);
            })
            ->orderByDesc('p.post_date')->orderByDesc('p.ID');
    }

    private function posts(Connection $db, Collection $rows): Collection
    {
        $thumbnails = $db->table('postmeta')->whereIn('post_id', $rows->pluck('ID'))
            ->where('meta_key', '_thumbnail_id')->pluck('meta_value', 'post_id');
        $paths = $db->table('postmeta')->whereIn('post_id', $thumbnails->values()->map(fn ($value) => (int) $value))
            ->where('meta_key', '_wp_attached_file')->pluck('meta_value', 'post_id');

        return $rows->map(function ($row) use ($thumbnails, $paths) {
            [$content, $firstImage] = WordPressContent::clean($row->post_content);
            $cover = WordPressContent::imagePath($paths->get((int) $thumbnails->get($row->ID)) ?? '') ?? $firstImage;

            return (new Post())->forceFill([
                'title' => Post::sanitizeExcerpt($row->post_title),
                'created_at' => $row->post_date,
                'category' => 'berita',
                'content' => $content,
                'excerpt' => Post::sanitizeExcerpt($row->post_excerpt ?: $content, 200),
                'wordpress_id' => (int) $row->ID,
                'wordpress_cover_path' => $cover,
                'public_url' => route('agenda.wordpress.show', ['id' => $row->ID]),
            ])->setRelation('user', null)->setRelation('postCategory', null);
        });
    }

    private function read(Closure $query): mixed
    {
        if (!config('database.connections.wordpress.database')) {
            return null;
        }

        try {
            return $query(DB::connection('wordpress'));
        } catch (PDOException) {
            return null;
        }
    }
}
