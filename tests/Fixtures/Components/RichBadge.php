<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests\Fixtures\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RichBadge extends Component
{
    /**
     * Bootstrap color variant of the badge.
     *
     * @var 'primary'|'secondary'|'danger'|null
     */
    public ?string $variant = null;

    /** Pill style (rounded corners). */
    public bool $pill = false;

    /**
     * @param  string  $label  Badge label. Required.
     */
    public function __construct(
        public string $label,
    ) {}

    public function render(): View
    {
        return view('fixtures::card');
    }
}
