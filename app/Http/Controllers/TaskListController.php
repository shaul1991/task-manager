<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Src\Application\TaskList\DTOs\CreateTaskListDTO;
use Src\Application\TaskList\UseCases\CreateTaskList;
use Src\Application\TaskList\UseCases\GetTaskListList;

class TaskListController extends Controller
{
    public function __construct(
        private readonly GetTaskListList $getTaskListList,
        private readonly CreateTaskList $createTaskList
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
}
