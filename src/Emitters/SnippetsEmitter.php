<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Emitters;

use Forxer\BladeComponentsIdeHelper\Metadata\ComponentMetadata;

final class SnippetsEmitter
{
    /**
     * @param  list<ComponentMetadata>  $components
     * @param  list<string>  $valueAttributes  attribute names rendered as a value dropdown
     * @return array<string, array{scope: string, prefix: string, description: string, body: list<string>}>
     */
    public static function emit(array $components, array $valueAttributes): array
    {
        $snippets = [];

        foreach ($components as $component) {
            $snippets[$component->tag] = [
                'scope' => 'blade',
                'prefix' => $component->tag,
                'description' => $component->description ?? $component->tag,
                'body' => [self::body($component, $valueAttributes)],
            ];
        }

        return $snippets;
    }

    /**
     * Snippets are the zero-install fallback, so they stay deliberately lean: required
     * attributes as plain tab stops, plus the configured value attributes as dropdowns.
     *
     * Other constrained attributes are intentionally not scaffolded: a snippet choice always
     * keeps its first value when tabbed past, which would force unwanted defaults. Full,
     * non-forcing value completion for every attribute is the dedicated VS Code extension's job.
     *
     * @param  list<string>  $valueAttributes
     */
    private static function body(ComponentMetadata $component, array $valueAttributes): string
    {
        $tabstop = 0;
        $parts = [];

        foreach ($component->attributes as $attribute) {
            if ($attribute->required) {
                $tabstop++;
                $parts[] = $attribute->name.'="${'.$tabstop.':'.$attribute->name.'}"';
            }
        }

        foreach ($component->attributes as $attribute) {
            if (! $attribute->required && in_array($attribute->name, $valueAttributes, true) && $attribute->values !== []) {
                $tabstop++;
                $parts[] = $attribute->name.'="${'.$tabstop.'|'.implode(',', $attribute->values).'|}"';
            }
        }

        $attributesString = $parts === [] ? '' : ' '.implode(' ', $parts);

        return $component->hasSlot
            ? '<'.$component->tag.$attributesString.'>$0</'.$component->tag.'>'
            : '<'.$component->tag.$attributesString.' />$0';
    }
}
