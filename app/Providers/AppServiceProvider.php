<?php

namespace App\Providers;

use App\Helpers\ViewConfigHelper;
use App\Models\Pengaduan;
use App\Models\PengajuanSurat;
use App\Repositories\MenuRepository;
use App\Repositories\ProductsRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\SettingService;
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
            \App\Repositories\TiketRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\PemohonRepository::class,
            \App\Repositories\PemohonRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\KategoriPengaduanRepository::class,
            \App\Repositories\KategoriPengaduanRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\JenisSuratRepository::class,
            \App\Repositories\JenisSuratRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\BeritaRepository::class,
            \App\Repositories\BeritaRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\JadwalPelayananRepository::class,
            \App\Repositories\JadwalPelayananRepository::class
        );
        $this->app->bind(
            \App\Contracts\Repositories\InstansiRepository::class,
            \App\Repositories\InstansiRepository::class
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
        $this->app->bind(
            \App\Contracts\Repositories\ProductsRepository::class,
            ProductsRepository::class
        );
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

        // Share menu data with all views
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
        });

        // Share template variables config from Database or defaults
        $settingService = app(SettingService::class);

        config([
            'variables' => [
                'templateName' => $settingService->get('app_name', 'Base Laravel'),
                'templateDescription' => $settingService->get('app_description', 'Base project builder'),
                'templateKeyword' => $settingService->get('app_keywords', 'laravel, dashboard'),
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
                'documentation' => 'https://demos.pixinvent.com/materialize-html-admin-template/documentation/',
                'contactEmail' => $settingService->get('contact_email'),
                'contactPhone' => $settingService->get('contact_phone'),
                'socialInstagram' => $settingService->get('social_instagram'),
            ],
        ]);
    }
}
