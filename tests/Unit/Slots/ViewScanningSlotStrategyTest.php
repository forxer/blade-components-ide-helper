<?php

declare(strict_types=1);

use Forxer\BladeComponentsIdeHelper\Slots\NullSlotStrategy;
use Forxer\BladeComponentsIdeHelper\Slots\ViewScanningSlotStrategy;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\InlineSlot;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaCard;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components\VanillaInput;

it('detects a slot in a file-based view', function (): void {
    expect((new ViewScanningSlotStrategy())->hasSlot(VanillaCard::class))->toBeTrue();
});

it('reports no slot for a slotless file view (required ctor arg handled)', function (): void {
    expect((new ViewScanningSlotStrategy())->hasSlot(VanillaInput::class))->toBeFalse();
});

it('detects a slot in an inline string template', function (): void {
    expect((new ViewScanningSlotStrategy())->hasSlot(InlineSlot::class))->toBeTrue();
});

it('the null strategy always reports no slot', function (): void {
    expect((new NullSlotStrategy())->hasSlot(VanillaCard::class))->toBeFalse();
});
