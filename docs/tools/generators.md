# Generators

Two fluent builders, both `random_int()` (CSPRNG) throughout, handed out fresh per call from
the `PasswordTools` facade — a builder is a recipe, and recipes must not leak between call
sites.

## `password()`

```php
PasswordTools::password()
    ->length(20)              // 4–1024, default 16
    ->letters(lower: true, upper: true)
    ->numbers()               // default on
    ->symbols()               // default off
    ->withoutAmbiguous()      // drop 0/O, 1/l/I, … for human transcription
    ->atLeast(3)              // regenerate until the scorer agrees (0–4)
    ->make();                 // or ->makeMany(10)
```

Two guarantees: every ENABLED class appears at least once, and the guaranteed characters are
placed by a secure Fisher–Yates — a generator that always puts the digit last teaches
attackers where the digit is. Contradictory recipes (`length(3)`, all classes off,
`length(4)->atLeast(4)` that never converges) throw instead of degrading.

## `passphrase()`

```php
PasswordTools::passphrase()
    ->words(5)                // 3–32, default 5 (~64.6 bits on the EFF list)
    ->separator('-')
    ->capitalize()
    ->withNumber()            // a digit on a securely-chosen word
    ->wordList($custom)       // any WordList of ≥1024 distinct words
    ->make();

PasswordTools::passphrase()->words(6)->bits();   // 77.5 — know your entropy
```

The bundled list is the EFF large wordlist — 7,776 words, the diceware shape, ≈12.9
bits/word (see CREDITS.md). A custom `WordList` below 1,024 words is refused: under ~10
bits/word a passphrase stops being a passphrase.

---

[← Docs index](../../README.md#documentation)
