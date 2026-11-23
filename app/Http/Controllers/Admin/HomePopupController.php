<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class HomePopupController extends Controller
{
    public function index()
    {
        return view('admin.home-popup', ['popup' => Setting::homePopup()]);
    }

    public function update(Request $request)
    {
        $previous = Setting::homePopup()['image'] ?? null;
        $data = $request->validate([
            'image' => [$previous ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'alt' => 'required|string|max:255',
        ]);

        $image = $previous;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('home-popups', 'public');
            if (!$image) {
                throw ValidationException::withMessages(['image' => 'Gambar tidak dapat disimpan.']);
            }
        }

        try {
            Setting::set('home_popup', json_encode(['image' => $image, 'alt' => $data['alt']], JSON_THROW_ON_ERROR));
        } catch (Throwable $exception) {
            if ($image !== $previous) {
                $this->deleteImage($image);
            }
            throw $exception;
        }
        if ($previous !== $image) {
            $this->deleteImage($previous);
        }

        return redirect()->route('admin.home-popup.index')->with('success', 'Popup beranda aktif.');
    }

    public function destroy()
    {
        $previous = Setting::homePopup()['image'] ?? null;
        Setting::where('key', 'home_popup')->delete();
        $this->deleteImage($previous);

        return redirect()->route('admin.home-popup.index')->with('success', 'Popup beranda dinonaktifkan.');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && dirname($path) === 'home-popups') {
            Storage::disk('public')->delete($path);
        }
    }
}
