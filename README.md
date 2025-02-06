# PHP Watcher

A simple directory and file watcher that was made using PHP.

## Requirements

* PHP >= 8.1

## Installation

```shell
composer require cydrickn/php-watcher
```

## Usage

To use this package you just need to initialize the watcher and call the tick function

```php
<?php

require_once './vendor/autoload.php';

use \Cydrickn\PHPWatcher\Adapters\SwooleTimerAdapter;

$watcher = new \Cydrickn\PHPWatcher\Watcher(
    new SwooleTimerAdapter(),
    [__DIR__],
    [__DIR__ . '/vendor/'],
    function (array $changes) {
        echo json_encode($changes) . PHP_EOL;
    }
);

$watcher->tick();
```

## Adapters

Currently, we have 3 premade adapters:

### \Cydrickn\PHPWatcher\Adapters\SwooleTimerAdapter

If you are using Swoole/Openswoole then best choice to use this adapter

### \Cydrickn\PHPWatcher\Adapters\NativeAdapter

If not using Swoole/Openswoole then you can go for this one, it uses PHP's do-while and sleep

### \Cydrickn\PHPWatcher\Adapters\HybridAdapter

This is same as the version 1 where it will automatically use the SwooleTimerAdapter, if available or fallback to NativeAdapter.
This might be useful in a mixed environment.

## Custom Adapter

If you don't want to use any pre-made adapter you can create your own adapter.
Example:

```php
<?php

namespace Your\Namespace;

use Cydrickn\PHPWatcher\Adapters\AdapterInterface;

class YourAdapter implements AdapterInterface
{
    public function tick(callable $handler, int $interval = 1000)
    {
        // Implement your own logic
        // Also make sure to call the handler to trigger the checking of changed files
        // e.g. call_user_func($handler);
    }
}
```