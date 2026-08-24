# The meter endpoint

An opt-in live strength meter: POST a candidate, get the score and translated feedback.
Disabled by default.

```php
// config/laranail-password-tools.php
'meter' => [
    'enabled' => true,
    'path' => '/_laranail/password-tools/meter',
    'middleware' => ['web'],
    'throttle' => '30,1',
],
```

```
POST /_laranail/password-tools/meter
{ "password": "candidate", "user_inputs": ["john@acme.com"] }

200 { "score": 1, "feedback": ["Add another word or two. …"], "guesses_log10": 5.1 }
```

The candidate is scored and DISCARDED — never logged, stored, or echoed back; the response
carries `Cache-Control: no-store`. The configured throttle guards probing. Over TLS this is
the same exposure as submitting the form; a same-page JS estimator that never leaves the
browser remains future scope.

---

[← Docs index](../../README.md#documentation)
