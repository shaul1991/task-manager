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
$table->dateTimeTz('updated_at')->useCurrent()->useCurrentOnUpdate()->index('idx_updated_at');
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
            $table->unsignedBigInteger('task_list_id')->nullable()->comment('task_lists.id');

            // 타임스탬프 컬럼 (timezone 지원 + 자동 관리 + 인덱스)
            $table->dateTimeTz('created_at')->useCurrent()->index('idx_created_at');
            $table->dateTimeTz('updated_at')->useCurrent()->useCurrentOnUpdate()->index('idx_updated_at');
            $table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');

            // 비즈니스 로직 인덱스
            $table->index('task_list_id', 'idx_task_list_id');
            $table->index('completed_datetime', 'idx_completed_datetime');
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
- `comment()`: 외래키 컬럼은 참조하는 테이블과 키를 명시하여 가독성 향상

#### 5. 외래키 규칙

**외래키 제약조건(foreign key constraints)을 사용하지 않습니다.**

Laravel Migration에서 `foreign()` 메서드를 사용하여 데이터베이스 레벨의 외래키 제약조건을 생성하지 않습니다. 대신 네이밍 규칙과 주석(comment)을 통해 관계를 명시합니다.

**이유:**
- 애플리케이션 레벨에서 참조 무결성 관리
- 마이그레이션 및 롤백 작업의 유연성 확보
- 테스트 환경에서의 데이터 조작 자유도 향상
- 대규모 데이터베이스에서의 성능 이슈 회피

**외래키 컬럼 네이밍 규칙:**

형식: `{단수형_테이블명}_{key}`

```php
// ✅ 올바른 예시
$table->unsignedBigInteger('user_id')->nullable();        // users 테이블 참조
$table->unsignedBigInteger('task_list_id')->nullable();   // task_lists 테이블 참조
$table->unsignedBigInteger('task_id')->nullable();        // tasks 테이블 참조

// ❌ 잘못된 예시
$table->unsignedBigInteger('users_id')->nullable();       // 복수형 사용 금지
$table->unsignedBigInteger('taskListId')->nullable();     // camelCase 사용 금지
$table->unsignedBigInteger('list_id')->nullable();        // 모호한 이름 사용 금지
```

**Comment 규칙:**

모든 외래키 컬럼에는 `comment('{table_name}.{key}')` 형식으로 주석을 추가합니다.

```php
// ✅ 올바른 예시
$table->unsignedBigInteger('user_id')->nullable()->comment('users.id (게스트는 NULL)');
$table->unsignedBigInteger('task_list_id')->nullable()->comment('task_lists.id');
$table->unsignedBigInteger('task_id')->comment('tasks.id');

// ❌ 잘못된 예시
$table->unsignedBigInteger('user_id')->nullable();                    // comment 누락
$table->unsignedBigInteger('task_list_id')->comment('TaskList');      // 잘못된 형식
```

**인덱스 설정:**

모든 외래키 컬럼에는 인덱스를 설정합니다. (조회 성능 향상 및 조인 최적화)

```php
// ✅ 올바른 예시
$table->unsignedBigInteger('task_list_id')->nullable()->comment('task_lists.id');
$table->index('task_list_id', 'idx_task_list_id');

// 또는 체이닝 방식 (간단한 경우)
$table->unsignedBigInteger('user_id')->nullable()->comment('users.id')->index('idx_user_id');
```

**전체 예시:**

```php
Schema::create('task_lists', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255);
    $table->text('description')->nullable();

    // 외래키 컬럼 (네이밍 규칙 + comment + 인덱스)
    $table->unsignedBigInteger('user_id')->nullable()->comment('users.id (게스트는 NULL)');

    // 타임스탬프 컬럼
    $table->dateTimeTz('created_at')->useCurrent()->index('idx_created_at');
    $table->dateTimeTz('updated_at')->useCurrent()->useCurrentOnUpdate()->index('idx_updated_at');
    $table->dateTimeTz('deleted_at')->nullable()->index('idx_deleted_at');

    // 비즈니스 로직 인덱스
    $table->index('user_id', 'idx_user_id');
});
```

**주의사항:**
- 외래키 제약조건을 사용하지 않으므로, 애플리케이션 레벨(Repository, UseCase)에서 참조 무결성을 보장해야 합니다.
- 삭제 시 관련 데이터 처리는 SoftDelete와 애플리케이션 로직으로 관리합니다.
- 데이터 정합성은 테스트 코드로 검증합니다.

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
**최근 업데이트**: TaskList 연동을 위한 GroupId → TaskListId 마이그레이션 완료 (2025-10-30)

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

#### 3. TaskList Context (할 일 목록 컨텍스트)
**책임**: TaskList 관리 및 할 일 컨테이너 역할 (기존 Group을 TaskList로 명확화)
**상태**: 🚧 **부분 구현 완료** (Domain, Infrastructure 완료 / Application 진행 중)
**최근 업데이트**: Domain 및 Infrastructure Layer 완성 (2025-10-30)

**Domain Layer (완료):**
```
Aggregate Root:
- TaskList (src/Domain/TaskList/Entities/TaskList.php)

Value Objects:
- TaskListName (src/Domain/TaskList/ValueObjects/TaskListName.php)
- TaskListDescription (src/Domain/TaskList/ValueObjects/TaskListDescription.php)

Repository Interfaces:
- TaskListRepositoryInterface (src/Domain/TaskList/Repositories/TaskListRepositoryInterface.php)

Exceptions:
- InvalidTaskListNameException (src/Domain/TaskList/Exceptions/InvalidTaskListNameException.php)
- TaskListNameTooLongException (src/Domain/TaskList/Exceptions/TaskListNameTooLongException.php)

비즈니스 규칙:
- TaskList 이름은 1-100자 사이여야 함
- TaskList는 User에 속할 수 있음 (게스트는 user_id NULL)
- SoftDelete 적용
```

**Infrastructure Layer (완료):**
```
Repository Implementations:
- EloquentTaskListRepository (src/Infrastructure/TaskList/Repositories/EloquentTaskListRepository.php)

Eloquent Models:
- TaskList (app/Models/TaskList.php)

Database Migrations:
- 2025_10_30_000002_create_task_lists_table.php

Service Provider Bindings:
- DomainServiceProvider (app/Providers/DomainServiceProvider.php)
  → TaskListRepositoryInterface → EloquentTaskListRepository
```

**Application Layer (부분 완료):**
```
Use Cases (완료):
- CreateTaskList (src/Application/TaskList/UseCases/CreateTaskList.php)

DTOs (완료):
- TaskListDTO (src/Application/TaskList/DTOs/TaskListDTO.php)
- CreateTaskListDTO (src/Application/TaskList/DTOs/CreateTaskListDTO.php)

Use Cases (미구현):
- UpdateTaskList
- DeleteTaskList
- AddTaskToTaskList
- RemoveTaskFromTaskList
- GetTaskListTasks
```

**테스트 커버리지 (미착수):**
```
- TaskList Domain Layer 테스트 필요
- TaskList Application Layer 테스트 필요
- TaskList Infrastructure Layer 테스트 필요
- TaskList 통합 테스트 필요
```

**Phase 2 확장 계획:**
```
SubTask Context (하위 작업 컨텍스트):
- SubTask Entity (Task에 종속)
- sub_task(n) : task(1) 관계
- Task 생성 후 SubTask 추가 가능

TaskGroup Context (카테고리 컨텍스트):
- TaskGroup Entity (상위 카테고리)
- task_list(n) : task_group(1) 관계
- TaskList들을 묶는 최상위 계층
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

### 최근 변경사항 (2025-10-30)

**Group → TaskList 마이그레이션:**
- `groups` 테이블 → `task_lists` 테이블로 명확화
- `tasks.group_id` → `tasks.task_list_id` 컬럼명 변경
- 모든 외래키 컬럼에 `comment('{table_name}.{key}')` 추가
- SoftDelete 및 인덱스 네이밍 규칙 전면 적용

### MVP 스키마 설계

```sql
-- 사용자 테이블 (예정)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_email (email)
);

-- TaskList 테이블 (기존 groups 테이블 → task_lists로 명확화) ✅ 구현 완료
CREATE TABLE task_lists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    user_id BIGINT UNSIGNED NULL COMMENT 'users.id (게스트는 NULL)',
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_user_id (user_id)
) COMMENT 'TaskList (할 일 목록)';

-- 할 일 테이블 ✅ 구현 완료
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    completed_datetime TIMESTAMP NULL COMMENT '완료 처리 시간 (NULL이면 미완료)',
    task_list_id BIGINT UNSIGNED NULL COMMENT 'task_lists.id',
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_task_list_id (task_list_id),
    INDEX idx_completed_datetime (completed_datetime)
) COMMENT 'Task (할 일)';

-- Phase 2: SubTask 테이블 (향후 구현)
/*
CREATE TABLE sub_tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL COMMENT 'tasks.id',
    title VARCHAR(255) NOT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_task_id (task_id)
) COMMENT 'SubTask (할 일 내부 체크리스트)';
*/

-- Phase 3: TaskGroup 테이블 (향후 구현)
/*
CREATE TABLE task_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL COMMENT 'users.id (게스트는 NULL)',
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_user_id (user_id)
) COMMENT 'TaskGroup (TaskList를 묶는 상위 카테고리)';

-- TaskList에 task_group_id 컬럼 추가 (Phase 3)
ALTER TABLE task_lists ADD COLUMN task_group_id BIGINT UNSIGNED NULL COMMENT 'task_groups.id';
ALTER TABLE task_lists ADD INDEX idx_task_group_id (task_group_id);
*/
```

**주요 특징:**
- ✅ **외래키 제약조건 미사용**: `FOREIGN KEY` 제약 없이 애플리케이션 레벨에서 참조 무결성 관리
- ✅ **Comment 규칙 준수**: 모든 외래키 컬럼에 `comment('{table_name}.{key}')` 추가
- ✅ **SoftDelete 적용**: 모든 테이블에 `deleted_at` 컬럼 및 인덱스 설정
- ✅ **Timezone 지원**: `TIMESTAMP(6)` 사용 (마이크로초 정밀도)
- ✅ **인덱스 네이밍 규칙**: `idx_{column_name}` 형식 통일
- ✅ **자동 타임스탬프**: `DEFAULT CURRENT_TIMESTAMP(6)` 및 `ON UPDATE CURRENT_TIMESTAMP(6)`

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
│   └── TaskList/
│       ├── Entities/
│       │   └── TaskList.php
│       ├── ValueObjects/
│       │   ├── TaskListName.php
│       │   └── TaskListDescription.php
│       ├── Services/
│       │   └── TaskListTaskService.php
│       ├── Repositories/
│       │   └── TaskListRepositoryInterface.php
│       └── Events/
│           ├── TaskListCreated.php
│           ├── TaskListUpdated.php
│           ├── TaskListDeleted.php
│           ├── TaskAddedToTaskList.php
│           └── TaskRemovedFromTaskList.php
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
│   └── TaskList/
│       ├── UseCases/
│       │   ├── CreateTaskList.php
│       │   ├── UpdateTaskList.php
│       │   ├── DeleteTaskList.php
│       │   ├── AddTaskToTaskList.php
│       │   ├── RemoveTaskFromTaskList.php
│       │   └── GetTaskListTasks.php
│       └── DTOs/
│           ├── TaskListDTO.php
│           └── TaskListTasksDTO.php
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
    └── TaskList/
        └── Repositories/
            └── EloquentTaskListRepository.php
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
