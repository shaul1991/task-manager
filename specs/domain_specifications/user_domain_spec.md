# User 도메인 상세 명세

**도메인명**: User (사용자)
**상태**: 📋 예정 (0% 완료)
**최종 업데이트**: 2025-10-30

---

## 1. 도메인 개요

User는 시스템의 사용자를 나타내는 도메인입니다. 게스트 모드와 회원 모드를 지원하며, 게스트 데이터의 회원 전환 기능을 제공합니다.

### 1.1 책임

- 사용자 인증 (회원가입, 로그인, 로그아웃)
- 게스트 세션 관리
- 게스트 데이터 → 회원 데이터 마이그레이션
- 프로필 관리

### 1.2 비즈니스 규칙

- **게스트 모드**:
  - 로그인 없이 LocalStorage에 데이터 저장
  - 게스트 세션 ID로 식별
  - Task 및 TaskList의 `user_id`는 NULL
- **회원 모드**:
  - 이메일/비밀번호로 가입 및 로그인
  - 서버 DB에 데이터 저장
  - Task 및 TaskList의 `user_id`에 User ID 할당
- **게스트 전환**:
  - 게스트가 회원 가입 시 LocalStorage 데이터를 서버로 마이그레이션
  - 기존 게스트 Task/TaskList의 `user_id` 업데이트

---

## 2. Entity 설계 (예정)

### 2.1 User Entity

```php
final class User
{
    private function __construct(
        private ?int $id,
        private Username $username,
        private Email $email,
        private Password $password,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {}

    public static function create(
        Username $username,
        Email $email,
        Password $password
    ): self;

    public static function reconstruct(...): self;

    public function updateUsername(Username $username): void;
    public function updateEmail(Email $email): void;
    public function changePassword(Password $password): void;
}
```

### 2.2 GuestSession Entity

```php
final class GuestSession
{
    private function __construct(
        private string $sessionId,
        private DateTimeImmutable $createdAt
    ) {}

    public static function create(): self;
    public function sessionId(): string;
}
```

---

## 3. Value Objects (예정)

### 3.1 Email

```php
final class Email
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate();
    }

    public static function fromString(string $value): self;
    public function value(): string;
    private function validate(): void; // 이메일 형식 검증
}
```

### 3.2 Username

```php
final class Username
{
    private const MIN_LENGTH = 2;
    private const MAX_LENGTH = 50;

    private function __construct(
        private readonly string $value
    ) {
        $this->validate();
    }

    public static function fromString(string $value): self;
    public function value(): string;
    private function validate(): void;
}
```

### 3.3 Password

```php
final class Password
{
    private function __construct(
        private readonly string $hashedValue
    ) {}

    public static function fromPlainText(string $plainText): self;
    public static function fromHash(string $hash): self;
    public function hash(): string;
    public function verify(string $plainText): bool;
}
```

---

## 4. Domain Services (예정)

### 4.1 AuthenticationService

**책임**: 사용자 인증 처리

```php
final class AuthenticationService
{
    public function authenticate(Email $email, Password $password): ?User;
    public function register(Username $username, Email $email, Password $password): User;
    public function logout(User $user): void;
}
```

### 4.2 GuestMigrationService

**책임**: 게스트 데이터를 회원 데이터로 마이그레이션

```php
final class GuestMigrationService
{
    public function migrate(GuestSession $guestSession, User $user): void;
    public function migrateLocalStorageData(string $sessionId, int $userId, array $guestData): void;
}
```

---

## 5. Repository Interface (예정)

### 5.1 UserRepositoryInterface

```php
interface UserRepositoryInterface
{
    public function save(User $user): User;
    public function findById(int $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function existsByEmail(Email $email): bool;
    public function delete(int $id): void;
}
```

### 5.2 GuestSessionRepositoryInterface

```php
interface GuestSessionRepositoryInterface
{
    public function save(GuestSession $session): GuestSession;
    public function findBySessionId(string $sessionId): ?GuestSession;
}
```

---

## 6. Use Cases (예정)

### 6.1 RegisterUser

**입력**:
```php
class RegisterUserDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $password
    ) {}
}
```

**출력**: `UserDTO`

**플로우**:
1. 이메일 중복 확인
2. User Entity 생성
3. Repository를 통해 저장
4. UserDTO로 변환하여 반환

### 6.2 LoginUser

**입력**:
```php
class LoginUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {}
}
```

**출력**: `UserDTO` + 세션 토큰

**플로우**:
1. 이메일로 User 조회
2. 비밀번호 검증
3. 세션 생성
4. UserDTO 반환

### 6.3 MigrateGuestData

**입력**:
```php
class MigrateGuestDataDTO
{
    public function __construct(
        public readonly string $sessionId,
        public readonly int $userId,
        public readonly array $tasks,
        public readonly array $taskLists
    ) {}
}
```

**출력**: `MigrationResultDTO`

**플로우**:
1. 게스트 세션 확인
2. LocalStorage 데이터 파싱
3. Task 및 TaskList Entity 생성
4. `user_id` 할당
5. Repository를 통해 저장
6. LocalStorage 정리 안내

---

## 7. 게스트 모드 플로우

### 7.1 게스트 데이터 저장 (LocalStorage)

```javascript
// LocalStorage 구조
{
  "guest_session_id": "uuid-v4",
  "guest_tasks": [...],
  "guest_task_lists": [...]
}
```

### 7.2 게스트 → 회원 전환 플로우

```
1. 사용자가 회원 가입 버튼 클릭
   ↓
2. LocalStorage에서 게스트 데이터 수집
   ↓
3. 회원 가입 API 호출 (RegisterUser)
   ↓
4. 게스트 데이터 마이그레이션 API 호출 (MigrateGuestData)
   ↓
5. 서버에서 Task/TaskList Entity 생성 및 user_id 할당
   ↓
6. LocalStorage 정리
   ↓
7. 회원 모드로 전환 완료
```

---

## 8. 테스트 계획 (예정)

### 8.1 예상 테스트

- User Entity Tests (10-15개)
- Value Object Tests (Email, Username, Password) (15개)
- AuthenticationService Tests (10개)
- GuestMigrationService Tests (10개)
- Use Case Tests (15개)
- Repository Tests (10개)
- Integration Tests (10개)

**예상 총 테스트**: 80-90개

---

## 9. 다음 단계

### 9.1 Feature 2 구현 계획

1. [ ] Domain Layer 설계 및 구현
2. [ ] Application Layer 구현 (UseCases, DTOs)
3. [ ] Infrastructure Layer 구현 (Repository, Migration)
4. [ ] Presentation Layer 구현 (Controller, Blade)
5. [ ] LocalStorage 연동 구현 (JavaScript)
6. [ ] 테스트 작성
7. [ ] 문서 업데이트

**예상 작업 기간**: 5-7일

---

**문서 버전**: 1.0
**최초 작성**: 2025-10-30
**최근 업데이트**: 2025-10-30
