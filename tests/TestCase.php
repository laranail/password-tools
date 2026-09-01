<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Tests;

use Simtabi\Laranail\Package\Tools\Testing\IsolatedTestCase;
use Simtabi\Laranail\PasswordTools\Providers\PasswordToolsServiceProvider;

abstract class TestCase extends IsolatedTestCase
{
    protected function getPackageProviders($app): array
    {
        return [PasswordToolsServiceProvider::class];
    }
}
