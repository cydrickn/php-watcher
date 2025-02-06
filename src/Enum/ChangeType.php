<?php

declare(strict_types=1);

namespace Cydrickn\PHPWatcher\Enum;

enum ChangeType: int
{
    case NEW = 1;
    case UPDATED = 2;
    case DELETED = 3;
}
