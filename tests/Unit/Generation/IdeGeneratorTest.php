<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Definition\ComponentDefinition;
use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;
use Forxer\BladeComponentsIdeHelper\Generation\IdeGenerator;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\RichBadge;
use Illuminate\Support\Facades\File;

afterEach(fn () => File::deleteDirectory(base_path('build')));

it('writes the requested formats and returns their paths', function (): void {
    $target = new IdeTarget(
        definition: new ComponentDefinition(components: ['badge' => RichBadge::class]),
        fileBaseName: 'gen-demo',
    );

    $written = app(IdeGenerator::class)->generate(
        target: $target,
        formats: ['snippets', 'json', 'ide-json'],
        vscodeDirectory: base_path('build/vscode'),
        ideDirectory: base_path('build/idea'),
    );

    expect(File::exists(base_path('build/vscode/gen-demo.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('build/vscode/gen-demo.html-data.json')))->toBeTrue()
        ->and(File::exists(base_path('build/idea/ide.json')))->toBeTrue()
        ->and($written)->toHaveCount(3);
});

it('writes only the formats requested', function (): void {
    $target = new IdeTarget(
        definition: new ComponentDefinition(components: ['badge' => RichBadge::class]),
        fileBaseName: 'gen-demo',
    );

    app(IdeGenerator::class)->generate(
        target: $target,
        formats: ['snippets'],
        vscodeDirectory: base_path('build/vscode'),
        ideDirectory: base_path('build/idea'),
    );

    expect(File::exists(base_path('build/vscode/gen-demo.code-snippets')))->toBeTrue()
        ->and(File::exists(base_path('build/vscode/gen-demo.html-data.json')))->toBeFalse()
        ->and(File::exists(base_path('build/idea/ide.json')))->toBeFalse();
});
