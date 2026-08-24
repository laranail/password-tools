<?php

declare(strict_types=1);

use Simtabi\Laranail\PasswordTools\Facades\PasswordTools;
use Simtabi\Laranail\PasswordTools\Generators\PassphraseBuilder;
use Simtabi\Laranail\PasswordTools\Generators\PasswordBuilder;
use Simtabi\Laranail\PasswordTools\PasswordToolsManager;
use Simtabi\Laranail\PasswordTools\Support\WordList;

// =========================================================================
// Password builder
// =========================================================================

it('generates at the requested length from the default classes', function (): void {
    $password = PasswordTools::password()->length(24)->make();

    expect(strlen($password))->toBe(24)
        ->and($password)->toMatch('/[a-z]/')
        ->toMatch('/[A-Z]/')
        ->toMatch('/[0-9]/');
});

it('guarantees every enabled class appears — even at tight lengths', function (): void {
    foreach (range(1, 50) as $i) {
        $password = PasswordTools::password()->length(4)->symbols()->make();

        expect($password)->toMatch('/[a-z]/')
            ->toMatch('/[A-Z]/')
            ->toMatch('/[0-9]/')
            ->toMatch('/[!@#$%^&*()\-_=+\[\]{};:,.<>?]/');
    }
});

it('drops look-alike glyphs when asked', function (): void {
    foreach (range(1, 25) as $i) {
        expect(PasswordTools::password()->length(32)->withoutAmbiguous()->make())
            ->not->toMatch('/[0O1lI|]/');
    }
});

it('regenerates until the scorer agrees with atLeast()', function (): void {
    $password = PasswordTools::password()->length(16)->symbols()->atLeast(4)->make();

    expect(PasswordTools::score($password)->score)->toBe(4);
});

it('refuses a bare-constructed builder asking for atLeast()', function (): void {
    (new PasswordBuilder)->atLeast(3);
})->throws(InvalidArgumentException::class, 'needs a PasswordScorer');

it('refuses contradictory recipes loudly', function (): void {
    expect(fn (): string => PasswordTools::password()->letters(false, false)->numbers(false)->make())
        ->toThrow(InvalidArgumentException::class, 'disabled')
        ->and(fn () => PasswordTools::password()->length(3))
        ->toThrow(InvalidArgumentException::class, 'between 4 and 1024');
});

it('does not repeat itself — a CSPRNG sanity check', function (): void {
    $many = PasswordTools::password()->length(16)->makeMany(50);

    expect(count(array_unique($many)))->toBe(50);
});

// =========================================================================
// Passphrase builder
// =========================================================================

it('builds diceware phrases from the bundled EFF list', function (): void {
    $phrase = PasswordTools::passphrase()->words(5)->make();

    expect(explode('-', $phrase))->toHaveCount(5);
});

it('capitalizes, separates, and lands the digit on a random word', function (): void {
    $phrase = PasswordTools::passphrase()->words(4)->separator('.')->capitalize()->withNumber()->make();
    $words = explode('.', $phrase);

    expect($words)->toHaveCount(4)
        ->and(implode('', $words))->toMatch('/[0-9]/');

    foreach ($words as $word) {
        expect($word)->toMatch('/^[A-Z]/');
    }
});

it('knows its own entropy', function (): void {
    $builder = PasswordTools::passphrase()->words(5);

    // 5 EFF words = 5 × log2(7776) ≈ 64.6 bits.
    expect($builder->bits())->toBeGreaterThan(64.0)->toBeLessThan(65.0);
});

it('accepts an injected word list and rejects a tiny one', function (): void {
    $list = new WordList(array_map(static fn (int $i): string => "word{$i}", range(1, 2048)));

    expect(PasswordTools::passphrase()->wordList($list)->words(3)->make())
        ->toMatch('/^word\d+-word\d+-word\d+$/')
        ->and(fn (): WordList => new WordList(['a', 'b']))
        ->toThrow(InvalidArgumentException::class, 'at least 1024');
});

it('bundles the full EFF large list', function (): void {
    expect(app(WordList::class)->count())->toBe(7776);
});

// =========================================================================
// The manager hands out fresh builders
// =========================================================================

it('never leaks one call site\'s recipe into another', function (): void {
    $manager = app(PasswordToolsManager::class);

    $long = $manager->password()->length(40);
    $short = $manager->password();

    expect($long)->not->toBe($short)
        ->and(strlen($short->make()))->toBe(16)
        ->and(strlen($long->make()))->toBe(40)
        ->and($manager->passphrase())->toBeInstanceOf(PassphraseBuilder::class)
        ->not->toBe($manager->passphrase());
});

// =========================================================================
// The generate command
// =========================================================================

it('generates from the terminal in both modes', function (): void {
    $this->artisan('laranail::password-tools.generate', ['--count' => 2, '--symbols' => true])
        ->assertSuccessful();

    $this->artisan('laranail::password-tools.generate', ['--passphrase' => true, '--words' => 4])
        ->assertSuccessful();
});
