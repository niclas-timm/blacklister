<?php

namespace NiclasTimm\Blacklister\Tests;

use NiclasTimm\EloquentSchemaViewer\EloquentSchemaViewerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            EloquentSchemaViewerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}