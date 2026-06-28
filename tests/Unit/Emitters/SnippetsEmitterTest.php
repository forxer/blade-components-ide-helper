<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Emitters\SnippetsEmitter;
use Forxer\BladeComponentsIdeHelper\Metadata\AttributeMetadata;
use Forxer\BladeComponentsIdeHelper\Metadata\ComponentMetadata;

function btnMeta(bool $hasSlot = false): ComponentMetadata
{
    return new ComponentMetadata(
        tag: 'x-btn',
        description: 'Bootstrap button.',
        attributes: [
            new AttributeMetadata('variant', 'Color variant.', ['primary', 'secondary'], false, false),
            new AttributeMetadata('disabled', 'Disabled.', [], true, false),
        ],
        hasSlot: $hasSlot,
    );
}

it('builds a blade-scoped snippet keyed by display name', function (): void {
    $out = SnippetsEmitter::emit([btnMeta()], ['variant']);

    expect($out)->toHaveKey('x-btn')
        ->and($out['x-btn']['scope'])->toBe('blade')
        ->and($out['x-btn']['prefix'])->toBe('x-btn')
        ->and($out['x-btn']['description'])->toBe('Bootstrap button.');
});

it('scaffolds a configured value attribute as a choice dropdown without an empty option', function (): void {
    $body = SnippetsEmitter::emit([btnMeta()], ['variant'])['x-btn']['body'][0];

    expect($body)->toContain('variant="${1|primary,secondary|}"')
        ->not->toContain('${1|,')
        ->not->toContain('disabled');
});

it('scaffolds only the configured value attributes', function (): void {
    $meta = new ComponentMetadata('x-btn-archive', 'Archive button.', [
        new AttributeMetadata('confirm-variant', 'Confirm variant.', ['primary', 'secondary'], false, false),
        new AttributeMetadata('size', 'Size.', ['lg', 'sm'], false, false),
        new AttributeMetadata('variant', 'Variant.', ['primary', 'secondary'], false, false),
    ], false);

    $body = SnippetsEmitter::emit([$meta], ['variant'])['x-btn-archive']['body'][0];

    expect($body)->toContain('variant="${1|primary,secondary|}"')
        ->not->toContain('confirm-variant=')
        ->not->toContain('size=');
});

it('self-closes when there is no slot', function (): void {
    $body = SnippetsEmitter::emit([btnMeta(false)], ['variant'])['x-btn']['body'][0];
    expect($body)->toEndWith('/>$0')->and($body)->toStartWith('<x-btn ');
});

it('pairs tags when there is a slot', function (): void {
    $body = SnippetsEmitter::emit([btnMeta(true)], ['variant'])['x-btn']['body'][0];
    expect($body)->toContain('>$0</x-btn>');
});

it('places required attributes before value dropdowns', function (): void {
    $meta = new ComponentMetadata('x-text', 'Text input.', [
        new AttributeMetadata('name', 'Field name.', [], false, true),
        new AttributeMetadata('variant', 'Variant.', ['a', 'b'], false, false),
    ], false);

    $body = SnippetsEmitter::emit([$meta], ['variant'])['x-text']['body'][0];

    expect($body)->toContain('name="${1:name}"')
        ->and($body)->toContain('variant="${2|a,b|}"');
});
