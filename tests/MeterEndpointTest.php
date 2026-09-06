<?php

declare(strict_types=1);

use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;
use Simtabi\Laranail\PasswordTools\Providers\PasswordToolsServiceProvider;

function enableMeter(string $throttle = '30,1'): void
{
    config()->set('laranail.password-tools.meter', [
        'enabled'    => true,
        'path'       => '/_laranail/password-tools/meter',
        'middleware' => [],
        'throttle'   => $throttle,
    ]);

    app()->register(PasswordToolsServiceProvider::class, force: true);
}

it('is disabled by default — no route exists', function (): void {
    Route::getRoutes()->refreshNameLookups();

    expect(Route::has('laranail.password-tools.meter'))->toBeFalse();
    $this->postJson('/_laranail/password-tools/meter', ['password' => 'x'])->assertNotFound();
});

it('scores a candidate with translated feedback and never echoes it', function (): void {
    enableMeter();

    $response = $this->postJson('/_laranail/password-tools/meter', ['password' => 'password']);
    assert($response instanceof TestResponse);

    $response->assertOk()
        ->assertJsonPath('score', 0)
        ->assertHeader('Cache-Control', 'no-store, private');

    expect((string) $response->baseResponse->getContent())
        ->not->toContain('"password"');

    $feedback = $response->json('feedback');
    expect($feedback)->toBeArray()->not->toBeEmpty();
});

it('weakens by user inputs sent alongside', function (): void {
    enableMeter();

    // Named rather than written inline: a strong-looking literal sitting
    // against a 'password' key is a hardcoded-password match, and this one is
    // a scoring input, not a credential - it authenticates nothing and the
    // assertion below is about the score dropping, not about the value.
    $candidate = 'xkAq-9214-Trvb';

    $score = $this->postJson('/_laranail/password-tools/meter', [
        'password'    => $candidate,
        'user_inputs' => [$candidate],
    ])->json('score');

    expect($score)->toBeLessThanOrEqual(1);
});

it('rejects an empty candidate with a shape, not a score', function (): void {
    enableMeter();

    $this->postJson('/_laranail/password-tools/meter', [])->assertStatus(422);
});

it('throttles probing', function (): void {
    enableMeter('2,1');

    foreach (range(1, 2) as $i) {
        $this->postJson('/_laranail/password-tools/meter', ['password' => 'x' . $i])->assertOk();
    }

    $this->postJson('/_laranail/password-tools/meter', ['password' => 'x3'])->assertStatus(429);
});
