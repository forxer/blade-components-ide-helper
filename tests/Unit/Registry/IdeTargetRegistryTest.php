<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;
use Forxer\BladeComponentsIdeHelper\Registry\IdeTargetRegistry;

beforeEach(function (): void {
    // Temporary: the package service provider (added in a later task) will bind this
    // singleton. Until then, bind it here so the static proxies share one instance.
    $this->app->singleton(IdeTargetRegistry::class);
});

function makeTarget(string $base): IdeTarget
{
    return new IdeTarget(
        definition: new ComponentDefinition(components: []),
        fileBaseName: $base,
    );
}

it('registers and lists targets', function (): void {
    IdeTargetRegistry::register(makeTarget('alpha'));
    IdeTargetRegistry::register(makeTarget('beta'));

    expect(IdeTargetRegistry::all())->toHaveCount(2)
        ->and(collect(IdeTargetRegistry::all())->pluck('fileBaseName')->all())
        ->toEqualCanonicalizing(['alpha', 'beta']);
});

it('finds a target by file base name', function (): void {
    $alpha = makeTarget('alpha');
    IdeTargetRegistry::register($alpha);

    expect(IdeTargetRegistry::get('alpha'))->toBe($alpha)
        ->and(IdeTargetRegistry::get('missing'))->toBeNull();
});

it('is idempotent by file base name (re-register replaces, never duplicates)', function (): void {
    IdeTargetRegistry::register(makeTarget('alpha'));
    $replacement = makeTarget('alpha');
    IdeTargetRegistry::register($replacement);

    expect(IdeTargetRegistry::all())->toHaveCount(1)
        ->and(IdeTargetRegistry::get('alpha'))->toBe($replacement);
});
