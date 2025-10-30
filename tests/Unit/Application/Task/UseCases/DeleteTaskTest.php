<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Task\UseCases;

use PHPUnit\Framework\TestCase;
use Src\Application\Task\UseCases\DeleteTask;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Shared\Exceptions\NotFoundException;

final class DeleteTaskTest extends TestCase
{
    public function test_할일을_삭제함(): void
    {
        // Given
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('existsById')
            ->with(1)
            ->willReturn(true);
        $repository->expects($this->once())
            ->method('delete')
            ->with(1);

        $useCase = new DeleteTask($repository);

        // When
        $useCase->execute(1);

        // Then - 예외 없이 완료
        $this->assertTrue(true);
    }

    public function test_존재하지_않는_할일_삭제_시_예외_발생(): void
    {
        // Given
        $repository = $this->createMock(TaskRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('existsById')
            ->with(999)
            ->willReturn(false);

        $useCase = new DeleteTask($repository);

        // Then
        $this->expectException(NotFoundException::class);

        // When
        $useCase->execute(999);
    }
}
