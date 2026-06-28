<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VanillaInput extends Component
{
    /**
     * @param  string  $name  Field name. Required.
     * @param  string  $type  Input type. Defaults to `text`.
     */
    public function __construct(
        public string $name,
        public string $type = 'text',
    ) {}

    public function render(): View
    {
        return view('fixtures::input');
    }
}
