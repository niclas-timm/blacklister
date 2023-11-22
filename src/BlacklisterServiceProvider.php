<?php

namespace NiclasTimm\Blacklister;

use Illuminate\Support\ServiceProvider;
use NiclasTimm\Blacklister\Console\InstallBlacklister;

class BlacklisterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'blacklister');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('blacklister.php'),
            ], 'config');

            $this->commands([
                InstallBlacklister::class,
            ]);
        }

        \Illuminate\Support\Facades\Validator::extend('blacklist', Validator::class.'@validate');
        \Illuminate\Support\Facades\Validator::replacer('blacklist', Validator::class."@message");
    }
}