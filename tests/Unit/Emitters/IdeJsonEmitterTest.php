<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Emitters\IdeJsonEmitter;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaInput;

it('maps tag names to component classes', function (): void {
    $out = IdeJsonEmitter::emit(['x-input' => VanillaInput::class]);

    expect($out['$schema'])->toContain('laravel-ide.com')
        ->and($out['blade']['components'])->toBe([
            ['name' => 'x-input', 'class' => VanillaInput::class],
        ]);
});
