<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $menuGroups = Page::menuGroupOptions();
        $navigation = $request->routeIs('admin.navigation.index');
        $listRoute = $navigation ? 'admin.navigation.index' : 'admin.pages.index';
        $group = $request->query('menu_group');
        $keyword = $request->query('keyword');
        $statusOptions = [
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
        ];
        $structureOptions = [
            'root' => 'Level Atas',
            'child' => 'Subhalaman',
        ];

        $pages = Page::query()
            ->with('parent')
            ->when($navigation, fn ($query) => $query->whereIn('menu_group', array_keys($menuGroups)))
            ->when(is_string($keyword) && trim($keyword) !== '', function ($query) use ($keyword) {
                $query->where('title', 'like', '%' . trim($keyword) . '%');
            })
            ->when(is_string($group) && isset($menuGroups[$group]), function ($query) use ($group) {
                $query->where('menu_group', $group);
            })
            ->when($request->status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($request->status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->when($request->structure === 'root', function ($query) {
                $query->whereNull('parent_id');
            })
            ->when($request->structure === 'child', function ($query) {
                $query->whereNotNull('parent_id');
            })
            ->orderBy('menu_group')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->paginate(25)
            ->appends($request->query());

        return view('admin.pages.index', compact('pages', 'menuGroups', 'statusOptions', 'structureOptions', 'navigation', 'listRoute'));
    }

    public function managed(string $slug)
    {
        $definition = config('admin-content.pages')[$slug] ?? null;
        abort_unless($definition, 404);

        $page = Page::where('slug', $slug)->first();
        return $page ? $this->edit($page) : $this->createForm($slug, $definition[0], $definition[1], $definition[2] ?? null);
    }

    public function create(Request $request)
    {
        $group = $request->query('menu_group');
        return $this->createForm(null, null, is_string($group) && isset(Page::menuGroupOptions()[$group]) ? $group : null);
    }

    private function createForm(?string $managedSlug = null, ?string $defaultTitle = null, ?string $defaultGroup = null, ?string $guidance = null)
    {
        $parents = Page::orderBy('menu_group')->orderBy('title')->get();
        $menuGroups = Page::menuGroupOptions();
        return view('admin.pages.create', compact('parents', 'menuGroups', 'managedSlug', 'defaultTitle', 'defaultGroup', 'guidance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'menu_group' => 'nullable|string',
            'parent_id' => 'nullable|exists:pages,id',
            'managed_slug' => ['nullable', 'string', Rule::in(array_keys(config('admin-content.pages'))), 'unique:pages,slug'],
        ]);

        $managedSlug = $request->input('managed_slug');

        Page::create([
            'title' => $request->title,
            'slug' => $managedSlug ?: Page::generateUniqueSlug($request->title),
            'content' => $request->content,
            'menu_group' => $request->menu_group,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return ($managedSlug ? redirect()->route('admin.pages.managed', $managedSlug) : redirect()->route('admin.pages.index'))
            ->with('success', 'Halaman berhasil ditambahkan.');
    }

    public function edit(Page $page)
    {
        $parents = Page::where('id', '!=', $page->id)->orderBy('menu_group')->orderBy('title')->get();
        $menuGroups = Page::menuGroupOptions();
        $guidance = config('admin-content.pages')[$page->slug][2] ?? null;
        return view('admin.pages.edit', compact('page', 'parents', 'menuGroups', 'guidance'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'menu_group' => 'nullable|string',
            'parent_id' => 'nullable|exists:pages,id',
        ]);

        $managed = isset(config('admin-content.pages')[$page->slug]);
        $page->update([
            'title' => $request->title,
            'slug' => $managed ? $page->slug : Page::generateUniqueSlug($request->title, $page->id),
            'content' => $request->content,
            'menu_group' => $request->menu_group,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return ($managed ? redirect()->route('admin.pages.managed', $page->slug) : redirect()->route('admin.pages.index'))
            ->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil dihapus.');
    }
}
