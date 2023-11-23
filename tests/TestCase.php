<?php

namespace NiclasTimm\Blacklister\Tests;

use Illuminate\Http\Request;
use NiclasTimm\Blacklister\BlacklisterServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
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