<?php

namespace App\Http\Controllers\Admin;

use App\FloatingIcon;
use App\Http\Controllers\Controller;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FloatingIconController extends Controller
{
    public function index()
    {
        $floatingIcons = FloatingIcon::orderBy('order')->orderBy('title')->paginate(20);

        return view('admin.floating-icons.index', compact('floatingIcons'));
    }

    public function create()
    {
        $positionOptions = FloatingIcon::positionOptions();

        return view('admin.floating-icons.create', compact('positionOptions'));
    }

    public function store(Request $request)
    {
        $data = $this->validateFloatingIcon($request, true);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active');
        $data['icon'] = $request->file('icon')->store('floating-icons', 'public');

        $floatingIcon = FloatingIcon::create($data);
        ActivityLogger::record('floating-icon.created', $floatingIcon, 'Menambahkan ikon mengambang.', [
            'position' => $floatingIcon->position,
        ]);

        return redirect()->route('admin.floating-icons.index')->with('success', 'Ikon mengambang berhasil ditambahkan.');
    }

    public function edit(FloatingIcon $floatingIcon)
    {
        $positionOptions = FloatingIcon::positionOptions();

        return view('admin.floating-icons.edit', compact('floatingIcon', 'positionOptions'));
    }

    public function update(Request $request, FloatingIcon $floatingIcon)
    {
        $data = $this->validateFloatingIcon($request, false);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('icon')) {
            if ($floatingIcon->icon) {
                Storage::disk('public')->delete($floatingIcon->icon);
            }

            $data['icon'] = $request->file('icon')->store('floating-icons', 'public');
        }

        $floatingIcon->update($data);
        ActivityLogger::record('floating-icon.updated', $floatingIcon, 'Memperbarui ikon mengambang.', [
            'position' => $floatingIcon->position,
        ]);

        return redirect()->route('admin.floating-icons.index')->with('success', 'Ikon mengambang berhasil diperbarui.');
    }

    public function destroy(FloatingIcon $floatingIcon)
    {
        if ($floatingIcon->icon) {
            Storage::disk('public')->delete($floatingIcon->icon);
        }

        ActivityLogger::record('floating-icon.deleted', null, 'Menghapus ikon mengambang.', [
            'title' => $floatingIcon->title,
            'position' => $floatingIcon->position,
        ]);
        $floatingIcon->delete();

        return redirect()->route('admin.floating-icons.index')->with('success', 'Ikon mengambang berhasil dihapus.');
    }

    protected function validateFloatingIcon(Request $request, $requireIcon)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'nullable|string|max:255',
            'icon' => ($requireIcon ? 'required' : 'nullable') . '|image|max:2048',
            'url' => 'required|url|max:500',
            'position' => 'required|in:' . implode(',', array_keys(FloatingIcon::positionOptions())),
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
