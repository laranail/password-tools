# Changelog

All notable changes to `laranail/password-strength` are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
- The `PasswordStrength` facade and `laranail::password-strength.check` — which takes NO
  password argument, reads through a hidden prompt, and never echoes the value.
- The flat `laranail.password-strength.*` config, `laranail-password-strength::` translations,
  a live-registry naming guard, and the guarded bridge onto `laranail/validation`'s
  `password()` builder (`->strength()` appears when the validator is installed).
