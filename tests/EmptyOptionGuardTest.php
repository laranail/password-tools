<?php

declare(strict_types=1);

use Simtabi\Laranail\PasswordTools\Commands\GenerateCommand;
use Simtabi\Laranail\Package\Tools\Testing\AssertsDriverContract;

uses(AssertsDriverContract::class);

/**
 * `is_numeric($this->option('x')) ? (int) $this->option('x') : $default` IS
 * `intOption('x', $default)`. These were already correct -- the guard sat in the
 * right place -- so this is the safe half of the sweep: same behaviour, one
 * accessor, and the intent legible at a glance.
 */
it('resolves numeric options with the normalising accessor', function (): void {
    $source = (string) file_get_contents((string) (new ReflectionClass(GenerateCommand::class))->getFileName());

    expect($source)->not->toContain('is_numeric($this->option(')
        ->and($source)->toContain("intOption('count', 1)")
        ->and($source)->toContain("intOption('words', 5)")
        ->and($source)->toContain("intOption('length', 16)");
});

it('still clamps count to the documented range', function (): void {
    // max(1, min(100, …)) is behaviour the accessor does not provide and must survive.
    $source = (string) file_get_contents((string) (new ReflectionClass(GenerateCommand::class))->getFileName());

    expect($source)->toContain('max(1, min(100,');
});

it('has no console option defaulted by a null-only test', function (): void {
    $this->assertNoNullOnlyOptionGuards(__DIR__ . '/../src');
});
