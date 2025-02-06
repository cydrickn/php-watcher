# Upgrade Notes

When upgrading to a newer version, some configuration structures must be updated before applying the modifications.

## 2.0

- Upgrade your PHP version to 8.1 or higher

### Breaking changes

It's expecting the when upgrading from version 1.x to 2.0 that it will break,
this is due to the new version is now using Adapter, where you need to pass the Adapter instead of automatically
detecting the Swoole Timer and use that.

From this code (v1)
```php
<?php

require_once './vendor/autoload.php';

$watcher = new \Cydrickn\PHPWatcher\Watcher(
    [__DIR__],
    [__DIR__ . '/vendor/'],
    function (array $changes) {
        echo json_encode($changes) . PHP_EOL;
    }
);

$watcher->tick();
```

To this code (v2)
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

Take note that `tick()` method now don't accept any parameter unlike from version 1.