<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordStrength\Contracts;

use Simtabi\Laranail\PasswordStrength\Scorers\ZxcvbnScorer;
use Simtabi\Laranail\PasswordStrength\Support\Score;

/**
 * The injectable seam: the rule, the facade and the check command all
 * speak this, so the scoring engine is an implementation detail. Resolve
 * it from the container to score outside validation — a strength-meter
 * endpoint, an admin audit.
 *
 * Implementations MUST never log, cache or echo the password.
 *
 * @see ZxcvbnScorer the default
 */
interface PasswordScorer
{
    /** @param list<string> $userInputs tokens that should score WEAK (the user's own email, name, the app's brand) */
    public function score(string $password, array $userInputs = []): Score;
}
