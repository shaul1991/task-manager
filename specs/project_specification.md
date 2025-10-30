# 프로젝트 전체 명세서

**프로젝트명**: Task Manager (할 일 관리 서비스)
**버전**: 1.0
**작성일**: 2025-10-30

---

## 1. 프로젝트 개요

### 1.1 비전

**일상 생활의 할 일(Todo) 관리를 효율적이고 체계적으로 지원하는 서비스**

사용자가 개별 할 일(Task)을 생성하고, 관련된 할 일들을 목록(TaskList)으로 묶어 체계적으로 관리할 수 있는 웹 기반 애플리케이션입니다. 게스트 모드와 회원 모드를 모두 지원하여 사용자의 진입 장벽을 낮추고, DDD 아키텍처를 통해 확장 가능하고 유지보수하기 쉬운 구조를 제공합니다.

### 1.2 핵심 가치

- **접근성**: 로그인 없이 게스트 모드로 즉시 사용 가능
- **체계성**: TaskList를 통한 할 일 그룹화 및 분류
- **확장성**: DDD 아키텍처 기반의 확장 가능한 구조
- **안전성**: 회원 전환 시 게스트 데이터 마이그레이션 지원
- **편의성**: 직관적인 UI/UX와 반응형 디자인

### 1.3 타겟 사용자

- **개인 사용자**: 일상 생활의 할 일을 관리하고 싶은 사람
- **게스트 사용자**: 간단히 시도해보고 싶은 사람
- **프로 사용자**: 복잡한 프로젝트와 할 일을 체계적으로 관리하고 싶은 사람

---

## 2. 핵심 비즈니스 요구사항

### 2.1 핵심 개념

```
SubTask (하위 작업) ← Task (할 일) ← TaskList (목록) ← TaskGroup (카테고리)
```

#### Task (할 일)
- 개별적인 할 일 항목
- 예: "우유 사기", "보고서 작성", "운동하기"
- 속성: 제목, 설명, 완료 여부, 완료 시간
- 선택적으로 TaskList에 속할 수 있음

#### TaskList (할 일 목록)
- 관련된 할 일들을 묶는 컨테이너
- 예: "쇼핑 목록", "업무 프로젝트", "운동 루틴"
- 속성: 이름, 설명
- 여러 Task를 포함 가능 (task_list_id 외래키)

#### SubTask (하위 작업) - Phase 2
- Task 내부의 체크리스트 형태
- 예: "보고서 작성" Task 안에 "자료 조사", "초안 작성", "검토" SubTask
- Task에 종속되며 독립적으로 존재 불가

#### TaskGroup (카테고리) - Phase 3
- TaskList들을 묶는 최상위 계층
- 예: "회사", "개인", "프로젝트 A"
- 여러 TaskList를 포함 가능

### 2.2 MVP 범위 (1차 구현 목표)

#### ✅ 완료: Task 기본 관리
- Task 생성 (제목, 설명)
- Task 완료/미완료 토글
- Task 수정 (제목, 설명)
- Task 삭제 (SoftDelete)
- Task 목록 조회 (전체/완료/미완료 필터링)
- 완료 처리 시간 확인

#### 🚧 진행중: TaskList 기본 관리
- TaskList 생성/수정/삭제
- TaskList에 Task 할당/해제
- TaskList별 Task 조회
- TaskList 목록 조회

#### 📋 예정: 게스트 모드
- 로그인 없이 사용 가능
- LocalStorage를 활용한 데이터 저장
- 회원 가입 유도 UI
- 게스트 → 회원 전환 시 로컬 데이터 마이그레이션

#### 📋 예정: 사용자 관리
- 회원가입/로그인
- 프로필 관리
- 게스트 데이터를 회원 계정으로 마이그레이션

#### 📋 예정: 프론트엔드 통합
- Task 목록/상세 Blade 컴포넌트
- TaskList 관리 UI
- 반응형 디자인 (Tailwind CSS 4.0)
- 토스트 알림 컴포넌트

### 2.3 MVP 이후 기능

#### Phase 2: SubTask 및 할 일 고급 기능
- SubTask (하위 작업) 구현
- 우선순위 설정 (높음/보통/낮음)
- 반복 일정 (매일, 매주, 매월)
- 할 일 첨부파일 지원
- 할 일 순서 변경 (드래그 앤 드롭)

#### Phase 3: TaskGroup 및 TaskList 고급 기능
- TaskGroup (상위 카테고리) 구현
- TaskList 색상/아이콘 커스터마이징
- TaskList 정렬/순서 변경
- TaskList 즐겨찾기
- TaskList 아카이브
- TaskList 템플릿

#### Phase 4: 공유 및 협업
- TaskList를 다른 사용자와 공유
- 공유 TaskList 멤버 관리
- TaskList 멤버별 권한 설정 (소유자/멤버)
- 할 일 담당자 할당
- 활동 로그 및 댓글 기능

#### Phase 5: 추가 편의 기능
- 태그 시스템
- 전체 검색 (할 일, TaskList, TaskGroup)
- 알림 기능 (마감일 임박, 공유 TaskList 변경)
- 통계 및 대시보드 (완료율, 생산성 차트)
- 캘린더 뷰
- 모바일 앱 지원

---

## 3. 기술 스택

### 3.1 아키텍처

**DDD (Domain-Driven Design)**
- 비즈니스 로직을 도메인 중심으로 설계
- 레이어 간 명확한 책임 분리
- 확장 가능하고 유지보수하기 쉬운 구조

### 3.2 백엔드

| 구분 | 기술 | 버전 | 역할 |
|------|------|------|------|
| **프레임워크** | Laravel | 12.0 | 웹 애플리케이션 프레임워크 |
| **언어** | PHP | 8.4 | 백엔드 언어 |
| **ORM** | Eloquent | Laravel 12.0 | 데이터베이스 ORM |
| **데이터베이스** | MySQL | 8.0+ | 프로덕션 DB |
| **캐시** | Redis | 6.0+ | 세션, 캐시, 큐 |
| **테스팅** | PHPUnit | 11.5.3 | 유닛/통합 테스트 |
| **코드 스타일** | Laravel Pint | - | 코드 포맷팅 |

### 3.3 프론트엔드

| 구분 | 기술 | 버전 | 역할 |
|------|------|------|------|
| **빌드 도구** | Vite | - | 프론트엔드 빌드 |
| **CSS 프레임워크** | Tailwind CSS | 4.0 | 스타일링 |
| **템플릿 엔진** | Laravel Blade | - | 서버 사이드 렌더링 |
| **컴포넌트** | Blade Components | - | 재사용 가능한 UI 컴포넌트 |
| **HTTP 클라이언트** | Axios | - | API 통신 |
| **로컬 스토리지** | LocalStorage API | - | 게스트 모드 데이터 저장 |

### 3.4 개발 환경

| 구분 | 기술 | 역할 |
|------|------|------|
| **패키지 관리** | Composer, npm | PHP 및 JavaScript 패키지 관리 |
| **개발 서버** | Laravel Artisan, Vite Dev Server | 로컬 개발 서버 |
| **로그 모니터링** | Laravel Pail | 실시간 로그 모니터링 |
| **큐 워커** | Laravel Queue | 백그라운드 작업 처리 |
| **버전 관리** | Git | 소스 코드 버전 관리 |

---

## 4. DDD 아키텍처 설계

### 4.1 Bounded Context

프로젝트는 3개의 주요 Bounded Context로 구성됩니다:

#### 1. User Context (사용자 컨텍스트) - 📋 예정
**책임**: 사용자 인증, 권한 관리, 게스트 모드 관리

**주요 도메인 모델**:
- User Entity (회원 사용자)
- GuestSession Entity (게스트 세션)
- Email, Username, Password Value Objects
- AuthenticationService
- GuestMigrationService

#### 2. Task Context (할 일 컨텍스트) - ✅ 완료
**책임**: 할 일의 생명주기 관리 (생성, 수정, 완료, 삭제)

**주요 도메인 모델**:
- Task Entity (Aggregate Root)
- TaskTitle, TaskDescription, CompletedDateTime Value Objects
- TaskRepositoryInterface
- Task Domain Events

#### 3. TaskList Context (할 일 목록 컨텍스트) - 🚧 진행중
**책임**: TaskList 관리 및 할 일 컨테이너 역할

**주요 도메인 모델**:
- TaskList Entity (Aggregate Root)
- TaskListName, TaskListDescription Value Objects
- TaskListRepositoryInterface
- TaskList Domain Events

### 4.2 레이어 구조

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

**의존성 규칙**:
- Presentation → Application → Domain ← Infrastructure
- Domain 레이어는 다른 레이어에 의존하지 않음 (순수 비즈니스 로직)
- Infrastructure는 Domain의 인터페이스를 구현
- Application 레이어는 Domain을 조율하여 유스케이스 구현

### 4.3 디렉토리 구조 (현재 MVP)

```
src/
├── Domain/                          # 도메인 레이어
│   ├── Task/                        # ✅ 완료
│   │   ├── Entities/
│   │   ├── ValueObjects/
│   │   ├── Repositories/
│   │   ├── Services/
│   │   ├── Events/
│   │   └── Exceptions/
│   ├── TaskList/                    # 🚧 진행중
│   │   ├── Entities/
│   │   ├── ValueObjects/
│   │   ├── Repositories/
│   │   └── Exceptions/
│   └── User/                        # 📋 예정
│       ├── Entities/
│       ├── ValueObjects/
│       ├── Repositories/
│       ├── Services/
│       └── Events/
├── Application/                     # 애플리케이션 레이어
│   ├── Task/                        # ✅ 완료
│   │   ├── UseCases/
│   │   └── DTOs/
│   ├── TaskList/                    # 🚧 진행중 (30%)
│   │   ├── UseCases/
│   │   └── DTOs/
│   └── User/                        # 📋 예정
│       ├── UseCases/
│       └── DTOs/
├── Infrastructure/                  # 인프라 레이어
│   ├── Task/                        # ✅ 완료
│   │   └── Repositories/
│   ├── TaskList/                    # ✅ 완료
│   │   └── Repositories/
│   └── User/                        # 📋 예정
│       └── Repositories/
├── Presentation/                    # 프레젠테이션 레이어 (예정)
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   └── Views/
│       ├── Components/
│       └── Pages/
└── Shared/                          # 공유 컴포넌트
    ├── Exceptions/
    │   ├── DomainException.php
    │   ├── ApplicationException.php
    │   ├── ValidationException.php
    │   ├── NotFoundException.php
    │   └── ...
    └── Responses/
        ├── SuccessResponse.php
        ├── ErrorResponse.php
        └── ResponseFactory.php
```

---

## 5. 데이터베이스 설계

### 5.1 설계 원칙

1. **SoftDelete 필수**: 모든 테이블에 `deleted_at` 컬럼
2. **외래키 제약조건 미사용**: 애플리케이션 레벨에서 참조 무결성 관리
3. **Comment 규칙 준수**: 모든 외래키 컬럼에 `comment('{table_name}.{key}')` 추가
4. **인덱스 네이밍**: `idx_{column_name}` 형식 통일
5. **Timezone 지원**: `TIMESTAMP(6)` 사용 (마이크로초 정밀도)

### 5.2 현재 스키마 (MVP)

#### tasks 테이블 (✅ 구현 완료)

```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    completed_datetime TIMESTAMP NULL COMMENT '완료 처리 시간 (NULL이면 미완료)',
    task_list_id BIGINT UNSIGNED NULL COMMENT 'task_lists.id',
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_task_list_id (task_list_id),
    INDEX idx_completed_datetime (completed_datetime),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at)
) COMMENT 'Task (할 일)';
```

#### task_lists 테이블 (✅ 구현 완료)

```sql
CREATE TABLE task_lists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    user_id BIGINT UNSIGNED NULL COMMENT 'users.id (게스트는 NULL)',
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at)
) COMMENT 'TaskList (할 일 목록)';
```

#### users 테이블 (📋 예정)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at TIMESTAMP(6) NULL,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_deleted_at (deleted_at)
) COMMENT 'User (사용자)';
```

### 5.3 향후 스키마

#### sub_tasks 테이블 (Phase 2)

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

#### task_groups 테이블 (Phase 3)

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
```

---

## 6. 개발 규칙 및 컨벤션

### 6.1 Laravel 개발 규칙

#### Model 규칙
- **SoftDelete 사용 필수**: 모든 Model은 `SoftDeletes` trait 사용
- **Casts 메서드 방식**: Laravel 11+ 스타일의 `casts()` 메서드 사용
- **HasFactory 트레잇**: 모든 Model은 `HasFactory` 트레잇 포함

#### Migration 규칙
- **타임스탬프**: `dateTimeTz()` 사용, `useCurrent()`, `useCurrentOnUpdate()`
- **SoftDelete**: 모든 테이블에 `deleted_at` 컬럼 및 인덱스
- **인덱스 네이밍**: `idx_{column_name}` 또는 `idx_{first_column}_{numbering}`
- **외래키 Comment**: `comment('{table_name}.{key}')` 필수

### 6.2 DDD 개발 규칙

#### Domain Layer
- **프레임워크 독립적**: Laravel, Eloquent 등 외부 의존성 금지
- **순수 비즈니스 로직**: 도메인 규칙과 불변식 검증
- **Value Object 불변성**: Value Object는 불변 객체로 설계
- **도메인 이벤트**: 중요한 상태 변경은 도메인 이벤트로 표현

#### Application Layer
- **트랜잭션 관리**: UseCase에서 트랜잭션 경계 관리
- **DTO 사용**: 외부와 통신 시 DTO 사용
- **비즈니스 로직 위임**: 복잡한 로직은 Domain Layer로 위임

#### Infrastructure Layer
- **Repository 패턴**: Repository Interface 구현
- **Eloquent 사용**: Eloquent Model을 통한 데이터 접근
- **Entity 변환**: Eloquent Model ↔ Domain Entity 변환

### 6.3 테스트 규칙

- **테스트 작성 필수**: 모든 도메인 코드는 테스트 필수
- **커버리지 목표**: 유닛 테스트 80-90%, 통합 테스트 70-80%
- **Given-When-Then**: 모든 테스트는 GWT 패턴 준수
- **한글 메서드명**: 테스트 메서드명은 한글로 작성
- **독립성**: 각 테스트는 독립적으로 실행 가능

### 6.4 코드 스타일

- **Laravel Pint**: 커밋 전 `vendor/bin/pint` 실행
- **PSR-12**: PSR-12 코딩 스타일 준수
- **타입 힌팅**: 모든 메서드 파라미터 및 반환 타입 명시
- **Declare Strict Types**: 모든 PHP 파일 상단에 `declare(strict_types=1);`

---

## 7. 개발 프로세스

### 7.1 Feature 개발 플로우

```
1. 요구사항 분석
   ↓
2. Specs 문서 작성/업데이트
   ↓
3. Domain Layer 설계
   ├─ Entity 설계
   ├─ Value Object 설계
   ├─ Repository Interface 정의
   └─ Domain Event 정의
   ↓
4. Domain Layer 구현
   ↓
5. Domain Layer 테스트 작성 (TDD 권장)
   ↓
6. Application Layer 구현
   ├─ UseCase 구현
   └─ DTO 정의
   ↓
7. Application Layer 테스트 작성
   ↓
8. Infrastructure Layer 구현
   ├─ Repository 구현
   ├─ Migration 작성
   └─ Service Provider 바인딩
   ↓
9. Infrastructure Layer 테스트 작성
   ↓
10. Presentation Layer 구현 (필요 시)
    ├─ Controller 구현
    ├─ Blade Components 구현
    └─ API 엔드포인트 구현
    ↓
11. 통합 테스트 작성
    ↓
12. 문서 업데이트
    ├─ implementation_status.md
    ├─ 도메인 명세서
    └─ API 명세서 (필요 시)
    ↓
13. Code Review 및 Merge
```

### 7.2 테스트 전략

#### 테스트 레벨
1. **Unit Test**: Domain Layer, Application Layer
2. **Integration Test**: Infrastructure Layer, 레이어 간 통합
3. **Feature Test**: Presentation Layer, E2E 시나리오

#### 테스트 우선순위
1. **Critical Path**: 핵심 비즈니스 로직 (Task 완료 처리, TaskList 생성 등)
2. **Happy Path**: 정상 플로우
3. **Edge Cases**: 경계 조건, 예외 상황
4. **Error Handling**: 예외 처리 로직

---

## 8. 배포 및 운영

### 8.1 환경 구성

#### 로컬 개발 환경
- **PHP**: 8.4+
- **MySQL**: 8.0+ (로컬 또는 Docker)
- **Redis**: 6.0+ (로컬 또는 Docker)
- **Node.js**: 18+

#### 프로덕션 환경 (예정)
- **웹 서버**: Nginx 또는 Apache
- **데이터베이스**: MySQL 8.0+
- **캐시**: Redis 6.0+
- **큐**: Redis Queue
- **세션**: Redis

### 8.2 성능 목표

- **응답 시간**: 평균 200ms 이하
- **동시 사용자**: 1,000명 지원
- **데이터베이스**: 인덱스 최적화를 통한 쿼리 성능 개선
- **캐싱**: Redis를 통한 자주 조회되는 데이터 캐싱

---

## 9. 향후 로드맵

### 단기 (1-2개월)
- ✅ Task 도메인 완성
- 🚧 TaskList 도메인 완성
- 📋 게스트 모드 구현
- 📋 사용자 인증 시스템
- 📋 프론트엔드 통합

### 중기 (3-6개월)
- SubTask 구현 (Phase 2)
- TaskGroup 구현 (Phase 3)
- 고급 기능 추가 (우선순위, 반복 일정 등)
- API 문서 자동화

### 장기 (6개월 이상)
- 공유 및 협업 기능 (Phase 4)
- 알림 및 대시보드 (Phase 5)
- 모바일 앱 개발
- 서드파티 통합 (Google Calendar, Trello 등)

---

## 10. 참고 문서

- **프로젝트 개요**: `/CLAUDE.md`
- **백엔드 개발 가이드**: `/BACKEND.md`
- **프론트엔드 개발 가이드**: `/FRONTEND.md`
- **구현 진행 상황**: `specs/implementation_status.md`
- **도메인 명세서**: `specs/domain_specifications/`
- **API 명세서**: `specs/api_specification.md`
- **테스팅 계획**: `specs/testing_plan.md`

---

**문서 버전**: 1.0
**최초 작성**: 2025-10-30
**최근 업데이트**: 2025-10-30
