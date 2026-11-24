<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Gallery;
use App\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        $this->saveItem($request, new Gallery());
        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery)
    {
        $galleryCategories = GalleryCategory::orderBy('is_active', 'desc')->orderBy('order')->orderBy('name')->get();

        return view('admin.galleries.edit', compact('gallery', 'galleryCategories'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $this->saveItem($request, $gallery);
        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil diperbarui.');
    }

    private function saveItem(Request $request, Gallery $gallery): void
    {
        $type = $request->input('type');
        $video = $type === 'video';
        $previousFile = $gallery->file;
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(array_keys(Gallery::typeOptions()))],
            'gallery_category_id' => in_array($type, ['image', 'video'], true) ? 'required|exists:gallery_categories,id' : 'exclude',
            'file' => $video ? 'exclude' : [$previousFile ? 'nullable' : 'required', 'image', 'max:5120'],
            'video_url' => $video ? ['bail', 'required', 'url:http,https', 'max:255', function ($attribute, $value, $fail) {
                if (!Gallery::youtubeId($value)) {
                    $fail('Masukkan tautan video YouTube yang valid.');
                }
            }] : 'exclude',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
        ]);

        $upload = $data['file'] ?? null;
        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $data['order'] ?? 0;
        $data['video_url'] = $data['video_url'] ?? null;
        $data['gallery_category_id'] = $data['gallery_category_id'] ?? null;
        $data['file'] = $video ? null : $previousFile;
        if ($upload) {
            $data['file'] = $upload->store('galleries', 'public');
            if (!$data['file']) {
                throw ValidationException::withMessages(['file' => 'Gambar tidak dapat disimpan.']);
            }
        }

        $gallery->fill($data)->save();
        if ($previousFile && $previousFile !== $gallery->file) {
            Storage::disk('public')->delete($previousFile);
        }
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->file) Storage::disk('public')->delete($gallery->file);
        $gallery->delete();
        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil dihapus.');
    }
}
