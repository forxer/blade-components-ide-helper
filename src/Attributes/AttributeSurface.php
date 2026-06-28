<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Attributes;

use Forxer\BladeComponentsIdeHelper\Metadata\AttributeMetadata;

interface AttributeSurface
{
    /**
     * The settable attributes of a component class, sorted by kebab name.
     *
     * @return list<AttributeMetadata>
     */
    public function attributes(string $class): array;
}
