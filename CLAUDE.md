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

## 전문 문서 참조

개발 영역에 따라 다음 전문 문서를 참조하세요:

- **백엔드 개발**: @BACKEND.md
  - Laravel 개발 규칙 (Model, Migration)
  - DDD 아키텍처 설계
  - 데이터베이스 스키마
  - Repository 패턴
  - 백엔드 테스트 작성 규칙

- **프론트엔드 개발**: @FRONTEND.md
  - Tailwind CSS 4.0 설정
  - Blade Components 작성 규칙
  - LocalStorage 관리 (게스트 모드)
  - JavaScript 모듈 구조
  - UI/UX 가이드라인

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
├── Providers/          # 서비스 프로바이더
└── View/Components/    # Blade 컴포넌트

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

src/
├── Domain/            # 도메인 레이어 (순수 비즈니스 로직)
├── Application/       # 애플리케이션 레이어 (유스케이스, DTOs)
└── Infrastructure/    # 인프라 레이어 (Repository 구현)

tests/
├── Unit/              # 단위 테스트
└── Feature/           # 기능 테스트
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

**상태**: ✅ **완전 구현 완료** (Domain, Application, Infrastructure Layers)

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
**상태**: ✅ **완료**

- [x] Task Entity 및 Value Objects 설계
- [x] Task Repository 구현 (Eloquent)
- [x] CreateTask UseCase
- [x] UpdateTask UseCase
- [x] CompleteTask UseCase
- [x] DeleteTask UseCase
- [x] GetTaskList UseCase (필터링: 전체/완료/미완료)
- [ ] Task 목록/상세 Blade 컴포넌트
- [ ] Task 입력 폼 컴포넌트

**백엔드 작업 기간**: 완료 (95개 테스트 통과)
**프론트엔드 작업 기간**: 5-7일 (예정)

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

## 개발 시 참고사항

- **PHP 버전**: PHP 8.4 이상 필수
- **Node.js**: Node.js 18 이상 권장
- **데이터베이스**: MySQL 8.0 이상, Redis 6.0 이상
- **로그**: `composer dev`로 실행 시 Laravel Pail이 실시간 로그 표시
- **코드 스타일**: 커밋 전 `vendor/bin/pint` 실행하여 코드 스타일 통일
- **DDD 준수**: 레이어 간 의존성 규칙을 엄격히 준수
- **테스트**: Feature별로 단위/통합 테스트 작성 필수

## 상세 개발 가이드

**백엔드 개발**: @BACKEND.md 참조
- Laravel 개발 규칙
- DDD 아키텍처 설계
- 데이터베이스 스키마
- Repository 패턴
- 테스트 작성 규칙

**프론트엔드 개발**: @FRONTEND.md 참조
- Tailwind CSS 4.0 설정
- Blade Components 작성 규칙
- LocalStorage 관리
- JavaScript 모듈 구조
- UI/UX 가이드라인
