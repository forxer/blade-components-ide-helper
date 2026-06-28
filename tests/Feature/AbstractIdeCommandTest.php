<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

afterEach(function (): void {
    File::deleteDirectory(base_path('.vscode'));
    File::deleteDirectory(base_path('ide-helper'));
    File::deleteDirectory(base_path('build'));
});

it('generates all three files non-interactively, ide.json in its own folder', function (): void {
    $this->artisan('fixtures:ide', ['--no-interaction' => true])->assertSuccessful();

    expect(File::exists(base_path('.vscode/fixtures.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/fixtures.html-data.json')))->toBeTrue()
        ->and(File::exists(base_path('ide-helper/fixtures/ide.json')))->toBeTrue()
        ->and(File::exists(base_path('ide.json')))->toBeFalse()
        ->and(File::exists(base_path('.vscode/ide.json')))->toBeFalse();

    $ideJson = json_decode(File::get(base_path('ide-helper/fixtures/ide.json')), true);
    expect(collect($ideJson['blade']['components'])->pluck('name'))->toContain('x-badge');
});

it('restricts output when a single format flag is passed', function (): void {
    $this->artisan('fixtures:ide', ['--snippets' => true, '--no-interaction' => true])->assertSuccessful();

    expect(File::exists(base_path('.vscode/fixtures.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('.vscode/fixtures.html-data.json')))->toBeFalse();
});

it('honours a custom --output directory', function (): void {
    $this->artisan('fixtures:ide', ['--output' => 'build/ide', '--json' => true, '--no-interaction' => true])
        ->assertSuccessful();

    expect(File::exists(base_path('build/ide/fixtures.html-data.json')))->toBeTrue();
});

it('honours a custom --ide-output directory', function (): void {
    $this->artisan('fixtures:ide', ['--ide-output' => 'build/idea', '--ide-json' => true, '--no-interaction' => true])
        ->assertSuccessful();

    expect(File::exists(base_path('build/idea/ide.json')))->toBeTrue();
});
