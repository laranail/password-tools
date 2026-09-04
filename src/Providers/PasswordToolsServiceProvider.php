<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Providers;

use Illuminate\Support\Facades\Route;
use Simtabi\Laranail\Package\Tools\Package;
use Simtabi\Laranail\PasswordTools\Support\WordList;
use Simtabi\Laranail\PasswordTools\Http\MeterController;
use Simtabi\Laranail\PasswordTools\PasswordToolsManager;
use Simtabi\Laranail\PasswordTools\Rules\StrongPassword;
use Simtabi\Laranail\PasswordTools\Scorers\ZxcvbnScorer;
use Simtabi\Laranail\PasswordTools\Commands\CheckCommand;
use Simtabi\Laranail\Validation\Builder\Nodes\PasswordRule;
use Simtabi\Laranail\PasswordTools\Commands\GenerateCommand;
use Simtabi\Laranail\PasswordTools\Contracts\PasswordScorer;
use Simtabi\Laranail\Package\Tools\Providers\PackageServiceProvider;

class PasswordToolsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laranail/password-tools')
            ->hasTranslations();
    }

    public function registeringPackage(): void
    {
        // The prefixed file with the flat org key, per the family pattern.
        $this->mergeConfigFrom($this->configPath(), 'laranail.password-tools');

        // singletonIf: an application-bound scorer wins regardless of
        // provider order; otherwise the config names the engine.
        $this->app->singletonIf(PasswordScorer::class, function (): PasswordScorer {
            $scorer = config('laranail.password-tools.scorer');

            if (is_string($scorer) && is_subclass_of($scorer, PasswordScorer::class)) {
                $resolved = $this->app->make($scorer);

                if ($resolved instanceof PasswordScorer) {
                    return $resolved;
                }
            }

            return new ZxcvbnScorer;
        });

        // The word list loads its 7,776 entries once per process.
        $this->app->singletonIf(WordList::class, static fn (): WordList => WordList::bundled());

        $this->app->singleton(PasswordToolsManager::class);
    }

    public function bootingPackage(): void
    {
        $this->registerValidationBridge();
        $this->registerMeterRoute();

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [$this->configPath() => config_path('laranail-password-tools.php')],
                $this->package->getNamespacedPublishTag('config'),
            );

            $this->commands([CheckCommand::class, GenerateCommand::class]);
        }
    }

    /**
     * The one-way, guarded bridge (design §4.2): when laranail/validation
     * is installed, its password() builder node gains ->strength(). The
     * validator itself knows nothing of this package.
     */
    private function registerValidationBridge(): void
    {
        $node = PasswordRule::class;

        if (! class_exists($node)) {
            return;
        }

        $node::macro('strength', fn (?int $minScore = null): mixed => $this->rule(new StrongPassword($minScore)));
    }

    /** The opt-in live meter — disabled by default, throttled, no-store. */
    private function registerMeterRoute(): void
    {
        $meter = config('laranail.password-tools.meter');

        if (! is_array($meter) || ($meter['enabled'] ?? false) !== true) {
            return;
        }

        $path = is_string($meter['path'] ?? null) ? $meter['path'] : '/_laranail/password-tools/meter';
        $middleware = array_values(array_filter(
            is_array($meter['middleware'] ?? null) ? $meter['middleware'] : ['web'],
            is_string(...),
        ));
        $throttle = is_string($meter['throttle'] ?? null) ? $meter['throttle'] : '30,1';

        Route::post($path, MeterController::class)
            ->middleware([...$middleware, 'throttle:' . $throttle])
            ->name('laranail.password-tools.meter');
    }

    private function configPath(): string
    {
        return $this->packagePath('config/laranail-password-tools.php');
    }
}
