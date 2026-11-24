<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransparencyDocument extends Model
{
    public const PAGE_GROUP = 'transparansi';
    public const STORAGE_DIRECTORY = 'transparency-documents';

    protected $fillable = [
        'page_id', 'title', 'year', 'period', 'version', 'budget_code',
        'source', 'pdf_path', 'external_url',
    ];

    protected $casts = ['published_at' => 'datetime'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeVisibleToPublic($query)
    {
        return $query->published()->whereHas('page', function ($pages) {
            $pages->where('menu_group', static::PAGE_GROUP)->where('is_active', true);
        });
    }

    public function download()
    {
        $disk = Storage::disk('local');
        abort_unless($this->pdf_path && $disk->exists($this->pdf_path), 404);

        return $disk->download($this->pdf_path, (Str::slug($this->title) ?: 'dokumen') . '.pdf', [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function preview()
    {
        $disk = Storage::disk('local');
        abort_unless($this->pdf_path && $disk->exists($this->pdf_path), 404);

        $response = response()->file($disk->path($this->pdf_path), [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);

        $response->setPrivate();
        return $response->setContentDisposition('inline', (Str::slug($this->title) ?: 'dokumen') . '.pdf');
    }
}
