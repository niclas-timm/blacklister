<?php

namespace NiclasTimm\Blacklister\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use NiclasTimm\Blacklister\BlacklisterServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Config::set('blacklister.blacklist_path', __DIR__.'/fixtures/blacklist.json');
    }

    public function defineRoutes($router): void
    {
        $router->post('/test', function (Request $request) {
            $request->validate([
                'email' => 'blacklist',
            ]);

            return response(status: 200);
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            BlacklisterServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}