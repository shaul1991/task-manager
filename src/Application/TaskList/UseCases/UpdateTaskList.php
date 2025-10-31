<?php

declare(strict_types=1);

namespace Src\Application\TaskList\UseCases;

use Src\Application\TaskList\DTOs\TaskListDTO;
use Src\Application\TaskList\DTOs\UpdateTaskListDTO;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;
use Src\Domain\TaskList\ValueObjects\TaskListDescription;
use Src\Domain\TaskList\ValueObjects\TaskListName;
use Src\Shared\Exceptions\NotFoundException;

/**
 * Update TaskList Use Case
 *
 * TaskList를 업데이트합니다.
 */
final readonly class UpdateTaskList
{
    public function __construct(
        private TaskListRepositoryInterface $taskListRepository,
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param int $id TaskList ID
     * @param UpdateTaskListDTO $dto 업데이트 요청 데이터
     * @return TaskListDTO 업데이트된 TaskList DTO
     * @throws NotFoundException TaskList를 찾을 수 없는 경우
     * @throws \Src\Domain\TaskList\Exceptions\InvalidTaskListNameException
     * @throws \Src\Domain\TaskList\Exceptions\TaskListNameTooLongException
     */
    public function execute(int $id, UpdateTaskListDTO $dto): TaskListDTO
    {
        // TaskList 조회
        $taskList = $this->taskListRepository->findById($id);

        // 존재하지 않으면 예외 발생
        if ($taskList === null) {
            throw new NotFoundException("TaskList를 찾을 수 없습니다. ID: {$id}");
        }

        // name이 제공되었으면 업데이트
        if ($dto->name !== null) {
            $taskList->updateName(new TaskListName($dto->name));
        }

        // description이 제공되었으면 업데이트
        if ($dto->description !== null) {
            $taskList->updateDescription(new TaskListDescription($dto->description));
        }

        // Repository를 통해 저장
        $updatedTaskList = $this->taskListRepository->save($taskList);

        // 미완료 Task 개수 조회
        $incompleteTaskCount = $this->taskRepository->countIncompleteByTaskListId($id);

        // DTO로 변환하여 반환
        return TaskListDTO::fromEntity($updatedTaskList, $incompleteTaskCount);
    }
}
