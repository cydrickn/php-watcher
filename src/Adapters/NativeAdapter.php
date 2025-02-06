<?php

declare(strict_types=1);

namespace Cydrickn\PHPWatcher\Adapters;

class NativeAdapter implements AdapterInterface
{
    public function tick(callable $handler, int $interval = 1000): void
    {
        do {
            call_user_func($handler);
            sleep($interval / 1000);
        } while (true);
    }
}