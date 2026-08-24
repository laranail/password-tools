# The scorer contract

```php
interface PasswordScorer
{
    /** @param list<string> $userInputs */
    public function score(string $password, array $userInputs = []): Score;
}
```

`Score` is `final readonly`: `int $score` (0–4), `?FeedbackKey $warning`,
`list<FeedbackKey> $suggestions`, `?float $guessesLog10`, with `isAtLeast(int)` and
`messages(Translator)`. `FeedbackKey` is the canonical vocabulary (zxcvbn-ts key set); some
cases never fire from a given engine and the catalogue defines them anyway.

Swap the engine via config (`scorer`) or a container binding — implementations must never
log, cache, or echo the password.

---

[← Docs index](../../README.md#documentation)
