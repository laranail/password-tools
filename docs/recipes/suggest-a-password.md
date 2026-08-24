# Suggest a password

Offer the user a strong generated password (a "use suggested password" affordance):

```php
$suggestion = PasswordTools::password()->length(20)->withoutAmbiguous()->atLeast(4)->make();

// Or a memorable one:
$suggestion = PasswordTools::passphrase()->words(5)->capitalize()->withNumber()->make();
```

Return it once, over TLS, and never log it. Reference: [Generators](../tools/generators.md).

---

[← Docs index](../../README.md#documentation)
