<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordStrength\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Simtabi\Laranail\PasswordStrength\PasswordStrengthServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [PasswordStrengthServiceProvider::class];
    }
}
