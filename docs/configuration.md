# Configuration

All keys live under the flat `laranail.password-tools.*`.

| Key | Default | Meaning |
|---|---|---|
| `min_score` | `3` | Default floor when the rule gets none (`LARANAIL_PASSWORD_TOOLS_MIN_SCORE`) |
| `scorer` | `ZxcvbnScorer::class` | The `PasswordScorer` binding — swap the engine |
| `user_inputs` | `[]` | Global weak tokens: the app's name, brand, domain |

| `meter.enabled` | `false` | The opt-in live meter endpoint |
| `meter.path` | `/_laranail/password-tools/meter` | Its route |
| `meter.middleware` | `['web']` | Wrapped around it — add your auth |
| `meter.throttle` | `'30,1'` | Probing guard |

Per-form weak tokens (the user's own email) ride through the rule's `userInputsField`
instead — global config is for values every form shares.

---

[← Docs index](../README.md#documentation)
