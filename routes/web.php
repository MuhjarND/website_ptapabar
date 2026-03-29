<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes(['register' => false]);

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', 'Admin\DashboardController@index')->name('dashboard');

    // Posts - accessible by admin + author
    Route::resource('posts', 'Admin\PostController')->except(['show']);
    Route::post('ckeditor/upload', 'Admin\CkeditorController@upload')->name('ckeditor.upload');
    Route::delete('posts/photo/{id}', 'Admin\PostController@destroyPhoto')->name('posts.photo.destroy');

    // Admin-only routes
    Route::middleware('admin.only')->group(function () {
        Route::resource('pages', 'Admin\PageController')->except(['show']);
        Route::resource('sliders', 'Admin\SliderController')->except(['show']);
        Route::resource('surveys', 'Admin\SurveyController')->except(['show']);
        Route::resource('quicklinks', 'Admin\QuickLinkController')->except(['show']);
        Route::resource('galleries', 'Admin\GalleryController')->except(['show']);
        Route::get('settings', 'Admin\SettingController@index')->name('settings.index');
        Route::post('settings', 'Admin\SettingController@update')->name('settings.update');
    });
});

// Public Routes
Route::get('/', 'PublicController@index')->name('home');
Route::get('/berita', 'PublicController@berita')->name('berita.index');
Route::get('/pengumuman', 'PublicController@pengumuman')->name('pengumuman.index');
Route::get('/berita/{slug}', 'PublicController@postDetail')->name('post.detail');
Route::get('/pengumuman/{slug}', 'PublicController@postDetail')->name('pengumuman.detail');
Route::get('/halaman/{slug}', 'PublicController@page')->name('page.show');
Route::get('/cari', 'PublicController@search')->name('search');

