<?php

declare(strict_types=1);

/*
 * Namespaced `laranail/password-tools::` — the feedback catalogue.
 * Keys mirror the FeedbackKey enum values; a guard asserts every case
 * resolves, so no engine update can leak an untranslated key.
 */
return [
    'weak'                             => 'The :attribute is too easy to guess. Make it longer and less predictable.',
    'warning.straightRow'              => 'Straight rows of keys are easy to guess.',
    'warning.keyPattern'               => 'Short keyboard patterns are easy to guess.',
    'warning.simpleRepeat'             => 'Repeated characters like "aaa" are easy to guess.',
    'warning.extendedRepeat'           => 'Repeated patterns like "abcabcabc" are only slightly harder to guess than "abc".',
    'warning.sequences'                => 'Common sequences like "abc" or "6543" are easy to guess.',
    'warning.recentYears'              => 'Recent years are easy to guess.',
    'warning.dates'                    => 'Dates are often easy to guess.',
    'warning.topTen'                   => 'This is one of the ten most used passwords.',
    'warning.topHundred'               => 'This is one of the hundred most used passwords.',
    'warning.common'                   => 'This is a very commonly used password.',
    'warning.similarToCommon'          => 'This is similar to a commonly used password.',
    'warning.wordByItself'             => 'Single words are easy to guess.',
    'warning.namesByThemselves'        => 'Single names or surnames are easy to guess.',
    'warning.commonNames'              => 'Common names and surnames are easy to guess.',
    'warning.userInputs'               => 'There should not be personal or page-related data in the password.',
    'suggestion.l33t'                  => 'Avoid predictable letter substitutions like \'@\' for \'a\'.',
    'suggestion.reverseWords'          => 'Avoid reversed spellings of common words.',
    'suggestion.allUppercase'          => 'Capitalize some letters, but not all of them.',
    'suggestion.capitalization'        => 'Capitalize more than the first letter.',
    'suggestion.dates'                 => 'Avoid dates and years that are associated with you.',
    'suggestion.recentYears'           => 'Avoid recent years.',
    'suggestion.associatedYears'       => 'Avoid years that are associated with you.',
    'suggestion.sequences'             => 'Avoid common character sequences.',
    'suggestion.repeated'              => 'Avoid repeated words and characters.',
    'suggestion.longerKeyboardPattern' => 'Use longer keyboard patterns and change typing direction multiple times.',
    'suggestion.anotherWord'           => 'Add another word or two. Uncommon words are better.',
    'suggestion.useWords'              => 'Use multiple words, but avoid common phrases.',
    'suggestion.noNeed'                => 'Strong passwords are possible without symbols, digits, or uppercase letters.',
];
