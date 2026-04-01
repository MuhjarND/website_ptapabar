<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'required|file|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $path = $file->store('uploads', 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'uploaded' => 1,
                'fileName' => basename($path),
                'url' => $url,
            ]);
        }

        return response()->json(['uploaded' => 0, 'error' => ['message' => 'File tidak ditemukan.']]);
    }
}
