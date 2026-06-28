<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Slots;

interface SlotStrategy
{
    /**
     * Whether the component renders its `$slot` (i.e. accepts inner content).
     */
    public function hasSlot(string $class): bool;
}
