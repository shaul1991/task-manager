<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskListRequest;
use App\Http\Requests\UpdateTaskListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Src\Application\Task\UseCases\GetTaskList as GetTaskListTasks;
use Src\Application\TaskList\DTOs\CreateTaskListDTO;
use Src\Application\TaskList\DTOs\UpdateTaskListDTO;
use Src\Application\TaskList\UseCases\CreateTaskList;
use Src\Application\TaskList\UseCases\GetTaskList;
use Src\Application\TaskList\UseCases\GetTaskListList;
use Src\Application\TaskList\UseCases\UpdateTaskList;
use Src\Application\TaskList\UseCases\UpdateTaskListOrder;
use Src\Application\TaskList\UseCases\MoveTaskListToGroup;
use Src\Application\TaskList\DTOs\UpdateTaskListOrderDTO;
use Src\Application\TaskList\DTOs\MoveTaskListToGroupDTO;
use Src\Shared\Exceptions\NotFoundException;

class TaskListController extends Controller
{
    public function __construct(
        private readonly GetTaskListList $getTaskListList,
        private readonly GetTaskList $getTaskList,
        private readonly CreateTaskList $createTaskList,
        private readonly UpdateTaskList $updateTaskList,
        private readonly GetTaskListTasks $getTaskListTasks,
        private readonly UpdateTaskListOrder $updateTaskListOrder,
        private readonly MoveTaskListToGroup $moveTaskListToGroup
    ) {
    }

    /**
     * Display a listing of task lists.
     */
    public function index(): JsonResponse
    {
        try {
            $taskListsDto = $this->getTaskListList->execute(
                userId: null, // 게스트 모드
                limit: 100,
                offset: 0
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'taskLists' => array_map(
                        fn($taskList) => [
                            'id' => $taskList->id,
                            'name' => $taskList->name,
                            'description' => $taskList->description,
                            'created_at' => $taskList->createdAt,
                            'updated_at' => $taskList->updatedAt,
                        ],
                        $taskListsDto->taskLists
                    ),
                    'total' => $taskListsDto->total,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '목록 조회 중 오류가 발생했습니다: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified task list.
     */
    public function show(Request $request, int $id): View|JsonResponse
    {
        try {
            // TaskList 조회
            $taskListDto = $this->getTaskList->execute($id);

            // JSON response for AJAX requests (BFF 패턴)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $taskListDto->id,
                        'name' => $taskListDto->name,
                        'description' => $taskListDto->description,
                        'incomplete_task_count' => $taskListDto->incompleteTaskCount,
                        'task_group_id' => $taskListDto->taskGroupId,
                        'created_at' => $taskListDto->createdAt,
                        'updated_at' => $taskListDto->updatedAt,
                    ],
                ]);
            }

            // 해당 TaskList에 속한 Tasks 조회 (View용)
            $tasksDto = $this->getTaskListTasks->execute(
                taskListId: $id,
                completed: null, // 전체 조회
                limit: 100,
                offset: 0
            );

            return view('task-lists.show', [
                'taskList' => $taskListDto,
                'tasks' => $tasksDto->tasks,
                'total' => $tasksDto->total,
            ]);
        } catch (NotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'TaskList를 찾을 수 없습니다.',
                ], 404);
            }

            abort(404, 'TaskList를 찾을 수 없습니다.');
        }
    }

    /**
     * Store a newly created task list in storage.
     */
    public function store(StoreTaskListRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $dto = new CreateTaskListDTO(
                name: $request->validated('name'),
                description: $request->validated('description')
            );

            $taskListDto = $this->createTaskList->execute($dto);

            // JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '목록이 성공적으로 생성되었습니다.',
                    'taskList' => [
                        'id' => $taskListDto->id,
                        'name' => $taskListDto->name,
                        'description' => $taskListDto->description,
                        'created_at' => $taskListDto->createdAt,
                        'updated_at' => $taskListDto->updatedAt,
                    ],
                ], 201);
            }

            // Redirect response for form submissions
            return redirect()
                ->back()
                ->with('success', '목록이 성공적으로 생성되었습니다.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '목록 생성 중 오류가 발생했습니다: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', '목록 생성 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified task list in storage.
     */
    public function update(UpdateTaskListRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateTaskListDTO::fromArray($request->validated());

            $taskListDto = $this->updateTaskList->execute($id, $dto);

            return response()->json([
                'success' => true,
                'message' => '목록이 성공적으로 수정되었습니다.',
                'taskList' => [
                    'id' => $taskListDto->id,
                    'name' => $taskListDto->name,
                    'description' => $taskListDto->description,
                    'incomplete_task_count' => $taskListDto->incompleteTaskCount,
                    'created_at' => $taskListDto->createdAt,
                    'updated_at' => $taskListDto->updatedAt,
                ],
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => '목록을 찾을 수 없습니다.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '목록 수정 중 오류가 발생했습니다: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder TaskLists
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|integer',
                'items.*.order' => 'required|integer',
            ]);

            // Create DTO
            $dto = UpdateTaskListOrderDTO::fromArray($validated['items']);

            // Execute use case
            $this->updateTaskListOrder->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'TaskList order updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '순서 변경 중 오류가 발생했습니다: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Move TaskList to different TaskGroup
     */
    public function move(Request $request, int $id): JsonResponse
    {
        try {
            // Validate input
            $validated = $request->validate([
                'task_group_id' => 'nullable|integer',
                'order' => 'required|integer',
            ]);

            // Create DTO
            $dto = new MoveTaskListToGroupDTO(
                taskListId: $id,
                taskGroupId: $validated['task_group_id'] ?? null,
                order: $validated['order']
            );

            // Execute use case
            $this->moveTaskListToGroup->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'TaskList moved successfully',
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => '목록을 찾을 수 없습니다.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '목록 이동 중 오류가 발생했습니다: ' . $e->getMessage(),
            ], 500);
        }
    }
}
