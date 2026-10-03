<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use Simtabi\Laranail\PasswordTools\Providers\PasswordToolsServiceProvider;

/*
 * Laravel loads config/laranail/password-tools.php as `laranail.password-tools`,
 * the key this package reads. A file published anywhere else is never loaded under
 * that key, so an override written there silently changes nothing.
 */
it('publishes the config to the file Laravel loads as laranail.password-tools', function (): void {
    $paths = ServiceProvider::pathsToPublish(PasswordToolsServiceProvider::class, 'laranail::password-tools-config');

    expect($paths)->toHaveCount(1)
        ->and(array_values($paths)[0])->toBe(config_path('laranail/password-tools.php'));
});
