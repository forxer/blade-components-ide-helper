<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Commands;

use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Emitters\HtmlDataEmitter;
use Forxer\BladeComponentsIdeHelper\Emitters\IdeJsonEmitter;
use Forxer\BladeComponentsIdeHelper\Emitters\SnippetsEmitter;
use Forxer\BladeComponentsIdeHelper\Introspection\ComponentIntrospector;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;
use function Laravel\Prompts\text;

abstract class AbstractIdeCommand extends Command
{
    /** @var list<string> */
    private const array ALL_FORMATS = ['snippets', 'json', 'ide-json'];

    /**
     * The component definition describing the consumer's components.
     */
    abstract protected function definition(): ComponentDefinition;

    /**
     * Base name for the VS Code files: "<base>.code-snippets" / "<base>.html-data.json".
     */
    abstract protected function fileBaseName(): string;

    /**
     * Subfolder for the PhpStorm/Laravel Idea ide.json (default: "ide-helper/<fileBaseName>").
     */
    protected function ideJsonSubdirectory(): string
    {
        return 'ide-helper/'.$this->fileBaseName();
    }

    public function handle(Filesystem $files): int
    {
        $definition = $this->definition();
        $formats = $this->resolveFormats();

        $vscodeDirectory = array_intersect($formats, ['snippets', 'json']) !== [] ? $this->resolveDirectory() : null;
        $ideDirectory = \in_array('ide-json', $formats, true) ? $this->resolveIdeDirectory() : null;

        $model = new ComponentIntrospector($definition)->introspect();

        $filenames = [
            'snippets' => $this->fileBaseName().'.code-snippets',
            'json' => $this->fileBaseName().'.html-data.json',
            // PhpStorm / Laravel Idea only reads files named exactly `ide.json`. It scans the
            // project recursively and merges every `ide.json`, so this one lives in a
            // package-owned subfolder: it never collides with an app's own root `ide.json`.
            'ide-json' => 'ide.json',
        ];

        $written = [];

        foreach ($formats as $format) {
            $directory = $format === 'ide-json' ? $ideDirectory : $vscodeDirectory;

            $payload = match ($format) {
                'snippets' => SnippetsEmitter::emit($model, $definition->snippetValueAttributes),
                'json' => HtmlDataEmitter::emit($model),
                'ide-json' => IdeJsonEmitter::emit($this->tagToClass($definition)),
            };

            $files->ensureDirectoryExists($directory);
            $path = $directory.DIRECTORY_SEPARATOR.$filenames[$format];
            $files->put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
            $written[] = $path;
        }

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

    /**
     * @return list<string>
     */
    private function resolveFormats(): array
    {
        $selected = array_values(array_filter(
            self::ALL_FORMATS,
            fn (string $format): bool => (bool) $this->option($format)
        ));

        if ($selected !== []) {
            return $selected;
        }

        if (! $this->input->isInteractive()) {
            return self::ALL_FORMATS;
        }

        return multiselect(
            label: 'Which IDE metadata files do you want to generate?',
            options: [
                'snippets' => 'VS Code snippets (.code-snippets) — fallback, zero install',
                'json' => 'VS Code Custom Data (.html-data.json) — for the extension (primary)',
                'ide-json' => 'PhpStorm / Laravel Idea (ide.json)',
            ],
            default: self::ALL_FORMATS,
            required: true,
        );
    }

    private function resolveDirectory(): string
    {
        $output = $this->option('output');

        if ($output === null && $this->input->isInteractive()) {
            $output = text(label: 'VS Code output directory', default: '.vscode');
        }

        $output ??= '.vscode';

        return str_starts_with((string) $output, DIRECTORY_SEPARATOR) ? $output : base_path($output);
    }

    private function resolveIdeDirectory(): string
    {
        $output = $this->option('ide-output') ?? $this->ideJsonSubdirectory();

        return str_starts_with($output, DIRECTORY_SEPARATOR) ? $output : base_path($output);
    }

    /**
     * @return array<string, class-string>
     */
    private function tagToClass(ComponentDefinition $definition): array
    {
        $map = [];

        foreach ($definition->components as $alias => $class) {
            $map[ComponentIntrospector::tagFor($definition->prefix, $alias)] = $class;
        }

        return $map;
    }
}
