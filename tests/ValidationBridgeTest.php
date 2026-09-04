<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use Simtabi\Laranail\Validation\FluentRule;
use Simtabi\Laranail\Validation\Builder\Nodes\PasswordRule;
use Simtabi\Laranail\PasswordHistory\Providers\PasswordHistoryServiceProvider;

/**
 * The §4.2 bridge, proven end to end — including the FULL chain the whole
 * two-package design exists for: with the validator and BOTH password
 * packages installed, `password()->strength()->notReused()` composes on
 * one node. The dependency direction stays one-way: both packages are
 * DEV dependencies here, every other test passes without them, and the
 * validator never learns either exists.
 */
beforeEach(function (): void {
    if (! class_exists(PasswordRule::class)) {
        $this->markTestSkipped('laranail/validation not installed');
    }
});

it('teaches password() the strength macro', function (): void {
    expect(PasswordRule::hasMacro('strength'))->toBeTrue();
});

it('rejects a weak password through the fluent chain, end to end', function (): void {
    $rules = ['password' => [FluentRule::password(defaults: false)->strength(3)]];

    expect(Validator::make(['password' => 'password'], $rules)->passes())->toBeFalse()
        ->and(Validator::make(['password' => 'correct horse battery staple violet umbrella'], $rules)->passes())
        ->toBeTrue();
});

it('composes the whole chain: strength AND reuse on one node', function (): void {
    if (! class_exists(PasswordHistoryServiceProvider::class)) {
        $this->markTestSkipped('laranail/password-history not installed');
    }

    // Both sister providers register their macros.
    app()->register(PasswordHistoryServiceProvider::class);

    $node = FluentRule::password(defaults: false)->strength(3)->notReused();

    expect($node)->toBeInstanceOf(PasswordRule::class);

    // With nobody authenticated, notReused() no-ops (signup) and strength
    // still carries the verdict on the same composed node.
    $rules = ['password' => [$node]];

    expect(Validator::make(['password' => 'password'], $rules)->passes())->toBeFalse()
        ->and(Validator::make(['password' => 'correct horse battery staple violet umbrella'], $rules)->passes())
        ->toBeTrue();
});
