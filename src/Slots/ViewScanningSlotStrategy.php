<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Slots;

use Illuminate\Contracts\View\View;
use ReflectionClass;
use ReflectionNamedType;
use Throwable;

final class ViewScanningSlotStrategy implements SlotStrategy
{
    public function hasSlot(string $class): bool
    {
        try {
            $rendered = $this->instantiate($class)->render();

            if ($rendered instanceof View) {
                $path = app('view')->getFinder()->find($rendered->name());

                return str_contains((string) file_get_contents($path), '$slot');
            }

            if (\is_string($rendered)) {
                return str_contains($rendered, '$slot');
            }

            return false;
        } catch (Throwable) {
            return false;
        }
    }

    private function instantiate(string $class): object
    {
        $constructor = new ReflectionClass($class)->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $args = [];

        foreach ($constructor->getParameters() as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();

                continue;
            }

            $type = $parameter->getType();
            $typeName = $type instanceof ReflectionNamedType ? $type->getName() : 'string';

            $args[] = match ($typeName) {
                'int' => 0,
                'float' => 0.0,
                'bool' => false,
                'array' => [],
                default => 'x',
            };
        }

        return new $class(...$args);
    }
}
