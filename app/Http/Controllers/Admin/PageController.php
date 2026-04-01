<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $menuGroups = Page::menuGroupOptions();
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
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . trim($request->keyword) . '%');
            })
            ->when($request->filled('menu_group') && array_key_exists($request->menu_group, $menuGroups), function ($query) use ($request) {
                $query->where('menu_group', $request->menu_group);
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

        return view('admin.pages.index', compact('pages', 'menuGroups', 'statusOptions', 'structureOptions'));
    }

    public function create()
    {
        $parents = Page::orderBy('menu_group')->orderBy('title')->get();
        $menuGroups = Page::menuGroupOptions();
        return view('admin.pages.create', compact('parents', 'menuGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'menu_group' => 'nullable|string',
            'parent_id' => 'nullable|exists:pages,id',
        ]);

        Page::create([
            'title' => $request->title,
            'slug' => Page::generateUniqueSlug($request->title),
            'content' => $request->content,
            'menu_group' => $request->menu_group,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil ditambahkan.');
    }

    public function edit(Page $page)
    {
        $parents = Page::where('id', '!=', $page->id)->orderBy('menu_group')->orderBy('title')->get();
        $menuGroups = Page::menuGroupOptions();
        return view('admin.pages.edit', compact('page', 'parents', 'menuGroups'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'menu_group' => 'nullable|string',
            'parent_id' => 'nullable|exists:pages,id',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => Page::generateUniqueSlug($request->title, $page->id),
            'content' => $request->content,
            'menu_group' => $request->menu_group,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil dihapus.');
    }
}
