<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('parent')->orderBy('menu_group')->orderBy('order')->paginate(25);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $parents = Page::orderBy('menu_group')->orderBy('title')->get();
        $menuGroups = [
            'tentang-pengadilan' => 'Tentang Pengadilan',
            'informasi-umum' => 'Informasi Umum',
            'informasi-hukum' => 'Informasi Hukum',
            'transparansi' => 'Transparansi',
            'peraturan-kebijakan' => 'Peraturan dan Kebijakan',
            'informasi' => 'Informasi',
        ];
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
            'slug' => Str::slug($request->title),
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
        $menuGroups = [
            'tentang-pengadilan' => 'Tentang Pengadilan',
            'informasi-umum' => 'Informasi Umum',
            'informasi-hukum' => 'Informasi Hukum',
            'transparansi' => 'Transparansi',
            'peraturan-kebijakan' => 'Peraturan dan Kebijakan',
            'informasi' => 'Informasi',
        ];
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
            'slug' => Str::slug($request->title),
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
