# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 프로젝트 개요

**일상 생활의 할 일(Todo) 관리 서비스**

일상 생활의 다양한 할 일을 효율적으로 관리하는 서비스입니다. 그룹으로 관련 할 일을 묶어 체계적으로 관리하며, 게스트 모드와 회원 모드를 모두 지원합니다.

**핵심 기술 스택:**
- **아키텍처**: DDD (Domain-Driven Design)
- **백엔드**: Laravel 12.0 + PHP 8.4
- **프론트엔드**: Vite + Tailwind CSS 4.0 + Laravel Blade Components
- **데이터베이스**: MySQL (3306)
- **캐시**: Redis (6379)
- **테스팅**: PHPUnit 11.5.3
- **로컬 스토리지**: LocalStorage (게스트 모드)

**핵심 개념:**
- **Task (할 일)**: 개별적인 할 일 항목 (예: "우유 사기", "운동하기")
- **Group (그룹)**: 관련된 할 일들을 묶는 컨테이너 (예: "쇼핑 목록", "운동 루틴")

## 개발 명령어

### 초기 설정

```bash
composer setup
```

프로젝트 초기 설정을 완료합니다:
- Composer 의존성 설치
- `.env` 파일 생성
- 애플리케이션 키 생성
- 데이터베이스 마이그레이션 실행
- npm 의존성 설치
- 프론트엔드 빌드

### 개발 서버 실행

```bash
composer dev
```

**4개의 서비스를 동시에 실행**합니다 (색상별로 구분된 로그):
- Laravel 개발 서버 (포트 8000) - 파란색
- 큐 워커 - 보라색
- 로그 모니터링 (Laravel Pail) - 분홍색
- Vite 개발 서버 (HMR) - 주황색

각 서비스는 `concurrently`로 관리되며, 개별적으로 중지/재시작할 수 있습니다.

### 테스트 실행

**전체 테스트 스위트:**
```bash
composer test
# 또는
php artisan test
```

**특정 테스트만 실행:**
```bash
php artisan test --filter ExampleTest
# 또는
vendor/bin/phpunit tests/Feature/ExampleTest.php
```

테스트는 인메모리 SQLite를 사용하여 격리된 환경에서 실행됩니다.

### 데이터베이스 관리

```bash
php artisan migrate              # 마이그레이션 실행
php artisan migrate:fresh        # DB 초기화 후 마이그레이션
php artisan migrate:rollback     # 이전 마이그레이션으로 롤백
php artisan db                   # DB CLI 접속
php artisan tinker              # Laravel REPL
```

### 코드 품질

```bash
vendor/bin/pint                  # Laravel Pint로 코드 스타일 자동 포맷
```

### 프론트엔드

```bash
npm run dev                      # Vite 개발 서버 (HMR 활성화)
npm run build                    # 프로덕션 빌드
```

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
모든 테이블에 `softDeletes()` 메서드를 사용하여 `deleted_at` 컬럼을 추가합니다.

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    // ... 다른 컬럼들
    $table->timestamps();
    $table->softDeletes();  // deleted_at 컬럼 추가
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

#### 3. 필수 인덱스
다음 컬럼들은 **항상 인덱스를 추가**합니다:
- `created_at`: 생성일시 기반 정렬/조회를 위해
- `updated_at`: 수정일시 기반 조회를 위해
- `deleted_at`: SoftDelete 조회 성능 향상을 위해

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    // ... 다른 컬럼들
    $table->timestamps();
    $table->softDeletes();

    // 필수 인덱스
    $table->index('created_at', 'idx_created_at');
    $table->index('updated_at', 'idx_updated_at');
    $table->index('deleted_at', 'idx_deleted_at');
});
```

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
            $table->timestamps();
            $table->softDeletes();

            // 비즈니스 로직 인덱스
            $table->index('group_id', 'idx_group_id');
            $table->index('completed_datetime', 'idx_completed_datetime');

            // 필수 타임스탬프 인덱스
            $table->index('created_at', 'idx_created_at');
            $table->index('updated_at', 'idx_updated_at');
            $table->index('deleted_at', 'idx_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

## 아키텍처

### Laravel 12.0 부트스트랩 구조

이 프로젝트는 Laravel 12.0의 **새로운 플루언트 API 부트스트랩 방식**을 사용합니다 (`bootstrap/app.php`):

```php
Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',  // 헬스체크 엔드포인트 자동 등록
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 미들웨어 설정
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 예외 처리 설정
    })->create();
```

기존 `app/Http/Kernel.php` 방식이 아닌 클로저 기반 설정 방식입니다.

### 데이터 저장소 구성

**프로덕션 환경:**
- **데이터베이스**: MySQL (포트 3306)
- **캐시**: Redis (포트 6379)
- **세션**: Redis
- **큐**: Redis

**개발 환경:**
- **데이터베이스**: MySQL (로컬 또는 Docker)
- **캐시**: Redis (로컬 또는 Docker)

**게스트 모드:**
- **데이터 저장**: JavaScript LocalStorage API
- 회원 전환 시 로컬 데이터를 서버 DB로 마이그레이션

### Tailwind CSS 4.0 통합

**새로운 `@source` 디렉티브**를 사용한 스캔 경로 지정 (`resources/css/app.css`):

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

Vite 네이티브 플러그인 (`@tailwindcss/vite`)을 사용하여 최적의 성능을 제공합니다.

### 모델 패턴

Laravel 11+ 스타일의 **`casts()` 메서드**를 사용합니다:

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

기존 `$casts` 프로퍼티 대신 메서드 방식으로 타입 캐스팅을 정의합니다.

## 주요 디렉토리

```
app/
├── Models/              # Eloquent 모델 (HasFactory, Notifiable 트레잇 사용)
├── Http/Controllers/    # 컨트롤러
└── Providers/          # 서비스 프로바이더

database/
├── migrations/         # 마이그레이션 파일
├── factories/          # 모델 팩토리
└── seeders/           # 데이터 시더

resources/
├── css/               # Tailwind CSS
├── js/                # JavaScript (Axios 전역 설정 포함)
└── views/             # Blade 템플릿

routes/
├── web.php            # 웹 라우트
└── console.php        # Artisan 커맨드

tests/
├── Feature/           # 기능 테스트
└── Unit/              # 단위 테스트
```

## 프로젝트별 특징

### 1. 올인원 개발 환경

`composer dev` 명령어는 개발에 필요한 모든 서비스를 한 번에 실행합니다. 각 서비스는 색상으로 구분된 로그를 출력하여 디버깅이 용이합니다.

### 2. 테스트 격리

PHPUnit 설정 (`phpunit.xml`)에서 테스트는 완전히 격리된 환경에서 실행됩니다:
- 인메모리 SQLite 데이터베이스
- Array 캐시 드라이버
- Array 세션 드라이버
- Sync 큐 연결 (동기 실행)

### 3. 헬스체크 엔드포인트

Laravel 12.0의 기본 헬스체크 엔드포인트 `/up`이 자동으로 등록되어 있습니다. 컨테이너나 로드밸런서의 헬스체크에 활용하세요.

### 4. JavaScript 설정

`resources/js/bootstrap.js`에서 Axios가 전역으로 설정되며, 모든 요청에 CSRF 토큰과 `X-Requested-With` 헤더가 자동으로 포함됩니다.

## 서비스 기능 범위

### MVP 범위 (1차 구현 목표)

#### 1. 게스트 모드
- 로그인 없이 사용 가능
- LocalStorage를 활용한 데이터 저장
- 회원 가입 유도 UI
- 게스트 → 회원 전환 시 로컬 데이터 마이그레이션

#### 2. 사용자 관리
- 회원가입/로그인
- 프로필 관리
- 게스트 데이터를 회원 계정으로 마이그레이션

#### 3. 할 일(Task) 기본 관리
- 할 일 생성 (제목, 설명)
- 할 일 완료/미완료 토글
- 할 일 수정/삭제
- 할 일 목록 조회 (전체/완료/미완료)
- 완료 처리 시간 확인

#### 4. 그룹(Group) 기본 관리
- 그룹 생성/수정/삭제
- 그룹에 할 일 추가/제거
- 그룹별 할 일 조회
- 그룹 목록 조회

### MVP 이후 기능 (추후 논의 및 구현)

#### Phase 2: 할 일 고급 기능
- 우선순위 설정 (높음/보통/낮음)
- 반복 일정 (매일, 매주, 매월)
- 할 일 첨부파일 지원
- 체크리스트 (서브태스크)
- 할 일 순서 변경 (드래그 앤 드롭)

#### Phase 3: 그룹 고급 기능
- 그룹 색상/아이콘 커스터마이징
- 그룹 정렬/순서 변경
- 그룹 즐겨찾기
- 그룹 아카이브
- 그룹 템플릿

#### Phase 4: 공유 및 협업
- 그룹을 다른 사용자와 공유
- 공유 그룹 멤버 관리
- 그룹 멤버별 권한 설정 (소유자/멤버)
- 할 일 담당자 할당
- 활동 로그 및 댓글 기능

#### Phase 5: 추가 편의 기능
- 태그 시스템
- 전체 검색 (할 일, 그룹)
- 알림 기능 (마감일 임박, 공유 그룹 변경)
- 통계 및 대시보드 (완료율, 생산성 차트)
- 캘린더 뷰
- 모바일 앱 지원

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

## Feature 단위 작업 계획

### Feature 1: 게스트 모드 구현
**목표**: 로그인 없이 LocalStorage 기반으로 할 일 관리 가능

- [ ] LocalStorage 기반 Task CRUD 구현
- [ ] LocalStorage 기반 Group CRUD 구현
- [ ] 게스트 세션 관리 로직
- [ ] 회원 가입 유도 UI 컴포넌트
- [ ] 게스트 데이터 마이그레이션 API

**예상 작업 기간**: 3-5일

### Feature 2: 사용자 인증 시스템
**목표**: 회원가입, 로그인, 게스트 데이터 전환

- [ ] User Entity 및 Value Objects 설계
- [ ] User Repository 구현 (Eloquent)
- [ ] 회원가입 UseCase 구현
- [ ] 로그인/로그아웃 UseCase 구현
- [ ] GuestMigrationService 구현
- [ ] 인증 미들웨어 설정
- [ ] 회원가입/로그인 Blade 컴포넌트

**예상 작업 기간**: 5-7일

### Feature 3: 할 일 기본 관리
**목표**: 할 일 CRUD 및 상태 관리

- [ ] Task Entity 및 Value Objects 설계
- [ ] Task Repository 구현 (Eloquent)
- [ ] CreateTask UseCase
- [ ] UpdateTask UseCase
- [ ] CompleteTask UseCase
- [ ] DeleteTask UseCase
- [ ] GetTaskList UseCase (필터링: 전체/완료/미완료)
- [ ] Task Domain Events 구현
- [ ] Task 목록/상세 Blade 컴포넌트
- [ ] Task 입력 폼 컴포넌트

**예상 작업 기간**: 7-10일

### Feature 4: 그룹 기본 관리
**목표**: 그룹 CRUD 및 할 일 연결 관리

- [ ] Group Aggregate Root 설계
- [ ] Group Repository 구현 (Eloquent)
- [ ] CreateGroup UseCase
- [ ] UpdateGroup UseCase
- [ ] DeleteGroup UseCase
- [ ] AddTaskToGroup UseCase
- [ ] RemoveTaskFromGroup UseCase
- [ ] GetGroupTasks UseCase
- [ ] Group Domain Events 구현
- [ ] 그룹 목록/상세 Blade 컴포넌트
- [ ] 그룹 관리 UI

**예상 작업 기간**: 7-10일

### Feature 5: 프론트엔드 통합 및 UX
**목표**: 사용자 경험 최적화 및 반응형 디자인

- [ ] 레이아웃 컴포넌트 설계 (헤더, 사이드바, 푸터)
- [ ] 대시보드 화면 구현
- [ ] 할 일 목록 화면
- [ ] 그룹 관리 화면
- [ ] 반응형 디자인 (Tailwind CSS)
- [ ] 로딩 상태 및 에러 처리 UI
- [ ] 토스트 알림 컴포넌트

**예상 작업 기간**: 5-7일

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
├── Infrastructure/                  # 인프라 레이어 (기술 구현)
│   ├── User/
│   │   └── Repositories/
│   │       ├── EloquentUserRepository.php
│   │       └── LocalStorageGuestSessionRepository.php
│   │
│   ├── Task/
│   │   └── Repositories/
│   │       └── EloquentTaskRepository.php
│   │
│   └── Group/
│       └── Repositories/
│           └── EloquentGroupRepository.php
│
└── Presentation/                    # 프레젠테이션 레이어 (UI)
    ├── Http/
    │   ├── Controllers/
    │   │   ├── Auth/
    │   │   │   ├── RegisterController.php
    │   │   │   ├── LoginController.php
    │   │   │   └── LogoutController.php
    │   │   ├── TaskController.php
    │   │   └── GroupController.php
    │   │
    │   ├── Requests/
    │   │   ├── RegisterRequest.php
    │   │   ├── LoginRequest.php
    │   │   ├── CreateTaskRequest.php
    │   │   ├── UpdateTaskRequest.php
    │   │   └── CreateGroupRequest.php
    │   │
    │   └── Middleware/
    │       ├── AuthenticateOrGuest.php
    │       └── GuestMigrationPrompt.php
    │
    └── Views/
        ├── Components/
        │   ├── Layout/
        │   │   ├── App.php
        │   │   ├── Guest.php
        │   │   ├── Header.php
        │   │   └── Sidebar.php
        │   ├── Task/
        │   │   ├── TaskList.php
        │   │   ├── TaskItem.php
        │   │   ├── TaskForm.php
        │   │   └── TaskFilter.php
        │   └── Group/
        │       ├── GroupList.php
        │       ├── GroupCard.php
        │       └── GroupForm.php
        │
        └── Pages/
            ├── welcome.blade.php
            ├── dashboard.blade.php
            ├── auth/
            │   ├── register.blade.php
            │   └── login.blade.php
            ├── tasks/
            │   ├── index.blade.php
            │   └── show.blade.php
            └── groups/
                ├── index.blade.php
                └── show.blade.php
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
        $task = new Task(
            title: new TaskTitle($dto->title),
            description: new TaskDescription($dto->description)
        );

        // Repository를 통해 영속화
        $this->taskRepository->save($task);

        // DTO로 변환하여 반환
        return TaskDTO::fromEntity($task);
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
    public function save(Task $task): void {
        TaskModel::create([
            'title' => $task->getTitle()->value(),
            'description' => $task->getDescription()->value(),
            'completed_datetime' => $task->getCompletedDateTime()?->toDateTime(),
            // ...
        ]);
    }

    public function findById(int $id): ?Task {
        $model = TaskModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    private function toDomain(TaskModel $model): Task {
        // Eloquent 모델 → Domain Entity 변환
        return new Task(
            id: $model->id,
            title: new TaskTitle($model->title),
            description: new TaskDescription($model->description),
            completedDateTime: $model->completed_datetime
                ? CompletedDateTime::fromDateTime($model->completed_datetime)
                : null,
            // ...
        );
    }
}
```

#### Presentation Layer (프레젠테이션 레이어)
**책임**: HTTP 요청 처리, 응답 생성, 뷰 렌더링

**규칙:**
- Application 레이어의 UseCase 호출
- Request 객체로 입력 검증
- DTO를 뷰로 전달
- 비즈니스 로직 포함 금지

**예시:**
```php
// ✅ 올바른 Controller
namespace Src\Presentation\Http\Controllers;

class TaskController extends Controller {
    public function __construct(
        private CreateTask $createTask
    ) {}

    public function store(CreateTaskRequest $request) {
        $dto = new CreateTaskDTO(
            title: $request->input('title'),
            description: $request->input('description')
        );

        $task = $this->createTask->execute($dto);

        return redirect()->route('tasks.show', $task->id)
            ->with('success', '할 일이 생성되었습니다.');
    }
}
```

### 2. Repository 패턴 구현

**Interface 정의 (Domain):**
```php
namespace Src\Domain\Task\Repositories;

interface TaskRepositoryInterface {
    public function save(Task $task): void;
    public function findById(int $id): ?Task;
    public function findByUserId(int $userId): array;
    public function delete(Task $task): void;
}
```

**Service Provider 바인딩:**
```php
// AppServiceProvider.php
public function register(): void {
    $this->app->bind(
        \Src\Domain\Task\Repositories\TaskRepositoryInterface::class,
        \Src\Infrastructure\Task\Repositories\EloquentTaskRepository::class
    );
}
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

### 4. Blade Component 작성 규칙

**단일 책임 원칙:**
```php
// ✅ 명확한 단일 책임
namespace App\View\Components\Task;

class TaskItem extends Component {
    public function __construct(
        public TaskDTO $task
    ) {}
}
```

**Props 타입 힌팅:**
```php
// ✅ 명확한 타입 힌팅
public function __construct(
    public TaskDTO $task,
    public bool $showActions = true,
    public ?string $variant = null
) {}
```

**Slot 활용:**
```blade
{{-- task-card.blade.php --}}
<div class="task-card">
    <h3>{{ $task->title }}</h3>

    @isset($actions)
        <div class="actions">
            {{ $actions }}
        </div>
    @endisset
</div>
```

### 5. 테스트 작성 규칙

#### 테스트 작성 필수
**도메인 코드 작성 시 테스트 코드 작성은 필수입니다.**

모든 도메인 레이어 컴포넌트(Entity, Value Object, Exception)는 해당하는 유닛 테스트를 가져야 하며, 주요 비즈니스 시나리오는 통합 테스트로 검증해야 합니다.

#### 커버리지 목표
- **유닛 테스트**: 80-90% 목표
- **통합 테스트**: 70-80% 목표

유닛 테스트는 개별 컴포넌트의 동작을 검증하고, 통합 테스트는 여러 컴포넌트가 함께 동작하는 실제 시나리오를 검증합니다.

#### 테스트 파일 위치
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
└── Feature/Domain/        # 도메인 통합 테스트
    ├── Task/
    │   └── TaskLifecycleTest.php
    ├── User/
    └── Group/
```

#### 테스트 패턴
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

#### 테스트 명명 규칙
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

#### 도메인 컴포넌트별 테스트 가이드

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
public function test_exception_has_correct_status_code(): void
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
public function test_complete_task_lifecycle_from_creation_to_completion(): void
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

#### 테스트 실행
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
php artisan test --filter test_complete_task_sets_completed_datetime
```

#### 테스트 작성 시기
- **TDD 권장**: 도메인 코드 작성 전 테스트 먼저 작성
- **최소 요구사항**: 도메인 코드와 함께 동일 PR에 테스트 포함
- **리팩토링 전**: 기존 동작을 보장하는 테스트 먼저 작성

#### 테스트 품질 기준
- **독립성**: 각 테스트는 독립적으로 실행 가능
- **반복성**: 동일한 입력은 항상 동일한 결과
- **명확성**: 테스트 이름과 구조만으로 의도 파악 가능
- **빠른 실행**: 유닛 테스트는 1초 이내 실행

## 개발 시 참고사항

- **PHP 버전**: PHP 8.4 이상 필수
- **Node.js**: Node.js 18 이상 권장
- **데이터베이스**: MySQL 8.0 이상, Redis 6.0 이상
- **로그**: `composer dev`로 실행 시 Laravel Pail이 실시간 로그 표시
- **코드 스타일**: 커밋 전 `vendor/bin/pint` 실행하여 코드 스타일 통일
- **DDD 준수**: 레이어 간 의존성 규칙을 엄격히 준수
- **테스트**: Feature별로 단위/통합 테스트 작성 필수
