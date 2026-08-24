<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Simtabi\Laranail\PasswordTools\Contracts\PasswordScorer;

/**
 * The live strength meter: POST a candidate, get the score and the
 * translated feedback. Opt-in and disabled by default; the configured
 * throttle guards it; the password is scored and DISCARDED — never
 * logged, never echoed back, never stored.
 *
 * @internal Routed, never called directly; not part of the stable API.
 */
final readonly class MeterController
{
    public function __construct(private PasswordScorer $scorer) {}

    public function __invoke(Request $request): JsonResponse
    {
        $password = $request->input('password');

        if (! is_string($password) || $password === '') {
            return new JsonResponse(['message' => 'A password field is required.'], 422);
        }

        $inputs = $request->input('user_inputs');
        $userInputs = is_array($inputs)
            ? array_values(array_filter($inputs, is_string(...)))
            : [];

        $score = $this->scorer->score($password, $userInputs);

        return new JsonResponse([
            'score' => $score->score,
            'feedback' => $score->messages(app('translator')),
            'guesses_log10' => $score->guessesLog10,
        ], 200, ['Cache-Control' => 'no-store']);
    }
}
