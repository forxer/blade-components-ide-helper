CHANGELOG
=========

1.0.0-RC.2 (2026-06-29)
-----------------------

- Add an `IdeTarget` value object, an `IdeTargetRegistry` (consumers declare themselves) and an
  `IdeGenerator` service.
- Add the aggregate command `blade-components-ide-helper:generate` that regenerates every
  registered consumer at once (`--only`, format flags, interactive selection).
- The package now ships an auto-discovered service provider hosting the registry and the
  aggregate command.
- BREAKING: `AbstractIdeCommand` now requires a single `target(): IdeTarget` method, replacing
  `definition()`, `fileBaseName()` and `ideJsonSubdirectory()`.

1.0.0-RC.1 (2026-06-28)
-----------------------

- First release: generate IDE metadata (VS Code snippets & Custom Data, PhpStorm/Laravel Idea `ide.json`) for Laravel packages shipping class-based Blade components.
- Contract `ComponentDefinition` with pluggable `AttributeSurface` and `SlotStrategy` strategies, plus an `AbstractIdeCommand` consumers extend.
