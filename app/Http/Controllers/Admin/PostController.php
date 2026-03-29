<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Post;
use App\PostPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('user')->latest();
        if ($request->category) {
            $query->where('category', $request->category);
        }
        $posts = $query->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'required|in:berita,pengumuman',
            'image' => 'nullable|image|max:2048',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
            'captions.*' => 'nullable|string|max:255',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'content' => $request->content,
            'excerpt' => Str::limit(strip_tags($request->content), 200),
            'category' => $request->category,
            'is_published' => $request->has('is_published'),
            'user_id' => auth()->id(),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create($data);

        // Handle documentation photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('post-photos', 'public');
                PostPhoto::create([
                    'post_id' => $post->id,
                    'image' => $path,
                    'caption' => $request->captions[$index] ?? null,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post berhasil ditambahkan.');
    }

    public function edit(Post $post)
    {
        $post->load('photos');
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'required|in:berita,pengumuman',
            'image' => 'nullable|image|max:2048',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
            'captions.*' => 'nullable|string|max:255',
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => Str::limit(strip_tags($request->content), 200),
            'category' => $request->category,
            'is_published' => $request->has('is_published'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        // Handle new documentation photos
        if ($request->hasFile('photos')) {
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

        return redirect()->route('admin.posts.index')->with('success', 'Post berhasil diperbarui.');
    }

    public function destroyPhoto($id)
    {
        $photo = PostPhoto::findOrFail($id);
        Storage::disk('public')->delete($photo->image);
        $photo->delete();
        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    }

    public function destroy(Post $post)
    {
        // Only admin can delete posts
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('admin.posts.index')->with('error', 'Anda tidak memiliki akses untuk menghapus post.');
        }

        // Delete all associated photos
        foreach ($post->photos as $photo) {
            Storage::disk('public')->delete($photo->image);
        }
        $post->photos()->delete();
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post berhasil dihapus.');
    }
}
