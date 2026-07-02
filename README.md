# Blade Components IDE Helper

Generate IDE metadata for Laravel packages (and apps) that ship **class-based Blade components**:

- **VS Code Custom Data** (`<base>.html-data.json`) — full attribute-name/value completion and hover
  inside `<x-…>` tags, via the
  **[Blade Components IDE Helper](https://marketplace.visualstudio.com/items?itemName=forxer.blade-components-ide-helper)**
  VS Code extension (also on
  [Open VSX](https://open-vsx.org/extension/forxer/blade-components-ide-helper)). **This is the
  recommended solution.**
- **VS Code snippets** (`<base>.code-snippets`) — zero-install *fallback* that scaffolds `<x-…>` tags
  without any extension. Use it only when the extension can't be installed.
- **PhpStorm / Laravel Idea** (`ide.json`) — tag → component-class mapping, auto-merged by Laravel Idea.

> **Pick one VS Code output, not both.** The snippets and the extension both complete `<x-…>`, and
> when generated together the snippets outrank the extension's suggestions. If your team uses the
> extension, generate `--json --ide-json` (skip `--snippets`); otherwise generate
> `--snippets --ide-json`. The `ide.json` (PhpStorm) is independent — keep it in either case.

It is a small, framework-only library: you describe your components with a `ComponentDefinition`,
and either extend the provided `AbstractIdeCommand` or call the services directly.

## Installation

```bash
composer require --dev forxer/blade-components-ide-helper
```

Use a plain (non-dev) `require` instead if your package calls any of this library's
services at runtime — for example reusing `Reflection\AttributeReflector` to hydrate
component attributes — rather than only generating IDE metadata.

## The contract: `ComponentDefinition`

```php
use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;

$definition = new ComponentDefinition(
    components: ['alert' => Alert::class, 'badge' => Badge::class], // alias => class
    prefix: '',                                                     // <x-alert> vs <x-bs-alert>
    // attributeSurface: ...   (see below — defaults to constructor parameters)
    // slotStrategy: ...       (see below — defaults to view scanning)
    // snippetValueAttributes: ['variant'],  // which attribute gets a value dropdown in snippets
);
```

### Attribute surfaces — what counts as a settable attribute

- `ConstructorParametersSurface` (default): constructor parameters only. Correct for a standard
  Illuminate component, whose settable attributes are exactly its constructor parameters.
- `PropertiesAndConstructorSurface`: the union of public settable properties **and** constructor
  parameters. Use it when your components hydrate public properties in addition to constructor
  arguments.

Both read descriptions and constrained value sets from docblocks: a property's summary and its
`@var 'a'|'b'` literal union, or a constructor parameter's `@param` summary.

You can supply your own by implementing `Forxer\BladeComponentsIdeHelper\Attributes\AttributeSurface`.

### Slot strategies — does the component accept inner content?

- `ViewScanningSlotStrategy` (default): instantiates the component with dummy arguments, calls
  `render()`, and scans the resolved view (file or inline string) for `$slot`. Any failure degrades
  to "no slot" (self-closing snippet).
- `NullSlotStrategy`: always reports no slot.

Implement `Forxer\BladeComponentsIdeHelper\Slots\SlotStrategy` for a custom rule.

## Wiring a command

Describe your components once as an `IdeTarget` (a `ComponentDefinition` plus the file base name),
in your service provider. Register it with the `IdeTargetRegistry` so the aggregate command can find
it, and expose a thin per-package command whose `target()` returns that same target.

```php
use Forxer\BladeComponentsIdeHelper\Attributes\PropertiesAndConstructorSurface;
use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;
use Forxer\BladeComponentsIdeHelper\Registry\IdeTargetRegistry;

// In your service provider — the single source of truth for the target:
public static function ideTarget(): IdeTarget
{
    return new IdeTarget(
        definition: new ComponentDefinition(
            components: config('my-package.components'),
            prefix: (string) config('my-package.prefix', ''),
            attributeSurface: new PropertiesAndConstructorSurface(),
        ),
        fileBaseName: 'my-package',
    );
}

public function boot(): void
{
    // so `blade-components-ide-helper:generate` regenerates this package too:
    IdeTargetRegistry::register(self::ideTarget());
    $this->commands([IdeCommand::class]);
}
```

```php
use Forxer\BladeComponentsIdeHelper\Commands\AbstractIdeCommand;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;

class IdeCommand extends AbstractIdeCommand
{
    protected $signature = 'my-package:ide
        {--output= : Output directory for the VS Code files (default: .vscode)}
        {--ide-output= : Output directory for ide.json}
        {--snippets : Generate the VS Code snippets file}
        {--json : Generate the VS Code Custom Data file}
        {--ide-json : Generate the PhpStorm/Laravel Idea ide.json file}';

    protected $description = 'Generate IDE metadata for the components';

    protected function target(): IdeTarget
    {
        return MyServiceProvider::ideTarget();
    }
}
```

Running `php artisan my-package:ide` writes `.vscode/my-package.code-snippets`,
`.vscode/my-package.html-data.json`, and `ide-helper/my-package/ide.json`.

## Regenerating every consumer at once

The package auto-registers an aggregate command. Once each consumer has registered its target,
`php artisan blade-components-ide-helper:generate` regenerates the metadata of **every** registered
package in a single run — pass `--only=base1,base2` to restrict it, plus the usual
`--snippets` / `--json` / `--ide-json` format flags. A host application can then wire one
`post-autoload-dump` line instead of one per consumer.

## Multiple packages side by side

Each consumer writes files under its own base name, so several packages coexist without collision:
VS Code loads every `*.code-snippets`, and Laravel Idea recursively merges every `ide.json`. The
`ide.json` is written to a package-owned subfolder (`ide-helper/<base>/`), never the shared project
root and never `.vscode/`.

## License

MIT
