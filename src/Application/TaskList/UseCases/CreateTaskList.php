<?php

declare(strict_types=1);

namespace Src\Application\TaskList\UseCases;

use Src\Application\TaskList\DTOs\CreateTaskListDTO;
use Src\Application\TaskList\DTOs\TaskListDTO;
use Src\Domain\TaskList\Entities\TaskList;
use Src\Domain\TaskList\Repositories\TaskListRepositoryInterface;
use Src\Domain\TaskList\ValueObjects\TaskListName;
use Src\Domain\TaskList\ValueObjects\TaskListDescription;

/**
 * Create TaskList Use Case
 *
 * 새로운 TaskList를 생성합니다.
 */
final readonly class CreateTaskList
{
    public function __construct(
        private TaskListRepositoryInterface $taskListRepository
    ) {
    }

    /**
     * Use Case 실행
     *
     * @param CreateTaskListDTO $dto 생성 요청 데이터
     * @return TaskListDTO 생성된 TaskList DTO
     * @throws \Src\Domain\TaskList\Exceptions\InvalidTaskListNameException
     * @throws \Src\Domain\TaskList\Exceptions\TaskListNameTooLongException
     */
    public function execute(CreateTaskListDTO $dto): TaskListDTO
    {
        // Domain Entity 생성
        $taskList = TaskList::create(
            name: new TaskListName($dto->name),
            description: new TaskListDescription($dto->description)
        );

        // Repository를 통해 저장
        $savedTaskList = $this->taskListRepository->save($taskList);

        // DTO로 변환하여 반환
        return TaskListDTO::fromEntity($savedTaskList);
    }
}
