<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Reflection\AttributeReflector;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\RichBadge;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaInput;

it('lists settable non-promoted public properties, kebab-cased', function (): void {
    $properties = AttributeReflector::settableProperties(RichBadge::class);
    $byName = collect($properties)->keyBy('name');

    expect($byName)->toHaveKey('variant')->toHaveKey('pill')
        ->and($byName['variant']['kebab'])->toBe('variant')
        ->and($byName['pill']['type'])->toBe('bool')
        ->and($byName)->not->toHaveKey('label');
});

it('excludes Illuminate\View\Component internals', function (): void {
    $names = array_column(AttributeReflector::settableProperties(RichBadge::class), 'name');

    expect($names)->not->toContain('componentName')
        ->not->toContain('attributes')
        ->not->toContain('except');
});

it('lists constructor parameters with the required flag', function (): void {
    $byName = collect(AttributeReflector::constructorParameters(VanillaInput::class))->keyBy('name');

    expect($byName)->toHaveKey('name')->toHaveKey('type')
        ->and($byName['name']['required'])->toBeTrue()
        ->and($byName['type']['required'])->toBeFalse()
        ->and($byName['type']['kebab'])->toBe('type');
});

it('returns an empty array for a class without a constructor', function (): void {
    $anon = new class {};

    expect(AttributeReflector::constructorParameters($anon::class))->toBe([]);
});
