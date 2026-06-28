<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Metadata\AttributeMetadata;
use Forxer\BladeComponentsIdeHelper\Metadata\ComponentMetadata;

it('constructs an attribute metadata value object', function (): void {
    $attribute = new AttributeMetadata('variant', 'Color variant.', ['primary'], false, true);

    expect($attribute->name)->toBe('variant')
        ->and($attribute->description)->toBe('Color variant.')
        ->and($attribute->values)->toBe(['primary'])
        ->and($attribute->boolean)->toBeFalse()
        ->and($attribute->required)->toBeTrue();
});

it('constructs a component metadata value object', function (): void {
    $component = new ComponentMetadata('x-btn', 'Button.', [], true);

    expect($component->tag)->toBe('x-btn')
        ->and($component->description)->toBe('Button.')
        ->and($component->attributes)->toBe([])
        ->and($component->hasSlot)->toBeTrue();
});
