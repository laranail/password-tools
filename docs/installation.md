# Installation

## Requirements

| Dimension | Supported |
|---|---|
| PHP | `^8.4.1 \|\| ^8.5` |
| Laravel | `^13.0` |
| Engine | `bjeavons/zxcvbn-php ^1.4` (bundled dictionary ≈1 MB in memory per process — the reason this is its own package) |

## Install

laranail packages resolve through git VCS repositories, not Packagist:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/laranail/password-tools" },
        { "type": "vcs", "url": "https://github.com/laranail/package-tools" },
        { "type": "vcs", "url": "https://github.com/laranail/console" }
    ]
}
```

```bash
composer require laranail/password-tools
php artisan vendor:publish --tag=laranail::password-tools-config   # optional
```

---

[← Docs index](../README.md#documentation)
