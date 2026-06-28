<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Attributes\PropertiesAndConstructorSurface;
use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Introspection\ComponentIntrospector;
use Forxer\BladeComponentsIdeHelper\Metadata\ComponentMetadata;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\RichBadge;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaCard;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaInput;

/**
 * @param  array<string, class-string>  $components
 * @return array<string, ComponentMetadata>
 */
function metaFor(array $components, string $prefix = '', ?object $surface = null): array
{
    $definition = new ComponentDefinition(
        components: $components,
        prefix: $prefix,
        attributeSurface: $surface ?? new PropertiesAndConstructorSurface(),
    );

    return collect(new ComponentIntrospector($definition)->introspect())->keyBy('tag')->all();
}

it('computes the tag from the alias and empty prefix', function (): void {
    expect(metaFor(['input' => VanillaInput::class]))->toHaveKey('x-input');
});

it('prefixes the tag when a prefix is configured', function (): void {
    expect(metaFor(['input' => VanillaInput::class], 'bs'))->toHaveKey('x-bs-input');
});

it('emits properties with their @var value set', function (): void {
    $badge = metaFor(['badge' => RichBadge::class])['x-badge'];
    $variant = collect($badge->attributes)->firstWhere('name', 'variant');

    expect($variant->values)->toBe(['primary', 'secondary', 'danger']);
});

it('marks slot-bearing components from their resolved view', function (): void {
    expect(metaFor(['card' => VanillaCard::class])['x-card']->hasSlot)->toBeTrue()
        ->and(metaFor(['input' => VanillaInput::class])['x-input']->hasSlot)->toBeFalse();
});

it('exposes the static tag rule', function (): void {
    expect(ComponentIntrospector::tagFor('', 'btn'))->toBe('x-btn')
        ->and(ComponentIntrospector::tagFor('bs', 'btn'))->toBe('x-bs-btn');
});
