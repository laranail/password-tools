# Credits

## The scoring engine

- [`bjeavons/zxcvbn-php`](https://github.com/bjeavons/zxcvbn-php) (MIT) — the PHP port of
  zxcvbn this package's default scorer wraps. Its bundled frequency lists and matchers do the
  actual estimation; this package contributes the contract, the key-based feedback pipeline,
  and the Laravel surfaces.
- [zxcvbn](https://github.com/dropbox/zxcvbn) (MIT, Dropbox) — the original algorithm and the
  research behind it (Wheeler, USENIX Security 2016).
- The canonical `FeedbackKey` vocabulary follows the maintained
  [`zxcvbn-ts`](https://github.com/zxcvbn-ts/zxcvbn) `en` language pack's key set — a stable
  key basis, unlike prose.

## The passphrase wordlist

- [EFF's large wordlist for random passphrases](https://www.eff.org/deeplinks/2018/08/dice-roll-your-passwords)
  (`eff_large_wordlist.txt`, CC-BY 3.0, © Electronic Frontier Foundation) — bundled at
  `resources/data/eff-large-wordlist.txt` (dice indices stripped, words unchanged). 7,776
  words chosen by the EFF for memorability and edit distance.

## Prior art

- [`ziming/laravel-zxcvbn`](https://github.com/ziming/laravel-zxcvbn) — the closest existing
  Laravel wrapper, studied not copied; this package exists for the contract, the translated
  catalogue, and the laranail `password()` bridge.
- The archived `enekia` translation block (Simtabi's own) established the "~20 translated
  suggestions" idea, redesigned here as a keyed catalogue instead of string matching.
