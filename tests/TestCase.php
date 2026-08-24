<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Simtabi\Laranail\PasswordTools\PasswordToolsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [PasswordToolsServiceProvider::class];
    }
}
