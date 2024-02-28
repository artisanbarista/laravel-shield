<?php

namespace Webdevartisan\LaravelShield\Tests;

use Webdevartisan\LaravelShield\LaravelShieldServiceProvider;
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


