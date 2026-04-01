<?php

namespace App\Http\Controllers\Admin;

use App\GalleryCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryCategoryController extends Controller
{
    public function index()
    {
        $categories = GalleryCategory::withCount('galleries')->orderBy('order')->orderBy('name')->paginate(20);

        return view('admin.gallery-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.gallery-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $validated['slug'] = GalleryCategory::generateUniqueSlug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        GalleryCategory::create($validated);

        return redirect()->route('admin.gallery-categories.index')->with('success', 'Kategori galeri berhasil ditambahkan.');
    }

    public function edit(GalleryCategory $galleryCategory)
    {
        return view('admin.gallery-categories.edit', compact('galleryCategory'));
    }

    public function update(Request $request, GalleryCategory $galleryCategory)
    {
        $validated = $this->validateCategory($request);
        $validated['slug'] = GalleryCategory::generateUniqueSlug($validated['name'], $galleryCategory->id);
        $validated['is_active'] = $request->has('is_active');

        $galleryCategory->update($validated);

        return redirect()->route('admin.gallery-categories.index')->with('success', 'Kategori galeri berhasil diperbarui.');
    }

    public function destroy(GalleryCategory $galleryCategory)
    {
        if ($galleryCategory->galleries()->exists()) {
            return redirect()->route('admin.gallery-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh item galeri.');
        }

        $galleryCategory->delete();

        return redirect()->route('admin.gallery-categories.index')->with('success', 'Kategori galeri berhasil dihapus.');
    }

    protected function validateCategory(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
