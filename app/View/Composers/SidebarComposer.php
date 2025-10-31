<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\TaskGroup;
use App\Models\TaskList;
use Illuminate\View\View;

/**
 * Sidebar Composer
 *
 * Sidebar에 필요한 데이터를 모든 페이지에서 자동으로 제공합니다.
 */
class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view): void
    {
        // 모든 TaskGroup 조회 (TaskList와 함께, 빈 TaskGroup도 포함)
        $taskGroups = TaskGroup::with(['taskLists' => function ($query) {
            $query->select('id', 'name', 'description', 'task_group_id')
                ->withCount(['tasks as incompleteTaskCount' => function ($q) {
                    $q->whereNull('completed_datetime');
                }]);
        }])
            ->orderBy('created_at', 'desc')
            ->get();

        // TaskGroup에 속하지 않은 TaskList 조회 (미분류)
        $ungroupedTaskLists = TaskList::whereNull('task_group_id')
            ->withCount(['tasks as incompleteTaskCount' => function ($q) {
                $q->whereNull('completed_datetime');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Sidebar에 데이터 전달
        $view->with('taskGroups', $taskGroups);
        $view->with('ungroupedTaskLists', $ungroupedTaskLists);

        // 하위 호환성을 위해 전체 TaskList도 전달
        $allTaskLists = TaskList::withCount(['tasks as incompleteTaskCount' => function ($q) {
            $q->whereNull('completed_datetime');
        }])
            ->orderBy('created_at', 'desc')
            ->get();
        $view->with('taskLists', $allTaskLists);
    }
}
