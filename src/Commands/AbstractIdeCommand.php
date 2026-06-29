<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Commands;

use Forxer\BladeComponentsIdeHelper\Commands\Concerns\ResolvesIdeOptions;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;
use Forxer\BladeComponentsIdeHelper\Generation\IdeGenerator;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

abstract class AbstractIdeCommand extends Command
{
    use ResolvesIdeOptions;

    /**
     * The target (definition + file base name + ide.json subfolder) this command generates.
     */
    abstract protected function target(): IdeTarget;

    public function handle(IdeGenerator $generator): int
    {
        $target = $this->target();
        $formats = $this->resolveFormats();

        $vscodeDirectory = array_intersect($formats, ['snippets', 'json']) !== []
            ? $this->resolveVscodeDirectory()
            : null;

        $ideDirectory = \in_array('ide-json', $formats, true)
            ? $this->resolveIdeDirectory($target->ideJsonSubdirectory)
            : null;

        $written = $generator->generate(
            target: $target,
            formats: $formats,
            vscodeDirectory: (string) $vscodeDirectory,
            ideDirectory: (string) $ideDirectory,
        );

        info('Generated '.\count($written).' IDE metadata file(s):');

        foreach ($written as $path) {
            $this->line('  • '.$path);
        }

        note(
            "Commit these files so your team gets completion without re-running this command.\n".
            "• Snippets (.vscode) work out of the box (fallback).\n".
            "• The .html-data.json feeds the VS Code extension (primary).\n".
            '• ide.json lives in its own folder and is auto-detected (and merged) by Laravel Idea (PhpStorm).'
        );

        return self::SUCCESS;
    }
}
