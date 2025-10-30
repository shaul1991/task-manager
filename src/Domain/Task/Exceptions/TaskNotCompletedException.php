<?php

declare(strict_types=1);

namespace Src\Domain\Task\Exceptions;

use DomainException;

final class TaskNotCompletedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Task is not completed yet');
    }
}
