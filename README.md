# PHP Watcher

A simple directory and file watcher that was made using PHP.

## Installation

```shell
composer require cydrickn/php-watcher
```

## Usage

To use this package you just need to initialize the watcher and call the tick function

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

## \Cydrickn\PHPWatcher\Watcher::__construct

\Cydrickn\PHPWatcher\Watcher::__construct - Creates the instance representing the watcher

### Description

```php
public \Cydrickn\PHPWatcher\Watcher::__construct(
    array $watchFor,
    array $excludes,
    callable $handler,
    int $interval = 1000
)
```

### Parameters

**watchFor**

List of files and folder that will watch by the watcher.

For folders this will include its sub-folders.

**excludes**

List of files and folder that will be excluded in watching.

For folders this will include its sub-folders.

**handler**

A function that will be called once their are changes

**interval**

This is delay for how long it will wait before it will do checking of the files / folders. Default to 1000 milliseconds.

## \Cydrickn\PHPWatcher\Watcher::checkChanges

\Cydrickn\PHPWatcher\Watcher::checkChanges - Check the changes

### Description

```php
public \Cydrickn\PHPWatcher\Watcher::checkChanges(): void
```

## \Cydrickn\PHPWatcher\Watcher::tick

\Cydrickn\PHPWatcher\Watcher::tick - Start the watching of files

### Description

```php
public \Cydrickn\PHPWatcher\Watcher::tick(
    ?callable $handler = null
): void
```

### Parameters

**handler**

A callable use for watching the file, this is default to null.

Once the handler is null, it will use the default handler.

There are two default handler.

- Swoole\Timer - Will be only use when swoole is enabled in your server
- The do while - If the swoole is not enabled

Once you pass your own handler, this will pass two argument

- The first argument would be the checkChanges function
- The second is the interval
