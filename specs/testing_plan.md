# 테스팅 계획

**문서 목적**: 프로젝트 테스팅 전략 및 계획
**최종 업데이트**: 2025-10-30

---

## 1. 테스팅 전략

### 1.1 테스팅 피라미드

```
        ┌──────────────┐
        │   E2E Tests  │  10%
        │   (Feature)  │
        └──────────────┘
      ┌──────────────────┐
      │ Integration Tests│  30%
      └──────────────────┘
    ┌──────────────────────┐
    │    Unit Tests        │  60%
    └──────────────────────┘
```

**테스트 비율 목표**:
- **Unit Tests (60%)**: 개별 컴포넌트 테스트
- **Integration Tests (30%)**: 레이어 간 통합 테스트
- **Feature Tests (E2E) (10%)**: 전체 시나리오 테스트

### 1.2 커버리지 목표

| 레이어 | 목표 커버리지 | 현재 상태 |
|--------|---------------|-----------|
| **Domain Layer** | 90-95% | Task: 95% ✅, TaskList: 0% 📋 |
| **Application Layer** | 85-90% | Task: 80% ⚠️, TaskList: 0% 📋 |
| **Infrastructure Layer** | 80-85% | Task: 85% ✅, TaskList: 0% 📋 |
| **Presentation Layer** | 70-75% | 0% 📋 |
| **전체** | 80-85% | Task: 85% ✅, 전체: 22% 🚧 |

---

## 2. 테스트 작성 규칙

### 2.1 Given-When-Then 패턴

모든 테스트는 GWT 패턴을 따라야 합니다:

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

### 2.2 한글 메서드명 규칙

- `test_` 접두사 사용 필수
- 메서드명은 **한글**로 작성
- 언더스코어(_)로 단어 구분
- 동작을 명확히 설명

**예시**:
```php
// ✅ 올바른 예시
public function test_할일_완료_시_완료_일시_설정(): void
public function test_이미_완료된_할일_완료_시_예외_발생(): void
public function test_빈_문자열은_예외_발생(): void

// ❌ 잘못된 예시
public function test_complete_task_sets_completed_datetime(): void
public function test_empty_string_throws_exception(): void
```

### 2.3 테스트 독립성

- 각 테스트는 독립적으로 실행 가능
- 테스트 간 데이터 공유 금지
- setUp/tearDown 활용
- 테스트 순서에 의존하지 않음

### 2.4 Arrange-Act-Assert (AAA)

테스트 코드 구조:
1. **Arrange**: 테스트 준비 (Given)
2. **Act**: 실행 (When)
3. **Assert**: 검증 (Then)

---

## 3. 테스트 레벨별 가이드

### 3.1 Unit Tests (단위 테스트)

**대상**:
- Domain Layer (Entity, Value Object, Domain Service)
- Application Layer (UseCase)
- Infrastructure Layer (Repository)

**작성 규칙**:
- Mock/Stub을 활용하여 의존성 격리
- 각 메서드의 모든 경로 테스트
- 경계 조건 및 예외 상황 테스트

**예시** (Value Object):
```php
class TaskTitleTest extends TestCase
{
    public function test_유효한_제목으로_생성_가능(): void
    {
        // Given
        $titleString = '할 일 제목';

        // When
        $title = TaskTitle::fromString($titleString);

        // Then
        $this->assertEquals($titleString, $title->value());
    }

    public function test_빈_문자열은_예외_발생(): void
    {
        // Then
        $this->expectException(InvalidTaskTitleException::class);

        // When
        TaskTitle::fromString('');
    }

    public function test_200자_초과_시_예외_발생(): void
    {
        // Given
        $longTitle = str_repeat('a', 201);

        // Then
        $this->expectException(TaskTitleTooLongException::class);

        // When
        TaskTitle::fromString($longTitle);
    }
}
```

### 3.2 Integration Tests (통합 테스트)

**대상**:
- Repository와 Database
- UseCase와 Repository
- 레이어 간 데이터 흐름

**작성 규칙**:
- 실제 데이터베이스 사용 (테스트 DB)
- 트랜잭션 롤백으로 데이터 격리
- RefreshDatabase 트레잇 활용

**예시** (Repository):
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class EloquentTaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(TaskRepositoryInterface::class);
    }

    public function test_Task_저장_및_조회(): void
    {
        // Given
        $task = Task::create(
            new TaskTitle('Test task'),
            new TaskDescription('Description')
        );

        // When
        $savedTask = $this->repository->save($task);
        $foundTask = $this->repository->findById($savedTask->id());

        // Then
        $this->assertNotNull($foundTask);
        $this->assertEquals('Test task', $foundTask->title()->value());
    }
}
```

### 3.3 Feature Tests (E2E 테스트)

**대상**:
- 전체 사용자 시나리오
- API 엔드포인트
- UI 플로우

**작성 규칙**:
- 실제 사용자 관점에서 테스트
- HTTP 요청/응답 검증
- 데이터베이스 상태 확인

**예시** (Task 생명주기):
```php
class TaskLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_할일_생명주기_생성부터_완료까지(): void
    {
        // Given - 새 Task 생성
        $createDTO = new CreateTaskDTO(
            title: 'Buy groceries',
            description: 'Milk, eggs',
            taskListId: null
        );
        $useCase = app(CreateTask::class);

        // When - Task 생성
        $taskDTO = $useCase->execute($createDTO);

        // Then - Task 생성 확인
        $this->assertNotNull($taskDTO->id);
        $this->assertEquals('Buy groceries', $taskDTO->title);
        $this->assertFalse($taskDTO->isCompleted);

        // When - Task 완료 처리
        $completeUseCase = app(CompleteTask::class);
        $completedTaskDTO = $completeUseCase->execute($taskDTO->id);

        // Then - 완료 확인
        $this->assertTrue($completedTaskDTO->isCompleted);
        $this->assertNotNull($completedTaskDTO->completedDateTime);

        // When - Task 삭제
        $deleteUseCase = app(DeleteTask::class);
        $deleteUseCase->execute($taskDTO->id);

        // Then - 삭제 확인
        $getUseCase = app(GetTask::class);
        $this->expectException(NotFoundException::class);
        $getUseCase->execute($taskDTO->id);
    }
}
```

---

## 4. 도메인별 테스트 현황

### 4.1 Task 도메인 테스트 (✅ 95% 완료)

#### Domain Layer (69개 테스트)
- [x] `TaskTest.php` (20개) - ⚠️ 5개 수정 필요 (groupId → taskListId)
- [x] `TaskTitleTest.php` (8개)
- [x] `TaskDescriptionTest.php` (8개)
- [x] `CompletedDateTimeTest.php` (9개)
- [x] Exception Tests (24개)

#### Application Layer (7개 테스트)
- [x] `CreateTaskTest.php` (3개) - ⚠️ 수정 필요
- [x] `GetTaskTest.php` (2개) - ⚠️ 수정 필요
- [x] `DeleteTaskTest.php` (2개)

#### Infrastructure Layer (12개 테스트)
- [x] `EloquentTaskRepositoryTest.php` (12개)

#### Integration Tests (5개 테스트)
- [x] `TaskLifecycleTest.php` (5개)

**총 테스트**: 93개 (176 assertions)
**통과율**: 95% (88/93 통과, 5개 수정 필요)

### 4.2 TaskList 도메인 테스트 (📋 0% 예정)

#### Domain Layer (예상 30-35개)
- [ ] `TaskListTest.php` (15-20개)
- [ ] `TaskListNameTest.php` (8개)
- [ ] `TaskListDescriptionTest.php` (5개)
- [ ] Exception Tests (10개)

#### Application Layer (예상 15-20개)
- [ ] `CreateTaskListTest.php` (3개)
- [ ] `UpdateTaskListTest.php` (3개)
- [ ] `DeleteTaskListTest.php` (2개)
- [ ] `GetTaskListTest.php` (2개)
- [ ] `GetTaskListTasksTest.php` (3개)
- [ ] `AddTaskToTaskListTest.php` (3개)
- [ ] `RemoveTaskFromTaskListTest.php` (2개)

#### Infrastructure Layer (예상 12개)
- [ ] `EloquentTaskListRepositoryTest.php` (12개)

#### Integration Tests (예상 10개)
- [ ] `TaskListLifecycleTest.php` (5개)
- [ ] `TaskListTaskRelationTest.php` (5개)

**예상 총 테스트**: 60-80개

### 4.3 User 도메인 테스트 (📋 예정)

- User Entity Tests (10-15개)
- Value Object Tests (15개)
- AuthenticationService Tests (10개)
- GuestMigrationService Tests (10개)
- Use Case Tests (15개)
- Repository Tests (10개)
- Integration Tests (10개)

**예상 총 테스트**: 80-90개

---

## 5. 테스트 환경 설정

### 5.1 PHPUnit 설정

**파일**: `phpunit.xml`

```xml
<phpunit ...>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
    </php>
</phpunit>
```

**특징**:
- 인메모리 SQLite 사용 (빠른 테스트)
- Array 캐시/세션 드라이버
- Sync 큐 연결 (동기 실행)

### 5.2 테스트 실행 명령어

```bash
# 전체 테스트 실행
php artisan test
# 또는
vendor/bin/phpunit

# 특정 도메인 테스트
php artisan test tests/Unit/Domain/Task

# 특정 테스트 파일
php artisan test tests/Unit/Domain/Task/Entities/TaskTest.php

# 특정 테스트 메서드
php artisan test --filter test_할일_완료_시_완료_일시_설정

# 커버리지 리포트 (요약)
php artisan test --coverage

# 커버리지 리포트 (상세 HTML)
php artisan test --coverage-html coverage
```

### 5.3 테스트 격리

```php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    // 옵션 1: 테스트마다 DB 초기화 (느림, 완전 격리)
    use RefreshDatabase;

    // 옵션 2: 트랜잭션 롤백 (빠름, 격리)
    use DatabaseTransactions;
}
```

---

## 6. Mock 및 Stub 가이드

### 6.1 Repository Mock

```php
public function test_UseCase에서_Repository_Mock_사용(): void
{
    // Given
    $mockRepository = $this->createMock(TaskRepositoryInterface::class);
    $task = Task::create(
        new TaskTitle('Test'),
        new TaskDescription('Desc')
    );

    // Mock 설정
    $mockRepository
        ->expects($this->once())
        ->method('save')
        ->willReturn($task);

    // When
    $useCase = new CreateTask($mockRepository);
    $dto = new CreateTaskDTO('Test', 'Desc', null);
    $result = $useCase->execute($dto);

    // Then
    $this->assertEquals('Test', $result->title);
}
```

### 6.2 Value Object Stub

```php
private function createTaskStub(string $title = 'Test task'): Task
{
    return Task::create(
        new TaskTitle($title),
        new TaskDescription('Description'),
        null
    );
}

public function test_Stub_활용_예시(): void
{
    // Given
    $task = $this->createTaskStub('Custom title');

    // When & Then
    $this->assertEquals('Custom title', $task->title()->value());
}
```

---

## 7. CI/CD 통합 (예정)

### 7.1 GitHub Actions 워크플로우

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.4
          extensions: mbstring, sqlite
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test --coverage
```

### 7.2 테스트 실패 시 처리

- Pull Request 머지 차단
- 코드 커버리지 80% 미만 시 경고
- 테스트 실패 원인 자동 코멘트

---

## 8. 테스트 작성 체크리스트

### 8.1 Domain Layer 테스트

- [ ] Entity 팩토리 메서드 (create, reconstruct)
- [ ] Entity 모든 비즈니스 메서드
- [ ] Value Object 유효성 검증
- [ ] Value Object 동등성 비교
- [ ] Domain Exception 발생 조건
- [ ] Exception HTTP 상태 코드 및 메시지

### 8.2 Application Layer 테스트

- [ ] UseCase 정상 플로우 (Happy Path)
- [ ] UseCase 예외 상황 (Sad Path)
- [ ] DTO 변환 정확성
- [ ] Repository 호출 검증 (Mock)

### 8.3 Infrastructure Layer 테스트

- [ ] Repository save/find/delete
- [ ] Eloquent Model ↔ Domain Entity 변환
- [ ] 필터링 및 페이지네이션
- [ ] SoftDelete 동작
- [ ] 트랜잭션 격리

### 8.4 Integration 테스트

- [ ] 전체 생명주기 시나리오
- [ ] 레이어 간 데이터 흐름
- [ ] 데이터베이스 상태 검증
- [ ] 비즈니스 규칙 종단간 검증

---

## 9. 다음 단계

### 9.1 즉시 작업 필요 (우선순위: 높음)

1. [ ] Task 도메인 테스트 수정 (groupId → taskListId)
2. [ ] TaskList Domain Layer 테스트 작성 (30-35개)
3. [ ] TaskList Application Layer 테스트 작성 (15-20개)
4. [ ] TaskList Infrastructure Layer 테스트 작성 (12개)

### 9.2 단기 목표 (1-2주)

1. [ ] TaskList 통합 테스트 작성 (10개)
2. [ ] Task 전체 커버리지 100% 달성
3. [ ] TaskList 전체 커버리지 80% 이상 달성
4. [ ] CI/CD 파이프라인 구축

### 9.3 장기 목표 (1-3개월)

1. [ ] User 도메인 테스트 작성 (80-90개)
2. [ ] API Feature Tests 작성
3. [ ] E2E 테스트 (Playwright 또는 Cypress)
4. [ ] 성능 테스트 (PHPBench)
5. [ ] 전체 프로젝트 커버리지 80% 이상

---

## 10. 참고 자료

- **PHPUnit 공식 문서**: https://phpunit.de/
- **Laravel Testing 문서**: https://laravel.com/docs/12.x/testing
- **Given-When-Then 패턴**: https://martinfowler.com/bliki/GivenWhenThen.html
- **테스팅 피라미드**: https://martinfowler.com/articles/practical-test-pyramid.html

---

**문서 버전**: 1.0
**최초 작성**: 2025-10-30
**최근 업데이트**: 2025-10-30
