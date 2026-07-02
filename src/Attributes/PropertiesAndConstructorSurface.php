<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Attributes;

use Forxer\BladeComponentsReflection\AttributeReflector;
use ReflectionClass;

final class PropertiesAndConstructorSurface implements AttributeSurface
{
    use BuildsAttributeMetadata;

    public function attributes(string $class): array
    {
        $attributes = [];

        foreach (AttributeReflector::settableProperties($class) as $property) {
            $attributes[$property['kebab']] = $this->attributeFromProperty($class, $property);
        }

        $constructorDoc = new ReflectionClass($class)->getConstructor()?->getDocComment() ?? false;

        foreach (AttributeReflector::constructorParameters($class) as $parameter) {
            if (isset($attributes[$parameter['kebab']])) {
                continue;
            }

            $attributes[$parameter['kebab']] = $this->attributeFromParameter($parameter, $constructorDoc);
        }

        ksort($attributes);

        return array_values($attributes);
    }
}
