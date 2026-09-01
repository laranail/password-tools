<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Support;

/**
 * The canonical feedback vocabulary — the stable key set the translated
 * catalogue is written against. Based on the maintained zxcvbn-ts `en`
 * language pack, NOT on any engine's prose: engines emit strings, this
 * enum is what survives an engine swap.
 *
 * A few cases (`UserInputs`, `SimilarToCommon` on some engines) may never
 * fire from a given scorer; the catalogue still defines every case so a
 * future scorer cannot leak an untranslated key.
 */
enum FeedbackKey: string
{
    // Warnings — why the password is weak.
    case WarningStraightRow = 'warning.straightRow';
    case WarningKeyPattern = 'warning.keyPattern';
    case WarningSimpleRepeat = 'warning.simpleRepeat';
    case WarningExtendedRepeat = 'warning.extendedRepeat';
    case WarningSequences = 'warning.sequences';
    case WarningRecentYears = 'warning.recentYears';
    case WarningDates = 'warning.dates';
    case WarningTopTen = 'warning.topTen';
    case WarningTopHundred = 'warning.topHundred';
    case WarningCommon = 'warning.common';
    case WarningSimilarToCommon = 'warning.similarToCommon';
    case WarningWordByItself = 'warning.wordByItself';
    case WarningNamesByThemselves = 'warning.namesByThemselves';
    case WarningCommonNames = 'warning.commonNames';
    case WarningUserInputs = 'warning.userInputs';

    // Suggestions — what would make it stronger.
    case SuggestionL33t = 'suggestion.l33t';
    case SuggestionReverseWords = 'suggestion.reverseWords';
    case SuggestionAllUppercase = 'suggestion.allUppercase';
    case SuggestionCapitalization = 'suggestion.capitalization';
    case SuggestionDates = 'suggestion.dates';
    case SuggestionRecentYears = 'suggestion.recentYears';
    case SuggestionAssociatedYears = 'suggestion.associatedYears';
    case SuggestionSequences = 'suggestion.sequences';
    case SuggestionRepeated = 'suggestion.repeated';
    case SuggestionLongerKeyboardPattern = 'suggestion.longerKeyboardPattern';
    case SuggestionAnotherWord = 'suggestion.anotherWord';
    case SuggestionUseWords = 'suggestion.useWords';
    case SuggestionNoNeed = 'suggestion.noNeed';

    /** The translation key in the shipped catalogue. */
    public function translationKey(): string
    {
        return 'laranail/password-tools::messages.'.$this->value;
    }
}
