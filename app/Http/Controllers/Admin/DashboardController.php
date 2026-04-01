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
        $user = auth()->user();
        $postQuery = Post::query();

        if ($user && !$user->isAdmin()) {
            $postQuery->ownedBy($user);

            if ($user->isAuthorSatker()) {
                $postQuery->berita()->newsScope(Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT);
            }
        }

        $data = [
            'totalPages' => Page::count(),
            'totalPosts' => (clone $postQuery)->count(),
            'totalBerita' => (clone $postQuery)->where('category', 'berita')->count(),
            'totalPengumuman' => $user && $user->isAuthorSatker() ? 0 : (clone $postQuery)->where('category', 'pengumuman')->count(),
            'totalSliders' => Slider::count(),
            'totalUsers' => User::count(),
            'latestPosts' => (clone $postQuery)->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', $data);
    }
}
