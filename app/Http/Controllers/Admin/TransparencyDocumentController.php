<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Page;
use App\TransparencyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class TransparencyDocumentController extends Controller
{
    public function index(Request $request)
    {
        $pages = $this->pages();
        $pageId = filter_var($request->query('page_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $documents = TransparencyDocument::with('page')
            ->when($pageId, function ($query) use ($pageId) {
                $query->where('page_id', $pageId);
            })
            ->orderByDesc('year')->latest('id')->paginate(20)
            ->appends($request->only('page_id'));

        return view('admin.transparency-documents.index', compact('documents', 'pages', 'pageId'));
    }

    public function types()
    {
        $pages = Page::where('menu_group', TransparencyDocument::PAGE_GROUP)
            ->withCount('transparencyDocuments')->orderBy('title')->paginate(25);

        return view('admin.transparency-documents.types', compact('pages'));
    }

    public function create(Request $request)
    {
        $id = filter_var($request->query('page_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $this->form(new TransparencyDocument(['page_id' => $id ?: null]));
    }

    public function edit(TransparencyDocument $transparencyDocument)
    {
        return $this->form($transparencyDocument);
    }

    public function store(Request $request)
    {
        return $this->save($request, new TransparencyDocument);
    }

    public function update(Request $request, TransparencyDocument $transparencyDocument)
    {
        return $this->save($request, $transparencyDocument);
    }

    public function publish(TransparencyDocument $transparencyDocument)
    {
        $page = $transparencyDocument->page;
        if (!$page || !$page->is_active || $page->menu_group !== TransparencyDocument::PAGE_GROUP) {
            return back()->with('error', 'Pilih halaman Transparansi yang aktif sebelum menerbitkan.');
        }

        if (!$transparencyDocument->external_url &&
            (!$transparencyDocument->pdf_path || !Storage::disk('local')->exists($transparencyDocument->pdf_path))) {
            return back()->with('error', 'Berkas tidak tersedia. Unggah PDF baru atau isi tautan HTTPS.');
        }

        $transparencyDocument->published_at = now();
        $transparencyDocument->save();

        return back()->with('success', 'Dokumen diterbitkan.');
    }

    public function unpublish(TransparencyDocument $transparencyDocument)
    {
        $transparencyDocument->published_at = null;
        $transparencyDocument->save();

        return back()->with('success', 'Dokumen dikembalikan ke draf.');
    }

    public function download(TransparencyDocument $transparencyDocument)
    {
        return $transparencyDocument->download();
    }

    private function pages()
    {
        return Page::where('menu_group', TransparencyDocument::PAGE_GROUP)->orderBy('title')->get();
    }

    private function form(TransparencyDocument $document)
    {
        return view('admin.transparency-documents.form', ['document' => $document, 'pages' => $this->pages()]);
    }

    private function save(Request $request, TransparencyDocument $document)
    {
        $data = $request->validate([
            'page_id' => ['required', 'integer', Rule::exists('pages', 'id')->where('menu_group', TransparencyDocument::PAGE_GROUP)],
            'title' => 'required|string|max:255',
            'year' => 'required|integer|between:1900,2100',
            'period' => 'nullable|string|max:80',
            'version' => 'nullable|string|max:80',
            'budget_code' => 'nullable|string|max:30',
            'source' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'external_url' => ['nullable', 'string', 'max:2048', 'url', 'regex:/\Ahttps:\/\//i'],
        ]);

        $hasUpload = $request->hasFile('pdf_file');
        $url = $data['external_url'] ?? null;
        if (($hasUpload && $url) || (!$hasUpload && !$url && !$document->pdf_path && !$document->external_url)) {
            throw ValidationException::withMessages(['pdf_file' => 'Pilih satu sumber: unggah PDF atau isi tautan HTTPS.']);
        }

        $oldPath = $document->pdf_path;
        $newPath = null;
        unset($data['pdf_file'], $data['external_url']);

        if ($hasUpload) {
            $newPath = $request->file('pdf_file')->store(TransparencyDocument::STORAGE_DIRECTORY, 'local');
            if (!$newPath) {
                throw ValidationException::withMessages(['pdf_file' => 'PDF tidak dapat disimpan. Coba unggah kembali.']);
            }
            $data['pdf_path'] = $newPath;
            $data['external_url'] = null;
        } elseif ($url) {
            $data['pdf_path'] = null;
            $data['external_url'] = $url;
        }

        try {
            $document->fill($data);
            $document->published_at = null;
            $document->save();
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }

        if ($oldPath && $oldPath !== $document->pdf_path) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()->route('admin.transparency-documents.index')->with('success', 'Dokumen disimpan sebagai draf.');
    }
}
