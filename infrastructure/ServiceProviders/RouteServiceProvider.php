<?php

declare(strict_types=1);

namespace Infrastructure\ServiceProviders;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';
    /**
     * This namespace is applied to your action routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Presentation\Http\Actions';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    /**
     * Define the Web routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Presentation\Http\Actions')
            ->group(base_path('presentation/Http/Routes/web.php'));
    }

    /**
     * Define the Web routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware('api')
            ->namespace('Presentation\Http\Actions')
            ->group(base_path('presentation/Http/Routes/api.php'));
    }
}
