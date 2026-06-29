<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Registry\IdeTargetRegistry;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    // Start from an empty registry, independent of targets registered during boot.
    $this->app->singleton(IdeTargetRegistry::class);
});

afterEach(function (): void {
    File::deleteDirectory(base_path('.vscode'));
    File::deleteDirectory(base_path('ide-helper'));
});

it('is a no-op success when no target is registered', function (): void {
    expect(IdeTargetRegistry::all())->toBe([]);

    $this->artisan('blade-components-ide-helper:generate', ['--no-interaction' => true])
        ->assertSuccessful();

    expect(File::exists(base_path('.vscode')))->toBeFalse()
        ->and(File::exists(base_path('ide-helper')))->toBeFalse();
});
