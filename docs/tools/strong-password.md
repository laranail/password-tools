# `StrongPassword`

```php
new StrongPassword(
    ?int $minScore = null,                    // 0–4; default from config (3)
    array $userInputs = [],                   // literal weak tokens
    string|array|null $userInputsField = null, // form field(s) whose values score weak
    ?string $message = null,
);
```

Empty/non-string values pass (pair with `required`/`string`). On failure the first message is
the translated headline, followed by the engine's warning and suggestions resolved through
the catalogue — never raw engine prose, never the password.

---

[← Docs index](../../README.md#documentation)
