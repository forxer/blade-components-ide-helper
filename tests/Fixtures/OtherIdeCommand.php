<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests\Fixtures;

use Forxer\BladeComponentsIdeHelper\Commands\AbstractIdeCommand;
use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaInput;

class OtherIdeCommand extends AbstractIdeCommand
{
    protected $signature = 'other:ide
        {--output= : Output directory for the VS Code files (default: .vscode)}
        {--ide-output= : Output directory for ide.json}
        {--snippets : Generate the VS Code snippets file}
        {--json : Generate the VS Code Custom Data file}
        {--ide-json : Generate the PhpStorm/Laravel Idea ide.json file}';

    protected $description = 'Generate IDE metadata for a second fixture package';

    protected function definition(): ComponentDefinition
    {
        return new ComponentDefinition(
            components: ['input' => VanillaInput::class],
            prefix: 'other',
        );
    }

    protected function fileBaseName(): string
    {
        return 'other-package';
    }
}
