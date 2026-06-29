<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Generation;

use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;
use Forxer\BladeComponentsIdeHelper\Emitters\HtmlDataEmitter;
use Forxer\BladeComponentsIdeHelper\Emitters\IdeJsonEmitter;
use Forxer\BladeComponentsIdeHelper\Emitters\SnippetsEmitter;
use Forxer\BladeComponentsIdeHelper\Introspection\ComponentIntrospector;
use Illuminate\Filesystem\Filesystem;

class IdeGenerator
{
    public function __construct(private readonly Filesystem $files) {}

    /**
     * @param  list<string>  $formats  subset of ['snippets', 'json', 'ide-json']
     * @return list<string>  absolute paths of the files written
     */
    public function generate(IdeTarget $target, array $formats, string $vscodeDirectory, string $ideDirectory): array
    {
        $definition = $target->definition;
        $model = (new ComponentIntrospector($definition))->introspect();

        $filenames = [
            'snippets' => $target->fileBaseName.'.code-snippets',
            'json' => $target->fileBaseName.'.html-data.json',
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

            $this->files->ensureDirectoryExists($directory);
            $path = $directory.DIRECTORY_SEPARATOR.$filenames[$format];
            $this->files->put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
            $written[] = $path;
        }

        return $written;
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
