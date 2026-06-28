<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Definition;

use Forxer\BladeComponentsIdeHelper\Attributes\AttributeSurface;
use Forxer\BladeComponentsIdeHelper\Attributes\ConstructorParametersSurface;
use Forxer\BladeComponentsIdeHelper\Slots\SlotStrategy;
use Forxer\BladeComponentsIdeHelper\Slots\ViewScanningSlotStrategy;

final readonly class ComponentDefinition
{
    /**
     * @param  array<string, class-string>  $components  alias => component class
     * @param  list<string>  $snippetValueAttributes  attribute names that get a value dropdown in snippets
     */
    public function __construct(
        public array $components,
        public string $prefix = '',
        public AttributeSurface $attributeSurface = new ConstructorParametersSurface(),
        public SlotStrategy $slotStrategy = new ViewScanningSlotStrategy(),
        public array $snippetValueAttributes = ['variant'],
    ) {}
}
