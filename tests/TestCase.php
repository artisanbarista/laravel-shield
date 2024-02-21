<?php

namespace Webdevartisan\LaravelBlocker\Tests;

use Webdevartisan\LaravelBlocker\LaravelBlockerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{

    protected function getPackageProviders($app)
    {
        return [
            LaravelBlockerServiceProvider::class,
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
    }
}


