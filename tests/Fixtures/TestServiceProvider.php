<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests\Fixtures;

use Forxer\BladeComponentsIdeHelper\Registry\IdeTargetRegistry;
use Illuminate\Support\ServiceProvider;

final class TestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/views', 'fixtures');

        if (class_exists(IdeTargetRegistry::class)) {
            IdeTargetRegistry::register(TestIdeCommand::ideTarget());
            IdeTargetRegistry::register(OtherIdeCommand::ideTarget());
        }

        if ($this->app->runningInConsole()) {
            if (class_exists(TestIdeCommand::class)) {
                $this->commands([TestIdeCommand::class]);
            }

            if (class_exists(OtherIdeCommand::class)) {
                $this->commands([OtherIdeCommand::class]);
            }
        }
    }
}
