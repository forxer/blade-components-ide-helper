<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Attributes\ConstructorParametersSurface;
use Forxer\BladeComponentsIdeHelper\Attributes\PropertiesAndConstructorSurface;
use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Slots\ViewScanningSlotStrategy;

it('applies vanilla defaults', function (): void {
    $definition = new ComponentDefinition(components: ['card' => 'X']);

    expect($definition->prefix)->toBe('')
        ->and($definition->attributeSurface)->toBeInstanceOf(ConstructorParametersSurface::class)
        ->and($definition->slotStrategy)->toBeInstanceOf(ViewScanningSlotStrategy::class)
        ->and($definition->snippetValueAttributes)->toBe(['variant']);
});

it('accepts overrides', function (): void {
    $definition = new ComponentDefinition(
        components: ['badge' => 'X'],
        prefix: 'bs',
        attributeSurface: new PropertiesAndConstructorSurface(),
        snippetValueAttributes: ['variant', 'size'],
    );

    expect($definition->prefix)->toBe('bs')
        ->and($definition->attributeSurface)->toBeInstanceOf(PropertiesAndConstructorSurface::class)
        ->and($definition->snippetValueAttributes)->toBe(['variant', 'size']);
});
