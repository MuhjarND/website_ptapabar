<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('order')->latest()->paginate(20);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categoryOptions = Article::categoryOptions();

        return view('admin.articles.create', compact('categoryOptions'));
    }

    public function store(Request $request)
    {
        $data = $this->validateArticle($request);
        $data['content'] = $data['content'] ?? '';
        $data['slug'] = Article::generateUniqueSlug($data['title']);
        $data['excerpt'] = Article::sanitizeExcerpt($data['excerpt'] ?: ($data['content'] ?: 'Dokumen artikel tersedia dalam format PDF.'), 220);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('pdf_file')) {
            $data['pdf_file'] = $request->file('pdf_file')->store('articles/pdfs', 'public');
        }

        $data['image'] = '';

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Article $article)
    {
        $categoryOptions = Article::categoryOptions();

        return view('admin.articles.edit', compact('article', 'categoryOptions'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validateArticle($request);
        $data['content'] = $data['content'] ?? '';
        $data['slug'] = Article::generateUniqueSlug($data['title'], $article->id);
        $data['excerpt'] = Article::sanitizeExcerpt($data['excerpt'] ?: ($data['content'] ?: 'Dokumen artikel tersedia dalam format PDF.'), 220);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('pdf_file')) {
            if ($article->pdf_file) {
                Storage::disk('public')->delete($article->pdf_file);
            }

            $data['pdf_file'] = $request->file('pdf_file')->store('articles/pdfs', 'public');
        } elseif (!empty($data['pdf_url']) && $article->pdf_file) {
            Storage::disk('public')->delete($article->pdf_file);
            $data['pdf_file'] = null;
        }

        $data['image'] = $article->image ?: '';
        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        if ($article->pdf_file) {
            Storage::disk('public')->delete($article->pdf_file);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }

    protected function validateArticle(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_url' => 'nullable|url|max:500',
            'article_category' => ['required', Rule::in(array_keys(Article::categoryOptions()))],
            'order' => 'nullable|integer|min:0',
        ]);

        if (empty($data['content']) && empty($data['pdf_url']) && !$request->hasFile('pdf_file')) {
            throw ValidationException::withMessages([
                'content' => 'Isi artikel atau file/link PDF wajib diisi.',
            ]);
        }

        if ($request->hasFile('pdf_file')) {
            $data['pdf_url'] = null;
        }

        return $data;
    }
}
