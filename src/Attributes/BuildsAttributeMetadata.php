<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Attributes;

use Forxer\BladeComponentsIdeHelper\Introspection\DocblockParser;
use Forxer\BladeComponentsIdeHelper\Metadata\AttributeMetadata;
use ReflectionProperty;

trait BuildsAttributeMetadata
{
    /**
     * @param  array{name: string, kebab: string, type: ?string, required: bool}  $parameter
     */
    private function attributeFromParameter(array $parameter, string|false $constructorDoc): AttributeMetadata
    {
        return new AttributeMetadata(
            name: $parameter['kebab'],
            description: DocblockParser::paramSummary($constructorDoc, $parameter['name']),
            values: [],
            boolean: $parameter['type'] === 'bool',
            required: $parameter['required'],
        );
    }

    /**
     * @param  array{name: string, kebab: string, type: ?string}  $property
     */
    private function attributeFromProperty(string $class, array $property): AttributeMetadata
    {
        $doc = new ReflectionProperty($class, $property['name'])->getDocComment();
        $values = DocblockParser::varLiterals($doc);

        return new AttributeMetadata(
            name: $property['kebab'],
            description: DocblockParser::summary($doc),
            values: $values,
            boolean: $property['type'] === 'bool' && $values === [],
            required: false,
        );
    }
}
