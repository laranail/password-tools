<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use Simtabi\Laranail\PasswordTools\Support\Score;
use Simtabi\Laranail\PasswordTools\Support\FeedbackKey;
use Simtabi\Laranail\PasswordTools\Rules\StrongPassword;
use Simtabi\Laranail\PasswordTools\Scorers\ZxcvbnScorer;
use Simtabi\Laranail\PasswordTools\Facades\PasswordTools;
use Simtabi\Laranail\PasswordTools\Contracts\PasswordScorer;

/** @param array<string, mixed> $data */
function strongPasses(StrongPassword $rule, string $candidate, array $data = []): bool
{
    return Validator::make(
        ['password' => $candidate, ...$data],
        ['password' => [$rule]],
    )->passes();
}

// =========================================================================
// Score boundaries
// =========================================================================

it('scores a top-100 password 0 and a long random passphrase at least 3', function (): void {
    $scorer = app(PasswordScorer::class);

    expect($scorer->score('password')->score)->toBe(0)
        ->and($scorer->score('correct horse battery staple violet umbrella')->score)
        ->toBeGreaterThanOrEqual(3);
});

it('fails below the floor and passes at it, for every floor', function (): void {
    foreach (range(0, 4) as $floor) {
        $weak = strongPasses(new StrongPassword(minScore: $floor), 'password');
        $strong = strongPasses(new StrongPassword(minScore: $floor), 'correct horse battery staple violet umbrella');

        expect($weak)->toBe($floor === 0, "floor {$floor} vs a score-0 password")
            ->and($strong)->toBeTrue("floor {$floor} vs a strong passphrase");
    }
});

it('reads its default floor from config', function (): void {
    config()->set('laranail.password-tools.min_score', 4);

    // 'sensible-okay-word' style mid-strength candidates sit below 4.
    expect(strongPasses(new StrongPassword, 'okay-mid-password'))->toBeFalse();

    config()->set('laranail.password-tools.min_score', 0);
    expect(strongPasses(new StrongPassword, 'password'))->toBeTrue();
});

// =========================================================================
// User inputs — the form's own values must score weak
// =========================================================================

it('weakens a password matching a user input token', function (): void {
    $scorer = app(PasswordScorer::class);

    $without = $scorer->score('xkAq-9214-Trvb');
    $with = $scorer->score('xkAq-9214-Trvb', ['xkAq-9214-Trvb']);

    expect($with->score)->toBeLessThan($without->score);
});

it('feeds named form fields to the engine through DataAwareRule', function (): void {
    $rule = new StrongPassword(minScore: 1, userInputsField: 'email');

    expect(strongPasses($rule, 'john.quartz@acme.com', ['email' => 'john.quartz@acme.com']))
        ->toBeFalse()
        ->and(strongPasses(new StrongPassword(minScore: 1), 'john.quartz@acme.com'))
        ->toBeTrue();
});

it('honours globally configured weak tokens', function (): void {
    config()->set('laranail.password-tools.user_inputs', ['AcmeRocketCorp']);

    $scorer = app(PasswordScorer::class);
    expect($scorer->score('AcmeRocketCorp')->score)->toBeLessThanOrEqual(1);
});

// =========================================================================
// Feedback — keys and catalogue, never engine prose
// =========================================================================

it('surfaces translated catalogue sentences on failure, never raw engine strings', function (): void {
    $validator = Validator::make(
        ['password' => 'password'],
        ['password' => [new StrongPassword(minScore: 3)]],
    );

    $messages = $validator->errors()->get('password');

    expect($messages)->not->toBeEmpty()
        ->and(implode(' ', $messages))->toContain('too easy to guess')
        // bjeavons's raw prose says "This is a very common password" —
        // ours says "commonly used". The raw string leaking means the
        // key mapping was bypassed.
        ->and($messages)->not->toContain('This is a very common password');
});

it('resolves every FeedbackKey case through the catalogue', function (): void {
    $translator = app('translator');

    foreach (FeedbackKey::cases() as $case) {
        $resolved = $translator->get($case->translationKey());

        expect($resolved)->toBeString()
            ->and($resolved)->not->toBe($case->translationKey(), "{$case->name} does not resolve")
            ->and($resolved)->not->toContain(':', "{$case->name} carries an unresolved placeholder");
    }
});

it('maps every string the vendored engine can emit — the exhaustiveness guard', function (): void {
    $files = glob(dirname(__DIR__) . '/vendor/bjeavons/zxcvbn-php/src/{Matchers/*.php,Feedback.php}', GLOB_BRACE);
    assert(is_array($files));
    expect($files)->not->toBeEmpty();

    $emitted = [];

    foreach ($files as $file) {
        // A real tokenizer, not a regex: feedback strings contain quotes
        // of the other kind ("Capitalization doesn't…"), which a
        // quote-bounded pattern would split.
        foreach (token_get_all((string) file_get_contents($file)) as $token) {
            if (! is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }

            $literal = stripcslashes(substr($token[1], 1, -1));

            // Sentence-shaped: starts with a capital, contains a space.
            if (preg_match('/^[A-Z].* /', $literal) === 1 && mb_strlen($literal) >= 10) {
                $emitted[$literal] = true;
            }
        }
    }

    $mapped = [...array_keys(ZxcvbnScorer::WARNINGS), ...array_keys(ZxcvbnScorer::SUGGESTIONS)];
    $known = ['Alice Smith']; // a docblock example, not feedback

    $unmapped = array_diff(array_keys($emitted), $mapped, $known);

    expect(array_values($unmapped))->toBe(
        [],
        'bjeavons emits strings the scorer does not map — English would leak past the catalogue.',
    );
});

// =========================================================================
// The swappable scorer
// =========================================================================

it('is swappable — the rule follows whatever scorer is bound', function (): void {
    app()->instance(PasswordScorer::class, new class implements PasswordScorer
    {
        public function score(string $password, array $userInputs = []): Score
        {
            return new Score(score: 4);
        }
    });

    expect(strongPasses(new StrongPassword(minScore: 4), 'password'))->toBeTrue();
});

it('resolves a config-named scorer class', function (): void {
    expect(app(PasswordScorer::class))->toBeInstanceOf(ZxcvbnScorer::class);
});

// =========================================================================
// Redaction
// =========================================================================

it('never repeats the password in the failure messages', function (): void {
    $validator = Validator::make(
        ['password' => 'hunter2-secret-value'],
        ['password' => [new StrongPassword(minScore: 4)]],
    );

    expect(implode(' ', $validator->errors()->get('password')))
        ->not->toContain('hunter2-secret-value');
});

// =========================================================================
// The facade
// =========================================================================

it('scores through the facade', function (): void {
    expect(PasswordTools::score('password'))->toBeInstanceOf(Score::class)
        ->and(PasswordTools::score('password')->score)->toBe(0);
});
