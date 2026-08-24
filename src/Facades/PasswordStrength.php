<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordStrength\Facades;

use Illuminate\Support\Facades\Facade;
use Simtabi\Laranail\PasswordStrength\Contracts\PasswordScorer;
use Simtabi\Laranail\PasswordStrength\Support\Score;

/**
 * @method static Score score(string $password, list<string> $userInputs = [])
 *
 * @see PasswordScorer
 */
final class PasswordStrength extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PasswordScorer::class;
    }
}
