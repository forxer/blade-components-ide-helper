<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Tests;

use Forxer\BladeComponentsIdeHelper\IdeHelperServiceProvider;
use Forxer\BladeComponentsIdeHelper\Tests\Fixtures\TestServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            IdeHelperServiceProvider::class,
            TestServiceProvider::class,
        ];
    }
}
