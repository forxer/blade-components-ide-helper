Upgrade
=======

From 1.x to 2.0.0
-----------------

### `AttributeReflector` moved to a dedicated package

The runtime reflection utility `AttributeReflector` has been extracted into a new, standalone package
[`forxer/blade-components-reflection`](https://packagist.org/packages/forxer/blade-components-reflection).
Its API is unchanged — the class is a verbatim move, only its namespace changed:

| Before                                                        | After                                            |
|--------------------------------------------------------------|--------------------------------------------------|
| `Forxer\BladeComponentsIdeHelper\Reflection\AttributeReflector` | `Forxer\BladeComponentsReflection\AttributeReflector` |

The two static methods (`settableProperties()` and `constructorParameters()`) keep the same
signatures and return shapes.

**Why:** `blade-components-ide-helper` is a metadata generator — a development tool. The only piece it
exposed for use at *render time* was `AttributeReflector`. Splitting it out lets this package be a
`require-dev` dependency everywhere, while packages that need reflection at runtime depend on the
small `blade-components-reflection` package instead.

### What you need to do

**If you only use this package to generate IDE metadata** (you never reference `AttributeReflector`
in your own runtime code — this is the common case):

- Nothing beyond bumping the constraint. Keep it in `require-dev`:

  ```bash
  composer require --dev "forxer/blade-components-ide-helper:^2.0"
  ```

**If your package uses `AttributeReflector` at runtime** (for example, to hydrate public component
properties from the Blade attribute bag while rendering):

1. Require the new package as a runtime dependency, and move this one to `require-dev`:

   ```bash
   composer require "forxer/blade-components-reflection:^1.0"
   composer require --dev "forxer/blade-components-ide-helper:^2.0"
   ```

2. Update the import in your runtime code:

   ```diff
   - use Forxer\BladeComponentsIdeHelper\Reflection\AttributeReflector;
   + use Forxer\BladeComponentsReflection\AttributeReflector;
   ```

   Call sites are unchanged (`AttributeReflector::settableProperties($class)`,
   `AttributeReflector::constructorParameters($class)`).

No other public API changed in this release.
