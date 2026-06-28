<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VanillaCard extends Component
{
    public function render(): View
    {
        return view('fixtures::card');
    }
}
