<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Post;
use App\PostCategory;
use App\PostPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        return $this->renderIndex($request);
    }

    public function newsIndex(Request $request)
    {
        return $this->renderIndex($request, 'berita');
    }

    public function announcementIndex(Request $request)
    {
        return $this->renderIndex($request, 'pengumuman');
    }

    public function create()
    {
        return $this->renderCreate();
    }

    public function createNews()
    {
        return $this->renderCreate('berita');
    }

    public function createAnnouncement()
    {
        return $this->renderCreate('pengumuman');
    }

    public function store(Request $request)
    {
        return $this->persistStore($request);
    }

    public function storeNews(Request $request)
    {
        return $this->persistStore($request, 'berita');
    }

    public function storeAnnouncement(Request $request)
    {
        return $this->persistStore($request, 'pengumuman');
    }

    public function edit(Post $post)
    {
        return $this->renderEdit($post);
    }

    public function editNews(Post $post)
    {
        return $this->renderEdit($post, 'berita');
    }

    public function editAnnouncement(Post $post)
    {
        return $this->renderEdit($post, 'pengumuman');
    }

    public function update(Request $request, Post $post)
    {
        return $this->persistUpdate($request, $post);
    }

    public function updateNews(Request $request, Post $post)
    {
        return $this->persistUpdate($request, $post, 'berita');
    }

    public function updateAnnouncement(Request $request, Post $post)
    {
        return $this->persistUpdate($request, $post, 'pengumuman');
    }

    public function destroy(Post $post)
    {
        return $this->performDestroy($post);
    }

    public function destroyNews(Post $post)
    {
        return $this->performDestroy($post, 'berita');
    }

    public function destroyAnnouncement(Post $post)
    {
        return $this->performDestroy($post, 'pengumuman');
    }

    public function destroyPhoto($id)
    {
        $photo = PostPhoto::with('post')->findOrFail($id);
        $this->authorizePostAccess($photo->post);

        Storage::disk('public')->delete($photo->image);
        $photo->delete();

        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    }

    protected function renderIndex(Request $request, $fixedCategory = null)
    {
        $fixedCategory = $this->resolveContextCategory($fixedCategory);
        $query = Post::with(['user', 'postCategory'])->latest();

        if (!auth()->user()->isAdmin()) {
            $query->ownedBy(auth()->user());
        }

        if ($fixedCategory) {
            $query->where('category', $fixedCategory);
        } elseif ($request->category) {
            $query->where('category', $request->category);
        }

        if ($fixedCategory !== 'pengumuman' && $request->post_category_id) {
            $query->where('post_category_id', $request->post_category_id);
        }

        if ($fixedCategory !== 'pengumuman' && $request->news_scope) {
            $query->newsScope($request->news_scope);
        }

        if ($fixedCategory !== 'berita' && $request->announcement_category) {
            $query->announcementCategory($request->announcement_category);
        }

        if (auth()->user()->isAuthorSatker()) {
            $query->berita()->newsScope(Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT);
        }

        $posts = $query->paginate(15);
        $postCategories = PostCategory::active()->orderBy('order')->orderBy('name')->get();
        $viewData = $this->buildViewData($fixedCategory);
        $viewData['pageTitle'] = $viewData['pageHeading'];

        return view('admin.posts.index', array_merge($viewData, compact('posts', 'postCategories')));
    }

    protected function renderCreate($fixedCategory = null)
    {
        $fixedCategory = $this->resolveContextCategory($fixedCategory);
        $postCategories = PostCategory::active()->orderBy('order')->orderBy('name')->get();
        $viewData = $this->buildViewData($fixedCategory, null);

        return view('admin.posts.create', array_merge($viewData, compact('postCategories')));
    }

    protected function renderEdit(Post $post, $fixedCategory = null)
    {
        $fixedCategory = $this->resolveContextCategory($fixedCategory);
        $this->authorizePostAccess($post);
        $this->ensureCategoryMatchesContext($post, $fixedCategory);

        $post->load('photos');
        $postCategories = PostCategory::orderBy('is_active', 'desc')->orderBy('order')->orderBy('name')->get();
        $viewData = $this->buildViewData($fixedCategory ?: $post->category, $post);

        return view('admin.posts.edit', array_merge($viewData, compact('post', 'postCategories')));
    }

    protected function persistStore(Request $request, $fixedCategory = null)
    {
        $fixedCategory = $this->resolveContextCategory($fixedCategory);
        $category = $this->resolveIncomingCategory($request, $fixedCategory);
        $validated = $this->validatePostRequest($request, $category);

        $data = [
            'title' => $validated['title'],
            'slug' => Post::generateUniqueSlug($validated['title']),
            'content' => $validated['content'] ?? null,
            'excerpt' => Post::sanitizeExcerpt($validated['content'] ?? '', 200),
            'category' => $category,
            'post_category_id' => $category === 'berita' ? ($validated['post_category_id'] ?? null) : null,
            'news_scope' => $category === 'berita' ? $this->resolveNewsScope($validated) : null,
            'announcement_category' => $category === 'pengumuman' ? ($validated['announcement_category'] ?? null) : null,
            'is_published' => $request->has('is_published'),
            'user_id' => auth()->id(),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create($data);
        $this->storeDocumentationFiles($request, $post);

        return redirect()->route($this->successRedirectRouteName($fixedCategory, $category))->with('success', ucfirst($category) . ' berhasil ditambahkan.');
    }

    protected function persistUpdate(Request $request, Post $post, $fixedCategory = null)
    {
        $fixedCategory = $this->resolveContextCategory($fixedCategory);
        $this->authorizePostAccess($post);
        $this->ensureCategoryMatchesContext($post, $fixedCategory);

        $category = $this->resolveIncomingCategory($request, $fixedCategory ?: $post->category);
        $validated = $this->validatePostRequest($request, $category);

        $data = [
            'title' => $validated['title'],
            'slug' => Post::generateUniqueSlug($validated['title'], $post->id),
            'content' => $validated['content'] ?? null,
            'excerpt' => Post::sanitizeExcerpt($validated['content'] ?? '', 200),
            'category' => $category,
            'post_category_id' => $category === 'berita' ? ($validated['post_category_id'] ?? null) : null,
            'news_scope' => $category === 'berita' ? $this->resolveNewsScope($validated, $post) : null,
            'announcement_category' => $category === 'pengumuman' ? ($validated['announcement_category'] ?? null) : null,
            'is_published' => $request->has('is_published'),
        ];

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);
        $this->storeDocumentationFiles($request, $post);

        return redirect()->route($this->successRedirectRouteName($fixedCategory, $category))->with('success', ucfirst($category) . ' berhasil diperbarui.');
    }

    protected function performDestroy(Post $post, $fixedCategory = null)
    {
        $fixedCategory = $this->resolveContextCategory($fixedCategory);
        $this->authorizePostAccess($post);
        $this->ensureCategoryMatchesContext($post, $fixedCategory);

        foreach ($post->photos as $photo) {
            Storage::disk('public')->delete($photo->image);
        }

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $category = $post->category;

        $post->photos()->delete();
        $post->delete();

        return redirect()->route($this->successRedirectRouteName($fixedCategory, $category))->with('success', ucfirst($category) . ' berhasil dihapus.');
    }

    protected function validatePostRequest(Request $request, $category)
    {
        $categoryRule = 'required|in:' . ($category ?: 'berita,pengumuman');

        return $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => $categoryRule,
            'post_category_id' => 'nullable|exists:post_categories,id',
            'news_scope' => 'nullable|in:' . implode(',', array_keys(Post::newsScopeOptions())),
            'announcement_category' => 'nullable|required_if:category,pengumuman|in:' . implode(',', array_keys(Post::announcementCategoryOptions())),
            'image' => 'nullable|image|max:2048',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
            'captions.*' => 'nullable|string|max:255',
        ]);
    }

    protected function storeDocumentationFiles(Request $request, Post $post)
    {
        if (!$request->hasFile('photos')) {
            return;
        }

        $existingCount = $post->photos()->count();

        foreach ($request->file('photos') as $index => $photo) {
            $path = $photo->store('post-photos', 'public');

            PostPhoto::create([
                'post_id' => $post->id,
                'image' => $path,
                'caption' => $request->captions[$index] ?? null,
                'order' => $existingCount + $index,
            ]);
        }
    }

    protected function resolveIncomingCategory(Request $request, $fixedCategory = null)
    {
        $category = $fixedCategory ?: $request->input('category');
        $request->merge(['category' => $category]);

        return $category;
    }

    protected function resolveContextCategory($fixedCategory = null)
    {
        if (auth()->user()->isAuthorSatker()) {
            if ($fixedCategory === 'pengumuman') {
                abort(403);
            }

            return 'berita';
        }

        return $fixedCategory;
    }

    protected function resolveNewsScope(array $validated, Post $post = null)
    {
        if (auth()->user()->isAuthorSatker()) {
            return Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT;
        }

        return $validated['news_scope'] ?? optional($post)->news_scope ?? Post::NEWS_SCOPE_TERKINI;
    }

    protected function ensureCategoryMatchesContext(Post $post, $fixedCategory = null)
    {
        if ($fixedCategory && $post->category !== $fixedCategory) {
            abort(404);
        }

        if (auth()->user()->isAuthorSatker() && $post->news_scope !== Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT) {
            abort(403);
        }
    }

    protected function buildViewData($fixedCategory = null, Post $post = null)
    {
        $category = $fixedCategory ?: optional($post)->category;

        if ($category === 'berita') {
            return [
                'pageTitle' => $post ? 'Edit Berita' : 'Tambah Berita',
                'pageHeading' => $post ? 'Edit Berita' : 'Daftar Berita',
                'formHeading' => $post ? 'Edit: ' . Str::limit($post->title, 50) : (auth()->user()->isAuthorSatker() ? 'Tambah Berita Peradilan Agama Papua Barat' : 'Tambah Berita Baru'),
                'createLabel' => 'Tambah Berita',
                'itemLabel' => 'berita',
                'fixedCategory' => 'berita',
                'lockedNewsScope' => auth()->user()->isAuthorSatker() ? Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT : null,
                'indexRoute' => route('admin.berita.index'),
                'createRoute' => route('admin.berita.create'),
                'storeRoute' => route('admin.berita.store'),
                'backRoute' => route('admin.berita.index'),
                'showCategoryFilter' => false,
                'showNewsCategoryFilter' => true,
                'showNewsScopeFilter' => !auth()->user()->isAuthorSatker(),
                'showAnnouncementCategoryFilter' => false,
            ];
        }

        if ($category === 'pengumuman') {
            return [
                'pageTitle' => $post ? 'Edit Pengumuman' : 'Tambah Pengumuman',
                'pageHeading' => $post ? 'Edit Pengumuman' : 'Daftar Pengumuman',
                'formHeading' => $post ? 'Edit: ' . Str::limit($post->title, 50) : 'Tambah Pengumuman Baru',
                'createLabel' => 'Tambah Pengumuman',
                'itemLabel' => 'pengumuman',
                'fixedCategory' => 'pengumuman',
                'lockedNewsScope' => null,
                'indexRoute' => route('admin.pengumuman.index'),
                'createRoute' => route('admin.pengumuman.create'),
                'storeRoute' => route('admin.pengumuman.store'),
                'backRoute' => route('admin.pengumuman.index'),
                'showCategoryFilter' => false,
                'showNewsCategoryFilter' => false,
                'showNewsScopeFilter' => false,
                'showAnnouncementCategoryFilter' => true,
            ];
        }

        return [
            'pageTitle' => $post ? 'Edit Post' : 'Tambah Post',
            'pageHeading' => $post ? 'Edit: ' . Str::limit($post->title, 50) : 'Daftar Post',
            'formHeading' => $post ? 'Edit: ' . Str::limit($post->title, 50) : 'Tambah Post Baru',
            'createLabel' => 'Tambah Post',
            'itemLabel' => 'post',
            'fixedCategory' => null,
            'lockedNewsScope' => auth()->user()->isAuthorSatker() ? Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT : null,
            'indexRoute' => route('admin.posts.index'),
            'createRoute' => route('admin.posts.create'),
            'storeRoute' => route('admin.posts.store'),
            'backRoute' => route('admin.posts.index'),
            'showCategoryFilter' => true,
            'showNewsCategoryFilter' => true,
            'showNewsScopeFilter' => !auth()->user()->isAuthorSatker(),
            'showAnnouncementCategoryFilter' => true,
        ];
    }

    protected function indexRouteName($category)
    {
        if ($category === 'berita') {
            return 'admin.berita.index';
        }

        if ($category === 'pengumuman') {
            return 'admin.pengumuman.index';
        }

        return 'admin.posts.index';
    }

    protected function successRedirectRouteName($fixedCategory, $category)
    {
        if ($fixedCategory) {
            return $this->indexRouteName($fixedCategory);
        }

        return 'admin.posts.index';
    }

    protected function authorizePostAccess(Post $post)
    {
        abort_unless($post->canBeManagedBy(auth()->user()), 403);
    }
}
