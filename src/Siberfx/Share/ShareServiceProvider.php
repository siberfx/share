<?php

declare(strict_types=1);

namespace Siberfx\Share;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class ShareServiceProvider extends ServiceProvider
{
    protected const string CONFIG = __DIR__.'/../../config/social-share.php';

    protected const string VIEWS = __DIR__.'/../../views';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG, 'social-share');

        // Scoped so long-running workers (Octane, queues) get a fresh instance per request/job.
        $this->app->scoped('share', fn (Container $app) => new Share($app));
        $this->app->alias('share', Share::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(self::VIEWS, 'social-share');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG => config_path('social-share.php'),
            ], ['social-share', 'social-share-config']);

            $this->publishes([
                self::VIEWS => resource_path('views/vendor/social-share'),
            ], ['social-share', 'social-share-views']);
        }
    }
}
