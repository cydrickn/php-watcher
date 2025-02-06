<?php

declare(strict_types=1);

namespace Cydrickn\PHPWatcher\Adapters;

class HybridAdapter implements AdapterInterface
{
    private NativeAdapter $nativeAdapter;
    private SwooleTimerAdapter $swooleTimer;

    public function __construct()
    {
       $this->nativeAdapter = new NativeAdapter();
       $this->swooleTimer  = new SwooleTimerAdapter();
    }

    public function tick(callable $handler, int $interval = 1000): void
    {
        if (extension_loaded('swoole') || extension_loaded('openswoole')) {
            $this->swooleTimer->tick($handler, $interval);
        } else {
            $this->nativeAdapter->tick($handler, $interval);
        }
    }
}
