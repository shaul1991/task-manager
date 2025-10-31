<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Src\Application\TaskGroup\UseCases\CreateTaskGroup;
use Src\Application\TaskGroup\UseCases\UpdateTaskGroup;
use Src\Application\TaskGroup\UseCases\DeleteTaskGroup;
use Src\Application\TaskGroup\UseCases\GetTaskGroup;
use Src\Application\TaskGroup\UseCases\GetTaskGroupList;
use Src\Application\TaskGroup\UseCases\UpdateTaskGroupOrder;
use Src\Application\TaskGroup\DTOs\CreateTaskGroupDTO;
use Src\Application\TaskGroup\DTOs\UpdateTaskGroupDTO;
use Src\Application\TaskGroup\DTOs\UpdateTaskGroupOrderDTO;
use Src\Shared\Exceptions\NotFoundException;

/**
 * TaskGroup Controller
 *
 * TaskGroup CRUD API
 */
class TaskGroupController extends Controller
{
    public function __construct(
        private readonly CreateTaskGroup $createTaskGroup,
        private readonly UpdateTaskGroup $updateTaskGroup,
        private readonly DeleteTaskGroup $deleteTaskGroup,
        private readonly GetTaskGroup $getTaskGroup,
        private readonly GetTaskGroupList $getTaskGroupList,
        private readonly UpdateTaskGroupOrder $updateTaskGroupOrder
    ) {
    }

    /**
     * Get all TaskGroups
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $taskGroups = $this->getTaskGroupList->execute(limit: 100, offset: 0);

            return response()->json([
                'success' => true,
                'taskGroups' => array_map(fn($dto) => $dto->toArray(), $taskGroups),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single TaskGroup (BFF API)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $taskGroupDto = $this->getTaskGroup->execute($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $taskGroupDto->id,
                    'name' => $taskGroupDto->name,
                    'incomplete_task_count' => $taskGroupDto->incompleteTaskCount,
                    'created_at' => $taskGroupDto->createdAt,
                    'updated_at' => $taskGroupDto->updatedAt,
                ],
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'TaskGroup을 찾을 수 없습니다.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'TaskGroup 조회 중 오류가 발생했습니다: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new TaskGroup
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:100',
            ]);

            // Create DTO
            $dto = new CreateTaskGroupDTO(
                name: $validated['name']
            );

            // Execute use case
            $taskGroupDTO = $this->createTaskGroup->execute($dto);

            return response()->json([
                'success' => true,
                'taskGroup' => $taskGroupDTO->toArray(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update a TaskGroup
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:100',
            ]);

            // Create DTO
            $dto = new UpdateTaskGroupDTO(
                id: $id,
                name: $validated['name']
            );

            // Execute use case
            $taskGroupDTO = $this->updateTaskGroup->execute($dto);

            return response()->json([
                'success' => true,
                'taskGroup' => $taskGroupDTO->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete a TaskGroup
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Execute use case
            $this->deleteTaskGroup->execute($id);

            return response()->json([
                'success' => true,
                'message' => 'TaskGroup deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reorder TaskGroups
     *
     * @param Request $request
     * @return JsonResponse
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
            $dto = UpdateTaskGroupOrderDTO::fromArray($validated['items']);

            // Execute use case
            $this->updateTaskGroupOrder->execute($dto);

            return response()->json([
                'success' => true,
                'message' => 'TaskGroup order updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
