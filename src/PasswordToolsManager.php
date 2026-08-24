<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools;

use Simtabi\Laranail\PasswordTools\Contracts\PasswordScorer;
use Simtabi\Laranail\PasswordTools\Generators\PassphraseBuilder;
use Simtabi\Laranail\PasswordTools\Generators\PasswordBuilder;
use Simtabi\Laranail\PasswordTools\Support\Score;
use Simtabi\Laranail\PasswordTools\Support\WordList;

/**
 * The package's front door — scoring plus the two fluent generators —
 * behind the {@see Facades\PasswordTools} facade:
 *
 * ```php
 * PasswordTools::score($candidate);                                   // Score
 * PasswordTools::password()->length(20)->symbols()->make();           // random password
 * PasswordTools::passphrase()->words(5)->withNumber()->make();        // diceware phrase
 * ```
 *
 * Builders come out fresh per call (a builder is a recipe, and recipes
 * must not leak between call sites); the scorer and word list are the
 * shared, injected collaborators.
 */
final readonly class PasswordToolsManager
{
    public function __construct(
        private PasswordScorer $scorer,
        private WordList $words,
    ) {}

    /** @param list<string> $userInputs */
    public function score(string $password, array $userInputs = []): Score
    {
        return $this->scorer->score($password, $userInputs);
    }

    public function password(): PasswordBuilder
    {
        return new PasswordBuilder($this->scorer);
    }

    public function passphrase(): PassphraseBuilder
    {
        return new PassphraseBuilder($this->words);
    }
}
