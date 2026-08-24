<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordStrength\Commands;

use Simtabi\Laranail\Console\Tools\Commands\Command;
use Simtabi\Laranail\Console\Tools\Commands\Concerns\SupportsNamespacedNames;
use Simtabi\Laranail\PasswordStrength\Contracts\PasswordScorer;

/**
 * A dev/debug scorer. Deliberately takes NO password argument: the value
 * is always read through a hidden prompt, so it never lands in shell
 * history or the process list, and it is never echoed back — the output
 * is the score and the translated feedback, nothing else.
 */
final class CheckCommand extends Command
{
    use SupportsNamespacedNames;

    protected $signature = 'laranail::password-strength.check';

    protected $description = 'Score a password (0–4) with translated feedback. Prompts; never echoes.';

    public function handle(PasswordScorer $scorer): int
    {
        $password = $this->secret('Password to score');

        if (! is_string($password) || $password === '') {
            $this->error('Nothing to score.');

            return self::FAILURE;
        }

        $score = $scorer->score($password);

        $this->info("Score: {$score->score}/4".($score->isAtLeast(3) ? ' — safely unguessable' : ''));

        foreach ($score->messages($this->laravel->make('translator')) as $message) {
            $this->line("  • {$message}");
        }

        return self::SUCCESS;
    }
}
