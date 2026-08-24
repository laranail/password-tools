# A signup form

```php
$request->validate([
    'email' => ['required', 'email'],
    'password' => ['required', 'string', 'min:12', 'confirmed',
        new StrongPassword(userInputsField: 'email')],
]);
```

History has no role on signup (there is no user yet); strength carries first-password
quality. Reference: [`StrongPassword`](../tools/strong-password.md).

---

[← Docs index](../../README.md#documentation)
