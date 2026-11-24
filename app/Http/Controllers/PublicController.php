<?php

namespace App\Http\Controllers;

use App\Agenda;
use App\Article;
use App\GalleryCategory;
use App\IntegrityZone;
use App\Page;
use App\Post;
use App\PostCategory;
use App\ServiceRequest;
use App\Setting;
use App\SiteApplication;
use App\Slider;
use App\Support\HomeLayout;
use App\SurveyIndex;
use App\TransparencyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Throwable;

class PublicController extends Controller
{
    public function index()
    {
        $homePopup = Setting::homePopup();
        $homeLayout = HomeLayout::saved();
        if ($homeLayout !== null) {
            usort($homeLayout, fn ($a, $b) => [$a['y'], $a['x']] <=> [$b['y'], $b['x']]);
        }
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
        $agendas = Agenda::active()
            ->upcoming()
            ->orderBy('start_at')
            ->take(6)
            ->get();
        $applications = SiteApplication::active()
            ->whereIn('group_type', [
                SiteApplication::GROUP_PTA_INOVASI,
                SiteApplication::GROUP_MAHKAMAH_AGUNG,
            ])
            ->whereRaw('LOWER(title) != ?', ['buku tamu'])
            ->orderByRaw("CASE WHEN group_type = ? THEN 0 WHEN group_type = ? THEN 1 ELSE 2 END", [
                SiteApplication::GROUP_MAHKAMAH_AGUNG,
                SiteApplication::GROUP_PTA_INOVASI,
            ])
            ->orderBy('order')
            ->orderBy('title')
            ->get()
            ->map(function ($application) {
                $application->source_label = $application->group_type === SiteApplication::GROUP_PTA_INOVASI
                    ? 'PTA Papua Barat'
                    : 'MA';

                return $application;
            });

        return view('public.home', compact(
            'homePopup',
            'homeLayout',
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
            'agendas',
            'applications'
        ));
    }

    public function agendas()
    {
        $agendaTypes = Agenda::typeOptions();
        $activeAgendaType = request('agenda_type');
        $agendas = Agenda::active()
            ->agendaType(array_key_exists($activeAgendaType, $agendaTypes) ? $activeAgendaType : null)
            ->orderBy('start_at')
            ->paginate(12);

        return view('public.agendas', [
            'agendas' => $agendas,
            'agendaTypes' => $agendaTypes,
            'activeAgendaType' => array_key_exists($activeAgendaType, $agendaTypes) ? $activeAgendaType : null,
        ]);
    }

    public function surveyReports()
    {
        $surveys = SurveyIndex::where('is_active', true)->orderBy('order')->latest()->get();

        return view('public.survey-reports', compact('surveys'));
    }

    public function page($slug)
    {
        $dedicatedPages = ['jadwal-sidang' => 'court-hearings.index', 'statistik-perkara' => 'case-statistics.index'];
        if (isset($dedicatedPages[$slug])) {
            return redirect()->route($dedicatedPages[$slug], request()->query());
        }

        $page = Page::where('slug', $slug)->first();
        abort_if($page && !$page->is_active, 404);
        $emptyServicePage = ['ecourt' => 'court-ecourt.index', 'direktori-putusan' => 'court-verdicts.index'][$slug] ?? null;
        if ($emptyServicePage && !$page?->hasPublicContent()) {
            return redirect()->route($emptyServicePage);
        }

        $fallback = Page::PUBLIC_FALLBACKS[$slug] ?? null;
        if ($fallback && !$page?->hasPublicContent()) {
            $contact = $fallback === 'contact' ? Setting::getMany([
                'address' => Setting::OFFICE_ADDRESS,
                'phone' => '0811 4088 3744',
                'email' => 'ptapapuabarat@gmail.com',
            ]) : [];

            return view('public.court-information', ['type' => $fallback, 'contact' => $contact]);
        }
        abort_unless($page, 404);
        $breadcrumbs = $page->ancestors();
        $siblings = $page->parent
            ? $page->parent->activeChildren
            : Page::where('menu_group', $page->menu_group)->whereNull('parent_id')->where('is_active', true)->orderBy('order')->get();
        $children = $page->activeChildren;
        $documents = collect();
        if ($page->menu_group === TransparencyDocument::PAGE_GROUP && Schema::hasTable('transparency_documents')) {
            $documents = TransparencyDocument::published()->where('page_id', $page->id)
                ->orderByDesc('year')->latest('published_at')->orderByDesc('id')->get();
        }

        return view('public.page', compact('page', 'breadcrumbs', 'siblings', 'children', 'documents'));
    }

    public function transparencyDocumentDownload($document)
    {
        abort_unless(Schema::hasTable('transparency_documents'), 404);

        return TransparencyDocument::visibleToPublic()->findOrFail($document)->preview();
    }

    public function berita()
    {
        $newsScopes = Post::newsScopeOptions();
        $activeNewsScope = request('news_scope');
        $hasWordPressArchive = Schema::hasColumn('posts', 'wordpress_id')
            && Post::published()->berita()->whereNotNull('wordpress_id')->exists();
        $showWordPressArchive = $hasWordPressArchive && request('sumber') === 'wordpress';
        $posts = Post::published()
            ->berita()
            ->when($showWordPressArchive, function ($query) {
                $query->whereNotNull('wordpress_id');
            }, function ($query) use ($activeNewsScope, $newsScopes) {
                $query->newsScope(array_key_exists($activeNewsScope, $newsScopes) ? $activeNewsScope : null);
            })
            ->latest()
            ->paginate(12);

        return view('public.posts', [
            'posts' => $posts,
            'title' => 'Berita',
            'category' => 'berita',
            'newsScopes' => $newsScopes,
            'activeNewsScope' => !$showWordPressArchive && array_key_exists($activeNewsScope, $newsScopes) ? $activeNewsScope : null,
            'hasWordPressArchive' => $hasWordPressArchive,
            'showWordPressArchive' => $showWordPressArchive,
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

    public function serviceRequestForm()
    {
        return view('public.service-request', [
            'requestTypes' => ServiceRequest::typeOptions(),
        ]);
    }

    public function submitServiceRequest(Request $request)
    {
        $data = $request->validate([
            'request_type' => 'required|in:' . implode(',', array_keys(ServiceRequest::typeOptions())),
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:3000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('private-service-requests', 'local');
            if (!$data['attachment']) {
                throw ValidationException::withMessages(['attachment' => 'Lampiran tidak dapat disimpan.']);
            }
        }

        $data['status'] = ServiceRequest::STATUS_BARU;
        $serviceRequest = ServiceRequest::create($data);

        $mailTarget = Setting::get('email', config('mail.from.address'));

        if ($mailTarget) {
            try {
                Mail::to($mailTarget)->send(new \App\Mail\ServiceRequestSubmitted($serviceRequest));
                $serviceRequest->update([
                    'emailed_to' => $mailTarget,
                    'emailed_at' => now(),
                ]);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return redirect()->route('service-request.form')->with('success', 'Permintaan Anda berhasil dikirim. Tim kami akan menindaklanjuti.');
    }

    public function postDetail($slug)
    {
        $post = Post::with(['photos', 'user'])->published()->where('slug', $slug)->firstOrFail();
        $related = Post::published()->where('category', $post->category)->where('id', '!=', $post->id)->latest()->take(4)->get();
        [$pdfFiles, $imageFiles] = $post->photos->filter(fn ($photo) => $photo->file_url)->partition('is_pdf');
        $pdfFiles = $pdfFiles->values();
        $imageFiles = $imageFiles->values();
        $lightboxPhotos = $imageFiles->map(function ($photo) {
            return [
                'url' => $photo->file_url,
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
