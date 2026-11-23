<?php

use App\Agenda;
use App\Article;
use App\Gallery;
use App\GalleryCategory;
use App\IntegrityZone;
use App\Post;
use App\PostCategory;
use App\QuickLink;
use App\Slider;
use App\SurveyIndex;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageDemoSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $this->seedSliders();
            $this->seedQuickLinks();
            $this->seedPosts();
            $this->seedArticles();
            $this->seedAgendas();
            $this->seedGalleries();
            $this->seedSurveys();
            $this->seedIntegrityZones();
        });
    }

    private function seedSliders()
    {
        $sliders = [
            [
                'title' => 'Pelayanan Peradilan yang Profesional dan Berintegritas',
                'description' => 'PTA Papua Barat berkomitmen memberikan layanan hukum yang cepat, transparan, dan mudah diakses masyarakat.',
                'image' => 'sliders/10ZqxzYPik5rC9oQmteZQhAOdWKzncCpKGNyWdrI.jpg',
            ],
            [
                'title' => 'Sinergi Peradilan Agama Se-Papua Barat',
                'description' => 'Memperkuat koordinasi dan kualitas pelayanan pada seluruh satuan kerja di wilayah hukum PTA Papua Barat.',
                'image' => 'sliders/pyKmjDnsynhyVldIgFiouEX9b9yzu5xbWR4uY1QB.jpg',
            ],
            [
                'title' => 'Informasi Publik dalam Satu Akses',
                'description' => 'Temukan berita, agenda pimpinan, pengumuman, dan layanan pengadilan melalui website resmi kami.',
                'image' => 'sliders/bAFee0de3rVCkjIkgSK36RVVDzNzuqMN5OejEAXz.jpg',
            ],
        ];

        foreach ($sliders as $index => $slider) {
            Slider::updateOrCreate(
                ['title' => $slider['title']],
                array_merge($slider, [
                    'link' => null,
                    'text_position' => 'bottom-left',
                    'order' => $index + 1,
                    'is_active' => true,
                ])
            );
        }
    }

    private function seedQuickLinks()
    {
        $links = [
            ['E-Court', 'Pendaftaran perkara, pembayaran, dan persidangan secara elektronik.', 'https://ecourt.mahkamahagung.go.id'],
            ['SIPP', 'Informasi penelusuran perkara pada pengadilan.', 'https://sipp.mahkamahagung.go.id'],
            ['Direktori Putusan', 'Akses putusan Mahkamah Agung dan badan peradilan.', 'https://putusan3.mahkamahagung.go.id'],
            ['Badilag', 'Portal resmi Direktorat Jenderal Badan Peradilan Agama.', 'https://badilag.mahkamahagung.go.id'],
            ['SIWAS', 'Sistem informasi pengawasan Mahkamah Agung.', 'https://siwas.mahkamahagung.go.id'],
            ['Layanan Publik', 'Sampaikan aspirasi, pengaduan, atau permohonan informasi.', url('/layanan-publik')],
        ];

        foreach ($links as $index => $link) {
            QuickLink::updateOrCreate(
                ['title' => $link[0]],
                [
                    'description' => $link[1],
                    'icon' => null,
                    'url' => $link[2],
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedPosts()
    {
        $admin = User::where('email', 'admin@admin.com')->firstOrFail();
        $satkerUsers = User::where('role', User::ROLE_AUTHOR_SATKER)->orderBy('id')->get();
        $categories = PostCategory::whereIn('slug', ['rapat', 'sosialisasi', 'rakor', 'bimtek', 'kunjungan', 'lainnya'])
            ->get()
            ->keyBy('slug');
        $now = Carbon::now();

        $latestNews = [
            ['pta-papua-barat-perkuat-layanan-terpadu-satu-pintu', 'PTA Papua Barat Perkuat Layanan Terpadu Satu Pintu', 'Peningkatan mutu layanan menjadi fokus utama untuk memberikan akses peradilan yang mudah dan transparan.', 'posts/FdLAz0RM0wGl2NY11aksZ9InsuArVeQvRlFZivBu.jpg', 'rapat'],
            ['pimpinan-pta-papua-barat-buka-rapat-koordinasi', 'Pimpinan PTA Papua Barat Buka Rapat Koordinasi Wilayah', 'Rapat koordinasi membahas evaluasi kinerja dan penguatan pelayanan publik di seluruh satuan kerja.', 'posts/AmshMNkVyTTNvUWZEjhExvS7nFkHi6I1fdhMfLp7.jpg', 'rakor'],
            ['sosialisasi-pelayanan-prima-bagi-aparatur', 'Sosialisasi Pelayanan Prima bagi Seluruh Aparatur', 'Kegiatan ini mendorong budaya kerja profesional, responsif, dan berorientasi kepada kebutuhan masyarakat.', 'posts/2bLe3B34Tx4CwdpQ155zGuevRoadKwgB3sOPoZ84.jpg', 'sosialisasi'],
            ['bimbingan-teknis-administrasi-perkara-elektronik', 'Bimbingan Teknis Administrasi Perkara Elektronik', 'Aparatur mengikuti penguatan kompetensi penggunaan layanan administrasi perkara berbasis elektronik.', 'posts/ytelUC7TESFK99jPFIkUXOeLEvhqpdf6LBtcr4kH.jpg', 'bimtek'],
            ['kunjungan-kerja-pimpinan-ke-satuan-kerja', 'Kunjungan Kerja Pimpinan ke Satuan Kerja', 'Pimpinan melakukan monitoring layanan dan berdialog langsung dengan aparatur pengadilan agama.', 'posts/VXKA9MJOIrxHhIUaD7Gcf9NOGQWOPmBjSyIxl1ny.jpg', 'kunjungan'],
            ['pta-papua-barat-gelar-apel-pagi-bersama', 'PTA Papua Barat Gelar Apel Pagi Bersama', 'Apel bersama menjadi sarana penguatan disiplin, integritas, dan koordinasi internal.', 'posts/8JUaPz4kHpFBiju9jMVYeNoEQpkgHl7H7m2SFQCb.jpg', 'lainnya'],
        ];

        foreach ($latestNews as $index => $item) {
            $this->upsertPost($item[0], [
                'title' => $item[1],
                'content' => '<p>' . $item[2] . '</p><p>Konten ini merupakan data contoh untuk menampilkan rancangan halaman website.</p>',
                'excerpt' => $item[2],
                'image' => $item[3],
                'category' => 'berita',
                'post_category_id' => optional($categories->get($item[4]))->id,
                'news_scope' => Post::NEWS_SCOPE_TERKINI,
                'announcement_category' => null,
                'workflow_status' => Post::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays($index),
                'is_published' => true,
                'user_id' => $admin->id,
            ], $now->copy()->subDays($index));
        }

        $satkerNews = [
            ['pa-manokwari-tingkatkan-kualitas-pelayanan-publik', 'PA Manokwari Tingkatkan Kualitas Pelayanan Publik', 'PA Manokwari melakukan evaluasi layanan untuk memastikan kebutuhan masyarakat terpenuhi dengan baik.', 'posts/9CSZLpjHlpUGM6I57SCk1lKR7L5tWVPjXHyrUqXT.jpg', 'rapat'],
            ['pa-sorong-gelar-sosialisasi-ecourt', 'PA Sorong Gelar Sosialisasi E-Court bagi Masyarakat', 'Sosialisasi memperkenalkan kemudahan pendaftaran dan administrasi perkara secara elektronik.', 'posts/CDzEP9yv8EyqqaeMXFJNUHoAL6A5RZHObyDSDktz.jpg', 'sosialisasi'],
            ['pa-kaimana-laksanakan-briefing-pelayanan', 'PA Kaimana Laksanakan Briefing Pelayanan Pagi', 'Briefing rutin dilaksanakan untuk memastikan kesiapan petugas dan kualitas pelayanan harian.', 'posts/GsusrgXS51Ez7lBESxXM7t1LWCLHJRQikqcsCtdT.jpg', 'lainnya'],
            ['pa-fakfak-perkuat-zona-integritas', 'PA Fak-Fak Perkuat Implementasi Zona Integritas', 'Seluruh aparatur berkomitmen mewujudkan pelayanan yang bersih, akuntabel, dan bebas korupsi.', 'posts/KrR2w9kdzZKoXvnrFaWUSeohAC64gPaS85JPmfWU.jpg', 'sosialisasi'],
            ['pa-manokwari-ikuti-rakor-wilayah', 'PA Manokwari Ikuti Rapat Koordinasi Wilayah', 'Rapat koordinasi menyelaraskan program kerja dan target kinerja satuan kerja tahun berjalan.', 'posts/ngXzmiaJrTjOW8dlirW7rErUCfEwFs00OJrbP2FW.jpg', 'rakor'],
            ['pa-sorong-laksanakan-bimtek-kepaniteraan', 'PA Sorong Laksanakan Bimtek Kepaniteraan', 'Kegiatan meningkatkan pemahaman aparatur terhadap administrasi dan layanan perkara.', 'posts/P2WI7gxP6Pl5759GfXKJQGKuaYYGGxUyDP2gmMVv.jpg', 'bimtek'],
        ];

        foreach ($satkerNews as $index => $item) {
            $author = $satkerUsers->get($index % max(1, $satkerUsers->count())) ?: $admin;
            $this->upsertPost($item[0], [
                'title' => $item[1],
                'content' => '<p>' . $item[2] . '</p><p>Konten ini merupakan data contoh dari satuan kerja.</p>',
                'excerpt' => $item[2],
                'image' => $item[3],
                'category' => 'berita',
                'post_category_id' => optional($categories->get($item[4]))->id,
                'news_scope' => Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT,
                'announcement_category' => null,
                'workflow_status' => Post::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays($index + 1),
                'is_published' => true,
                'user_id' => $author->id,
            ], $now->copy()->subDays($index + 1));
        }

        $announcements = [
            ['jadwal-pelayanan-ptsp-bulan-november', 'Jadwal Pelayanan PTSP Bulan November 2026', Post::ANNOUNCEMENT_CATEGORY_PTA_PAPUA_BARAT],
            ['pengumuman-seleksi-terbuka-mahkamah-agung', 'Pengumuman Seleksi Terbuka Mahkamah Agung RI', Post::ANNOUNCEMENT_CATEGORY_MAHKAMAH_AGUNG],
            ['informasi-pembinaan-tenaga-teknis-badilag', 'Informasi Pembinaan Tenaga Teknis Peradilan Agama', Post::ANNOUNCEMENT_CATEGORY_BADILAG],
            ['pemeliharaan-layanan-website-akhir-pekan', 'Pemeliharaan Layanan Website pada Akhir Pekan', Post::ANNOUNCEMENT_CATEGORY_LAIN_LAIN],
            ['perubahan-jadwal-kegiatan-pimpinan', 'Perubahan Jadwal Kegiatan Pimpinan PTA Papua Barat', Post::ANNOUNCEMENT_CATEGORY_PTA_PAPUA_BARAT],
            ['undangan-webinar-pelayanan-inklusif', 'Undangan Webinar Pelayanan Peradilan yang Inklusif', Post::ANNOUNCEMENT_CATEGORY_BADILAG],
        ];

        foreach ($announcements as $index => $item) {
            $excerpt = 'Informasi resmi terkait ' . strtolower($item[1]) . '. Silakan membuka pengumuman untuk melihat rincian selengkapnya.';
            $this->upsertPost($item[0], [
                'title' => $item[1],
                'content' => '<p>' . $excerpt . '</p>',
                'excerpt' => $excerpt,
                'image' => null,
                'category' => 'pengumuman',
                'post_category_id' => null,
                'news_scope' => null,
                'announcement_category' => $item[2],
                'workflow_status' => Post::STATUS_PUBLISHED,
                'published_at' => $now->copy()->subDays($index),
                'is_published' => true,
                'user_id' => $admin->id,
            ], $now->copy()->subDays($index));
        }
    }

    private function upsertPost($slug, array $attributes, Carbon $createdAt)
    {
        $post = Post::updateOrCreate(['slug' => $slug], $attributes);
        $post->timestamps = false;
        $post->created_at = $createdAt;
        $post->updated_at = $createdAt;
        $post->save();
    }

    private function seedArticles()
    {
        $articles = [
            ['membangun-budaya-pelayanan-prima', 'Membangun Budaya Pelayanan Prima di Lingkungan Peradilan', Article::CATEGORY_PTA_PAPUA_BARAT],
            ['transformasi-digital-administrasi-perkara', 'Transformasi Digital dalam Administrasi Perkara', Article::CATEGORY_MAHKAMAH_AGUNG],
            ['mediasi-sebagai-solusi-sengketa-keluarga', 'Mediasi sebagai Solusi Sengketa Keluarga', Article::CATEGORY_BADILAG],
            ['pentingnya-literasi-hukum-bagi-masyarakat', 'Pentingnya Literasi Hukum bagi Masyarakat', Article::CATEGORY_LAIN_LAIN],
            ['integritas-aparatur-dan-kepercayaan-publik', 'Integritas Aparatur dan Kepercayaan Publik', Article::CATEGORY_PTA_PAPUA_BARAT],
            ['peradilan-modern-yang-ramah-kelompok-rentan', 'Peradilan Modern yang Ramah Kelompok Rentan', Article::CATEGORY_BADILAG],
        ];
        $images = [
            'articles/82Rs349UmgCPAMEWXGfIfM6JF7R4s4jtOyjWQCMN.jpg',
            'articles/co78W6h5wViJSps9d4H8xRKweEB7dDLyHLkpx4Jb.jpg',
            'articles/fpqHw8Jf4wnkSeeeJbPfpF0FmcgzlArfTR02EiZA.jpg',
        ];

        foreach ($articles as $index => $item) {
            $excerpt = 'Tulisan pegawai mengenai ' . strtolower($item[1]) . ' sebagai bagian dari pengembangan pengetahuan dan pelayanan peradilan.';
            Article::updateOrCreate(
                ['slug' => $item[0]],
                [
                    'title' => $item[1],
                    'excerpt' => $excerpt,
                    'content' => '<p>' . $excerpt . '</p><p>Artikel ini merupakan data contoh untuk kebutuhan tampilan website.</p>',
                    'image' => $images[$index % count($images)],
                    'pdf_file' => null,
                    'pdf_url' => null,
                    'article_category' => $item[2],
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedAgendas()
    {
        $admin = User::where('email', 'admin@admin.com')->firstOrFail();
        $now = Carbon::now()->startOfDay();
        $agendas = [
            ['rapat-evaluasi-kinerja-bulanan', 'Rapat Evaluasi Kinerja Bulanan', 'Evaluasi capaian program kerja dan tindak lanjut pelayanan publik.', 'Ruang Rapat Pimpinan', 1, 9],
            ['pembinaan-aparatur-pengadilan-agama', 'Pembinaan Aparatur Pengadilan Agama', 'Pembinaan teknis dan administrasi bagi aparatur satuan kerja.', 'Aula PTA Papua Barat', 3, 10],
            ['audiensi-dengan-mitra-kerja', 'Audiensi dengan Mitra Kerja', 'Pertemuan pimpinan dalam rangka penguatan sinergi kelembagaan.', 'Ruang Ketua PTA Papua Barat', 5, 13],
            ['kunjungan-kerja-ke-pa-manokwari', 'Kunjungan Kerja ke PA Manokwari', 'Monitoring layanan, sarana prasarana, dan pelaksanaan program prioritas.', 'PA Manokwari', 8, 8],
        ];

        foreach ($agendas as $index => $item) {
            $startAt = $now->copy()->addDays($item[4])->setTime($item[5], 0);
            Agenda::updateOrCreate(
                ['slug' => $item[0]],
                [
                    'title' => $item[1],
                    'agenda_type' => Agenda::TYPE_AGENDA_PIMPINAN,
                    'summary' => $item[2],
                    'description' => '<p>' . $item[2] . '</p>',
                    'location' => $item[3],
                    'organizer' => 'Pimpinan PTA Papua Barat',
                    'start_at' => $startAt,
                    'end_at' => $startAt->copy()->addHours(2),
                    'order' => $index + 1,
                    'is_active' => true,
                    'user_id' => $admin->id,
                ]
            );
        }
    }

    private function seedGalleries()
    {
        $categories = GalleryCategory::all()->keyBy('slug');

        Gallery::where('title', 'Profil Pelayanan PTA Papua Barat')
            ->where('video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->delete();

        $galleries = [
            ['Rapat Koordinasi Wilayah', 'image', 'galleries/GeYhbED3VCeX6tA7gcWw8RsjDm1UZQy4WfEw3HeA.jpg', null, 'kegiatan'],
            ['Pelayanan Terpadu Satu Pintu', 'image', 'galleries/o8PlnGKHLnHDjBVtdkobkIhBZsota3OFee6RP5A9.jpg', null, 'layanan'],
            ['Sosialisasi Pelayanan Publik', 'image', 'galleries/TAHPGIciFjiWDWqcwKrxcI38IwKQlUgWLdBcMalE.jpg', null, 'sosialisasi'],
            ['Kegiatan Bersama Aparatur', 'image', 'galleries/AQby4QyjnNfluQWWN35Q540DqJunWIgMuQ2mBrZX.jpg', null, 'kegiatan'],
            ['Dokumentasi Pimpinan', 'image', 'galleries/MToAzgjJuVnEbZ1ag97BQ1ZmJU2tiyjS9pLFCDUy.jpg', null, 'lainnya'],
            ['Pelantikan di Lingkungan PTA Papua Barat', 'video', null, 'https://www.youtube.com/watch?v=CPVNh2xNJeM', 'layanan'],
        ];

        foreach ($galleries as $index => $item) {
            Gallery::updateOrCreate(
                ['title' => $item[0]],
                [
                    'type' => $item[1],
                    'gallery_category_id' => optional($categories->get($item[4]))->id,
                    'file' => $item[2],
                    'video_url' => $item[3],
                    'description' => 'Dokumentasi contoh kegiatan PTA Papua Barat.',
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedSurveys()
    {
        $surveys = [
            ['Indeks Kepuasan Masyarakat', 'fas fa-smile', 94.75, 'Sangat Baik'],
            ['Indeks Persepsi Anti Korupsi', 'fas fa-shield-alt', 96.20, 'Sangat Baik'],
            ['Indeks Pelayanan Publik', 'fas fa-hands-helping', 93.80, 'Sangat Baik'],
            ['Nilai Integritas Organisasi', 'fas fa-award', 95.10, 'Sangat Baik'],
        ];

        foreach ($surveys as $index => $survey) {
            SurveyIndex::updateOrCreate(
                ['title' => $survey[0]],
                [
                    'icon' => $survey[1],
                    'value' => $survey[2],
                    'label' => $survey[3],
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedIntegrityZones()
    {
        $images = [
            'integrity-zones/1XkDFGH2pX8wW7af7QAEHQ9UmCwyUQ49ImotJAbQ.png',
            'integrity-zones/eQua43ulsHWQc22D2Cl9EC7vyiURdrsk28PMu6GN.png',
            'integrity-zones/gc7LGtDpPaMTJvTjo8pLxe4DITRHw8q8tvs5Q3Sj.png',
            'integrity-zones/lXTNQVHjryBi88Y5PTnwEeeVLRECeHzLqrotx5q8.png',
            'integrity-zones/nRYHwfTx0ti0WMb6eFVdrD4fXp8V3afGiuxvKZuV.png',
            'integrity-zones/oZcGbSXVrpzBd8vIuF9NBPpdadcnHptbqFxtenhr.png',
        ];

        foreach ($images as $index => $image) {
            IntegrityZone::updateOrCreate(
                ['title' => 'Area ' . ($index + 1)],
                [
                    'image' => $image,
                    'url' => 'https://drive.google.com/',
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
