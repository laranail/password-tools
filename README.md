# laranail/password-strength

[![Latest version on Packagist](https://img.shields.io/packagist/v/laranail/password-strength.svg)](https://packagist.org/packages/laranail/password-strength)
[![Tests](https://github.com/laranail/password-strength/actions/workflows/run-tests.yml/badge.svg)](https://github.com/laranail/password-strength/actions/workflows/run-tests.yml)
[![Static analysis](https://github.com/laranail/password-strength/actions/workflows/phpstan.yml/badge.svg)](https://github.com/laranail/password-strength/actions/workflows/phpstan.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

> zxcvbn-style password strength scoring (0–4) with a translated feedback catalogue — a validation rule and a swappable scorer service.

Targets PHP `^8.4.1 || ^8.5` on Laravel `^13`. The engine is `bjeavons/zxcvbn-php` behind a contract, so it is an implementation detail.

## Install

```bash
composer require laranail/password-strength
```

laranail packages resolve through git VCS repositories — see [Installation](docs/installation.md).

## Quick start

```php
use Simtabi\Laranail\PasswordStrength\Rules\StrongPassword;

$request->validate([
    // min:12 stays: strength COMPLEMENTS a length floor, never replaces it.
    'password' => ['required', 'string', 'min:12', 'confirmed',
        new StrongPassword(minScore: 3, userInputsField: 'email')],
]);
```

`userInputsField` feeds the form's own values to the engine as weak tokens — so
`john@acme.com` scores terribly as a password on the form that collects it. Failures surface
**translated catalogue sentences** (engine → key enum → locale), never the engine's raw
English, and never the password.

```php
use Simtabi\Laranail\PasswordStrength\Facades\PasswordStrength;

PasswordStrength::score($candidate);   // Score { score: 0–4, warning, suggestions, guessesLog10 }
```

With [`laranail/validation`](https://github.com/laranail/validation) installed:

```php
FluentRule::password()->min(12)->uncompromised()->strength(3)->notReused();
```

## <a name="documentation"></a>Documentation

### Guides

- [Installation](docs/installation.md) — requirements, VCS repositories
- [Getting started](docs/getting-started.md) — the rule, the service, the bridge
- [Configuration](docs/configuration.md) — the floor, the scorer binding, global weak tokens
- [Architecture](docs/architecture.md) — why keys not strings, why a contract, the DoS cap
- [Release](docs/release.md) — versioning and tags

### Reference

- [`StrongPassword`](docs/tools/strong-password.md) — the rule and its user-inputs plumbing
- [The scorer contract](docs/tools/scorer.md) — `PasswordScorer`, `Score`, `FeedbackKey`, swapping engines
- [`laranail::password-strength.check`](docs/tools/check.md) — the prompting dev scorer

### Recipes

- [Signup form](docs/recipes/signup.md) · [A custom scorer](docs/recipes/custom-scorer.md) · [Contribute a locale](docs/recipes/contribute-a-locale.md)

## Sister packages

- [`laranail/password-history`](https://github.com/laranail/password-history) — reuse prevention for the same `password()` chain
- [`laranail/validation`](https://github.com/laranail/validation) — the fluent rule builder both compose into

## Contributing & security

See [CONTRIBUTING.md](CONTRIBUTING.md); report vulnerabilities per [SECURITY.md](SECURITY.md).

## License

MIT © Simtabi LLC. See [LICENSE](LICENSE) and [CREDITS.md](CREDITS.md).
