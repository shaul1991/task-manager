# BACKEND.md - 백엔드 개발 문서

Laravel 12.0 + PHP 8.4 기반 DDD 아키텍처 백엔드 개발 가이드

## Laravel 개발 규칙

### Model 규칙

**SoftDelete 사용 필수:**
- 모든 Model은 `SoftDeletes` trait를 사용합니다.
- 데이터 삭제 시 실제 레코드를 삭제하지 않고 `deleted_at` 컬럼을 설정합니다.
- 데이터 복구 및 감사 추적(audit trail)을 위해 필수적으로 적용합니다.

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description', ...];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

### Migration 파일 규칙

#### 1. SoftDelete 컬럼 추가
모든 테이블에 `deleted_at` 컬럼을 추가합니다.

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    // ... 다른 컬럼들
    $table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');
});
```

#### 2. 인덱스 네이밍 규칙

**단일 컬럼 인덱스:**
- 형식: `idx_{column_name}`
- 예: `idx_group_id`, `idx_completed_datetime`

```php
$table->index('group_id', 'idx_group_id');
$table->index('completed_datetime', 'idx_completed_datetime');
```

**복합 인덱스:**
- 형식: `idx_{first_column_name}_{numbering}`
- numbering은 `01`, `02`, `03` ... 형식으로 01부터 시작

```php
// 첫 번째 복합 인덱스
$table->index(['user_id', 'created_at'], 'idx_user_id_01');

// 두 번째 복합 인덱스
$table->index(['user_id', 'status'], 'idx_user_id_02');
```

#### 3. 타임스탬프 컬럼 설정

**모든 테이블은 다음 3개의 타임스탬프 컬럼을 필수로 포함합니다:**

```php
$table->dateTimeTz('created_at')->useCurrent()->index('idx_created_at');
$table->dateTimeTz('updated_at')->useCurrentOnUpdate()->index('idx_updated_at');
$table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');
```

**인덱스 설정 이유:**

- **deleted_at 컬럼에 인덱스를 설정한 이유:**
  - SoftDelete 조건에서 `deleted_at IS NULL` 조건이 대다수 들어가므로 인덱스를 설정하였습니다.
  - 삭제되지 않은 레코드 조회 시 성능 향상을 위해 필수입니다.

- **created_at 컬럼에 인덱스를 설정한 이유:**
  - 생성순 혹은 최신순 정렬 (`ORDER BY created_at DESC`)에 사용됩니다.
  - 디버깅용으로 생성시간 범위 조회 (`WHERE created_at BETWEEN ...`)에 활용됩니다.

- **updated_at 컬럼에 인덱스를 설정한 이유:**
  - 최근 수정된 데이터 동기화에 활용할 수 있습니다.
  - 디버깅용으로 수정시간 범위 조회에 활용할 수 있습니다.

#### 4. 전체 Migration 예시

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->timestamp('completed_datetime')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();

            // 타임스탬프 컬럼 (timezone 지원 + 자동 관리 + 인덱스)
            $table->dateTimeTz('created_at')->useCurrent()->index('idx_created_at');
            $table->dateTimeTz('updated_at')->useCurrentOnUpdate()->index('idx_updated_at');
            $table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');

            // 비즈니스 로직 인덱스
            $table->index('group_id', 'idx_group_id');
            $table->index('completed_datetime', 'idx_completed_datetime');

            // Note: group_id foreign key는 groups 테이블 생성 후 추가 예정
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

**주요 특징:**
- `dateTimeTz()`: Timezone 정보를 포함하는 타임스탬프 컬럼
- `useCurrent()`: 레코드 생성 시 자동으로 현재 시간 설정
- `useCurrentOnUpdate()`: 레코드 수정 시 자동으로 현재 시간으로 업데이트
- `nullable()`: deleted_at은 삭제되지 않은 레코드에서 NULL이므로 필수
- `index()`: 각 타임스탬프 컬럼에 인덱스를 직접 체이닝하여 설정

## DDD 아키텍처 설계

### Bounded Context

이 애플리케이션은 3개의 주요 Bounded Context로 구성됩니다:

#### 1. User Context (사용자 컨텍스트)
**책임**: 사용자 인증, 권한 관리, 게스트 모드 관리

**도메인 모델:**
```
Entities:
- User (회원 사용자)
- GuestSession (게스트 세션)

Value Objects:
- Email
- Username
- Password (해시된 비밀번호)

Domain Services:
- AuthenticationService (인증 처리)
- GuestMigrationService (게스트 → 회원 데이터 전환)

Repository Interfaces:
- UserRepositoryInterface
- GuestSessionRepositoryInterface

Domain Events:
- UserRegistered
- UserLoggedIn
- GuestDataMigrated
```

#### 2. Task Context (할 일 컨텍스트)
**책임**: 할 일의 생명주기 관리 (생성, 수정, 완료, 삭제)
**상태**: ✅ **완전 구현 완료** (Domain, Application, Infrastructure Layers)

**Domain Layer (완료):**
```
Entities:
- Task (src/Domain/Task/Entities/Task.php)

Value Objects:
- TaskTitle (src/Domain/Task/ValueObjects/TaskTitle.php)
- TaskDescription (src/Domain/Task/ValueObjects/TaskDescription.php)
- CompletedDateTime (src/Domain/Task/ValueObjects/CompletedDateTime.php)

Repository Interfaces:
- TaskRepositoryInterface (src/Domain/Task/Repositories/TaskRepositoryInterface.php)

Exceptions:
- InvalidTaskTitleException
- TaskTitleTooLongException
- InvalidCompletedDateTimeException
- TaskAlreadyCompletedException
- TaskNotCompletedException

비즈니스 규칙:
- completed_datetime이 NULL이면 미완료 상태
- completed_datetime이 NULL이 아니면 완료 상태
- 완료 처리 시 현재 시간을 completed_datetime에 설정
```

**Application Layer (완료):**
```
Use Cases:
- CreateTask (src/Application/Task/UseCases/CreateTask.php)
- UpdateTask (src/Application/Task/UseCases/UpdateTask.php)
- CompleteTask (src/Application/Task/UseCases/CompleteTask.php)
- UncompleteTask (src/Application/Task/UseCases/UncompleteTask.php)
- DeleteTask (src/Application/Task/UseCases/DeleteTask.php)
- GetTask (src/Application/Task/UseCases/GetTask.php)
- GetTaskList (src/Application/Task/UseCases/GetTaskList.php)

DTOs:
- TaskDTO (src/Application/Task/DTOs/TaskDTO.php)
- CreateTaskDTO (src/Application/Task/DTOs/CreateTaskDTO.php)
- UpdateTaskDTO (src/Application/Task/DTOs/UpdateTaskDTO.php)
- TaskListDTO (src/Application/Task/DTOs/TaskListDTO.php)
```

**Infrastructure Layer (완료):**
```
Repository Implementations:
- EloquentTaskRepository (src/Infrastructure/Task/Repositories/EloquentTaskRepository.php)

Eloquent Models:
- Task (app/Models/Task.php)

Database Migrations:
- 2025_10_30_000001_create_tasks_table.php

Service Provider Bindings:
- DomainServiceProvider (app/Providers/DomainServiceProvider.php)
  → TaskRepositoryInterface → EloquentTaskRepository
```

**테스트 커버리지 (완료):**
```
Domain Layer Tests:
- TaskTest.php (20개 테스트)
- TaskTitleTest.php (8개 테스트)
- TaskDescriptionTest.php (8개 테스트)
- CompletedDateTimeTest.php (9개 테스트)
- Exception Tests (5개 파일, 24개 테스트)

Application Layer Tests:
- CreateTaskTest.php (3개 테스트)
- GetTaskTest.php (2개 테스트)
- DeleteTaskTest.php (2개 테스트)

Infrastructure Layer Tests:
- EloquentTaskRepositoryTest.php (12개 테스트)

Integration Tests:
- TaskLifecycleTest.php (5개 테스트)

전체: 95개 테스트 통과 (176 assertions)
```

#### 3. Group Context (그룹 컨텍스트)
**책임**: 그룹 관리 및 할 일 컨테이너 역할

**도메인 모델:**
```
Aggregate Root:
- Group (할 일 컬렉션 포함)

Value Objects:
- GroupName
- GroupDescription

Domain Services:
- GroupTaskService (그룹-할 일 연결 관리)

Repository Interfaces:
- GroupRepositoryInterface

Domain Events:
- GroupCreated
- GroupUpdated
- GroupDeleted
- TaskAddedToGroup
- TaskRemovedFromGroup
```

### DDD 레이어 구조

```
┌─────────────────────────────────────────┐
│      Presentation Layer                 │
│  (Controllers, Views, Blade Components) │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│      Application Layer                  │
│    (Use Cases, DTOs, Handlers)          │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│        Domain Layer                     │
│  (Entities, Value Objects, Services,    │
│   Repository Interfaces, Events)        │
└─────────────────────────────────────────┘
                 ▲
                 │
┌────────────────┴────────────────────────┐
│     Infrastructure Layer                │
│  (Repository Implementations, External  │
│   Services, Framework Bindings)         │
└─────────────────────────────────────────┘
```

**의존성 규칙:**
- Presentation → Application → Domain ← Infrastructure
- Domain 레이어는 다른 레이어에 의존하지 않음 (순수 비즈니스 로직)
- Infrastructure는 Domain의 인터페이스를 구현
- Application 레이어는 Domain을 조율하여 유스케이스 구현

## 데이터베이스 스키마

### MVP 스키마 설계

```sql
-- 사용자 테이블
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_users_email (email)
);

-- 할 일 테이블
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL COMMENT '게스트는 NULL',
    group_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    completed_datetime TIMESTAMP NULL COMMENT '완료 처리 시간 (NULL이면 미완료)',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_tasks_user_id (user_id),
    INDEX idx_tasks_group_id (group_id),
    INDEX idx_tasks_completed (completed_datetime),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL
);

-- 그룹 테이블
CREATE TABLE groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL COMMENT '게스트는 NULL',
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_groups_user_id (user_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## DDD 디렉토리 구조

### 전체 구조 (MVP 기준)

```
src/
├── Domain/                          # 도메인 레이어 (비즈니스 로직)
│   ├── User/
│   │   ├── Entities/
│   │   │   ├── User.php
│   │   │   └── GuestSession.php
│   │   ├── ValueObjects/
│   │   │   ├── Email.php
│   │   │   ├── Username.php
│   │   │   └── Password.php
│   │   ├── Services/
│   │   │   ├── AuthenticationService.php
│   │   │   └── GuestMigrationService.php
│   │   ├── Repositories/
│   │   │   ├── UserRepositoryInterface.php
│   │   │   └── GuestSessionRepositoryInterface.php
│   │   └── Events/
│   │       ├── UserRegistered.php
│   │       ├── UserLoggedIn.php
│   │       └── GuestDataMigrated.php
│   │
│   ├── Task/
│   │   ├── Entities/
│   │   │   └── Task.php
│   │   ├── ValueObjects/
│   │   │   ├── TaskTitle.php
│   │   │   ├── TaskDescription.php
│   │   │   └── CompletedDateTime.php
│   │   ├── Services/
│   │   │   └── TaskCompletionService.php
│   │   ├── Repositories/
│   │   │   └── TaskRepositoryInterface.php
│   │   └── Events/
│   │       ├── TaskCreated.php
│   │       ├── TaskUpdated.php
│   │       ├── TaskCompleted.php
│   │       └── TaskDeleted.php
│   │
│   └── Group/
│       ├── Entities/
│       │   └── Group.php
│       ├── ValueObjects/
│       │   ├── GroupName.php
│       │   └── GroupDescription.php
│       ├── Services/
│       │   └── GroupTaskService.php
│       ├── Repositories/
│       │   └── GroupRepositoryInterface.php
│       └── Events/
│           ├── GroupCreated.php
│           ├── GroupUpdated.php
│           ├── GroupDeleted.php
│           ├── TaskAddedToGroup.php
│           └── TaskRemovedFromGroup.php
│
├── Application/                     # 애플리케이션 레이어 (유스케이스)
│   ├── User/
│   │   ├── UseCases/
│   │   │   ├── RegisterUser.php
│   │   │   ├── LoginUser.php
│   │   │   ├── LogoutUser.php
│   │   │   └── MigrateGuestData.php
│   │   └── DTOs/
│   │       ├── UserDTO.php
│   │       └── GuestMigrationDTO.php
│   │
│   ├── Task/
│   │   ├── UseCases/
│   │   │   ├── CreateTask.php
│   │   │   ├── UpdateTask.php
│   │   │   ├── CompleteTask.php
│   │   │   ├── DeleteTask.php
│   │   │   └── GetTaskList.php
│   │   └── DTOs/
│   │       ├── TaskDTO.php
│   │       └── TaskListDTO.php
│   │
│   └── Group/
│       ├── UseCases/
│       │   ├── CreateGroup.php
│       │   ├── UpdateGroup.php
│       │   ├── DeleteGroup.php
│       │   ├── AddTaskToGroup.php
│       │   ├── RemoveTaskFromGroup.php
│       │   └── GetGroupTasks.php
│       └── DTOs/
│           ├── GroupDTO.php
│           └── GroupTasksDTO.php
│
└── Infrastructure/                  # 인프라 레이어 (기술 구현)
    ├── User/
    │   └── Repositories/
    │       ├── EloquentUserRepository.php
    │       └── LocalStorageGuestSessionRepository.php
    │
    ├── Task/
    │   └── Repositories/
    │       └── EloquentTaskRepository.php
    │
    └── Group/
        └── Repositories/
            └── EloquentGroupRepository.php
```

## DDD 개발 가이드라인

### 1. 레이어별 책임

#### Domain Layer (도메인 레이어)
**책임**: 순수 비즈니스 로직, 프레임워크 독립적

**규칙:**
- 외부 레이어에 의존하지 않음
- Laravel, Eloquent 등 프레임워크 코드 사용 금지
- 비즈니스 규칙과 불변식(Invariant) 검증
- 도메인 이벤트로 중요한 상태 변경 표현

**예시:**
```php
// ✅ 올바른 Domain Entity
namespace Src\Domain\Task\Entities;

class Task {
    private TaskTitle $title;
    private ?CompletedDateTime $completedDateTime;

    public function complete(): void {
        if ($this->isCompleted()) {
            throw new TaskAlreadyCompletedException();
        }

        $this->completedDateTime = CompletedDateTime::now();

        // 도메인 이벤트 발생
        $this->recordEvent(new TaskCompleted($this->id, $this->completedDateTime));
    }

    public function isCompleted(): bool {
        return $this->completedDateTime !== null;
    }

    public function uncomplete(): void {
        if (!$this->isCompleted()) {
            throw new TaskNotCompletedException();
        }

        $this->completedDateTime = null;
    }
}

// ❌ 잘못된 Domain Entity
namespace Src\Domain\Task\Entities;

use Illuminate\Database\Eloquent\Model;  // Framework 의존

class Task extends Model {  // Domain은 프레임워크에 의존하면 안됨
    // ...
}
```

#### Application Layer (애플리케이션 레이어)
**책임**: 유스케이스 조율, 트랜잭션 관리, DTO 변환

**규칙:**
- Domain 레이어의 인터페이스만 의존
- 트랜잭션 경계 관리
- DTO로 외부와 통신
- 복잡한 비즈니스 로직은 Domain으로 위임

**예시:**
```php
// ✅ 올바른 UseCase
namespace Src\Application\Task\UseCases;

class CreateTask {
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function execute(CreateTaskDTO $dto): TaskDTO {
        // Domain 객체 생성
        $task = Task::create(
            title: new TaskTitle($dto->title),
            description: new TaskDescription($dto->description)
        );

        // Repository를 통해 영속화
        $savedTask = $this->taskRepository->save($task);

        // DTO로 변환하여 반환
        return TaskDTO::fromEntity($savedTask);
    }
}
```

#### Infrastructure Layer (인프라 레이어)
**책임**: Domain 인터페이스의 기술적 구현

**규칙:**
- Domain의 Repository 인터페이스 구현
- Eloquent, DB, 외부 API 등 기술 세부사항 처리
- Service Provider에서 바인딩

**예시:**
```php
// ✅ Repository 구현
namespace Src\Infrastructure\Task\Repositories;

use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Domain\Task\Entities\Task;
use App\Models\Task as TaskModel;  // Eloquent 모델

class EloquentTaskRepository implements TaskRepositoryInterface {
    public function save(Task $task): Task {
        if ($task->id() === null) {
            // 새로운 Task 생성
            $eloquentTask = TaskModel::create([
                'title' => $task->title()->value(),
                'description' => $task->description()->value(),
                'completed_datetime' => $task->completedDateTime()?->toDateTime(),
                'group_id' => $task->groupId(),
            ]);
        } else {
            // 기존 Task 업데이트
            $eloquentTask = TaskModel::findOrFail($task->id());
            $eloquentTask->update([
                'title' => $task->title()->value(),
                'description' => $task->description()->value(),
                'completed_datetime' => $task->completedDateTime()?->toDateTime(),
                'group_id' => $task->groupId(),
            ]);
        }

        return $this->toDomain($eloquentTask);
    }

    public function findById(int $id): ?Task {
        $model = TaskModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    private function toDomain(TaskModel $model): Task {
        // Eloquent 모델 → Domain Entity 변환
        return Task::reconstruct(
            id: $model->id,
            title: new TaskTitle($model->title),
            description: new TaskDescription($model->description),
            completedDateTime: $model->completed_datetime
                ? CompletedDateTime::fromDateTime($model->completed_datetime)
                : null,
            groupId: $model->group_id,
            createdAt: new DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new DateTimeImmutable($model->updated_at->toDateTimeString())
        );
    }
}
```

### 2. Repository 패턴 구현

**Interface 정의 (Domain):**
```php
namespace Src\Domain\Task\Repositories;

interface TaskRepositoryInterface {
    public function save(Task $task): Task;
    public function findById(int $id): ?Task;
    public function findAll(?int $groupId = null, ?bool $completed = null, int $limit = 100, int $offset = 0): array;
    public function delete(int $id): void;
    public function existsById(int $id): bool;
    public function countByGroupId(int $groupId): int;
    public function countCompleted(?int $groupId = null): int;
}
```

**Service Provider 바인딩:**
```php
// app/Providers/DomainServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Task\Repositories\TaskRepositoryInterface;
use Src\Infrastructure\Task\Repositories\EloquentTaskRepository;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void {
        $this->app->bind(
            TaskRepositoryInterface::class,
            EloquentTaskRepository::class
        );
    }
}
```

**Service Provider 등록:**
```php
// bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\DomainServiceProvider::class,
];
```

### 3. Domain Event 활용

**Event 정의:**
```php
namespace Src\Domain\Task\Events;

class TaskCompleted {
    public function __construct(
        public readonly int $taskId,
        public readonly CompletedDateTime $completedDateTime
    ) {}
}
```

**Event Listener (Infrastructure):**
```php
namespace Src\Infrastructure\Task\Listeners;

class SendTaskCompletionNotification {
    public function handle(TaskCompleted $event): void {
        // 알림 전송 로직
    }
}
```

## 테스트 작성 규칙

### 테스트 작성 필수
**도메인 코드 작성 시 테스트 코드 작성은 필수입니다.**

모든 도메인 레이어 컴포넌트(Entity, Value Object, Exception)는 해당하는 유닛 테스트를 가져야 하며, 주요 비즈니스 시나리오는 통합 테스트로 검증해야 합니다.

### 커버리지 목표
- **유닛 테스트**: 80-90% 목표
- **통합 테스트**: 70-80% 목표

유닛 테스트는 개별 컴포넌트의 동작을 검증하고, 통합 테스트는 여러 컴포넌트가 함께 동작하는 실제 시나리오를 검증합니다.

### 테스트 파일 위치
```
tests/
├── Unit/Domain/           # 도메인 유닛 테스트
│   ├── Task/
│   │   ├── Entities/
│   │   │   └── TaskTest.php
│   │   ├── ValueObjects/
│   │   │   ├── TaskTitleTest.php
│   │   │   ├── TaskDescriptionTest.php
│   │   │   └── CompletedDateTimeTest.php
│   │   └── Exceptions/
│   │       └── InvalidTaskTitleExceptionTest.php
│   ├── User/
│   └── Group/
├── Unit/Application/      # 애플리케이션 유닛 테스트
│   └── Task/
│       └── UseCases/
│           └── CreateTaskTest.php
├── Feature/Infrastructure/ # 인프라 통합 테스트
│   └── Task/
│       └── Repositories/
│           └── EloquentTaskRepositoryTest.php
└── Feature/Domain/        # 도메인 통합 테스트
    └── Task/
        └── TaskLifecycleTest.php
```

### 테스트 패턴
**Given-When-Then 패턴 사용:**

모든 테스트는 가독성을 위해 Given-When-Then 패턴을 따라야 합니다.

```php
public function test_할일_완료_시_완료_일시_설정(): void
{
    // Given - 테스트 준비
    $task = Task::create(
        new TaskTitle('Test task'),
        new TaskDescription('Description')
    );

    // When - 실행
    $task->complete();

    // Then - 검증
    $this->assertTrue($task->isCompleted());
    $this->assertNotNull($task->completedDateTime());
}
```

### 테스트 명명 규칙
- `test_` 접두사 사용 (필수)
- **메서드명은 한글로 작성** (필수)
- 동작을 명확히 설명하는 이름
- 언더스코어(_)로 단어 구분

**예시:**
```php
// ✅ 올바른 예시 - 한글 메서드명
public function test_할일_완료_시_완료_일시_설정(): void
public function test_이미_완료된_할일_완료_시_예외_발생(): void
public function test_빈_문자열은_예외_발생(): void
public function test_예외가_올바른_상태_코드를_가짐(): void

// ❌ 잘못된 예시 - 영어 메서드명
public function test_complete_task_sets_completed_datetime(): void
public function test_empty_string_throws_exception(): void
```

**한글 명명 가이드:**
- `test_` 접두사 다음 한글로 테스트 내용 설명
- 언더스코어로 단어/구문 구분
- 명확하고 간결한 설명 사용
- 테스트 의도가 즉시 이해되도록 작성

### 도메인 컴포넌트별 테스트 가이드

**Value Object 테스트:**
- 유효성 검증 로직 (경계값, 예외 케이스)
- 동등성 비교 (equals 메서드)
- 변환 메서드 (toString, value)
- 엣지 케이스 (null, 빈 값, 최대/최소값)

```php
public function test_빈_문자열은_예외_발생(): void
{
    // Then
    $this->expectException(InvalidTaskTitleException::class);

    // When
    new TaskTitle('');
}
```

**Entity 테스트:**
- 팩토리 메서드 (create, reconstruct)
- 모든 비즈니스 로직 메서드
- 상태 변경 및 불변성
- 도메인 규칙 위반 시 예외

```php
public function test_이미_완료된_할일_완료_시_예외_발생(): void
{
    // Given
    $task = Task::create(
        new TaskTitle('Task'),
        new TaskDescription('Description')
    );
    $task->complete();

    // Then
    $this->expectException(TaskAlreadyCompletedException::class);

    // When
    $task->complete();
}
```

**Exception 테스트:**
- HTTP 상태 코드 (getStatusCode)
- 에러 코드 (getErrorCode)
- 메시지 (getMessage)
- 컨텍스트 데이터 (getContext)
- 상속 구조 (DomainException, ApplicationException)

```php
public function test_예외가_올바른_상태_코드를_가짐(): void
{
    // When
    $exception = new InvalidTaskTitleException();

    // Then
    $this->assertEquals(422, $exception->getStatusCode());
}
```

**통합 테스트:**
- 실제 사용 시나리오 기반
- 여러 컴포넌트 협력 검증
- 라이프사이클 전체 흐름
- 예외 처리 시나리오

```php
public function test_할일_생명주기_생성부터_완료까지(): void
{
    // Given
    $task = Task::create(
        new TaskTitle('Buy groceries'),
        new TaskDescription('Milk, eggs')
    );

    // When & Then - 전체 라이프사이클 검증
    $this->assertFalse($task->isCompleted());

    $task->complete();
    $this->assertTrue($task->isCompleted());

    $task->uncomplete();
    $this->assertFalse($task->isCompleted());
}
```

### 테스트 실행
```bash
# 전체 테스트 실행
composer test
# 또는
php artisan test

# 특정 도메인 유닛 테스트
php artisan test tests/Unit/Domain/Task

# 특정 도메인 통합 테스트
php artisan test tests/Feature/Domain/Task

# 특정 테스트 파일만 실행
php artisan test tests/Unit/Domain/Task/Entities/TaskTest.php

# 커버리지 리포트 생성
php artisan test --coverage

# 특정 테스트 메서드만 실행
php artisan test --filter test_할일_완료_시_완료_일시_설정
```

### 테스트 작성 시기
- **TDD 권장**: 도메인 코드 작성 전 테스트 먼저 작성
- **최소 요구사항**: 도메인 코드와 함께 동일 PR에 테스트 포함
- **리팩토링 전**: 기존 동작을 보장하는 테스트 먼저 작성

### 테스트 품질 기준
- **독립성**: 각 테스트는 독립적으로 실행 가능
- **반복성**: 동일한 입력은 항상 동일한 결과
- **명확성**: 테스트 이름과 구조만으로 의도 파악 가능
- **빠른 실행**: 유닛 테스트는 1초 이내 실행

## API 설계 (예정)

추후 RESTful API 설계 가이드가 추가될 예정입니다.

## 참조

이 문서는 백엔드 개발에 특화된 문서입니다. 프론트엔드 개발은 @FRONTEND.md를 참조하세요.

프로젝트 전체 개요와 개발 환경 설정은 @CLAUDE.md를 참조하세요.
