<?php

namespace App\Providers;

use App\FloatingIcon;
use App\FooterLink;
use App\Page;
use App\Setting;
use App\Support\PublicNavigation;
use App\Support\VisitorAnalytics;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFour();

        View::composer('layouts.public', function ($view) {
            $defaultSettings = [
                'site_name' => 'Pengadilan Tinggi Agama Papua Barat',
                'site_description' => 'Website Resmi Pengadilan Tinggi Agama Papua Barat - Mahkamah Agung Republik Indonesia',
                'address' => Setting::OFFICE_ADDRESS,
                'phone' => '0811 4088 3744',
                'email' => 'ptapapuabarat@gmail.com',
                'fax' => '0811 4088 3744',
                'instagram_url' => '',
                'facebook_url' => '',
                'youtube_url' => '',
            ];

            $settings = $defaultSettings;
            $pagesByGroup = collect();
            $visitorStats = ['today' => 0, 'yesterday' => 0, 'total' => 0];
            $floatingIcons = collect();
            $footerLinks = collect();
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
            $settings['address'] = Setting::officeAddress($settings['address']);

            if (Schema::hasTable('pages')) {
                $pagesByGroup = Page::with(['activeChildren'])
                    ->whereIn('menu_group', array_keys($groupLabels))
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('menu_group')
                    ->orderBy('order')
                    ->get()
                    ->groupBy('menu_group');

            }

            if (Schema::hasTable('visitor_counts')) {
                $visitorStats = VisitorAnalytics::buildStatistics();
            }

            if (Schema::hasTable('floating_icons')) {
                $floatingIcons = FloatingIcon::active()
                    ->orderBy('order')
                    ->orderBy('title')
                    ->get();
            }

            if (Schema::hasTable('footer_links')) {
                $footerLinks = FooterLink::active()
                    ->orderBy('section')
                    ->orderBy('order')
                    ->get()
                    ->groupBy('section');
            }

            $view->with([
                'publicSiteSettings' => $settings,
                'publicNavigation' => PublicNavigation::groups($pagesByGroup),
                'publicVisitorStats' => $visitorStats,
                'publicFloatingIcons' => $floatingIcons,
                'publicFooterLinks' => $footerLinks,
            ]);
        });
    }
}
