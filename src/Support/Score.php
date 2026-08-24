<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordStrength\Support;

use Illuminate\Contracts\Translation\Translator;

/**
 * A scoring verdict: the 0–4 integer plus KEY-based feedback. Keys, not
 * engine prose — the engine's English never crosses this boundary, which
 * is what makes the catalogue translatable and the engine swappable.
 */
final readonly class Score
{
    /** @param list<FeedbackKey> $suggestions */
    public function __construct(
        public int $score,
        public ?FeedbackKey $warning = null,
        public array $suggestions = [],
        public ?float $guessesLog10 = null,
    ) {}

    public function isAtLeast(int $minimum): bool
    {
        return $this->score >= $minimum;
    }

    /**
     * The feedback resolved against the translated catalogue — warning
     * first, then suggestions.
     *
     * @return list<string>
     */
    public function messages(Translator $translator): array
    {
        $keys = ! $this->warning instanceof FeedbackKey ? [] : [$this->warning];

        $messages = [];

        foreach ([...$keys, ...$this->suggestions] as $key) {
            $resolved = $translator->get($key->translationKey());

            if (is_string($resolved) && $resolved !== $key->translationKey()) {
                $messages[] = $resolved;
            }
        }

        return $messages;
    }
}
