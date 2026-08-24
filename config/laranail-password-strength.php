<?php

declare(strict_types=1);
use Simtabi\Laranail\PasswordStrength\Scorers\ZxcvbnScorer;

/*
 * Read under the flat `laranail.password-strength.*` key, per the org
 * config convention; the file is prefixed so `vendor:publish` cannot
 * clobber an application's own config.
 */
return [

    // The default floor for StrongPassword when none is passed.
    // 0–4; 3 = "safely unguessable" in zxcvbn's scale.
    'min_score' => (int) env('LARANAIL_PASSWORD_STRENGTH_MIN_SCORE', 3),

    // The PasswordScorer binding — swap the engine here.
    'scorer' => ZxcvbnScorer::class,

    /*
     * Global tokens that should always score WEAK — the application's
     * name, brand, and domain belong here. Per-form tokens (the user's
     * own email) ride in through the rule's userInputsField instead.
     */
    'user_inputs' => [],
];
