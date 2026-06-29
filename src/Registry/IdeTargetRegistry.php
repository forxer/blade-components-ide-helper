<?php

declare(strict_types=1);

namespace Forxer\BladeComponentsIdeHelper\Registry;

use Forxer\BladeComponentsIdeHelper\Definition\IdeTarget;

class IdeTargetRegistry
{
    /** @var array<string, IdeTarget> */
    private array $targets = [];

    public function add(IdeTarget $target): void
    {
        $this->targets[$target->fileBaseName] = $target;
    }

    /**
     * @return list<IdeTarget>
     */
    public function targets(): array
    {
        return array_values($this->targets);
    }

    public function find(string $fileBaseName): ?IdeTarget
    {
        return $this->targets[$fileBaseName] ?? null;
    }

    public static function register(IdeTarget $target): void
    {
        app(self::class)->add($target);
    }

    /**
     * @return list<IdeTarget>
     */
    public static function all(): array
    {
        return app(self::class)->targets();
    }

    public static function get(string $fileBaseName): ?IdeTarget
    {
        return app(self::class)->find($fileBaseName);
    }
}
