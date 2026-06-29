<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

afterEach(function (): void {
    File::deleteDirectory(base_path('.vscode'));
    File::deleteDirectory(base_path('ide-helper'));
});

it('lets two packages generate coexisting, non-colliding IDE files', function (): void {
    $this->artisan('fixtures:ide', ['--no-interaction' => true])->assertSuccessful();
    $this->artisan('other:ide', ['--no-interaction' => true])->assertSuccessful();

    // Both packages' VS Code files coexist.
    expect(File::exists(base_path('.vscode/fixtures.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/other-package.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/fixtures.html-data.json')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/other-package.html-data.json')))->toBeTrue();

    // ide.json files live in separate, package-owned subfolders (never collide).
    expect(File::exists(base_path('ide-helper/fixtures/ide.json')))->toBeTrue()
        ->and(File::exists(base_path('ide-helper/other-package/ide.json')))->toBeTrue();

    // Each ide.json only contains its own package's components.
    $fixtures = json_decode(File::get(base_path('ide-helper/fixtures/ide.json')), true);
    $other = json_decode(File::get(base_path('ide-helper/other-package/ide.json')), true);
    $fixturesTags = collect($fixtures['blade']['components'])->pluck('name');
    $otherTags = collect($other['blade']['components'])->pluck('name');

    expect($fixturesTags)->toContain('x-badge')->not->toContain('x-other-input')
        ->and($otherTags)->toContain('x-other-input')->not->toContain('x-badge');
});

it('produces both packages coexisting files from a single aggregate run', function (): void {
    $this->artisan('blade-components-ide-helper:generate', ['--no-interaction' => true])->assertSuccessful();

    $fixtures = json_decode(File::get(base_path('ide-helper/fixtures/ide.json')), true);
    $other = json_decode(File::get(base_path('ide-helper/other-package/ide.json')), true);
    $fixturesTags = collect($fixtures['blade']['components'])->pluck('name');
    $otherTags = collect($other['blade']['components'])->pluck('name');

    expect($fixturesTags)->toContain('x-badge')->not->toContain('x-other-input')
        ->and($otherTags)->toContain('x-other-input')->not->toContain('x-badge');
});
