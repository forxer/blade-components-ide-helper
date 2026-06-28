<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests\Fixtures;

use Illuminate\Support\ServiceProvider;

final class TestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/views', 'fixtures');

        if ($this->app->runningInConsole() && class_exists(TestIdeCommand::class)) {
            $this->commands([TestIdeCommand::class]);
        }
    }
}
