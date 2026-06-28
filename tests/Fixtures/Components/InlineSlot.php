<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components;

use Illuminate\View\Component;

class InlineSlot extends Component
{
    public function render(): string
    {
        return '<div>{{ $slot }}</div>';
    }
}
