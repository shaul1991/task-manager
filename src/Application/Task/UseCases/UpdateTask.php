<?php

declare(strict_types=1);

namespace Src\Application\Task\UseCases;

use Src\Application\Task\DTOs\TaskDTO;
use Src\Application\Task\DTOs\UpdateTaskDTO;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\Task\ValueObjects\TaskDescription;
use Src\Domain\Task\ValueObjects\TaskTitle;
use Src\Shared\Exceptions\NotFoundException;

/**
 * Update Task Use Case
 *
 * 기존 Task를 수정합니다.
 */
final readonly class UpdateTask
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param int $taskId 수정할 Task ID
     * @param UpdateTaskDTO $dto 수정 요청 데이터
     * @return TaskDTO 수정된 Task DTO
     * @throws NotFoundException Task가 존재하지 않을 경우
     * @throws \Src\Domain\Task\Exceptions\InvalidTaskTitleException
     * @throws \Src\Domain\Task\Exceptions\TaskTitleTooLongException
     */
    public function execute(int $taskId, UpdateTaskDTO $dto): TaskDTO
    {
        // Task 조회
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            throw new NotFoundException(
                message: 'Task not found',
                context: ['task_id' => $taskId]
            );
        }

        // 제목 수정
        if ($dto->hasTitleUpdate()) {
            $task->updateTitle(new TaskTitle($dto->title));
        }

        // 설명 수정
        if ($dto->hasDescriptionUpdate()) {
            $task->updateDescription(new TaskDescription($dto->description));
        }

        // TaskList ID 수정
        if ($dto->hasTaskListIdUpdate()) {
            if ($dto->taskListId === null) {
                $task->removeFromTaskList();
            } else {
                $task->assignToTaskList($dto->taskListId);
            }
        }

        // 완료 상태 수정
        if ($dto->hasCompletedUpdate()) {
            if ($dto->completed) {
                $task->complete();
            } else {
                $task->uncomplete();
            }
        }

        // Repository를 통해 저장
        $updatedTask = $this->taskRepository->save($task);

        // DTO로 변환하여 반환
        return TaskDTO::fromEntity($updatedTask);
    }
}
