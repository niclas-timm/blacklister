<?php

namespace NiclasTimm\Blacklister;

use Illuminate\Support\ServiceProvider;
use NiclasTimm\Blacklister\Console\AddToBlacklist;
use NiclasTimm\Blacklister\Console\ExportBlackList;
use NiclasTimm\Blacklister\Console\ImportBlacklist;
use NiclasTimm\Blacklister\Console\InstallBlacklister;
use NiclasTimm\Blacklister\Console\RemoveFromBlacklist;
use NiclasTimm\Blacklister\Console\UpdateBlacklisterCache;
use NiclasTimm\Blacklister\Console\VerifyBlacklisterSettings;
use NiclasTimm\Blacklister\Console\ViewBlacklist;

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
                VerifyBlacklisterSettings::class,
                UpdateBlacklisterCache::class,
                AddToBlacklist::class,
                ViewBlacklist::class,
                RemoveFromBlacklist::class,
                ExportBlackList::class,
                ImportBlacklist::class,
            ]);
        }

        \Illuminate\Support\Facades\Validator::extend('blacklist', Validator::class.'@validate');
        \Illuminate\Support\Facades\Validator::replacer('blacklist', Validator::class."@message");
    }
}