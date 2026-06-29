<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests\Fixtures;

use Forxer\BladeComponentsIdeHelper\Attributes\PropertiesAndConstructorSurface;
use Forxer\BladeComponentsIdeHelper\Commands\AbstractIdeCommand;
use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\RichBadge;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaCard;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaInput;

class TestIdeCommand extends AbstractIdeCommand
{
    protected $signature = 'fixtures:ide
        {--output= : Output directory for the VS Code files (default: .vscode)}
        {--ide-output= : Output directory for ide.json}
        {--snippets : Generate the VS Code snippets file}
        {--json : Generate the VS Code Custom Data file}
        {--ide-json : Generate the PhpStorm/Laravel Idea ide.json file}';

    protected $description = 'Generate IDE metadata for the fixture components';

    public static function ideTarget(): IdeTarget
    {
        return new IdeTarget(
            definition: new ComponentDefinition(
                components: [
                    'input' => VanillaInput::class,
                    'card' => VanillaCard::class,
                    'badge' => RichBadge::class,
                ],
                attributeSurface: new PropertiesAndConstructorSurface(),
            ),
            fileBaseName: 'fixtures',
        );
    }

    protected function target(): IdeTarget
    {
        return self::ideTarget();
    }
}
