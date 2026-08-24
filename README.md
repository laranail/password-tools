# laranail/password-tools

[![Latest version on Packagist](https://img.shields.io/packagist/v/laranail/password-tools.svg)](https://packagist.org/packages/laranail/password-tools)
[![Tests](https://github.com/laranail/password-tools/actions/workflows/run-tests.yml/badge.svg)](https://github.com/laranail/password-tools/actions/workflows/run-tests.yml)
[![Static analysis](https://github.com/laranail/password-tools/actions/workflows/phpstan.yml/badge.svg)](https://github.com/laranail/password-tools/actions/workflows/phpstan.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

> Password tooling for Laravel: zxcvbn strength scoring with a translated feedback catalogue, fluent CSPRNG password and diceware passphrase generators, a validation rule, and an opt-in live strength-meter endpoint.

Targets PHP `^8.4.1 || ^8.5` on Laravel `^13`. The scoring engine (`bjeavons/zxcvbn-php`) sits behind a contract; generation is `random_int()` throughout.

## Install

```bash
composer require laranail/password-tools
```

laranail packages resolve through git VCS repositories — see [Installation](docs/installation.md).

## Quick start

**Score** — the rule and the service:

```php
use Simtabi\Laranail\PasswordTools\Rules\StrongPassword;

$request->validate([
    // min:12 stays: strength COMPLEMENTS a length floor, never replaces it.
    'password' => ['required', 'string', 'min:12', 'confirmed',
        new StrongPassword(minScore: 3, userInputsField: 'email')],
]);
```

**Generate** — fluent, cryptographically secure, every enabled class guaranteed present:

```php
use Simtabi\Laranail\PasswordTools\Facades\PasswordTools;

PasswordTools::password()->length(20)->symbols()->withoutAmbiguous()->make();
// → 20 random characters, every enabled class guaranteed present

PasswordTools::passphrase()->words(5)->capitalize()->withNumber()->make();
// "Copier-Sandpaper-Anthem7-Grievance-Overcast"   (~65 bits)

PasswordTools::password()->atLeast(3)->make();   // regenerate until the scorer agrees
PasswordTools::score($candidate);                // Score { 0–4, warning, suggestions }
```

Failures and feedback surface **translated catalogue sentences** (engine → key enum →
locale), never the engine's raw English, and never the password.

With [`laranail/validation`](https://github.com/laranail/validation) installed:

```php
FluentRule::password()->min(12)->uncompromised()->strength(3)->notReused();
```

## <a name="documentation"></a>Documentation

### Guides

- [Installation](docs/installation.md) — requirements, VCS repositories
- [Getting started](docs/getting-started.md) — scoring, generating, the bridge
- [Configuration](docs/configuration.md) — the floor, the scorer binding, the meter
- [Architecture](docs/architecture.md) — why keys not strings, the CSPRNG guarantees, the DoS cap
- [Release](docs/release.md) — versioning and tags

### Reference

- [`StrongPassword`](docs/tools/strong-password.md) — the rule and its user-inputs plumbing
- [Generators](docs/tools/generators.md) — the fluent password and passphrase builders
- [The scorer contract](docs/tools/scorer.md) — `PasswordScorer`, `Score`, `FeedbackKey`, swapping engines
- [The meter endpoint](docs/tools/meter.md) — the opt-in live strength meter
- [Console commands](docs/tools/check.md) — `check` (prompting, never echoes) and `generate`

### Recipes

- [Signup form](docs/recipes/signup.md) · [Suggest a password](docs/recipes/suggest-a-password.md) · [A custom scorer](docs/recipes/custom-scorer.md) · [Contribute a locale](docs/recipes/contribute-a-locale.md)

## Sister packages

- [`laranail/password-history`](https://github.com/laranail/password-history) — reuse prevention for the same `password()` chain
- [`laranail/validation`](https://github.com/laranail/validation) — the fluent rule builder both compose into

## Contributing & security

See [CONTRIBUTING.md](CONTRIBUTING.md); report vulnerabilities per [SECURITY.md](SECURITY.md).

## License

MIT © Simtabi LLC. See [LICENSE](LICENSE) and [CREDITS.md](CREDITS.md).
