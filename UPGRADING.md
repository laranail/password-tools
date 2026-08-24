# Upgrading

Breaking changes, and what to do about them. Versions not listed here need no action.

## v0.2.0 - 2026-08-24

The rename: `laranail/password-strength` → `laranail/password-tools`.

| Before | After |
|---|---|
| `laranail/password-strength` (composer) | `laranail/password-tools` |
| `Simtabi\Laranail\PasswordStrength\…` | `Simtabi\Laranail\PasswordTools\…` |
| `Facades\PasswordStrength` | `Facades\PasswordTools` |
| `config('laranail.password-strength.*')` | `config('laranail.password-tools.*')` |
| `config/laranail-password-strength.php` (published) | `config/laranail-password-tools.php` |
| `laranail-password-strength::` (translations) | `laranail-password-tools::` |
| `laranail::password-strength.check` | `laranail::password-tools.check` |
| `LARANAIL_PASSWORD_STRENGTH_MIN_SCORE` | `LARANAIL_PASSWORD_TOOLS_MIN_SCORE` |

GitHub redirects the old repository URL, but update the `vcs` entry anyway. `StrongPassword`,
`PasswordScorer`, `Score`, `FeedbackKey` and the `->strength()` macro are unchanged apart
from the namespace.

## v0.1.0 - 2026-08-24

Initial release — nothing to upgrade from.
