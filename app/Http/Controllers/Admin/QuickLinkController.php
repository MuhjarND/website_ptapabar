<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\QuickLink;
use Illuminate\Http\Request;

class QuickLinkController extends Controller
{
    public function index()
    {
        $links = QuickLink::orderBy('order')->get();
        return view('admin.quicklinks.index', compact('links'));
    }

    public function create()
    {
        return view('admin.quicklinks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|image|max:2048',
            'url' => 'nullable|string|max:500',
        ]);

        $data = $request->only(['title', 'description', 'url', 'order']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('quicklinks', 'public');
        }

        QuickLink::create($data);

        return redirect()->route('admin.quicklinks.index')->with('success', 'Link cepat berhasil ditambahkan.');
    }

    public function edit(QuickLink $quicklink)
    {
        return view('admin.quicklinks.edit', compact('quicklink'));
    }

    public function update(Request $request, QuickLink $quicklink)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|image|max:2048',
            'url' => 'nullable|string|max:500',
        ]);

        $data = $request->only(['title', 'description', 'url', 'order']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('quicklinks', 'public');
        }

        $quicklink->update($data);

        return redirect()->route('admin.quicklinks.index')->with('success', 'Link cepat berhasil diperbarui.');
    }

    public function destroy(QuickLink $quicklink)
    {
        $quicklink->delete();
        return redirect()->route('admin.quicklinks.index')->with('success', 'Link cepat berhasil dihapus.');
    }
}
