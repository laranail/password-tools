# Changelog

All notable changes to `laranail/password-tools` are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v0.2.0 - 2026-08-24

The package outgrows its first name: `laranail/password-strength` becomes
**`laranail/password-tools`** — one home for scoring, generation, and the meter, not one rule.

### Changed

- **Breaking (rename).** Composer name `laranail/password-tools`; namespace
  `Simtabi\Laranail\PasswordTools`; config `laranail.password-tools.*` in
  `config/laranail-password-tools.php`; translations `laranail-password-tools::`; commands
  `laranail::password-tools.*`; the facade is `PasswordTools` (accessor: the new
  `PasswordToolsManager`). GitHub redirects the old repo URL; class and key names do not
  redirect — a pre-rename install is a find-replace (`PasswordStrength` →
  `PasswordTools`, `password-strength` → `password-tools`).

### Added

- **Fluent password generator** — `PasswordTools::password()`: CSPRNG throughout, every
  enabled character class guaranteed present with securely-shuffled placement,
  `->withoutAmbiguous()` for transcribable secrets, `->atLeast(score)` regeneration against
  the scorer, and loud refusal of contradictory recipes.
- **Fluent passphrase generator** — `PasswordTools::passphrase()`: diceware over the bundled
  EFF large wordlist (7,776 words, ≈12.9 bits/word, attributed in CREDITS), with
  `->capitalize()`, `->withNumber()` (digit on a securely-chosen word), injectable word lists
  (≥1,024 words enforced), and `->bits()` so a recipe knows its own entropy.
- **The meter endpoint** — opt-in, disabled by default: POST a candidate, get the score and
  translated feedback; scored and discarded (no logging, no echo, `no-store`), behind a
  configurable throttle.
- `laranail::password-tools.generate` — passwords or passphrases from the terminal.

## v0.1.0 - 2026-08-24

Initial release.

### Added

- `Rules\StrongPassword` — fails below a 0–4 floor (default 3, config-overridable), with
  `userInputsField` feeding the form's own values to the engine as weak tokens through
  `DataAwareRule`. Failures surface translated catalogue sentences — never the engine's raw
  English, never the password.
- `Contracts\PasswordScorer` + `Support\Score` + the `FeedbackKey` enum: the engine → key →
  locale pipeline. Two CI guards keep it honest — every enum case resolves in every shipped
  locale, and every string the vendored engine can emit is mapped (a bjeavons update that
  rewords feedback fails CI instead of leaking English).
- `Scorers\ZxcvbnScorer` over `bjeavons/zxcvbn-php ^1.4`, with the scored input capped at
  4096 characters (scoring is CPU-bound on length; beyond that, entropy is not the question).
- The `PasswordTools` facade (né `PasswordStrength`) and `laranail::password-tools.check` — which takes NO
  password argument, reads through a hidden prompt, and never echoes the value.
- The flat `laranail.password-tools.*` config, `laranail-password-tools::` translations,
  a live-registry naming guard, and the guarded bridge onto `laranail/validation`'s
  `password()` builder (`->strength()` appears when the validator is installed).
