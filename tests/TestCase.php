<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Tests;

use Simtabi\Laranail\PasswordTools\Providers\PasswordToolsServiceProvider;
use Simtabi\Laranail\Package\Tools\Testing\IsolatedTestCase;

abstract class TestCase extends IsolatedTestCase
{
    protected function getPackageProviders($app): array
    {
        return [PasswordToolsServiceProvider::class];
    }
}
