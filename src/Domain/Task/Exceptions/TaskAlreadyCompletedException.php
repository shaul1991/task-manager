<?php

declare(strict_types=1);

namespace Src\Domain\Task\Exceptions;

use DomainException;

final class TaskAlreadyCompletedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Task is already completed');
    }
}
