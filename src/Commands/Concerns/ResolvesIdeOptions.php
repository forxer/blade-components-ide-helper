<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Commands\Concerns;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

trait ResolvesIdeOptions
{
    /** @var list<string> */
    private const array ALL_FORMATS = ['snippets', 'json', 'ide-json'];

    /**
     * @return list<string>
     */
    protected function resolveFormats(): array
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

    protected function resolveVscodeDirectory(): string
    {
        $output = $this->option('output');

        if ($output === null && $this->input->isInteractive()) {
            $output = text(label: 'VS Code output directory', default: '.vscode');
        }

        $output ??= '.vscode';

        return str_starts_with((string) $output, DIRECTORY_SEPARATOR) ? $output : base_path($output);
    }

    protected function resolveIdeDirectory(string $defaultSubdirectory): string
    {
        $output = $this->option('ide-output') ?? $defaultSubdirectory;

        return str_starts_with($output, DIRECTORY_SEPARATOR) ? $output : base_path($output);
    }
}
