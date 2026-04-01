<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\SiteApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SiteApplicationController extends Controller
{
    public function index()
    {
        $applications = SiteApplication::orderBy('group_type')->orderBy('order')->orderBy('title')->paginate(20);
        $groupOptions = SiteApplication::groupOptions();

        return view('admin.site-applications.index', compact('applications', 'groupOptions'));
    }

    public function create()
    {
        $groupOptions = SiteApplication::groupOptions();

        return view('admin.site-applications.create', compact('groupOptions'));
    }

    public function store(Request $request)
    {
        $data = $this->validateApplication($request);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('site-applications', 'public');
        }

        SiteApplication::create($data);

        return redirect()->route('admin.site-applications.index')->with('success', 'Aplikasi berhasil ditambahkan.');
    }

    public function edit(SiteApplication $siteApplication)
    {
        $groupOptions = SiteApplication::groupOptions();

        return view('admin.site-applications.edit', compact('siteApplication', 'groupOptions'));
    }

    public function update(Request $request, SiteApplication $siteApplication)
    {
        $data = $this->validateApplication($request);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('icon')) {
            if ($siteApplication->icon) {
                Storage::disk('public')->delete($siteApplication->icon);
            }
            $data['icon'] = $request->file('icon')->store('site-applications', 'public');
        }

        $siteApplication->update($data);

        return redirect()->route('admin.site-applications.index')->with('success', 'Aplikasi berhasil diperbarui.');
    }

    public function destroy(SiteApplication $siteApplication)
    {
        if ($siteApplication->icon) {
            Storage::disk('public')->delete($siteApplication->icon);
        }

        $siteApplication->delete();

        return redirect()->route('admin.site-applications.index')->with('success', 'Aplikasi berhasil dihapus.');
    }

    protected function validateApplication(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|image|max:2048',
            'url' => 'nullable|string|max:500',
            'group_type' => ['required', Rule::in(array_keys(SiteApplication::groupOptions()))],
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
