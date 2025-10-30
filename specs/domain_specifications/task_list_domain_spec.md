# TaskList 도메인 상세 명세

**도메인명**: TaskList (할 일 목록)
**상태**: 🚧 진행중 (50% 완료)
**최종 업데이트**: 2025-10-30

---

## 1. 도메인 개요

TaskList는 관련된 Task들을 묶어 관리하는 컨테이너 역할을 하는 도메인입니다. 기존의 Group 개념을 TaskList로 명확화한 것입니다.

### 1.1 책임

- TaskList의 생명주기 관리 (생성, 수정, 삭제)
- TaskList 내 Task 목록 관리
- User와의 연결 관리 (게스트/회원 구분)

### 1.2 비즈니스 규칙

- **이름**: 1-100자 사이의 필수 값
- **설명**: 선택적 값
- **User 연결**:
  - TaskList는 선택적으로 User에 속할 수 있음
  - `user_id`가 NULL이면 게스트의 TaskList
  - `user_id`가 설정되면 해당 User의 TaskList
- **Task 연결**:
  - TaskList는 여러 Task를 포함할 수 있음
  - Task의 `task_list_id`로 관계 설정
- **SoftDelete**: 삭제 시 실제 레코드는 유지하고 `deleted_at` 설정

---

## 2. Entity 설계

### 2.1 TaskList Entity

**파일 위치**: `src/Domain/TaskList/Entities/TaskList.php`

```php
final class TaskList
{
    private function __construct(
        private ?int $id,
        private TaskListName $name,
        private TaskListDescription $description,
        private ?int $userId,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {}

    // 팩토리 메서드
    public static function create(
        TaskListName $name,
        TaskListDescription $description,
        ?int $userId = null
    ): self;

    public static function reconstruct(
        int $id,
        TaskListName $name,
        TaskListDescription $description,
        ?int $userId,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self;

    // 비즈니스 메서드
    public function updateName(TaskListName $name): void;
    public function updateDescription(TaskListDescription $description): void;
    public function assignToUser(?int $userId): void;

    // Getters
    public function id(): ?int;
    public function name(): TaskListName;
    public function description(): TaskListDescription;
    public function userId(): ?int;
    public function createdAt(): DateTimeImmutable;
    public function updatedAt(): DateTimeImmutable;
}
```

---

## 3. Value Objects

### 3.1 TaskListName

**파일 위치**: `src/Domain/TaskList/ValueObjects/TaskListName.php`

**목적**: TaskList의 이름을 나타내는 불변 객체

**비즈니스 규칙**:
- 빈 문자열 불가
- 1-100자 사이

**예외**:
- `InvalidTaskListNameException`: 빈 문자열
- `TaskListNameTooLongException`: 100자 초과

```php
final class TaskListName
{
    private const MAX_LENGTH = 100;

    private function __construct(
        private readonly string $value
    ) {
        $this->validate();
    }

    public static function fromString(string $value): self;
    public function value(): string;
    public function equals(TaskListName $other): bool;
    private function validate(): void;
}
```

### 3.2 TaskListDescription

**파일 위치**: `src/Domain/TaskList/ValueObjects/TaskListDescription.php`

**목적**: TaskList의 설명을 나타내는 불변 객체

**비즈니스 규칙**:
- 빈 문자열 허용 (선택적)
- NULL 허용
- 최대 길이 제한 없음

```php
final class TaskListDescription
{
    private function __construct(
        private readonly string $value
    ) {}

    public static function fromString(?string $value): self;
    public function value(): string;
    public function equals(TaskListDescription $other): bool;
}
```

---

## 4. Exceptions

### 4.1 Domain Exceptions

**기본 경로**: `src/Domain/TaskList/Exceptions/`

| Exception | HTTP 상태 코드 | 설명 |
|-----------|----------------|------|
| `InvalidTaskListNameException` | 422 | 빈 문자열 이름 |
| `TaskListNameTooLongException` | 422 | 100자 초과 이름 |

**공통 구조**:
```php
class InvalidTaskListNameException extends DomainException
{
    public function __construct(string $message = 'TaskList 이름은 빈 문자열일 수 없습니다.')
    {
        parent::__construct($message, 'INVALID_TASK_LIST_NAME', 422);
    }
}
```

---

## 5. Repository Interface

### 5.1 TaskListRepositoryInterface

**파일 위치**: `src/Domain/TaskList/Repositories/TaskListRepositoryInterface.php`

```php
interface TaskListRepositoryInterface
{
    public function save(TaskList $taskList): TaskList;
    public function findById(int $id): ?TaskList;
    public function findAll(?int $userId = null, int $limit = 100, int $offset = 0): array;
    public function delete(int $id): void;
    public function existsById(int $id): bool;
    public function countByUserId(int $userId): int;
}
```

**메서드 설명**:
- `save()`: TaskList 저장 (생성 또는 업데이트)
- `findById()`: ID로 TaskList 조회
- `findAll()`: TaskList 목록 조회
  - `$userId`: User로 필터링 (NULL이면 게스트 TaskList)
  - `$limit`, `$offset`: 페이지네이션
- `delete()`: TaskList 삭제 (SoftDelete)
- `existsById()`: TaskList 존재 여부 확인
- `countByUserId()`: User별 TaskList 개수

---

## 6. Use Cases

### 6.1 CreateTaskList (✅ 완료)

**파일 위치**: `src/Application/TaskList/UseCases/CreateTaskList.php`

**목적**: 새로운 TaskList 생성

**입력** (`CreateTaskListDTO`):
```php
class CreateTaskListDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?int $userId
    ) {}
}
```

**출력**: `TaskListDTO`

**플로우**:
1. DTO에서 Domain Value Objects 생성
2. TaskList Entity 생성 (`TaskList::create()`)
3. Repository를 통해 저장
4. TaskListDTO로 변환하여 반환

### 6.2 UpdateTaskList (📋 예정)

**목적**: 기존 TaskList의 이름 및 설명 수정

**입력** (`UpdateTaskListDTO`):
```php
class UpdateTaskListDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description
    ) {}
}
```

**출력**: `TaskListDTO`

**플로우**:
1. Repository에서 TaskList 조회
2. TaskList가 없으면 NotFoundException
3. TaskList 이름/설명 업데이트
4. Repository를 통해 저장
5. TaskListDTO로 변환하여 반환

### 6.3 DeleteTaskList (📋 예정)

**목적**: TaskList 삭제 (SoftDelete)

**입력**: `taskListId: int`

**출력**: `void`

**플로우**:
1. Repository에서 TaskList 존재 확인
2. TaskList가 없으면 NotFoundException
3. Repository를 통해 삭제 (SoftDelete)

### 6.4 GetTaskList (📋 예정)

**목적**: 특정 TaskList 조회

**입력**: `taskListId: int`

**출력**: `TaskListDTO`

**플로우**:
1. Repository에서 TaskList 조회
2. TaskList가 없으면 NotFoundException
3. TaskListDTO로 변환하여 반환

### 6.5 GetTaskListTasks (📋 예정)

**목적**: TaskList 내 Task 목록 조회

**입력**:
- `taskListId: int`
- `completed?: bool` (완료 여부 필터링)
- `limit: int = 100`
- `offset: int = 0`

**출력**: `TaskListWithTasksDTO` (Task 배열 포함)

**플로우**:
1. Repository에서 TaskList 조회
2. TaskList가 없으면 NotFoundException
3. TaskRepository에서 해당 TaskList의 Task 목록 조회
4. TaskListWithTasksDTO로 변환하여 반환

### 6.6 AddTaskToTaskList (📋 예정)

**목적**: 기존 Task를 TaskList에 추가

**입력**:
- `taskId: int`
- `taskListId: int`

**출력**: `TaskDTO`

**플로우**:
1. TaskRepository에서 Task 조회
2. Task가 없으면 NotFoundException
3. TaskListRepository에서 TaskList 존재 확인
4. TaskList가 없으면 NotFoundException
5. Task를 TaskList에 할당 (`task->assignToTaskList($taskListId)`)
6. TaskRepository를 통해 저장
7. TaskDTO로 변환하여 반환

### 6.7 RemoveTaskFromTaskList (📋 예정)

**목적**: TaskList에서 Task 제거

**입력**: `taskId: int`

**출력**: `TaskDTO`

**플로우**:
1. TaskRepository에서 Task 조회
2. Task가 없으면 NotFoundException
3. Task를 TaskList에서 제거 (`task->removeFromTaskList()`)
4. TaskRepository를 통해 저장
5. TaskDTO로 변환하여 반환

---

## 7. DTOs

### 7.1 TaskListDTO (✅ 완료)

**파일 위치**: `src/Application/TaskList/DTOs/TaskListDTO.php`

```php
class TaskListDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?int $userId,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}

    public static function fromEntity(TaskList $taskList): self;
}
```

### 7.2 CreateTaskListDTO (✅ 완료)

**파일 위치**: `src/Application/TaskList/DTOs/CreateTaskListDTO.php`

```php
class CreateTaskListDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?int $userId
    ) {}
}
```

### 7.3 UpdateTaskListDTO (📋 예정)

```php
class UpdateTaskListDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description
    ) {}
}
```

---

## 8. Infrastructure Layer

### 8.1 EloquentTaskListRepository (✅ 완료)

**파일 위치**: `src/Infrastructure/TaskList/Repositories/EloquentTaskListRepository.php`

**구현 내용**:
- `TaskListRepositoryInterface` 구현
- Eloquent Model ↔ Domain Entity 변환
- SoftDelete 처리

**주요 메서드**:
```php
class EloquentTaskListRepository implements TaskListRepositoryInterface
{
    public function save(TaskList $taskList): TaskList;
    public function findById(int $id): ?TaskList;
    public function findAll(?int $userId = null, int $limit = 100, int $offset = 0): array;
    public function delete(int $id): void;
    public function existsById(int $id): bool;
    public function countByUserId(int $userId): int;

    private function toDomain(TaskListModel $model): TaskList;
}
```

### 8.2 TaskList Eloquent Model (✅ 완료)

**파일 위치**: `app/Models/TaskList.php`

```php
class TaskList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
```

### 8.3 Migration (✅ 완료)

**파일 위치**: `database/migrations/2025_10_30_000002_create_task_lists_table.php`

```php
Schema::create('task_lists', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255);
    $table->text('description')->nullable();
    $table->unsignedBigInteger('user_id')->nullable()->comment('users.id (게스트는 NULL)');
    $table->dateTimeTz('created_at')->useCurrent()->index('idx_created_at');
    $table->dateTimeTz('updated_at')->useCurrent()->useCurrentOnUpdate()->index('idx_updated_at');
    $table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');

    $table->index('user_id', 'idx_user_id');
});
```

---

## 9. 테스트 커버리지

### 9.1 현재 상태

```
전체 테스트: 0개 (📋 작성 필요)
예상 테스트: 60-80개
```

### 9.2 예정 테스트

#### Domain Layer Tests
- [ ] `TaskListTest.php` (15-20개)
- [ ] `TaskListNameTest.php` (8개)
- [ ] `TaskListDescriptionTest.php` (5개)
- [ ] Exception Tests (10개)

#### Application Layer Tests
- [ ] `CreateTaskListTest.php` (3개)
- [ ] `UpdateTaskListTest.php` (3개)
- [ ] `DeleteTaskListTest.php` (2개)
- [ ] `GetTaskListTest.php` (2개)
- [ ] `GetTaskListTasksTest.php` (3개)
- [ ] `AddTaskToTaskListTest.php` (3개)
- [ ] `RemoveTaskFromTaskListTest.php` (2개)

#### Infrastructure Layer Tests
- [ ] `EloquentTaskListRepositoryTest.php` (12개)

#### Integration Tests
- [ ] `TaskListLifecycleTest.php` (5개)
- [ ] `TaskListTaskRelationTest.php` (5개)

---

## 10. 다음 단계

### 10.1 즉시 작업 필요 (우선순위 높음)
1. [ ] TaskList Domain Layer 테스트 작성 (20-30개)
2. [ ] TaskList UseCase 구현 (Update, Delete, Get, GetTasks)
3. [ ] TaskList Application Layer 테스트 작성
4. [ ] Task-TaskList 연동 UseCase (Add, Remove)

### 10.2 다음 Sprint
1. [ ] TaskList Infrastructure Layer 테스트 작성
2. [ ] TaskList API 엔드포인트 구현
3. [ ] TaskList Blade 컴포넌트 구현
4. [ ] TaskList 통합 테스트 작성

### 10.3 향후 개선 사항 (Phase 3)
- [ ] TaskList 색상/아이콘 커스터마이징
- [ ] TaskList 정렬/순서 변경
- [ ] TaskList 즐겨찾기
- [ ] TaskList 아카이브
- [ ] TaskList 템플릿

---

**문서 버전**: 1.0
**최초 작성**: 2025-10-30
**최근 업데이트**: 2025-10-30
