<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Slots;

final class NullSlotStrategy implements SlotStrategy
{
    public function hasSlot(string $class): bool
    {
        return false;
    }
}
