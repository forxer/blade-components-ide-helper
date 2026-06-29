<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Definition;

final readonly class IdeTarget
{
    public string $ideJsonSubdirectory;

    public function __construct(
        public ComponentDefinition $definition,
        public string $fileBaseName,
        ?string $ideJsonSubdirectory = null,
    ) {
        $this->ideJsonSubdirectory = $ideJsonSubdirectory ?? 'ide-helper/'.$fileBaseName;
    }
}
