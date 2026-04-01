<?php

namespace App\Providers;

use App\Page;
use App\Setting;
use App\Support\VisitorAnalytics;
use App\VisitorCount;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.public', function ($view) {
            $defaultSettings = [
                'site_name' => 'Pengadilan Tinggi Agama Papua Barat',
                'site_description' => 'Website Resmi Pengadilan Tinggi Agama Papua Barat - Mahkamah Agung Republik Indonesia',
                'address' => 'Jl. Brawijaya, Kelurahan Manokwari Timur, Distrik Manokwari, Provinsi Papua Barat. Kode POS 98311',
                'phone' => '0811 4088 3744',
                'email' => 'ptapapuabarat@gmail.com',
                'fax' => '0811 4088 3744',
            ];

            $settings = $defaultSettings;
            $navigation = [];
            $visitorStats = [
                'today' => 0,
                'week' => 0,
                'month' => 0,
                'total' => 0,
                'devices' => ['mobile' => 0, 'desktop' => 0, 'tablet' => 0, 'unknown' => 0],
                'countries' => collect(),
                'online' => 0,
            ];
            $groupLabels = [
                'tentang-pengadilan' => 'Tentang Pengadilan',
                'informasi-umum' => 'Informasi Umum',
                'informasi-hukum' => 'Informasi Hukum',
                'transparansi' => 'Transparansi',
                'peraturan-kebijakan' => 'Peraturan & Kebijakan',
                'informasi' => 'Informasi',
            ];

            if (Schema::hasTable('settings')) {
                $settings = Setting::getMany($defaultSettings);
            }

            if (Schema::hasTable('pages')) {
                $pagesByGroup = Page::with(['activeChildren'])
                    ->whereIn('menu_group', array_keys($groupLabels))
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('menu_group')
                    ->orderBy('order')
                    ->get()
                    ->groupBy('menu_group');

                foreach ($groupLabels as $groupSlug => $groupName) {
                    $navigation[$groupSlug] = [
                        'label' => $groupName,
                        'pages' => $pagesByGroup->get($groupSlug, collect()),
                    ];
                }
            }

            if (Schema::hasTable('visitor_counts') && Schema::hasTable('visitor_sessions')) {
                $visitorStats = VisitorAnalytics::buildStatistics();
            }

            $view->with([
                'publicSiteSettings' => $settings,
                'publicNavigation' => $navigation,
                'publicVisitorStats' => $visitorStats,
            ]);
        });
    }
}
