<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Generators;

use InvalidArgumentException;
use Simtabi\Laranail\PasswordTools\Contracts\PasswordScorer;

/**
 * Fluent, cryptographically secure password generation. Every random
 * draw is `random_int()` (CSPRNG); every ENABLED character class is
 * guaranteed to appear at least once, and the guarantee positions are
 * shuffled with secure draws too — a generator that always puts the
 * digit last teaches attackers where the digit is.
 *
 * ```php
 * PasswordTools::password()->length(20)->symbols()->withoutAmbiguous()->make();
 * PasswordTools::password()->atLeast(3)->make();   // regenerate until the scorer agrees
 * ```
 */
final class PasswordBuilder
{
    private const string LOWER = 'abcdefghijklmnopqrstuvwxyz';

    private const string UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private const string DIGITS = '0123456789';

    private const string SYMBOLS = '!@#$%^&*()-_=+[]{};:,.<>?';

    /** Glyphs that read as each other in most fonts. */
    private const string AMBIGUOUS = '0O1lI|`\'"S5B8Z2';

    private int $length = 16;

    private bool $lower = true;

    private bool $upper = true;

    private bool $digits = true;

    private bool $symbols = false;

    private bool $dropAmbiguous = false;

    private ?int $minScore = null;

    public function __construct(private readonly ?PasswordScorer $scorer = null) {}

    public function length(int $characters): self
    {
        if ($characters < 4 || $characters > 1024) {
            throw new InvalidArgumentException('Password length must be between 4 and 1024.');
        }

        $this->length = $characters;

        return $this;
    }

    public function letters(bool $lower = true, bool $upper = true): self
    {
        $this->lower = $lower;
        $this->upper = $upper;

        return $this;
    }

    public function numbers(bool $enabled = true): self
    {
        $this->digits = $enabled;

        return $this;
    }

    public function symbols(bool $enabled = true): self
    {
        $this->symbols = $enabled;

        return $this;
    }

    /** Drop glyphs that read as each other (0/O, 1/l/I, …) — for passwords a human will transcribe. */
    public function withoutAmbiguous(bool $enabled = true): self
    {
        $this->dropAmbiguous = $enabled;

        return $this;
    }

    /**
     * Regenerate until the scorer rates the result at least this (0–4).
     * Needs a scorer — the container-built builder has one; a bare
     * `new PasswordBuilder()` does not and refuses loudly.
     */
    public function atLeast(int $minScore): self
    {
        if ($minScore < 0 || $minScore > 4) {
            throw new InvalidArgumentException('Scores run 0–4.');
        }

        if (! $this->scorer instanceof PasswordScorer) {
            throw new InvalidArgumentException('atLeast() needs a PasswordScorer — resolve the builder from the container.');
        }

        $this->minScore = $minScore;

        return $this;
    }

    public function make(): string
    {
        // A 16+ char CSPRNG password over 2+ classes essentially always
        // scores 4; the loop exists for tight lengths, and the cap keeps a
        // contradictory recipe (length(4)->atLeast(4)) from spinning.
        foreach (range(1, 16) as $attempt) {
            $password = $this->generate();

            if ($this->minScore === null || ! $this->scorer instanceof PasswordScorer) {
                return $password;
            }

            if ($this->scorer->score($password)->isAtLeast($this->minScore)) {
                return $password;
            }
        }

        throw new InvalidArgumentException(
            "No {$this->length}-character password from this recipe reached score {$this->minScore} in 16 attempts — lengthen it.",
        );
    }

    /** @return list<string> */
    public function makeMany(int $count): array
    {
        if ($count < 1 || $count > 1000) {
            throw new InvalidArgumentException('Count must be between 1 and 1000.');
        }

        return array_map($this->make(...), range(1, $count));
    }

    private function generate(): string
    {
        $classes = $this->enabledClasses();

        if ($classes === []) {
            throw new InvalidArgumentException('Every character class is disabled — nothing to generate from.');
        }

        if ($this->length < count($classes)) {
            throw new InvalidArgumentException(
                'Length '.$this->length.' cannot include all '.count($classes).' enabled character classes.',
            );
        }

        $pool = implode('', $classes);

        // One guaranteed draw per class, the rest from the full pool…
        $characters = array_map(
            static fn (string $class): string => $class[random_int(0, strlen($class) - 1)],
            $classes,
        );

        while (count($characters) < $this->length) {
            $characters[] = $pool[random_int(0, strlen($pool) - 1)];
        }

        // …then a Fisher–Yates with secure draws, so the guaranteed
        // characters are not positionally predictable.
        for ($i = count($characters) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$characters[$i], $characters[$j]] = [$characters[$j], $characters[$i]];
        }

        return implode('', $characters);
    }

    /** @return list<string> */
    private function enabledClasses(): array
    {
        $classes = [];

        foreach ([
            [$this->lower, self::LOWER],
            [$this->upper, self::UPPER],
            [$this->digits, self::DIGITS],
            [$this->symbols, self::SYMBOLS],
        ] as [$enabled, $alphabet]) {
            if (! $enabled) {
                continue;
            }

            if ($this->dropAmbiguous) {
                $alphabet = implode('', array_diff(str_split($alphabet), str_split(self::AMBIGUOUS)));
            }

            if ($alphabet !== '') {
                $classes[] = $alphabet;
            }
        }

        return $classes;
    }
}
