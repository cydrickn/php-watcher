<?php

declare(strict_types=1);

namespace Cydrickn\PHPWatcher\Adapters;

use Swoole\Timer;

class SwooleTimerAdapter implements AdapterInterface
{
    public function tick(callable $handler, int $interval = 1000): void
    {
        Timer::tick($interval, function () use ($handler) {
            call_user_func($handler);
        });
    }
}