<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper;

use Forxer\BladeComponentsIdeHelper\Commands\GenerateIdeMetadataCommand;
use Forxer\BladeComponentsIdeHelper\Registry\IdeTargetRegistry;
use Illuminate\Support\ServiceProvider;

class IdeHelperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IdeTargetRegistry::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([GenerateIdeMetadataCommand::class]);
        }
    }
}
