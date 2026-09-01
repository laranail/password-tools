<?php

declare(strict_types=1);

namespace Simtabi\Laranail\PasswordTools\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Simtabi\Laranail\PasswordTools\Contracts\PasswordScorer;

/**
 * Fails when the score falls below the floor (default from config; 3 =
 * "safely unguessable"). The failure surfaces the engine's feedback as
 * TRANSLATED catalogue sentences — never raw engine prose, and never the
 * password itself.
 *
 * `DataAwareRule` powers `userInputsField`: name the form fields whose
 * values should score WEAK — an email field on the same form makes
 * "john@acme.com" a terrible password — and their submitted values ride
 * into the engine as user inputs.
 *
 * Strength COMPLEMENTS a length floor, never replaces one: zxcvbn can
 * score a short password highly. `password => ['required', 'min:12',
 * new StrongPassword(3)]` is the intended shape.
 */
final class StrongPassword implements DataAwareRule, ValidationRule
{
    /** @var array<string, mixed> */
    private array $data = [];

    /**
     * @param  list<string>  $userInputs  literal weak tokens
     * @param  string|list<string>|null  $userInputsField  form field(s) whose values score weak
     */
    public function __construct(
        private readonly ?int $minScore = null,
        private readonly array $userInputs = [],
        private readonly string|array|null $userInputsField = null,
        private readonly ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        /** @var PasswordScorer $scorer */
        $scorer = app(PasswordScorer::class);
        $score = $scorer->score($value, [...$this->userInputs, ...$this->fieldInputs()]);

        if ($score->isAtLeast($this->floor())) {
            return;
        }

        $fail($this->message ?? 'laranail/password-tools::messages.weak')->translate();

        foreach ($score->messages(app('translator')) as $feedback) {
            $fail($feedback);
        }
    }

    /** @return list<string> */
    private function fieldInputs(): array
    {
        $fields = match (true) {
            $this->userInputsField === null => [],
            is_string($this->userInputsField) => [$this->userInputsField],
            default => $this->userInputsField,
        };

        $inputs = [];

        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if (is_string($value) && $value !== '') {
                $inputs[] = $value;
            }
        }

        return $inputs;
    }

    private function floor(): int
    {
        if ($this->minScore !== null) {
            return $this->minScore;
        }

        $configured = config('laranail.password-tools.min_score', 3);

        return is_int($configured) ? $configured : 3;
    }
}
