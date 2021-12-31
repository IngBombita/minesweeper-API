<?php

declare(strict_types=1);

namespace Infrastructure\ServiceProviders;

use Application\Interfaces\CurrentUserServiceInterface;
use Application\Services\CacheService;
use Application\Services\CurrentUserService;
use GuzzleHttp\Client;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Cache\RedisCacheManager;
use Infrastructure\Hash\HashManager;
use Infrastructure\Hash\HashManagerInterface;
use Presentation\Http\View\Components\AppLayout;
use Presentation\Http\View\Components\GuestLayout;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::component('guest-layout', GuestLayout::class);
        Blade::component('app-layout', AppLayout::class);
        RateLimiter::for(
            'slack_jobs',
            function ($job) {
                return $job->queue == config('queue.beanstalkd.slack_queue') ? Limit::perMinute(50) : Limit::none();
            }
        );
    }

    public function register(): void
    {
        $this->app->bind(CacheService::class, RedisCacheManager::class);
    }
}
