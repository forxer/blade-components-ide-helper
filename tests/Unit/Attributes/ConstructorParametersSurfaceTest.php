<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Attributes\ConstructorParametersSurface;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\RichBadge;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaInput;

it('returns only constructor parameters, sorted by kebab name', function (): void {
    $attributes = (new ConstructorParametersSurface())->attributes(VanillaInput::class);
    $names = array_map(fn ($a): string => $a->name, $attributes);

    expect($names)->toBe(['name', 'type']);
});

it('marks required parameters and reads @param summaries', function (): void {
    $byName = collect((new ConstructorParametersSurface())->attributes(VanillaInput::class))->keyBy('name');

    expect($byName['name']->required)->toBeTrue()
        ->and($byName['name']->description)->toBe('Field name. Required.')
        ->and($byName['type']->required)->toBeFalse();
});

it('ignores public properties (constructor-only surface)', function (): void {
    $names = array_map(fn ($a): string => $a->name, (new ConstructorParametersSurface())->attributes(RichBadge::class));

    expect($names)->toBe(['label'])->not->toContain('variant');
});
