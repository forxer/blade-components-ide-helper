# CLAUDE.md

Guidance for Claude Code when working in this repository.

## What this is

A small **framework-only PHP library** (Laravel 12/13, PHP 8.4) that generates **IDE metadata** for
Laravel packages and apps shipping **class-based Blade components**. It introspects component classes
and emits three outputs:

- **VS Code Custom Data** — `<base>.html-data.json` (attribute name/value completion + hover).
- **VS Code snippets** — `<base>.code-snippets` (zero-install fallback that scaffolds `<x-…>` tags).
- **PhpStorm / Laravel Idea** — `ide.json` (tag → component-class mapping).

Package name: `forxer/blade-components-ide-helper` (Packagist, MIT). It does not ship components of
its own; consumers describe theirs.

## Ecosystem (important context)

This library is one of three related projects:

- **This package** produces the metadata files.
- **VS Code extension** `forxer.blade-components-ide-helper` (separate repo
  `/var/packages-dev/blade-components-ide-helper-vscode`) **consumes** the `*.html-data.json` and is
  the **recommended** completion solution. It is published on the Marketplace + Open VSX.
- **Consumers** that register a target: `forxer/blade-ui-kit-bootstrap` (public) and
  `axn/laravel-medialibrary-helpers` (private). The host app (`/var/www/html`) wires one
  `post-autoload-dump` line that calls the aggregate command.

**Snippets vs extension — pick ONE VS Code output.** Both complete `<x-…>`, and when generated
together the snippets outrank the extension's suggestions (VS Code ranks input-prefix match above
`sortText`, by design). If the team uses the extension, generate `--json --ide-json` (skip
`--snippets`); otherwise generate `--snippets --ide-json`. `ide.json` (PhpStorm) is independent —
keep it either way.

## Architecture

```
src/Definition/     ComponentDefinition (components alias=>class, prefix, attributeSurface,
                    slotStrategy, snippetValueAttributes) + IdeTarget (definition + fileBaseName
                    + ideJsonSubdirectory). These are the public contract for consumers.
src/Registry/       IdeTargetRegistry — consumers call ::register() in boot(); the aggregate
                    command iterates ::all().
src/Introspection/  ComponentIntrospector (builds the model) + DocblockParser (reads a @var
                    'a'|'b' literal union for constrained values, and @param/property summaries).
src/Attributes/     AttributeSurface strategy — what counts as a settable attribute:
                    ConstructorParametersSurface (default) | PropertiesAndConstructorSurface. Both
                    delegate the actual reflection to `AttributeReflector`, which now lives in the
                    separate package `forxer/blade-components-reflection` (a `require`, not
                    `require-dev`, of this package) — this package itself stays `require-dev` in
                    every consumer.
src/Slots/          SlotStrategy — does the component accept inner content:
                    ViewScanningSlotStrategy (default: render + scan for $slot) | NullSlotStrategy.
src/Emitters/       HtmlDataEmitter (json) | SnippetsEmitter | IdeJsonEmitter — pure array builders.
src/Generation/     IdeGenerator::generate(target, formats, vscodeDir, ideDir).
src/Commands/       GenerateIdeMetadataCommand (aggregate: blade-components-ide-helper:generate)
                    + AbstractIdeCommand (base class for a consumer's own per-package command).
```

Consumers wire it as shown in `README.md` ("Wiring a command"): expose a `static ideTarget(): IdeTarget`
in their service provider, `IdeTargetRegistry::register()` it in `boot()`, and optionally add a thin
per-package command extending `AbstractIdeCommand`.

## Gotchas (do not regress)

- **Byte-identical output is a proven property.** The extraction from `blade-ui-kit-bootstrap` proved
  the generated files are byte-for-byte identical to the pre-extraction output. Encoding is fixed:
  `json_encode(..., JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)`
  plus a trailing `PHP_EOL`. Do not change formatting flags casually — it churns every consumer's
  committed files.
- **`ide.json` must be named exactly `ide.json`** (Laravel Idea only reads that name and recursively
  merges every one it finds). It is written to a **package-owned subfolder** `ide-helper/<base>/`,
  never the project root nor `.vscode/`, to avoid collision. `.html-data.json`/`.code-snippets` go to
  `.vscode/` and are namespaced by `fileBaseName`, so multiple packages coexist.
- **`ViewScanningSlotStrategy` degrades gracefully.** It instantiates the component with dummy args
  and renders it; any failure falls back to "no slot" (self-closing). Keep that failure tolerance.

## Commands

```bash
composer install
vendor/bin/pest              # tests (Pest 3 + Orchestra Testbench)
vendor/bin/pest --filter=... # single test
vendor/bin/rector process    # refactor
vendor/bin/pint              # format — run AFTER rector (see git history)
```

Runtime (in a host app that has registered consumers):

```bash
php artisan blade-components-ide-helper:generate --json --ide-json   # aggregate, all consumers
php artisan blade-components-ide-helper:generate --only=base1,base2   # restrict
```

Interactive `multiselect` prompts for packages when run in a TTY with no `--only`.

## Git workflow

Two long-lived branches, `main` (default, latest published) and `develop` (next version); base work
on `develop`, PR `develop → main`, tag on `main`.

**Tag style differs from the VS Code extension:** this package is a Packagist library, tagged
**without a `v` prefix** (e.g. `1.0.0`). The extension repo uses `v1.0.4`. Match each repo's existing
convention.

## Working constraints

- **Never tag/release or `git push` without an explicit request.** The user tests manually first and
  drives all GitHub/Packagist actions himself.
- **Never commit without proposing the message first** (Conventional Commits) and getting approval.
- **No reference to Claude/AI** in commit messages.
- Do **not** commit `docs/superpowers/**` (specs/plans are working documents, git-ignored).
- English only for code, comments, identifiers, and docs.
