<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Scorers;

use Simtabi\Laranail\PasswordTools\Contracts\PasswordScorer;
use Simtabi\Laranail\PasswordTools\Support\FeedbackKey;
use Simtabi\Laranail\PasswordTools\Support\Score;
use ZxcvbnPhp\Zxcvbn;

/**
 * The default scorer over bjeavons/zxcvbn-php. Its one deliberate job
 * beyond delegation is the brittle part, ISOLATED here: bjeavons emits
 * hardcoded English prose, not keys, so this class maps every string it
 * can produce onto {@see FeedbackKey}. Everything downstream is
 * key-based; a bjeavons update that adds or rewords a string fails the
 * exhaustiveness guard in CI instead of leaking English past the
 * catalogue.
 */
final class ZxcvbnScorer implements PasswordScorer
{
    /** Every warning string bjeavons can emit → the canonical key. */
    public const array WARNINGS = [
        'Straight rows of keys are easy to guess' => FeedbackKey::WarningStraightRow,
        'Short keyboard patterns are easy to guess' => FeedbackKey::WarningKeyPattern,
        'Repeats like "aaa" are easy to guess' => FeedbackKey::WarningSimpleRepeat,
        'Repeats like "abcabcabc" are only slightly harder to guess than "abc"' => FeedbackKey::WarningExtendedRepeat,
        'Sequences like abc or 6543 are easy to guess' => FeedbackKey::WarningSequences,
        'Recent years are easy to guess' => FeedbackKey::WarningRecentYears,
        'Dates are often easy to guess' => FeedbackKey::WarningDates,
        'This is a top-10 common password' => FeedbackKey::WarningTopTen,
        'This is a top-100 common password' => FeedbackKey::WarningTopHundred,
        'This is a very common password' => FeedbackKey::WarningCommon,
        'This is similar to a commonly used password' => FeedbackKey::WarningSimilarToCommon,
        'A word by itself is easy to guess' => FeedbackKey::WarningWordByItself,
        'Names and surnames by themselves are easy to guess' => FeedbackKey::WarningNamesByThemselves,
        'Common names and surnames are easy to guess' => FeedbackKey::WarningCommonNames,
    ];

    /** Every suggestion string bjeavons can emit → the canonical key. */
    public const array SUGGESTIONS = [
        "Predictable substitutions like '@' instead of 'a' don't help very much" => FeedbackKey::SuggestionL33t,
        "Reversed words aren't much harder to guess" => FeedbackKey::SuggestionReverseWords,
        'All-uppercase is almost as easy to guess as all-lowercase' => FeedbackKey::SuggestionAllUppercase,
        "Capitalization doesn't help very much" => FeedbackKey::SuggestionCapitalization,
        'Avoid dates and years that are associated with you' => FeedbackKey::SuggestionDates,
        'Avoid recent years' => FeedbackKey::SuggestionRecentYears,
        'Avoid years that are associated with you' => FeedbackKey::SuggestionAssociatedYears,
        'Avoid sequences' => FeedbackKey::SuggestionSequences,
        'Avoid repeated words and characters' => FeedbackKey::SuggestionRepeated,
        'Use a longer keyboard pattern with more turns' => FeedbackKey::SuggestionLongerKeyboardPattern,
        'Add another word or two. Uncommon words are better.' => FeedbackKey::SuggestionAnotherWord,
        'Use a few words, avoid common phrases' => FeedbackKey::SuggestionUseWords,
        'No need for symbols, digits, or uppercase letters' => FeedbackKey::SuggestionNoNeed,
    ];

    /**
     * Scoring is CPU-bound on input length; beyond this, entropy is not
     * the question anymore and the scorer answers on the prefix.
     */
    private const int MAX_SCORED_LENGTH = 4096;

    public function score(string $password, array $userInputs = []): Score
    {
        $configured = config('laranail.password-tools.user_inputs', []);
        $inputs = [
            ...array_values(array_filter(is_array($configured) ? $configured : [], is_string(...))),
            ...$userInputs,
        ];

        $result = new Zxcvbn()->passwordStrength(
            mb_substr($password, 0, self::MAX_SCORED_LENGTH),
            $inputs,
        );

        $score = $result['score'] ?? 0;
        $feedback = $result['feedback'] ?? [];
        $warning = is_array($feedback) ? ($feedback['warning'] ?? '') : '';
        $rawSuggestions = is_array($feedback) ? ($feedback['suggestions'] ?? []) : [];

        $suggestions = [];

        foreach (is_array($rawSuggestions) ? $rawSuggestions : [] as $suggestion) {
            $key = is_string($suggestion) ? (self::SUGGESTIONS[$suggestion] ?? null) : null;

            if ($key !== null && ! in_array($key, $suggestions, true)) {
                $suggestions[] = $key;
            }
        }

        $guesses = $result['guesses'] ?? null;

        return new Score(
            score: is_int($score) ? $score : 0,
            warning: is_string($warning) ? (self::WARNINGS[$warning] ?? null) : null,
            suggestions: $suggestions,
            guessesLog10: is_numeric($guesses) && (float) $guesses > 0 ? log10((float) $guesses) : null,
        );
    }
}
