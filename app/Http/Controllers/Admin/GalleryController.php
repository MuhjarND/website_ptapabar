<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Gallery;
use App\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $galleries = Gallery::with('galleryCategory')
            ->when($request->gallery_category_id, function ($query) use ($request) {
                $query->where('gallery_category_id', $request->gallery_category_id);
            })
            ->orderBy('order')
            ->paginate(20);
        $galleryCategories = GalleryCategory::active()->orderBy('order')->orderBy('name')->get();

        return view('admin.galleries.index', compact('galleries', 'galleryCategories'));
    }

    public function create()
    {
        $galleryCategories = GalleryCategory::active()->orderBy('order')->orderBy('name')->get();

        return view('admin.galleries.create', compact('galleryCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'gallery_category_id' => 'required|exists:gallery_categories,id',
            'file' => 'required_if:type,image|nullable|image|max:5120',
            'video_url' => 'required_if:type,video|nullable|url|max:500',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only('title', 'type', 'gallery_category_id', 'video_url', 'description', 'order');
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('galleries', 'public');
        }

        Gallery::create($data);
        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery)
    {
        $galleryCategories = GalleryCategory::orderBy('is_active', 'desc')->orderBy('order')->orderBy('name')->get();

        return view('admin.galleries.edit', compact('gallery', 'galleryCategories'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'gallery_category_id' => 'required|exists:gallery_categories,id',
            'file' => 'nullable|image|max:5120',
            'video_url' => 'required_if:type,video|nullable|url|max:500',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only('title', 'type', 'gallery_category_id', 'video_url', 'description', 'order');
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('file')) {
            if ($gallery->file) Storage::disk('public')->delete($gallery->file);
            $data['file'] = $request->file('file')->store('galleries', 'public');
        }

        if ($data['type'] === 'video') {
            if ($gallery->file) {
                Storage::disk('public')->delete($gallery->file);
                $data['file'] = null;
            }
        } elseif ($data['type'] === 'image') {
            $data['video_url'] = null;
        }

        $gallery->update($data);
        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->file) Storage::disk('public')->delete($gallery->file);
        $gallery->delete();
        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil dihapus.');
    }
}
