<?php

declare(strict_types=1);

namespace Infrastructure\ServiceProviders;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Infrastructure\QueryBus\Handler\Locator\HandlerInstanceResolver;
use Infrastructure\QueryBus\Handler\Locator\InferableLocator;
use Infrastructure\QueryBus\Handler\MethodNameInflector\HandleInflector;
use Infrastructure\QueryBus\Handler\QueryNameExtractor\QueryNameExtractor;
use Infrastructure\QueryBus\Middleware\CacheableQueryMiddleware;
use Infrastructure\QueryBus\QueryBus;
use Infrastructure\QueryBus\QueryBusInterface;
use League\Tactician\Handler\CommandHandlerMiddleware;

class QueryBusServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(
            QueryBusInterface::class,
            static function (Application $application) {
                $cacheableQueryMiddleware = $application->make(CacheableQueryMiddleware::class);

                $handlerMiddleware = new CommandHandlerMiddleware(
                    new QueryNameExtractor(),
                    new InferableLocator(new HandlerInstanceResolver($application)),
                    new HandleInflector()
                );

                return new QueryBus([$cacheableQueryMiddleware, $handlerMiddleware]);
            }
        );
    }
}
