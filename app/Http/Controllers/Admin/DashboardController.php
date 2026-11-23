<?php

namespace App\Http\Controllers\Admin;

use App\ActivityLog;
use App\Article;
use App\FloatingIcon;
use App\FooterLink;
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
        $activityQuery = ActivityLog::with('user')->latest();

        if ($user && !$user->isAdmin()) {
            $postQuery->ownedBy($user);
            $activityQuery->where('user_id', $user->id);

            if ($user->isAuthorSatker()) {
                $postQuery->berita()->newsScope(Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT);
            }
        }

        $workflowCounts = [
            Post::STATUS_DRAFT => (clone $postQuery)->workflowStatus(Post::STATUS_DRAFT)->count(),
            Post::STATUS_REVIEW => (clone $postQuery)->workflowStatus(Post::STATUS_REVIEW)->count(),
            Post::STATUS_PUBLISHED => (clone $postQuery)->workflowStatus(Post::STATUS_PUBLISHED)->count(),
            Post::STATUS_ARCHIVED => (clone $postQuery)->workflowStatus(Post::STATUS_ARCHIVED)->count(),
        ];

        $data = [
            'canManageMasterData' => $user && $user->isAdmin(),
            'totalPages' => Page::count(),
            'totalPosts' => (clone $postQuery)->count(),
            'totalBerita' => (clone $postQuery)->where('category', 'berita')->count(),
            'totalPengumuman' => $user && $user->isAuthorSatker() ? 0 : (clone $postQuery)->where('category', 'pengumuman')->count(),
            'totalSliders' => Slider::count(),
            'totalUsers' => User::count(),
            'totalFooterLinks' => FooterLink::count(),
            'totalArticles' => Article::count(),
            'totalFloatingIcons' => FloatingIcon::count(),
            'totalAgendas' => \App\Agenda::count(),
            'totalServiceRequests' => $user->isAdmin() ? \App\ServiceRequest::count() : 0,
            'workflowCounts' => $workflowCounts,
            'latestPosts' => (clone $postQuery)->latest()->take(5)->get(),
            'pendingReviewPosts' => (clone $postQuery)->with('user')->workflowStatus(Post::STATUS_REVIEW)->latest()->take(5)->get(),
            'latestServiceRequests' => $user->isAdmin() ? \App\ServiceRequest::latest()->take(5)->get() : collect(),
            'recentActivities' => $activityQuery->take(8)->get(),
        ];

        return view('admin.dashboard', $data);
    }
}
