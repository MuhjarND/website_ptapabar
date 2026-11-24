<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SurveyIndex extends Model
{
    protected $fillable = ['title', 'icon', 'value', 'label', 'order', 'is_active', 'period', 'report_url'];

    public const EVIDENCE_TYPES = [
        'Survei Kepuasan Masyarakat' => ['kepuasan masyarakat', 'kepuasan layanan', 'skm', 'ikm'],
        'Survei Persepsi Anti Korupsi' => ['anti korupsi', 'antikorupsi', 'spak', 'ipak'],
        'Survei Persepsi Kualitas Layanan' => ['kualitas layanan', 'kualitas pelayanan', 'spkl', 'spkp'],
    ];

    public function evidenceType(): ?string
    {
        $title = ' ' . Str::lower(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $this->title)) . ' ';
        foreach (self::EVIDENCE_TYPES as $type => $aliases) {
            if (Str::contains($title, array_map(fn ($alias) => ' ' . $alias . ' ', $aliases))) {
                return $type;
            }
        }

        return null;
    }

    public function getPublicReportUrlAttribute(): ?string
    {
        $url = filter_var($this->report_url, FILTER_VALIDATE_URL);
        if (!$url || !str_starts_with(strtolower($url), 'https://')
            || parse_url($url, PHP_URL_USER) !== null || parse_url($url, PHP_URL_PASS) !== null) {
            return null;
        }

        $prefix = rtrim(config('app.url'), '/') . '/dokumen-transparansi/';
        if (str_starts_with($url, $prefix) && preg_match('/\A([0-9]+)\/file\z/', substr($url, strlen($prefix)), $match)) {
            $file = TransparencyDocument::visibleToPublic()->whereKey($match[1])->value('pdf_path');
            if (!$file || !Storage::disk('local')->exists($file)) {
                return null;
            }
        }

        return $url;
    }

    public function getDisplayValueAttribute()
    {
        return rtrim(rtrim(number_format((float) $this->value, 2, '.', ''), '0'), '.');
    }
}
