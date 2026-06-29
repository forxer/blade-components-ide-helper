<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Definition;

final class IdeTarget
{
    public readonly string $ideJsonSubdirectory;

    public function __construct(
        public readonly ComponentDefinition $definition,
        public readonly string $fileBaseName,
        ?string $ideJsonSubdirectory = null,
    ) {
        $this->ideJsonSubdirectory = $ideJsonSubdirectory ?? 'ide-helper/'.$fileBaseName;
    }
}
