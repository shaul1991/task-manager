# 향후 도메인 명세

**문서 목적**: Phase 2-3에서 구현될 도메인 명세
**최종 업데이트**: 2025-10-30

---

## 1. Phase 2: SubTask 도메인

**예정 시기**: Phase 2 (MVP 이후)
**우선순위**: 중
**예상 작업 기간**: 5-7일

### 1.1 도메인 개요

SubTask는 Task 내부의 체크리스트 형태로, Task에 종속되는 하위 작업입니다.

### 1.2 핵심 개념

```
SubTask (n) : Task (1)
```

- SubTask는 Task에 종속되며 독립적으로 존재할 수 없음
- Task가 삭제되면 모든 SubTask도 함께 삭제됨 (Cascade Delete)
- 예: "보고서 작성" Task 안에 "자료 조사", "초안 작성", "검토" SubTask

### 1.3 Entity 설계 (예정)

```php
final class SubTask
{
    private function __construct(
        private ?int $id,
        private int $taskId,  // Required - 반드시 Task에 속함
        private SubTaskTitle $title,
        private bool $isCompleted,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {}

    public static function create(
        int $taskId,
        SubTaskTitle $title
    ): self;

    public function complete(): void;
    public function uncomplete(): void;
    public function updateTitle(SubTaskTitle $title): void;
}
```

### 1.4 데이터베이스 스키마 (예정)

```sql
CREATE TABLE sub_tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL COMMENT 'tasks.id',
    title VARCHAR(255) NOT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_task_id (task_id),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at)
) COMMENT 'SubTask (할 일 내부 체크리스트)';
```

### 1.5 Use Cases (예정)

- CreateSubTask
- UpdateSubTask
- CompleteSubTask
- UncompleteSubTask
- DeleteSubTask
- GetSubTaskList (by taskId)

### 1.6 비즈니스 규칙

- SubTask는 Task 생성 후에만 추가 가능
- SubTask 제목은 1-200자 사이
- SubTask는 독립적으로 존재할 수 없음 (taskId 필수)
- Task 삭제 시 모든 SubTask도 SoftDelete
- SubTask의 완료 여부는 boolean (`is_completed`)
- Task의 진행률 계산: (완료된 SubTask 수 / 전체 SubTask 수) * 100

---

## 2. Phase 3: TaskGroup 도메인

**예정 시기**: Phase 3 (MVP 이후)
**우선순위**: 중
**예상 작업 기간**: 7-10일

### 2.1 도메인 개요

TaskGroup은 TaskList들을 묶는 최상위 계층으로, 카테고리 역할을 합니다.

### 2.2 핵심 개념

```
TaskList (n) : TaskGroup (1)
```

- TaskGroup은 여러 TaskList를 포함
- TaskList는 선택적으로 TaskGroup에 속할 수 있음
- 예: "회사" TaskGroup 안에 "프로젝트 A", "프로젝트 B" TaskList

### 2.3 Entity 설계 (예정)

```php
final class TaskGroup
{
    private function __construct(
        private ?int $id,
        private TaskGroupName $name,
        private TaskGroupDescription $description,
        private ?int $userId,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {}

    public static function create(
        TaskGroupName $name,
        TaskGroupDescription $description,
        ?int $userId = null
    ): self;

    public function updateName(TaskGroupName $name): void;
    public function updateDescription(TaskGroupDescription $description): void;
}
```

### 2.4 데이터베이스 스키마 (예정)

```sql
CREATE TABLE task_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL COMMENT 'users.id (게스트는 NULL)',
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at)
) COMMENT 'TaskGroup (TaskList를 묶는 상위 카테고리)';

-- TaskList 테이블에 task_group_id 컬럼 추가
ALTER TABLE task_lists ADD COLUMN task_group_id BIGINT UNSIGNED NULL COMMENT 'task_groups.id';
ALTER TABLE task_lists ADD INDEX idx_task_group_id (task_group_id);
```

### 2.5 Use Cases (예정)

- CreateTaskGroup
- UpdateTaskGroup
- DeleteTaskGroup
- GetTaskGroup
- GetTaskGroupTaskLists (TaskGroup 내 TaskList 목록)
- AddTaskListToTaskGroup
- RemoveTaskListFromTaskGroup

### 2.6 비즈니스 규칙

- TaskGroup 이름은 1-100자 사이
- TaskGroup은 User에 속할 수 있음 (게스트는 user_id NULL)
- TaskList는 선택적으로 TaskGroup에 속할 수 있음
- TaskGroup 삭제 시 TaskList는 유지 (task_group_id만 NULL로 설정)
- SoftDelete 적용

---

## 3. Phase 2 추가 기능: 할 일 고급 기능

### 3.1 우선순위 (Priority)

**Entity 확장**:
```php
enum TaskPriority: string
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
}

// Task Entity에 추가
private TaskPriority $priority;
```

**데이터베이스**:
```sql
ALTER TABLE tasks ADD COLUMN priority ENUM('high', 'medium', 'low') DEFAULT 'medium';
ALTER TABLE tasks ADD INDEX idx_priority (priority);
```

### 3.2 마감일 (Due Date)

**Value Object**:
```php
final class DueDate
{
    private function __construct(
        private readonly DateTimeImmutable $value
    ) {}

    public static function fromDateTime(DateTimeImmutable $dateTime): self;
    public function toDateTime(): DateTimeImmutable;
    public function isOverdue(): bool;
}
```

**데이터베이스**:
```sql
ALTER TABLE tasks ADD COLUMN due_date TIMESTAMP NULL;
ALTER TABLE tasks ADD INDEX idx_due_date (due_date);
```

### 3.3 반복 일정 (Recurring)

**Value Object**:
```php
enum RecurrencePattern: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
}

final class Recurrence
{
    private function __construct(
        private readonly RecurrencePattern $pattern,
        private readonly int $interval  // 매 N일/주/월
    ) {}

    public static function daily(int $interval = 1): self;
    public static function weekly(int $interval = 1): self;
    public static function monthly(int $interval = 1): self;
}
```

**데이터베이스**:
```sql
ALTER TABLE tasks ADD COLUMN recurrence_pattern ENUM('daily', 'weekly', 'monthly') NULL;
ALTER TABLE tasks ADD COLUMN recurrence_interval INT NULL;
```

### 3.4 첨부파일 (Attachments)

**Entity**:
```php
final class TaskAttachment
{
    private function __construct(
        private ?int $id,
        private int $taskId,
        private string $fileName,
        private string $filePath,
        private int $fileSize,
        private DateTimeImmutable $uploadedAt
    ) {}
}
```

**데이터베이스**:
```sql
CREATE TABLE task_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL COMMENT 'tasks.id',
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    uploaded_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    INDEX idx_task_id (task_id)
);
```

---

## 4. Phase 3 추가 기능: TaskList/TaskGroup 고급 기능

### 4.1 색상/아이콘 커스터마이징

**Value Object**:
```php
final class TaskListColor
{
    private function __construct(
        private readonly string $hexCode  // #RRGGBB
    ) {}
}

final class TaskListIcon
{
    private function __construct(
        private readonly string $iconName  // Material Icon 이름
    ) {}
}
```

**데이터베이스**:
```sql
ALTER TABLE task_lists ADD COLUMN color VARCHAR(7) DEFAULT '#3B82F6';
ALTER TABLE task_lists ADD COLUMN icon VARCHAR(50) DEFAULT 'list';
```

### 4.2 정렬 순서 (Display Order)

**데이터베이스**:
```sql
ALTER TABLE task_lists ADD COLUMN display_order INT UNSIGNED DEFAULT 0;
ALTER TABLE task_lists ADD INDEX idx_display_order (display_order);
```

### 4.3 즐겨찾기 (Favorite)

**데이터베이스**:
```sql
ALTER TABLE task_lists ADD COLUMN is_favorite BOOLEAN DEFAULT FALSE;
ALTER TABLE task_lists ADD INDEX idx_is_favorite (is_favorite);
```

### 4.4 아카이브 (Archive)

**데이터베이스**:
```sql
ALTER TABLE task_lists ADD COLUMN archived_at TIMESTAMP NULL;
ALTER TABLE task_lists ADD INDEX idx_archived_at (archived_at);
```

---

## 5. Phase 4: 공유 및 협업

**예정 시기**: Phase 4 (장기)
**우선순위**: 낮
**예상 작업 기간**: 10-15일

### 5.1 TaskList 공유

**Entity**:
```php
final class TaskListShare
{
    private function __construct(
        private ?int $id,
        private int $taskListId,
        private int $ownerId,
        private int $sharedWithUserId,
        private TaskListPermission $permission,
        private DateTimeImmutable $sharedAt
    ) {}
}

enum TaskListPermission: string
{
    case OWNER = 'owner';
    case EDITOR = 'editor';
    case VIEWER = 'viewer';
}
```

### 5.2 할 일 담당자

**데이터베이스**:
```sql
ALTER TABLE tasks ADD COLUMN assigned_to_user_id BIGINT UNSIGNED NULL COMMENT 'users.id';
ALTER TABLE tasks ADD INDEX idx_assigned_to_user_id (assigned_to_user_id);
```

### 5.3 활동 로그 및 댓글

**Entity**:
```php
final class TaskComment
{
    private function __construct(
        private ?int $id,
        private int $taskId,
        private int $userId,
        private string $content,
        private DateTimeImmutable $createdAt
    ) {}
}
```

---

## 6. Phase 5: 추가 편의 기능

**예정 시기**: Phase 5 (장기)
**우선순위**: 낮

### 6.1 태그 시스템

**Entity**:
```php
final class Tag
{
    private function __construct(
        private ?int $id,
        private string $name,
        private string $color
    ) {}
}

// Many-to-Many 관계
CREATE TABLE task_tags (
    task_id BIGINT UNSIGNED,
    tag_id BIGINT UNSIGNED,
    PRIMARY KEY (task_id, tag_id)
);
```

### 6.2 알림 기능

**Entity**:
```php
final class Notification
{
    private function __construct(
        private ?int $id,
        private int $userId,
        private NotificationType $type,
        private string $message,
        private bool $isRead,
        private DateTimeImmutable $createdAt
    ) {}
}

enum NotificationType: string
{
    case TASK_DUE_SOON = 'task_due_soon';
    case TASK_ASSIGNED = 'task_assigned';
    case TASKLIST_SHARED = 'tasklist_shared';
}
```

### 6.3 통계 및 대시보드

**Use Cases**:
- GetTaskStatistics (완료율, 생산성)
- GetWeeklyReport
- GetMonthlyReport
- GetProductivityChart

---

## 7. 구현 우선순위

### High Priority (MVP 이후 즉시)
1. SubTask 구현
2. 우선순위 기능
3. 마감일 기능

### Medium Priority (중기)
1. TaskGroup 구현
2. TaskList 커스터마이징 (색상, 아이콘)
3. 반복 일정

### Low Priority (장기)
1. 공유 및 협업
2. 태그 시스템
3. 알림 기능
4. 통계 및 대시보드

---

**문서 버전**: 1.0
**최초 작성**: 2025-10-30
**최근 업데이트**: 2025-10-30
