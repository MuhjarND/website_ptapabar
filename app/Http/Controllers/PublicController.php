<?php

namespace App\Http\Controllers;

use App\Article;
use App\GalleryCategory;
use App\IntegrityZone;
use App\Page;
use App\Post;
use App\PostCategory;
use App\SiteApplication;
use App\Slider;
use App\SurveyIndex;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('is_active', true)->orderBy('order')->get();
        $beritaTerkini = Post::with(['postCategory', 'user'])
            ->published()
            ->berita()
            ->newsScope(Post::NEWS_SCOPE_TERKINI)
            ->latest()
            ->take(9)
            ->get();
        $beritaPeradilanAgama = Post::with(['postCategory', 'user'])
            ->published()
            ->berita()
            ->newsScope(Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT)
            ->latest()
            ->take(9)
            ->get();
        $beritaCategories = PostCategory::active()->orderBy('order')->orderBy('name')->get();
        $pengumuman = Post::published()->pengumuman()->latest()->take(15)->get();
        $articles = Article::active()->orderBy('order')->latest()->take(15)->get();
        $surveys = SurveyIndex::where('is_active', true)->orderBy('order')->get();
        $quickLinks = \App\QuickLink::where('is_active', true)->orderBy('order')->get();
        $galleries = \App\Gallery::with('galleryCategory')->where('is_active', true)->orderBy('order')->take(16)->get();
        $galleryCategories = GalleryCategory::active()->orderBy('order')->orderBy('name')->get();
        $integrityZones = IntegrityZone::active()->orderBy('order')->orderBy('title')->get();
        $ptaInnovationApps = SiteApplication::active()
            ->where('group_type', SiteApplication::GROUP_PTA_INOVASI)
            ->orderBy('order')
            ->orderBy('title')
            ->get();
        $mahkamahAgungApps = SiteApplication::active()
            ->where('group_type', SiteApplication::GROUP_MAHKAMAH_AGUNG)
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return view('public.home', compact(
            'sliders',
            'beritaTerkini',
            'beritaPeradilanAgama',
            'beritaCategories',
            'pengumuman',
            'articles',
            'surveys',
            'quickLinks',
            'galleries',
            'galleryCategories',
            'integrityZones',
            'ptaInnovationApps',
            'mahkamahAgungApps'
        ));
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
        $newsScopes = Post::newsScopeOptions();
        $activeNewsScope = request('news_scope');
        $posts = Post::published()
            ->berita()
            ->newsScope(array_key_exists($activeNewsScope, $newsScopes) ? $activeNewsScope : null)
            ->latest()
            ->paginate(12);

        return view('public.posts', [
            'posts' => $posts,
            'title' => 'Berita',
            'category' => 'berita',
            'newsScopes' => $newsScopes,
            'activeNewsScope' => array_key_exists($activeNewsScope, $newsScopes) ? $activeNewsScope : null,
            'announcementCategories' => [],
            'activeAnnouncementCategory' => null,
        ]);
    }

    public function pengumuman()
    {
        $announcementCategories = Post::announcementCategoryOptions();
        $activeAnnouncementCategory = request('announcement_category');
        $posts = Post::published()
            ->pengumuman()
            ->announcementCategory(array_key_exists($activeAnnouncementCategory, $announcementCategories) ? $activeAnnouncementCategory : null)
            ->latest()
            ->paginate(12);

        return view('public.posts', [
            'posts' => $posts,
            'title' => 'Pengumuman',
            'category' => 'pengumuman',
            'newsScopes' => [],
            'activeNewsScope' => null,
            'announcementCategories' => $announcementCategories,
            'activeAnnouncementCategory' => array_key_exists($activeAnnouncementCategory, $announcementCategories) ? $activeAnnouncementCategory : null,
        ]);
    }

    public function galleries()
    {
        $galleryCategories = GalleryCategory::active()->orderBy('order')->orderBy('name')->get();
        $requestedGalleryCategory = (int) request('gallery_category_id');
        $activeGalleryCategory = $galleryCategories->contains('id', $requestedGalleryCategory) ? $requestedGalleryCategory : null;
        $galleries = \App\Gallery::with('galleryCategory')
            ->where('is_active', true)
            ->when($activeGalleryCategory, function ($query) use ($activeGalleryCategory) {
                $query->where('gallery_category_id', $activeGalleryCategory);
            })
            ->orderBy('order')
            ->paginate(16);

        return view('public.galleries', compact('galleries', 'galleryCategories', 'activeGalleryCategory'));
    }

    public function articles()
    {
        $articleCategories = Article::categoryOptions();
        $activeArticleCategory = request('article_category');
        $articles = Article::active()
            ->articleCategory(array_key_exists($activeArticleCategory, $articleCategories) ? $activeArticleCategory : null)
            ->orderBy('order')
            ->latest()
            ->paginate(12);

        return view('public.articles', [
            'articles' => $articles,
            'articleCategories' => $articleCategories,
            'activeArticleCategory' => array_key_exists($activeArticleCategory, $articleCategories) ? $activeArticleCategory : null,
        ]);
    }

    public function postDetail($slug)
    {
        $post = Post::with(['photos', 'user'])->published()->where('slug', $slug)->firstOrFail();
        $related = Post::published()->where('category', $post->category)->where('id', '!=', $post->id)->latest()->take(4)->get();
        $imageFiles = $post->photos->filter(function ($photo) {
            return !Str::endsWith(strtolower($photo->image), '.pdf');
        })->values();
        $pdfFiles = $post->photos->filter(function ($photo) {
            return Str::endsWith(strtolower($photo->image), '.pdf');
        })->values();
        $lightboxPhotos = $imageFiles->map(function ($photo) {
            return [
                'url' => asset('storage/' . $photo->image),
                'caption' => $photo->caption,
            ];
        })->values();

        return view('public.post-detail', compact('post', 'related', 'imageFiles', 'pdfFiles', 'lightboxPhotos'));
    }

    public function articleDetail(Article $article)
    {
        abort_unless($article->is_active, 404);

        if ($article->pdf_href && blank($article->content)) {
            return redirect()->away($article->pdf_href);
        }

        return view('public.article-detail', compact('article'));
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
