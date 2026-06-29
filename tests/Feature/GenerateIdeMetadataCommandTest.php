<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

afterEach(function (): void {
    File::deleteDirectory(base_path('.vscode'));
    File::deleteDirectory(base_path('ide-helper'));
});

it('regenerates every registered target non-interactively without collision', function (): void {
    $this->artisan('blade-components-ide-helper:generate', ['--no-interaction' => true])->assertSuccessful();

    expect(File::exists(base_path('.vscode/fixtures.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/other-package.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/fixtures.html-data.json')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/other-package.html-data.json')))->toBeTrue()
        ->and(File::exists(base_path('ide-helper/fixtures/ide.json')))->toBeTrue()
        ->and(File::exists(base_path('ide-helper/other-package/ide.json')))->toBeTrue();
});

it('restricts to the targets named in --only', function (): void {
    $this->artisan('blade-components-ide-helper:generate', ['--only' => 'other-package', '--no-interaction' => true])
        ->assertSuccessful();

    expect(File::exists(base_path('.vscode/other-package.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/fixtures.code-snippets')))->toBeFalse();
});

it('restricts formats with a format flag', function (): void {
    $this->artisan('blade-components-ide-helper:generate', ['--snippets' => true, '--no-interaction' => true])
        ->assertSuccessful();

    expect(File::exists(base_path('.vscode/fixtures.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/fixtures.html-data.json')))->toBeFalse();
});
