# Contribute a locale

Copy the English catalogue and translate every sentence, keeping the keys exactly:

```bash
cp resources/lang/en/messages.php resources/lang/de/messages.php
```

The suite asserts every `FeedbackKey` case resolves — a partial translation is a red build,
not silent English fallback. Applications overriding a few sentences publish instead:

```bash
php artisan vendor:publish --tag=laranail::password-strength-translations
```

---

[← Docs index](../../README.md#documentation)
