<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Setting;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'Pengadilan Tinggi Agama Papua Barat'),
            'site_description' => Setting::get('site_description', ''),
            'address' => Setting::get('address', ''),
            'phone' => Setting::get('phone', ''),
            'email' => Setting::get('email', ''),
            'fax' => Setting::get('fax', ''),
            'instagram_url' => Setting::get('instagram_url', ''),
            'facebook_url' => Setting::get('facebook_url', ''),
            'youtube_url' => Setting::get('youtube_url', ''),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'fax' => 'nullable|string|max:50',
            'instagram_url' => 'nullable|url|max:500',
            'facebook_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
        ]);

        $keys = ['site_name', 'site_description', 'address', 'phone', 'email', 'fax', 'instagram_url', 'facebook_url', 'youtube_url'];
        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        ActivityLogger::record('setting.updated', null, 'Memperbarui pengaturan situs.', [
            'site_name' => $request->input('site_name'),
            'email' => $request->input('email'),
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
