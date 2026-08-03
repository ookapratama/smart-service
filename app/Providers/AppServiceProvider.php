<?php

namespace App\Providers;

use App\Contracts\Services\WhatsAppNotifier;
use App\Helpers\ViewConfigHelper;
use App\Models\Pengaduan;
use App\Models\PengajuanSurat;
use App\Models\Setting;
use App\Repositories\AgendaRepository;
use App\Repositories\BeritaRepository;
use App\Repositories\GaleriRepository;
use App\Repositories\JadwalPelayananRepository;
use App\Repositories\JenisSuratRepository;
use App\Repositories\KategoriPengaduanRepository;
use App\Repositories\MenuRepository;
use App\Repositories\PemohonRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TiketRepository;
use App\Repositories\UserRepository;
use App\Services\SettingService;
use App\Services\Wa\FonnteWhatsAppNotifier;
use App\Services\Wa\LogWhatsAppNotifier;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\Repositories\TiketRepository::class,
            TiketRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\PemohonRepository::class,
            PemohonRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\KategoriPengaduanRepository::class,
            KategoriPengaduanRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\JenisSuratRepository::class,
            JenisSuratRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\BeritaRepository::class,
            BeritaRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\GaleriRepository::class,
            GaleriRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\AgendaRepository::class,
            AgendaRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\JadwalPelayananRepository::class,
            JadwalPelayananRepository::class
        );
        // Repository bindings (contract => concrete)
        $this->app->bind(
            \App\Contracts\Repositories\UserRepository::class,
            UserRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\RoleRepository::class,
            RoleRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\MenuRepository::class,
            MenuRepository::class
        );

        // WhatsApp notifier: driver dipilih dari setting `wa_driver` saat resolve.
        // Driver HTTP gateway tinggal ditambahkan ke map ini tanpa mengubah call site.
        $this->app->bind(WhatsAppNotifier::class, function () {
            $drivers = [
                'log' => LogWhatsAppNotifier::class,
                'fonnte' => FonnteWhatsAppNotifier::class,
            ];

            try {
                $driver = env('WA_DRIVER') ?: (Setting::getByKey('wa_driver') ?: 'log');
            } catch (\Throwable) {
                $driver = env('WA_DRIVER', 'log'); // tabel settings belum tersedia (migrasi awal / bootstrap test)
            }

            return $this->app->make($drivers[$driver] ?? $drivers['log']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Stable polymorphic aliases for tiket 'detail' and media 'mediable' morphs
        Relation::enforceMorphMap([
            'pengajuan_surat' => PengajuanSurat::class,
            'pengaduan' => Pengaduan::class,
        ]);

        // Define Gates for authorization
        Gate::before(function ($user, $ability) {
            if ($user->role && $user->role->slug === 'super-admin') {
                return true;
            }
        });

        Gate::define('access', function ($user, $slug, $action) {
            return $user->hasPermission($slug, $action);
        });

        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();

        // Create Helper alias for ViewConfigHelper
        if (! class_exists('Helper')) {
            class_alias(ViewConfigHelper::class, 'Helper');
        }

        // Blade directive for settings
        Blade::directive('setting', function ($expression) {
            return "<?php echo get_setting($expression); ?>";
        });

        // Share menu data and website settings with all views
        View::composer('*', function ($view) {
            $menus = collect();

            // Only authenticated users get a sidebar; guests see none.
            $role = auth()->check() ? auth()->user()->role : null;

            if ($role) {
                $menus = $role->menus()
                    ->whereNull('parent_id')
                    ->wherePivot('can_read', true)
                    ->with(['children' => function ($q) use ($role) {
                        $q->whereHas('roles', function ($rq) use ($role) {
                            $rq->where('roles.id', $role->id)->where('can_read', true);
                        })->orderBy('order_no');
                    }])
                    ->orderBy('order_no')
                    ->get();
            }

            $view->with('menuData', [$menus]);
            $view->with('menuHorizontal', [[]]); // Placeholder

            // Dynamically build siteInfo from database settings for landing page and public views
            $settingService = app(SettingService::class);
            $siteInfo = [
                'name' => $settingService->get('app_name', 'SOREANG SMART SERVICE'),
                'code' => '3S',
                'description' => $settingService->get('app_description', 'Sistem Pelayanan Publik Terintegrasi Berbasis Digital dan Kolaboratif Kecamatan Soreang'),
                'logo' => $settingService->get('app_logo'),
                'favicon' => $settingService->get('app_favicon'),

                // Banner & Hero settings
                'hero_bg' => $settingService->get('hero_bg'),
                'hero_image' => $settingService->get('hero_image'),
                'hero_badge' => $settingService->get('hero_badge', 'Portal Resmi Kecamatan Soreang • Kota Parepare'),
                'hero_title' => $settingService->get('hero_title', 'Soreang Smart Service (3S)'),
                'hero_subtitle' => $settingService->get('hero_subtitle', 'Pelayanan kependudukan digital terpadu, pengaduan publik, dan portal informasi resmi Kecamatan Soreang Kota Parepare secara cepat, mudah, dan transparan.'),
                'hero_btn1_text' => $settingService->get('hero_btn1_text', 'Pengajuan Surat Online'),
                'hero_btn2_text' => $settingService->get('hero_btn2_text', 'Profil Kecamatan'),
                'service_image' => $settingService->get('service_image'),
                'service_badge' => $settingService->get('service_badge', 'Solusi Terintegrasi'),
                'service_title' => $settingService->get('service_title', 'Pelayanan Cepat, Transparan, & Tanpa Antri'),
                'service_subtitle' => $settingService->get('service_subtitle', 'Warga Kecamatan Soreang kini dapat mengajukan surat keterangan online, melacak tiket status permohonan secara real-time, dan menyampaikan aspirasi tanpa harus datang mengantri di kantor kelurahan.'),

                // Profile settings
                'email' => $settingService->get('profile_email', 'layanan@soreang.parepare.go.id'),
                'phone' => $settingService->get('profile_telepon', '(0421) 21055'),
                'whatsapp' => $settingService->get('contact_phone', '+6281234567890'),
                'address_line1' => $settingService->get('profile_alamat', 'Jl. Jenderal Sudirman No. 45, Kecamatan Soreang'),
                'address_line2' => $settingService->get('profile_kota', 'Kota Parepare, Sulawesi Selatan 91131'),
                'kecamatan' => $settingService->get('profile_kecamatan', 'Kecamatan Soreang'),
                'kota' => $settingService->get('profile_kota', 'Kota Parepare'),
                'visi' => $settingService->get('profile_visi', '"Parepare Terkemuka & Soreang Smart Sejahtera"'),
                'deskripsi_lengkap' => $settingService->get('profile_deskripsi_lengkap', 'Kecamatan Soreang merupakan salah satu kawasan pusat pemerintahan dan aktivitas ekonomi masyarakat di Kota Parepare. Terdiri dari 7 Kelurahan, Kecamatan Soreang mengusung inovasi pelayanan digital Soreang Smart Service (3S) untuk memudahkan pengurusan surat, pengaduan publik, serta transparansi data kependudukan.'),
                'social_links' => [
                    'facebook' => $settingService->get('social_facebook', 'https://facebook.com/soreangsmartservice'),
                    'instagram' => $settingService->get('social_instagram', 'https://instagram.com/soreang.smartservice'),
                    'youtube' => $settingService->get('social_youtube', 'https://youtube.com/c/SoreangSmartService'),
                ],
            ];
            $view->with('siteInfo', $siteInfo);
        });

        // Share template variables config from Database or defaults
        $settingService = app(SettingService::class);

        config([
            'variables' => [
                'templateName' => $settingService->get('app_name', 'SOREANG SMART SERVICE'),
                'templateDescription' => $settingService->get('app_description', 'Platform pelayanan publik terpadu'),
                'templateLogo' => $settingService->get('app_logo'),
                'templateFavicon' => $settingService->get('app_favicon'),
                'templateVersion' => '1.0.0',
                'templateFree' => false,
                'templatePrefix' => '',
                'templateSuffix' => '',
                'templateDomain' => 'localhost',
                'templateAuthor' => 'Ooka Pratama',
                'templateAuthorUrl' => '#',
                'creatorName' => 'Ooka Pratama',
                'creatorUrl' => '#',
                'documentation' => '#',
            ],
        ]);
    }
}
