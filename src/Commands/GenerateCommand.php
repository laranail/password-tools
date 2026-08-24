<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Commands;

use Simtabi\Laranail\Console\Tools\Commands\Command;
use Simtabi\Laranail\Console\Tools\Commands\Concerns\SupportsNamespacedNames;
use Simtabi\Laranail\PasswordTools\PasswordToolsManager;

/**
 * Generate passwords or passphrases from the terminal. Printing the
 * result IS the point here — unlike the check command's input, a value
 * this command emits was never a secret before it existed, and the
 * caller is asking to see it.
 */
final class GenerateCommand extends Command
{
    use SupportsNamespacedNames;

    protected $signature = 'laranail::password-tools.generate
        {--passphrase : Diceware words instead of characters}
        {--length=16 : Characters (password mode)}
        {--words=5 : Words (passphrase mode)}
        {--symbols : Include symbols (password mode)}
        {--count=1 : How many to generate}';

    protected $description = 'Generate cryptographically secure passwords or diceware passphrases.';

    public function handle(PasswordToolsManager $tools): int
    {
        $count = is_numeric($this->option('count')) ? max(1, min(100, (int) $this->option('count'))) : 1;

        if ((bool) $this->option('passphrase')) {
            $builder = $tools->passphrase()
                ->words(is_numeric($this->option('words')) ? (int) $this->option('words') : 5)
                ->capitalize();

            foreach ($builder->makeMany($count) as $phrase) {
                $this->line($phrase);
            }

            $this->comment(sprintf('~%.0f bits of entropy each.', $builder->bits()));

            return self::SUCCESS;
        }

        $builder = $tools->password()
            ->length(is_numeric($this->option('length')) ? (int) $this->option('length') : 16)
            ->symbols((bool) $this->option('symbols'));

        foreach ($builder->makeMany($count) as $password) {
            $this->line($password);
        }

        return self::SUCCESS;
    }
}
