<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Support;

use InvalidArgumentException;

/**
 * The passphrase vocabulary. The bundled default is the EFF large
 * wordlist — 7,776 words (6^5, the diceware shape), chosen by the EFF
 * for memorability and edit distance, ≈12.9 bits of entropy per word.
 * See CREDITS.md for attribution.
 */
final readonly class WordList
{
    /** @var list<string> */
    private array $words;

    /** @param list<string> $words */
    public function __construct(array $words)
    {
        $cleaned = array_values(array_unique(array_filter($words, static fn (string $w): bool => $w !== '')));

        if (count($cleaned) < 1024) {
            // Below ~10 bits/word a passphrase stops being a passphrase.
            throw new InvalidArgumentException('A word list needs at least 1024 distinct words.');
        }

        $this->words = $cleaned;
    }

    public static function bundled(): self
    {
        $path = dirname(__DIR__, 2).'/resources/data/eff-large-wordlist.txt';
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidArgumentException("The bundled word list is missing at {$path}.");
        }

        return new self(explode("\n", trim($contents)));
    }

    public function random(): string
    {
        return $this->words[random_int(0, count($this->words) - 1)];
    }

    public function count(): int
    {
        return count($this->words);
    }

    /** Entropy per word, in bits. */
    public function bitsPerWord(): float
    {
        return log(count($this->words), 2);
    }
}
