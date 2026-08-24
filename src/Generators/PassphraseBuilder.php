<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Generators;

use InvalidArgumentException;
use Simtabi\Laranail\PasswordTools\Support\WordList;

/**
 * Fluent diceware-style passphrases over the bundled EFF large wordlist
 * (≈12.9 bits/word) or any injected list. Word selection is
 * `random_int()`; the optional digit lands on a securely-drawn word, not
 * always the last one.
 *
 * ```php
 * PasswordTools::passphrase()->words(5)->capitalize()->withNumber()->make();
 * // e.g. "Copier-Sandpaper-Anthem7-Grievance-Overcast"
 * ```
 */
final class PassphraseBuilder
{
    private int $words = 5;

    private string $separator = '-';

    private bool $capitalize = false;

    private bool $withNumber = false;

    private WordList $list;

    public function __construct(?WordList $list = null)
    {
        $this->list = $list ?? WordList::bundled();
    }

    public function words(int $count): self
    {
        if ($count < 3 || $count > 32) {
            // Below 3 EFF words (~39 bits) a passphrase is a bad password.
            throw new InvalidArgumentException('A passphrase needs between 3 and 32 words.');
        }

        $this->words = $count;

        return $this;
    }

    public function separator(string $separator): self
    {
        if (mb_strlen($separator) > 3) {
            throw new InvalidArgumentException('Separators longer than 3 characters are noise.');
        }

        $this->separator = $separator;

        return $this;
    }

    /** Title-case each word. */
    public function capitalize(bool $enabled = true): self
    {
        $this->capitalize = $enabled;

        return $this;
    }

    /** Append a random digit to one securely-chosen word — the diceware convention for digit-requiring policies. */
    public function withNumber(bool $enabled = true): self
    {
        $this->withNumber = $enabled;

        return $this;
    }

    public function wordList(WordList $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function make(): string
    {
        $words = array_map($this->list->random(...), range(1, $this->words));

        if ($this->capitalize) {
            $words = array_map(ucfirst(...), $words);
        }

        if ($this->withNumber) {
            $at = random_int(0, count($words) - 1);
            $words[$at] .= (string) random_int(0, 9);
        }

        return implode($this->separator, $words);
    }

    /** @return list<string> */
    public function makeMany(int $count): array
    {
        if ($count < 1 || $count > 1000) {
            throw new InvalidArgumentException('Count must be between 1 and 1000.');
        }

        return array_map($this->make(...), range(1, $count));
    }

    /** The recipe's entropy in bits (words only — the digit adds ~3.3). */
    public function bits(): float
    {
        return $this->words * $this->list->bitsPerWord();
    }
}
