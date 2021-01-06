<?php

namespace CapoSourceWordpress;

use Illuminate\Support\ServiceProvider;
use Capo\Services\Config;
use Illuminate\Support\Facades\File;

class CapoSourceWordpressServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        File::ensureDirectoryExists(site_cache_path() . '/capo-source-wordpress');

        $options = Config::getPluginOptions(self::class);
        
        $this->app->singleton(Api::class, function ($app) use ($options) {
            return new Api($options['baseUrl']);
        });
        
        $api = app(Api::class);

        $api->getPages();
    }
}
