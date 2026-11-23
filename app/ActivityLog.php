<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $appends = [
        'action_label',
        'actor_name',
        'subject_label',
    ];

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'subject_title',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute()
    {
        $labels = [
            'post.created' => 'Membuat post',
            'post.updated' => 'Memperbarui post',
            'post.deleted' => 'Menghapus post',
            'document.created' => 'Menambahkan dokumen',
            'document.updated' => 'Memperbarui dokumen',
            'document.deleted' => 'Menghapus dokumen',
            'footer-link.created' => 'Menambahkan tautan footer',
            'footer-link.updated' => 'Memperbarui tautan footer',
            'footer-link.deleted' => 'Menghapus tautan footer',
            'setting.updated' => 'Memperbarui pengaturan situs',
            'floating-icon.created' => 'Menambahkan ikon mengambang',
            'floating-icon.updated' => 'Memperbarui ikon mengambang',
            'floating-icon.deleted' => 'Menghapus ikon mengambang',
            'agenda.created' => 'Menambahkan agenda pimpinan',
            'agenda.updated' => 'Memperbarui agenda pimpinan',
            'agenda.deleted' => 'Menghapus agenda pimpinan',
            'faq.created' => 'Menambahkan FAQ layanan',
            'faq.updated' => 'Memperbarui FAQ layanan',
            'faq.deleted' => 'Menghapus FAQ layanan',
            'service-request.updated' => 'Memperbarui tindak lanjut layanan',
        ];

        return $labels[$this->action] ?? ucfirst(str_replace(['.', '-'], ' ', $this->action));
    }

    public function getActorNameAttribute()
    {
        return optional($this->user)->name ?: 'Sistem';
    }

    public function getSubjectLabelAttribute()
    {
        if ($this->subject_title) {
            return $this->subject_title;
        }

        if ($this->subject_type) {
            return class_basename($this->subject_type);
        }

        return 'Konten';
    }
}
