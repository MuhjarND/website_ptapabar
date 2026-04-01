<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\PostCategory;
use Illuminate\Http\Request;

class PostCategoryController extends Controller
{
    public function index()
    {
        $categories = PostCategory::withCount('posts')->orderBy('order')->orderBy('name')->paginate(20);

        return view('admin.post-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.post-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $validated['slug'] = PostCategory::generateUniqueSlug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        PostCategory::create($validated);

        return redirect()->route('admin.post-categories.index')->with('success', 'Kategori post berhasil ditambahkan.');
    }

    public function edit(PostCategory $postCategory)
    {
        return view('admin.post-categories.edit', compact('postCategory'));
    }

    public function update(Request $request, PostCategory $postCategory)
    {
        $validated = $this->validateCategory($request);
        $validated['slug'] = PostCategory::generateUniqueSlug($validated['name'], $postCategory->id);
        $validated['is_active'] = $request->has('is_active');

        $postCategory->update($validated);

        return redirect()->route('admin.post-categories.index')->with('success', 'Kategori post berhasil diperbarui.');
    }

    public function destroy(PostCategory $postCategory)
    {
        if ($postCategory->posts()->exists()) {
            return redirect()->route('admin.post-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh post.');
        }

        $postCategory->delete();

        return redirect()->route('admin.post-categories.index')->with('success', 'Kategori post berhasil dihapus.');
    }

    protected function validateCategory(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
