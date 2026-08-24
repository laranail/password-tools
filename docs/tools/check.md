# Console commands

## `laranail::password-tools.check`

```
php artisan laranail::password-tools.check
```

Scores a password with translated feedback. Deliberately takes NO argument: the value is read
through a hidden prompt, so it never lands in shell history or `ps` output, and it is never
echoed back.

## `laranail::password-tools.generate`

```
php artisan laranail::password-tools.generate --length=20 --symbols --count=3
php artisan laranail::password-tools.generate --passphrase --words=5
```

Prints generated passwords or capitalized diceware passphrases (with the recipe's entropy).
Printing IS the point here — unlike the check command's input, a value this command emits was
never a secret before it existed.

---

[← Docs index](../../README.md#documentation)
