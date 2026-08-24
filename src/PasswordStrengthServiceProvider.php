<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordStrength;

use Simtabi\Laranail\Package\Tools\Package;
use Simtabi\Laranail\Package\Tools\Providers\PackageServiceProvider;
use Simtabi\Laranail\PasswordStrength\Commands\CheckCommand;
use Simtabi\Laranail\PasswordStrength\Contracts\PasswordScorer;
use Simtabi\Laranail\PasswordStrength\Rules\StrongPassword;
use Simtabi\Laranail\PasswordStrength\Scorers\ZxcvbnScorer;

class PasswordStrengthServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laranail/password-strength')
            ->hasTranslations();
    }

    public function registeringPackage(): void
    {
        // The prefixed file with the flat org key, per the family pattern.
        $this->mergeConfigFrom($this->configPath(), 'laranail.password-strength');

        // singletonIf: an application-bound scorer wins regardless of
        // provider order; otherwise the config names the engine.
        $this->app->singletonIf(PasswordScorer::class, function (): PasswordScorer {
            $scorer = config('laranail.password-strength.scorer');

            if (is_string($scorer) && is_subclass_of($scorer, PasswordScorer::class)) {
                $resolved = $this->app->make($scorer);

                if ($resolved instanceof PasswordScorer) {
                    return $resolved;
                }
            }

            return new ZxcvbnScorer;
        });
    }

    public function bootingPackage(): void
    {
        $this->registerValidationBridge();

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [$this->configPath() => config_path('laranail-password-strength.php')],
                $this->package->getNamespacedPublishTag('config'),
            );

            $this->commands([CheckCommand::class]);
        }
    }

    /**
     * The one-way, guarded bridge (design §4.2): when laranail/validation
     * is installed, its password() builder node gains ->strength(). The
     * validator itself knows nothing of this package.
     */
    private function registerValidationBridge(): void
    {
        $node = '\Simtabi\Laranail\Validation\Builder\Nodes\PasswordRule';

        if (! class_exists($node)) {
            return;
        }

        $node::macro('strength',
            /** @phpstan-ignore-next-line $this is the PasswordRule node at call time */
            fn (?int $minScore = null): mixed => $this->rule(new StrongPassword($minScore)));
    }

    private function configPath(): string
    {
        return dirname(__DIR__).'/config/laranail-password-strength.php';
    }
}
