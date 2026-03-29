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
        $keys = ['site_name', 'site_description', 'address', 'phone', 'email', 'fax'];
        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }
        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
