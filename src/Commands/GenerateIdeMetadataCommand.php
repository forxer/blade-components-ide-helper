<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Commands;

use Forxer\BladeComponentsIdeHelper\Commands\Concerns\ResolvesIdeOptions;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;
use Forxer\BladeComponentsIdeHelper\Generation\IdeGenerator;
use Forxer\BladeComponentsIdeHelper\Registry\IdeTargetRegistry;
use Illuminate\Console\Command;
use Throwable;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class GenerateIdeMetadataCommand extends Command
{
    use ResolvesIdeOptions;

    protected $signature = 'blade-components-ide-helper:generate
        {--output= : Output directory for the VS Code files (default: .vscode), applied to all targets}
        {--ide-output= : Output directory for ide.json, applied to all targets}
        {--only= : Comma-separated file base names to restrict generation to}
        {--snippets : Generate the VS Code snippets file}
        {--json : Generate the VS Code Custom Data file}
        {--ide-json : Generate the PhpStorm/Laravel Idea ide.json file}';

    protected $description = 'Regenerate IDE metadata for every registered consumer package';

    public function handle(IdeGenerator $generator): int
    {
        $targets = $this->resolveTargets();

        if ($targets === []) {
            info('No IDE metadata consumer is registered — nothing to generate.');

            return self::SUCCESS;
        }

        $formats = $this->resolveFormats();

        $vscodeDirectory = array_intersect($formats, ['snippets', 'json']) !== []
            ? $this->resolveVscodeDirectory()
            : null;

        $failed = false;

        foreach ($targets as $target) {
            $ideDirectory = \in_array('ide-json', $formats, true)
                ? $this->resolveIdeDirectory($target->ideJsonSubdirectory)
                : null;

            try {
                $written = $generator->generate(
                    target: $target,
                    formats: $formats,
                    vscodeDirectory: (string) $vscodeDirectory,
                    ideDirectory: (string) $ideDirectory,
                );

                info($target->fileBaseName.': generated '.\count($written).' file(s):');

                foreach ($written as $path) {
                    $this->line('  • '.$path);
                }
            } catch (Throwable $e) {
                $failed = true;
                warning($target->fileBaseName.': failed — '.$e->getMessage());
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return list<IdeTarget>
     */
    private function resolveTargets(): array
    {
        $all = IdeTargetRegistry::all();

        $only = $this->option('only');

        if ($only !== null && $only !== '') {
            $wanted = array_map(trim(...), explode(',', $only));

            return array_values(array_filter(
                $all,
                fn (IdeTarget $target): bool => \in_array($target->fileBaseName, $wanted, true)
            ));
        }

        if (! $this->input->isInteractive() || $all === []) {
            return $all;
        }

        $bases = array_map(fn (IdeTarget $t): string => $t->fileBaseName, $all);
        $chosen = multiselect(
            label: 'Which packages do you want to regenerate?',
            options: array_combine($bases, $bases),
            default: $bases,
            required: true,
        );

        return array_values(array_filter(
            $all,
            fn (IdeTarget $target): bool => \in_array($target->fileBaseName, $chosen, true)
        ));
    }
}
