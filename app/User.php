<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_AUTHOR = 'author';
    const ROLE_AUTHOR_SATKER = 'author_satker';

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function roleOptions()
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_AUTHOR => 'Author',
            self::ROLE_AUTHOR_SATKER => 'Author Satker',
        ];
    }

    public function isAdmin()
    {
        return $this->role === static::ROLE_ADMIN;
    }

    public function isAuthor()
    {
        return $this->role === static::ROLE_AUTHOR;
    }

    public function isAuthorSatker()
    {
        return $this->role === static::ROLE_AUTHOR_SATKER;
    }

    public function canAccessAdminPanel()
    {
        return in_array($this->role, [static::ROLE_ADMIN, static::ROLE_AUTHOR, static::ROLE_AUTHOR_SATKER], true);
    }

    public function canManageAnnouncements()
    {
        return in_array($this->role, [static::ROLE_ADMIN, static::ROLE_AUTHOR], true);
    }

    public function getRoleLabelAttribute()
    {
        return static::roleOptions()[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role));
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
