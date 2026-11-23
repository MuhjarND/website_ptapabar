<?php

namespace App\Http\Controllers\Admin;

use App\FooterLink;
use App\Http\Controllers\Controller;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FooterLinkController extends Controller
{
    public function index()
    {
        $links = FooterLink::orderBy('section')->orderBy('order')->get();

        return view('admin.footer-links.index', compact('links'));
    }

    public function create()
    {
        $sectionOptions = FooterLink::sectionOptions();

        return view('admin.footer-links.create', compact('sectionOptions'));
    }

    public function store(Request $request)
    {
        $data = $this->validateLink($request);
        $data['is_active'] = $request->has('is_active');

        $link = FooterLink::create($data);
        ActivityLogger::record('footer-link.created', $link, 'Menambahkan tautan footer.', ['section' => $link->section]);

        return redirect()->route('admin.footer-links.index')->with('success', 'Tautan footer berhasil ditambahkan.');
    }

    public function edit(FooterLink $footerLink)
    {
        $sectionOptions = FooterLink::sectionOptions();

        return view('admin.footer-links.edit', compact('footerLink', 'sectionOptions'));
    }

    public function update(Request $request, FooterLink $footerLink)
    {
        $data = $this->validateLink($request);
        $data['is_active'] = $request->has('is_active');

        $footerLink->update($data);
        ActivityLogger::record('footer-link.updated', $footerLink, 'Memperbarui tautan footer.', ['section' => $footerLink->section]);

        return redirect()->route('admin.footer-links.index')->with('success', 'Tautan footer berhasil diperbarui.');
    }

    public function destroy(FooterLink $footerLink)
    {
        ActivityLogger::record('footer-link.deleted', null, 'Menghapus tautan footer.', ['title' => $footerLink->title, 'section' => $footerLink->section]);
        $footerLink->delete();

        return redirect()->route('admin.footer-links.index')->with('success', 'Tautan footer berhasil dihapus.');
    }

    protected function validateLink(Request $request)
    {
        return $request->validate([
            'section' => ['required', Rule::in(array_keys(FooterLink::sectionOptions()))],
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
