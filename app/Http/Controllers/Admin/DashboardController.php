<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Page;
use App\Post;
use App\Slider;
use App\User;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalPages' => Page::count(),
            'totalPosts' => Post::count(),
            'totalBerita' => Post::berita()->count(),
            'totalPengumuman' => Post::pengumuman()->count(),
            'totalSliders' => Slider::count(),
            'totalUsers' => User::count(),
            'latestPosts' => Post::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', $data);
    }
}
