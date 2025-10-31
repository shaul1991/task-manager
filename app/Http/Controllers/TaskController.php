<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Src\Application\Task\DTOs\CreateTaskDTO;
use Src\Application\Task\DTOs\UpdateTaskDTO;
use Src\Application\Task\UseCases\CreateTask;
use Src\Application\Task\UseCases\DeleteTask;
use Src\Application\Task\UseCases\GetTask;
use Src\Application\Task\UseCases\GetTaskList;
use Src\Application\Task\UseCases\UpdateTask;
use Src\Shared\Exceptions\NotFoundException;

class TaskController extends Controller
{
    public function __construct(
        private readonly GetTaskList $getTaskList,
        private readonly GetTask $getTask,
        private readonly CreateTask $createTask,
        private readonly UpdateTask $updateTask,
        private readonly DeleteTask $deleteTask
    ) {
    }

    /**
     * Display a listing of tasks.
     */
    public function index(): View
    {
        // UseCase를 통해 Task 목록 조회 (taskListName 포함)
        $taskListDto = $this->getTaskList->execute();

        return view('tasks.index', [
            'tasks' => $taskListDto->tasks,
            'total' => $taskListDto->total,
        ]);
    }

    /**
     * Show the form for creating a new task.
     * Redirects to index page (Quick Add UI 사용)
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('tasks.index');
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $dto = CreateTaskDTO::fromArray($request->validated());

            $taskDto = $this->createTask->execute($dto);

            // JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '할 일이 성공적으로 생성되었습니다.',
                    'task' => [
                        'id' => $taskDto->id,
                        'title' => $taskDto->title,
                        'description' => $taskDto->description,
                        'completed' => $taskDto->isCompleted,
                        'created_at' => $taskDto->createdAt,
                        'updated_at' => $taskDto->updatedAt,
                    ],
                ], 201);
            }

            // Redirect response for form submissions
            return redirect()
                ->route('tasks.index')
                ->with('success', '할 일이 성공적으로 생성되었습니다.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '할 일 생성 중 오류가 발생했습니다: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', '할 일 생성 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified task (API endpoint).
     */
    public function show(int $id): JsonResponse
    {
        try {
            $taskDto = $this->getTask->execute($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $taskDto->id,
                    'title' => $taskDto->title,
                    'description' => $taskDto->description,
                    'completed' => $taskDto->isCompleted,
                    'completed_datetime' => $taskDto->completedDateTime,
                    'task_list_id' => $taskDto->taskListId,
                    'created_at' => $taskDto->createdAt,
                    'updated_at' => $taskDto->updatedAt,
                ],
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => '할 일을 찾을 수 없습니다.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '할 일 조회 중 오류가 발생했습니다: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified task.
     * Redirects to index page (Right Side Panel 사용)
     */
    public function edit(int $id): RedirectResponse
    {
        try {
            $this->getTask->execute($id);

            return redirect()->route('tasks.index');
        } catch (NotFoundException $e) {
            abort(404, '할 일을 찾을 수 없습니다.');
        }
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $dto = UpdateTaskDTO::fromArray($request->validated());

            $taskDto = $this->updateTask->execute($id, $dto);

            // JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '할 일이 성공적으로 수정되었습니다.',
                    'data' => [
                        'id' => $taskDto->id,
                        'title' => $taskDto->title,
                        'description' => $taskDto->description,
                        'completed' => $taskDto->isCompleted,
                        'completed_datetime' => $taskDto->completedDateTime,
                        'task_list_id' => $taskDto->taskListId,
                        'created_at' => $taskDto->createdAt,
                        'updated_at' => $taskDto->updatedAt,
                    ],
                ]);
            }

            // Redirect response for form submissions
            return redirect()
                ->route('tasks.index')
                ->with('success', '할 일이 성공적으로 수정되었습니다.');
        } catch (NotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '할 일을 찾을 수 없습니다.',
                ], 404);
            }

            return redirect()
                ->route('tasks.index')
                ->with('error', '할 일을 찾을 수 없습니다.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '할 일 수정 중 오류가 발생했습니다: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', '할 일 수정 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $this->deleteTask->execute($id);

            // JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '할 일이 성공적으로 삭제되었습니다.',
                ]);
            }

            // Redirect response for form submissions
            return redirect()
                ->route('tasks.index')
                ->with('success', '할 일이 성공적으로 삭제되었습니다.');
        } catch (NotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '할 일을 찾을 수 없습니다.',
                ], 404);
            }

            return redirect()
                ->route('tasks.index')
                ->with('error', '할 일을 찾을 수 없습니다.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '할 일 삭제 중 오류가 발생했습니다: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('tasks.index')
                ->with('error', '할 일 삭제 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}
