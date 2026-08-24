<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Facades;

use Illuminate\Support\Facades\Facade;
use Simtabi\Laranail\PasswordTools\Generators\PassphraseBuilder;
use Simtabi\Laranail\PasswordTools\Generators\PasswordBuilder;
use Simtabi\Laranail\PasswordTools\PasswordToolsManager;
use Simtabi\Laranail\PasswordTools\Support\Score;

/**
 * @method static Score score(string $password, list<string> $userInputs = [])
 * @method static PasswordBuilder password()
 * @method static PassphraseBuilder passphrase()
 *
 * @see PasswordToolsManager
 */
final class PasswordTools extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PasswordToolsManager::class;
    }
}
