<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Attributes;

use Forxer\BladeComponentsIdeHelper\Reflection\AttributeReflector;
use ReflectionClass;

final class ConstructorParametersSurface implements AttributeSurface
{
    use BuildsAttributeMetadata;

    public function attributes(string $class): array
    {
        $constructorDoc = new ReflectionClass($class)->getConstructor()?->getDocComment() ?? false;

        $attributes = [];

        foreach (AttributeReflector::constructorParameters($class) as $parameter) {
            $attributes[$parameter['kebab']] = $this->attributeFromParameter($parameter, $constructorDoc);
        }

        ksort($attributes);

        return array_values($attributes);
    }
}
