<?php

declare(strict_types=1);
use Simtabi\Laranail\PasswordTools\Scorers\ZxcvbnScorer;

/*
 * Read under the flat `laranail.password-tools.*` key, per the org
 * config convention; the file is prefixed so `vendor:publish` cannot
 * clobber an application's own config.
 */
return [

    // The default floor for StrongPassword when none is passed.
    // 0–4; 3 = "safely unguessable" in zxcvbn's scale.
    'min_score' => (int) env('LARANAIL_PASSWORD_TOOLS_MIN_SCORE', 3),

    // The PasswordScorer binding — swap the engine here.
    'scorer' => ZxcvbnScorer::class,

    /*
     * Global tokens that should always score WEAK — the application's
     * name, brand, and domain belong here. Per-form tokens (the user's
     * own email) ride in through the rule's userInputsField instead.
     */
    'user_inputs' => [],

    /*
     * The opt-in live strength meter (POST {password} → score +
     * translated feedback). Disabled by default; the candidate is scored
     * and DISCARDED — never logged, stored, or echoed back.
     */
    'meter' => [
        'enabled' => false,
        'path' => '/_laranail/password-tools/meter',
        'middleware' => ['web'],
        'throttle' => '30,1',
    ],
];
