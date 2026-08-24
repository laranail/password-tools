# A custom scorer

Wrap any engine — the rule and catalogue follow the contract.

```php
final class EntropyScorer implements PasswordScorer
{
    public function score(string $password, array $userInputs = []): Score
    {
        return new Score(score: min(4, (int) (estimateBits($password) / 24)));
    }
}

// config/laranail-password-strength.php
'scorer' => EntropyScorer::class,
```

Reference: [the scorer contract](../tools/scorer.md).

---

[← Docs index](../../README.md#documentation)
