<?php

namespace Artisanbarista\LaravelShield\Tests;

use Artisanbarista\LaravelShield\LaravelShieldServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{

    protected function getPackageProviders($app)
    {
        return [
            LaravelShieldServiceProvider::class,
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
    }
}


