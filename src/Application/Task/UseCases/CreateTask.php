<?php

declare(strict_types=1);

namespace Src\Application\Task\UseCases;

use Src\Application\Task\DTOs\CreateTaskDTO;
use Src\Application\Task\DTOs\TaskDTO;
use Src\Domain\Task\Entities\Task;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\ValueObjects\TaskTitle;

/**
 * Create Task Use Case
 *
 * 새로운 Task를 생성합니다.
 */
final readonly class CreateTask
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param CreateTaskDTO $dto 생성 요청 데이터
     * @return TaskDTO 생성된 Task DTO
     * @throws \Src\Domain\Task\Exceptions\InvalidTaskTitleException
     * @throws \Src\Domain\Task\Exceptions\TaskTitleTooLongException
     */
    public function execute(CreateTaskDTO $dto): TaskDTO
    {
        // Domain Entity 생성
        $task = Task::create(
            title: new TaskTitle($dto->title),
            description: new TaskDescription($dto->description),
            groupId: $dto->groupId
        );

        // Repository를 통해 저장
        $savedTask = $this->taskRepository->save($task);

        // DTO로 변환하여 반환
        return TaskDTO::fromEntity($savedTask);
    }
}
