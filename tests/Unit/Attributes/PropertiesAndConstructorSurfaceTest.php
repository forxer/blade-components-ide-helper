<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Attributes\PropertiesAndConstructorSurface;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\RichBadge;

it('unions public properties and constructor params, deduped and sorted', function (): void {
    $names = array_map(fn ($a): string => $a->name, (new PropertiesAndConstructorSurface())->attributes(RichBadge::class));

    expect($names)->toBe(['label', 'pill', 'variant']);
});

it('reads @var literals and the summary from a public property', function (): void {
    $byName = collect((new PropertiesAndConstructorSurface())->attributes(RichBadge::class))->keyBy('name');

    expect($byName['variant']->values)->toBe(['primary', 'secondary', 'danger'])
        ->and($byName['variant']->description)->toBe('Bootstrap color variant of the badge.')
        ->and($byName['variant']->boolean)->toBeFalse();
});

it('flags a typed bool property with no values as boolean', function (): void {
    $byName = collect((new PropertiesAndConstructorSurface())->attributes(RichBadge::class))->keyBy('name');

    expect($byName['pill']->boolean)->toBeTrue()
        ->and($byName['pill']->values)->toBe([]);
});

it('marks the required constructor param', function (): void {
    $byName = collect((new PropertiesAndConstructorSurface())->attributes(RichBadge::class))->keyBy('name');

    expect($byName['label']->required)->toBeTrue()
        ->and($byName['label']->description)->toBe('Badge label. Required.');
});
