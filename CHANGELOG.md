CHANGELOG
=========

2.0.0 (2026-07-02)
------------------

- BREAKING: `AttributeReflector` moved to the new package `forxer/blade-components-reflection`. Update
  imports to `Forxer\BladeComponentsReflection\AttributeReflector`. See `UPGRADE.md`.
- `blade-components-ide-helper` is now a pure dev tool: require it as `require-dev` everywhere.

1.0.0 (2026-06-29)
------------------

- First stable release: generate IDE metadata (VS Code snippets & Custom Data, PhpStorm/Laravel Idea `ide.json`) for Laravel packages shipping class-based Blade components.
- Contract `ComponentDefinition` with pluggable `AttributeSurface` and `SlotStrategy` strategies; consumers extend `AbstractIdeCommand` and implement `target(): IdeTarget`.
- `IdeTargetRegistry` lets consumers declare themselves; the aggregate command `blade-components-ide-helper:generate` regenerates every registered consumer at once (`--only`, format flags, interactive selection).
- Auto-discovered service provider hosting the registry and the aggregate command.
