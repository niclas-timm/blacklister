<?php

namespace NiclasTimm\Blacklister;

use Illuminate\Support\ServiceProvider;

class BlacklisterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                // ViewSchema::class
            ]);
        }
    }
}