# Getting started

## The rule

```php
use Simtabi\Laranail\PasswordStrength\Rules\StrongPassword;

$request->validate([
    'password' => ['required', 'string', 'min:12', 'confirmed',
        new StrongPassword(minScore: 3, userInputsField: ['email', 'name'])],
]);
```

Keep the length floor: zxcvbn can score a short password highly and a long passphrase
modestly — `min(12)` and `strength(3)` answer different questions, and NIST 800-63B still
wants the length.

## The service

```php
use Simtabi\Laranail\PasswordStrength\Contracts\PasswordScorer;

$score = app(PasswordScorer::class)->score($candidate, [$user->email]);
$score->score;          // 0–4
$score->isAtLeast(3);
$score->messages(app('translator'));   // translated feedback sentences
```

## With laranail/validation

```php
FluentRule::password()->min(12)->uncompromised()->strength(3);
```

The `->strength()` macro appears when this package is installed; the validator knows nothing
of it.

---

[← Docs index](../README.md#documentation)
