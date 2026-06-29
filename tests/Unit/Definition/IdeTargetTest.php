<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;

it('exposes its definition, file base name and default ide.json subdirectory', function (): void {
    $definition = new ComponentDefinition(components: ['input' => stdClass::class], prefix: 'demo');
    $target = new IdeTarget(definition: $definition, fileBaseName: 'demo-package');

    expect($target->definition)->toBe($definition)
        ->and($target->fileBaseName)->toBe('demo-package')
        ->and($target->ideJsonSubdirectory)->toBe('ide-helper/demo-package');
});

it('accepts an explicit ide.json subdirectory override', function (): void {
    $target = new IdeTarget(
        definition: new ComponentDefinition(components: []),
        fileBaseName: 'demo-package',
        ideJsonSubdirectory: 'custom/idea',
    );

    expect($target->ideJsonSubdirectory)->toBe('custom/idea');
});
