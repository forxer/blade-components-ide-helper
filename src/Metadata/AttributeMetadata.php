<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Metadata;

final readonly class AttributeMetadata
{
    /**
     * @param  list<string>  $values
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public array $values,
        public bool $boolean,
        public bool $required,
    ) {}
}
