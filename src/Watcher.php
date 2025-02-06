<?php
declare(strict_types=1);

namespace Cydrickn\PHPWatcher;

use Cydrickn\PHPWatcher\Adapters\AdapterInterface;
use Cydrickn\PHPWatcher\Enum\ChangeType;
use RecursiveDirectoryIterator;
use RecursiveTreeIterator;
use Swoole\Timer;

class Watcher
{
    private array $files = [];
    private array $changes = [];
    private bool $initialized = false;
    private bool $checking = false;
    private array $excludeFiles = [];
    private array $excludeDirs = [];
    private mixed $handler;

    public function __construct(private readonly AdapterInterface $adapter, private readonly array $watchFor, array $excludes, callable $handler, private readonly int $interval = 1000)
    {
        foreach ($excludes as $exclude) {
            if (is_dir($exclude)) {
                $this->excludeDirs[] = $exclude;
            } else {
                $this->excludeFiles[] = $exclude;
            }
        }
        $this->handler = $handler;
    }

    protected function addChange(string $filename, ChangeType $type): void
    {
        $this->changes[] = [
            'name' => $filename,
            'type' => $type,
            'data' => $type !== ChangeType::DELETED ? filemtime($filename) : null,
        ];
    }

    protected function checkFile(string $file, bool $checkForDelete = false): void
    {
        if (array_key_exists($file, $this->files) && !file_exists($file)) {
            $this->addChange($file, ChangeType::DELETED);
            return;
        }

        if ($checkForDelete) {
            return;
        }

        if (!array_key_exists($file, $this->files) && file_exists($file)) {
            $this->addChange($file, ChangeType::NEW);
            return;
        }

        if (!(array_key_exists($file, $this->files) && file_exists($file))) {
            return;
        }

        $data = $this->files[$file];
        $checkData = filemtime($file);

        if ($checkData !== $data) {
            $this->addChange($file, ChangeType::UPDATED);
        }
    }

    protected function clearChanges(): void
    {
        $this->changes = [];
    }

    protected function commit(): void
    {
        $totalChanges = count($this->changes);
        foreach ($this->changes as $change) {
            if ($change['type'] === ChangeType::DELETED) {
                unset($this->files[$change['name']]);
                continue;
            }
            $this->files[$change['name']] = $change['data'];
        }

        if ($totalChanges > 0 && $this->initialized) {
            call_user_func($this->handler, $this->changes);
        }

        $this->clearChanges();
    }

    public function checkChanges(): void
    {
        if ($this->checking) {
            return;
        }

        $this->checking = true;

        foreach ($this->watchFor as $watchFor) {
            if (is_dir($watchFor)) {
                $allFiles = new RecursiveTreeIterator(new RecursiveDirectoryIterator($watchFor, RecursiveDirectoryIterator::SKIP_DOTS));
                foreach ($allFiles as $file) {
                    $filename = trim(str_replace(['|', ' ', '~', '\\'], '', $file), '-');
                    $filename = is_dir($filename) ? $filename . '/' : $filename;
                    if ((is_file($filename) && in_array($filename, $this->excludeFiles)) || (is_dir($filename) && in_array($filename, $this->excludeDirs))) {
                        continue;
                    }
                    foreach ($this->excludeDirs as $dir) {
                        if (str_starts_with($filename, $dir)) {
                            continue 2;
                        }
                    }
                    $this->checkFile($filename);
                }
                continue;
            }
            $this->checkFile($watchFor);
        }

        // Checks the deleted files
        foreach ($this->files as $key => $file) {
            $this->checkFile($key, true);
        }

        $this->commit();

        if (!$this->initialized) {
            $this->initialized = true;
        }

        $this->checking = false;
    }

    public function tick(): void
    {
        $this->adapter->tick([$this, 'checkChanges'], $this->interval);
    }
}
