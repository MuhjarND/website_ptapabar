<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    public const TYPE_ASPIRASI = 'aspirasi';
    public const TYPE_PENGADUAN = 'pengaduan';
    public const TYPE_PERMOHONAN_INFORMASI = 'permohonan-informasi';

    public const STATUS_BARU = 'baru';
    public const STATUS_DITINDAKLANJUTI = 'ditindaklanjuti';
    public const STATUS_SELESAI = 'selesai';

    protected $fillable = [
        'request_type',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'attachment',
        'status',
        'admin_notes',
        'handled_by',
        'emailed_to',
        'emailed_at',
    ];

    protected $casts = [
        'emailed_at' => 'datetime',
    ];

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeRequestType($query, $requestType)
    {
        if (!$requestType) {
            return $query;
        }

        return $query->where('request_type', $requestType);
    }

    public function scopeRequestStatus($query, $status)
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public static function typeOptions()
    {
        return [
            static::TYPE_ASPIRASI => 'Aspirasi',
            static::TYPE_PENGADUAN => 'Pengaduan',
            static::TYPE_PERMOHONAN_INFORMASI => 'Permohonan Informasi',
        ];
    }

    public static function statusOptions()
    {
        return [
            static::STATUS_BARU => 'Baru',
            static::STATUS_DITINDAKLANJUTI => 'Ditindaklanjuti',
            static::STATUS_SELESAI => 'Selesai',
        ];
    }

    public function getRequestTypeLabelAttribute()
    {
        return static::typeOptions()[$this->request_type] ?? ucfirst($this->request_type);
    }

    public function getStatusLabelAttribute()
    {
        return static::statusOptions()[$this->status] ?? ucfirst($this->status);
    }
}
