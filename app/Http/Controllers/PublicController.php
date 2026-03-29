<?php

namespace App\Http\Controllers;

use App\Page;
use App\Post;
use App\Setting;
use App\Slider;
use App\SurveyIndex;
use App\VisitorCount;

class PublicController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('is_active', true)->orderBy('order')->get();
        $berita = Post::published()->berita()->latest()->take(6)->get();
        $pengumuman = Post::published()->pengumuman()->latest()->take(5)->get();
        $surveys = SurveyIndex::where('is_active', true)->orderBy('order')->get();
        $quickLinks = \App\QuickLink::where('is_active', true)->orderBy('order')->get();
        $galleries = \App\Gallery::where('is_active', true)->orderBy('order')->take(8)->get();
        return view('public.home', compact('sliders', 'berita', 'pengumuman', 'surveys', 'quickLinks', 'galleries'));
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $breadcrumbs = $page->ancestors();
        $siblings = $page->parent
            ? $page->parent->activeChildren
            : Page::where('menu_group', $page->menu_group)->whereNull('parent_id')->where('is_active', true)->orderBy('order')->get();
        $children = $page->activeChildren;
        return view('public.page', compact('page', 'breadcrumbs', 'siblings', 'children'));
    }

    public function berita()
    {
        $posts = Post::published()->berita()->latest()->paginate(12);
        return view('public.posts', ['posts' => $posts, 'title' => 'Berita', 'category' => 'berita']);
    }

    public function pengumuman()
    {
        $posts = Post::published()->pengumuman()->latest()->paginate(12);
        return view('public.posts', ['posts' => $posts, 'title' => 'Pengumuman', 'category' => 'pengumuman']);
    }

    public function postDetail($slug)
    {
        $post = Post::with('photos')->published()->where('slug', $slug)->firstOrFail();
        $related = Post::published()->where('category', $post->category)->where('id', '!=', $post->id)->latest()->take(4)->get();
        return view('public.post-detail', compact('post', 'related'));
    }

    public function search()
    {
        $q = request('q');
        $posts = Post::published()->where(function($query) use ($q) {
            $query->where('title', 'like', "%{$q}%")
                  ->orWhere('content', 'like', "%{$q}%");
        })->latest()->paginate(12);
        $pages = Page::where('is_active', true)->where(function($query) use ($q) {
            $query->where('title', 'like', "%{$q}%")
                  ->orWhere('content', 'like', "%{$q}%");
        })->take(10)->get();
        return view('public.search', compact('posts', 'pages', 'q'));
    }
}
