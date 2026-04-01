<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\IntegrityZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IntegrityZoneController extends Controller
{
    public function index()
    {
        $integrityZones = IntegrityZone::orderBy('order')->orderBy('title')->paginate(20);

        return view('admin.integrity-zones.index', compact('integrityZones'));
    }

    public function create()
    {
        return view('admin.integrity-zones.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateIntegrityZone($request, true);
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;
        $data['image'] = $request->file('image')->store('integrity-zones', 'public');

        IntegrityZone::create($data);

        return redirect()->route('admin.integrity-zones.index')->with('success', 'Eviden zona integritas berhasil ditambahkan.');
    }

    public function edit(IntegrityZone $integrityZone)
    {
        return view('admin.integrity-zones.edit', compact('integrityZone'));
    }

    public function update(Request $request, IntegrityZone $integrityZone)
    {
        $data = $this->validateIntegrityZone($request, false);
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($integrityZone->image) {
                Storage::disk('public')->delete($integrityZone->image);
            }

            $data['image'] = $request->file('image')->store('integrity-zones', 'public');
        }

        $integrityZone->update($data);

        return redirect()->route('admin.integrity-zones.index')->with('success', 'Eviden zona integritas berhasil diperbarui.');
    }

    public function destroy(IntegrityZone $integrityZone)
    {
        if ($integrityZone->image) {
            Storage::disk('public')->delete($integrityZone->image);
        }

        $integrityZone->delete();

        return redirect()->route('admin.integrity-zones.index')->with('success', 'Eviden zona integritas berhasil dihapus.');
    }

    protected function validateIntegrityZone(Request $request, $requireImage)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'image' => ($requireImage ? 'required' : 'nullable') . '|image|max:4096',
            'url' => 'required|url|max:500',
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
