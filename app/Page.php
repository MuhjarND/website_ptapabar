<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['parent_id', 'title', 'slug', 'content', 'menu_group', 'order', 'is_active'];

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('order');
    }

    public function activeChildren()
    {
        return $this->hasMany(Page::class, 'parent_id')->where('is_active', true)->orderBy('order');
    }

    public function ancestors()
    {
        $ancestors = collect();
        $page = $this->parent;
        while ($page) {
            $ancestors->prepend($page);
            $page = $page->parent;
        }
        return $ancestors;
    }
}
