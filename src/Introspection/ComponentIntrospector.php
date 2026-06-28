<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Introspection;

use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Metadata\ComponentMetadata;
use ReflectionClass;

final readonly class ComponentIntrospector
{
    public function __construct(
        private ComponentDefinition $definition,
    ) {}

    /**
     * @return list<ComponentMetadata>
     */
    public function introspect(): array
    {
        $metadata = [];

        foreach ($this->definition->components as $alias => $class) {
            $metadata[] = new ComponentMetadata(
                tag: self::tagFor($this->definition->prefix, $alias),
                description: DocblockParser::summary(new ReflectionClass($class)->getDocComment()),
                attributes: $this->definition->attributeSurface->attributes($class),
                hasSlot: $this->definition->slotStrategy->hasSlot($class),
            );
        }

        return $metadata;
    }

    /**
     * Build the `<x-...>` tag name for an alias under a given prefix.
     */
    public static function tagFor(string $prefix, string $alias): string
    {
        return $prefix === '' ? 'x-'.$alias : 'x-'.$prefix.'-'.$alias;
    }
}
