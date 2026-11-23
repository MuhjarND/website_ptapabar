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
    Route::prefix('berita')->name('berita.')->group(function () {
        Route::get('/', 'Admin\PostController@newsIndex')->name('index');
        Route::get('/create', 'Admin\PostController@createNews')->name('create');
        Route::post('/', 'Admin\PostController@storeNews')->name('store');
        Route::get('/{post}/edit', 'Admin\PostController@editNews')->name('edit');
        Route::put('/{post}', 'Admin\PostController@updateNews')->name('update');
        Route::delete('/{post}', 'Admin\PostController@destroyNews')->name('destroy');
    });
    Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
        Route::get('/', 'Admin\PostController@announcementIndex')->name('index');
        Route::get('/create', 'Admin\PostController@createAnnouncement')->name('create');
        Route::post('/', 'Admin\PostController@storeAnnouncement')->name('store');
        Route::get('/{post}/edit', 'Admin\PostController@editAnnouncement')->name('edit');
        Route::put('/{post}', 'Admin\PostController@updateAnnouncement')->name('update');
        Route::delete('/{post}', 'Admin\PostController@destroyAnnouncement')->name('destroy');
    });
    Route::resource('posts', 'Admin\PostController')->except(['show']);
    Route::post('ckeditor/upload', 'Admin\CkeditorController@upload')->name('ckeditor.upload');
    Route::delete('posts/photo/{id}', 'Admin\PostController@destroyPhoto')->name('posts.photo.destroy');

    // Admin-only routes
    Route::middleware('admin.only')->group(function () {
        Route::get('editor-beranda', 'Admin\HomeLayoutController@index')->name('home-layout.index');
        Route::put('editor-beranda', 'Admin\HomeLayoutController@update')->name('home-layout.update');
        Route::delete('editor-beranda', 'Admin\HomeLayoutController@destroy')->name('home-layout.destroy');
        Route::get('popup-beranda', 'Admin\HomePopupController@index')->name('home-popup.index');
        Route::put('popup-beranda', 'Admin\HomePopupController@update')->name('home-popup.update');
        Route::delete('popup-beranda', 'Admin\HomePopupController@destroy')->name('home-popup.destroy');
        Route::get('sinova', 'Admin\SinovaController@index')->name('sinova.index');
        Route::get('menu-navigasi', 'Admin\PageController@index')->name('navigation.index');
        Route::get('halaman-khusus/{slug}', 'Admin\PageController@managed')->name('pages.managed');
        Route::resource('pages', 'Admin\PageController')->except(['show']);
        Route::prefix('informasi-perkara/{type}')->where(['type' => 'hearings|statistics'])->name('court-information.')->group(function () {
            Route::get('/', 'Admin\CourtInformationController@index')->name('index');
            Route::get('/create', 'Admin\CourtInformationController@form')->name('create');
            Route::post('/', 'Admin\CourtInformationController@save')->name('store');
            Route::get('/{entry}/edit', 'Admin\CourtInformationController@form')->where('entry', '[0-9]+')->name('edit');
            Route::put('/{entry}', 'Admin\CourtInformationController@save')->where('entry', '[0-9]+')->name('update');
        });
        Route::resource('transparency-documents', 'Admin\TransparencyDocumentController')
            ->parameters(['transparency-documents' => 'transparencyDocument'])
            ->except(['show', 'destroy']);
        Route::get('jenis-dokumen', 'Admin\TransparencyDocumentController@types')->name('document-types.index');
        Route::post('transparency-documents/{transparencyDocument}/publish', 'Admin\TransparencyDocumentController@publish')
            ->name('transparency-documents.publish');
        Route::post('transparency-documents/{transparencyDocument}/unpublish', 'Admin\TransparencyDocumentController@unpublish')
            ->name('transparency-documents.unpublish');
        Route::get('transparency-documents/{transparencyDocument}/file', 'Admin\TransparencyDocumentController@download')
            ->name('transparency-documents.download');
        Route::resource('post-categories', 'Admin\PostCategoryController')->except(['show']);
        Route::resource('gallery-categories', 'Admin\GalleryCategoryController')->except(['show']);
        Route::resource('site-applications', 'Admin\SiteApplicationController')->except(['show']);
        Route::resource('floating-icons', 'Admin\FloatingIconController')->except(['show']);
        Route::resource('footer-links', 'Admin\FooterLinkController')->except(['show']);
        Route::resource('agendas', 'Admin\AgendaController')->except(['show']);
        Route::resource('articles', 'Admin\ArticleController')->except(['show']);
        Route::resource('integrity-zones', 'Admin\IntegrityZoneController')->except(['show']);
        Route::resource('service-requests', 'Admin\ServiceRequestController')->only(['index', 'show', 'update']);
        Route::get('service-requests/{service_request}/attachment', 'Admin\ServiceRequestController@download')->name('service-requests.download');
        Route::resource('users', 'Admin\UserController')->except(['show']);
        Route::resource('sliders', 'Admin\SliderController')->except(['show']);
        Route::resource('surveys', 'Admin\SurveyController')->except(['show']);
        Route::resource('quicklinks', 'Admin\QuickLinkController')->except(['show']);
        Route::resource('galleries', 'Admin\GalleryController')->except(['show']);
        Route::get('settings', 'Admin\SettingController@index')->name('settings.index');
        Route::post('settings', 'Admin\SettingController@update')->name('settings.update');
    });
});

// Public Routes
Route::get('/storage/{path}', 'PublicMediaController')
    ->where('path', '(?:articles/pdfs|floating-icons|galleries|home-popups|integrity-zones|post-photos|posts|quicklinks|site-applications|sliders|uploads)/[A-Za-z0-9][A-Za-z0-9._-]*')
    ->withoutMiddleware('web')->name('public-storage.file');
Route::get('/', 'PublicController@index')->name('home');
Route::get('/agenda', 'PublicController@agendas')->name('agenda.index');
Route::get('/jadwal-sidang-banding', 'CourtInformationController@hearings')->name('court-hearings.index');
Route::get('/statistik-perkara', 'CourtInformationController@statistics')->name('case-statistics.index');
Route::get('/direktori-putusan', 'CourtInformationController@verdicts')->name('court-verdicts.index');
Route::get('/berita', 'PublicController@berita')->name('berita.index');
Route::get('/pengumuman', 'PublicController@pengumuman')->name('pengumuman.index');
Route::get('/tinta-peradilan', 'PublicController@articles')->name('article.index');
Route::get('/layanan-publik', 'PublicController@serviceRequestForm')->name('service-request.form');
Route::post('/layanan-publik', 'PublicController@submitServiceRequest')->middleware('throttle:10,1')->name('service-request.submit');
Route::get('/foto-video', 'PublicController@galleries')->name('gallery.index');
Route::get('/artikel/{article}', 'PublicController@articleDetail')->name('article.detail');
Route::get('/berita/{slug}', 'PublicController@postDetail')->name('post.detail');
Route::get('/pengumuman/{slug}', 'PublicController@postDetail')->name('pengumuman.detail');
Route::get('/halaman/{slug}', 'PublicController@page')->name('page.show');
Route::get('/dokumen-transparansi/{document}/file', 'PublicController@transparencyDocumentDownload')
    ->where('document', '[0-9]+')->name('transparency-documents.download');
Route::get('/cari', 'PublicController@search')->name('search');
