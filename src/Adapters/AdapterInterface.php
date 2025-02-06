<?php

declare(strict_types=1);

namespace Cydrickn\PHPWatcher\Adapters;

interface AdapterInterface
{
    public function tick(callable $handler, int $interval = 1000): void;
}