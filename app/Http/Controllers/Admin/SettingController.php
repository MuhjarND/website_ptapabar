<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Setting;
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
        ]);

        $keys = ['site_name', 'site_description', 'address', 'phone', 'email', 'fax'];
        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }
        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
