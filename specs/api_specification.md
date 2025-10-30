# API 명세서

**문서 목적**: REST API 엔드포인트 상세 명세
**API 버전**: v1
**최종 업데이트**: 2025-10-30

---

## 1. API 설계 원칙

### 1.1 RESTful API

- **자원 중심 설계**: 엔드포인트는 리소스를 나타냄
- **HTTP 메서드 활용**: GET, POST, PUT, PATCH, DELETE
- **상태 코드 명확히**: 2xx (성공), 4xx (클라이언트 오류), 5xx (서버 오류)
- **JSON 응답**: 모든 응답은 JSON 형식

### 1.2 네이밍 규칙

- **소문자 사용**: `/api/tasks`
- **복수형 사용**: `/api/tasks` (단수형 `/api/task` 아님)
- **케밥 케이스**: `/api/task-lists`
- **계층 구조**: `/api/task-lists/{id}/tasks`

### 1.3 버전 관리

- **URL 버전**: `/api/v1/tasks`
- 현재 버전: v1
- 하위 호환성 유지 원칙

---

## 2. 공통 사항

### 2.1 요청 헤더

```http
Content-Type: application/json
Accept: application/json
X-Requested-With: XMLHttpRequest
X-CSRF-TOKEN: {csrf_token}
Authorization: Bearer {token}  # 회원 모드 시
```

### 2.2 응답 형식

#### 성공 응답

```json
{
  "success": true,
  "data": { ... },
  "message": "작업이 성공적으로 완료되었습니다."
}
```

#### 에러 응답

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "입력 데이터가 유효하지 않습니다.",
    "details": [
      {
        "field": "title",
        "message": "제목은 필수 항목입니다."
      }
    ]
  }
}
```

### 2.3 HTTP 상태 코드

| 코드 | 설명 | 사용 예 |
|------|------|---------|
| 200 | OK | 조회 성공 |
| 201 | Created | 생성 성공 |
| 204 | No Content | 삭제 성공 (응답 본문 없음) |
| 400 | Bad Request | 잘못된 요청 |
| 401 | Unauthorized | 인증 실패 |
| 403 | Forbidden | 권한 없음 |
| 404 | Not Found | 리소스 없음 |
| 409 | Conflict | 비즈니스 규칙 위반 |
| 422 | Unprocessable Entity | 유효성 검증 실패 |
| 500 | Internal Server Error | 서버 오류 |

### 2.4 페이지네이션

**쿼리 파라미터**:
- `limit`: 페이지당 항목 수 (기본값: 100, 최대: 500)
- `offset`: 건너뛸 항목 수 (기본값: 0)

**응답 형식**:
```json
{
  "success": true,
  "data": {
    "items": [ ... ],
    "pagination": {
      "total": 250,
      "limit": 100,
      "offset": 0,
      "has_more": true
    }
  }
}
```

---

## 3. Task API

**Base URL**: `/api/v1/tasks`

### 3.1 Task 목록 조회

```http
GET /api/v1/tasks
```

**쿼리 파라미터**:
- `task_list_id` (optional): TaskList로 필터링
- `completed` (optional): 완료 여부 (true/false)
- `limit` (optional): 페이지당 항목 수 (기본값: 100)
- `offset` (optional): 건너뛸 항목 수 (기본값: 0)

**요청 예시**:
```http
GET /api/v1/tasks?task_list_id=5&completed=false&limit=50&offset=0
```

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "title": "우유 사기",
        "description": "저지방 우유 2L",
        "completed_datetime": null,
        "task_list_id": 5,
        "created_at": "2025-10-30T10:00:00Z",
        "updated_at": "2025-10-30T10:00:00Z"
      }
    ],
    "pagination": {
      "total": 10,
      "limit": 50,
      "offset": 0,
      "has_more": false
    }
  }
}
```

### 3.2 Task 조회

```http
GET /api/v1/tasks/{id}
```

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "우유 사기",
    "description": "저지방 우유 2L",
    "completed_datetime": null,
    "task_list_id": 5,
    "created_at": "2025-10-30T10:00:00Z",
    "updated_at": "2025-10-30T10:00:00Z"
  }
}
```

**에러 응답** (404 Not Found):
```json
{
  "success": false,
  "error": {
    "code": "NOT_FOUND",
    "message": "Task를 찾을 수 없습니다."
  }
}
```

### 3.3 Task 생성

```http
POST /api/v1/tasks
```

**요청 본문**:
```json
{
  "title": "우유 사기",
  "description": "저지방 우유 2L",
  "task_list_id": 5
}
```

**응답 예시** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "우유 사기",
    "description": "저지방 우유 2L",
    "completed_datetime": null,
    "task_list_id": 5,
    "created_at": "2025-10-30T10:00:00Z",
    "updated_at": "2025-10-30T10:00:00Z"
  },
  "message": "Task가 생성되었습니다."
}
```

**에러 응답** (422 Unprocessable Entity):
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "입력 데이터가 유효하지 않습니다.",
    "details": [
      {
        "field": "title",
        "message": "제목은 1-200자 사이여야 합니다."
      }
    ]
  }
}
```

### 3.4 Task 수정

```http
PUT /api/v1/tasks/{id}
```

**요청 본문**:
```json
{
  "title": "우유와 빵 사기",
  "description": "저지방 우유 2L, 식빵 1봉"
}
```

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "우유와 빵 사기",
    "description": "저지방 우유 2L, 식빵 1봉",
    "completed_datetime": null,
    "task_list_id": 5,
    "created_at": "2025-10-30T10:00:00Z",
    "updated_at": "2025-10-30T10:05:00Z"
  },
  "message": "Task가 수정되었습니다."
}
```

### 3.5 Task 완료 처리

```http
PATCH /api/v1/tasks/{id}/complete
```

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "우유 사기",
    "description": "저지방 우유 2L",
    "completed_datetime": "2025-10-30T15:30:00Z",
    "task_list_id": 5,
    "created_at": "2025-10-30T10:00:00Z",
    "updated_at": "2025-10-30T15:30:00Z"
  },
  "message": "Task가 완료되었습니다."
}
```

**에러 응답** (409 Conflict):
```json
{
  "success": false,
  "error": {
    "code": "TASK_ALREADY_COMPLETED",
    "message": "이미 완료된 Task입니다."
  }
}
```

### 3.6 Task 미완료 처리

```http
PATCH /api/v1/tasks/{id}/uncomplete
```

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "우유 사기",
    "description": "저지방 우유 2L",
    "completed_datetime": null,
    "task_list_id": 5,
    "created_at": "2025-10-30T10:00:00Z",
    "updated_at": "2025-10-30T15:35:00Z"
  },
  "message": "Task가 미완료 처리되었습니다."
}
```

### 3.7 Task 삭제

```http
DELETE /api/v1/tasks/{id}
```

**응답 예시** (204 No Content):
```
(응답 본문 없음)
```

---

## 4. TaskList API

**Base URL**: `/api/v1/task-lists`

### 4.1 TaskList 목록 조회

```http
GET /api/v1/task-lists
```

**쿼리 파라미터**:
- `user_id` (optional): User로 필터링 (회원 모드)
- `limit` (optional): 페이지당 항목 수
- `offset` (optional): 건너뛸 항목 수

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "name": "쇼핑 목록",
        "description": "마트에서 살 것들",
        "user_id": null,
        "created_at": "2025-10-30T09:00:00Z",
        "updated_at": "2025-10-30T09:00:00Z"
      }
    ],
    "pagination": {
      "total": 5,
      "limit": 100,
      "offset": 0,
      "has_more": false
    }
  }
}
```

### 4.2 TaskList 조회

```http
GET /api/v1/task-lists/{id}
```

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "쇼핑 목록",
    "description": "마트에서 살 것들",
    "user_id": null,
    "created_at": "2025-10-30T09:00:00Z",
    "updated_at": "2025-10-30T09:00:00Z"
  }
}
```

### 4.3 TaskList 생성

```http
POST /api/v1/task-lists
```

**요청 본문**:
```json
{
  "name": "쇼핑 목록",
  "description": "마트에서 살 것들",
  "user_id": null
}
```

**응답 예시** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "쇼핑 목록",
    "description": "마트에서 살 것들",
    "user_id": null,
    "created_at": "2025-10-30T09:00:00Z",
    "updated_at": "2025-10-30T09:00:00Z"
  },
  "message": "TaskList가 생성되었습니다."
}
```

### 4.4 TaskList 수정

```http
PUT /api/v1/task-lists/{id}
```

**요청 본문**:
```json
{
  "name": "주간 쇼핑 목록",
  "description": "이번 주 마트에서 살 것들"
}
```

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "주간 쇼핑 목록",
    "description": "이번 주 마트에서 살 것들",
    "user_id": null,
    "created_at": "2025-10-30T09:00:00Z",
    "updated_at": "2025-10-30T09:05:00Z"
  },
  "message": "TaskList가 수정되었습니다."
}
```

### 4.5 TaskList 삭제

```http
DELETE /api/v1/task-lists/{id}
```

**응답 예시** (204 No Content):
```
(응답 본문 없음)
```

### 4.6 TaskList 내 Task 목록 조회

```http
GET /api/v1/task-lists/{id}/tasks
```

**쿼리 파라미터**:
- `completed` (optional): 완료 여부
- `limit` (optional): 페이지당 항목 수
- `offset` (optional): 건너뛸 항목 수

**응답 예시** (200 OK):
```json
{
  "success": true,
  "data": {
    "task_list": {
      "id": 1,
      "name": "쇼핑 목록",
      "description": "마트에서 살 것들"
    },
    "tasks": {
      "items": [
        {
          "id": 1,
          "title": "우유 사기",
          "description": "저지방 우유 2L",
          "completed_datetime": null,
          "task_list_id": 1,
          "created_at": "2025-10-30T10:00:00Z",
          "updated_at": "2025-10-30T10:00:00Z"
        }
      ],
      "pagination": {
        "total": 3,
        "limit": 100,
        "offset": 0,
        "has_more": false
      }
    }
  }
}
```

---

## 5. 인증 API (예정)

**Base URL**: `/api/v1/auth`

### 5.1 회원가입

```http
POST /api/v1/auth/register
```

**요청 본문**:
```json
{
  "username": "john_doe",
  "email": "john@example.com",
  "password": "securePassword123!",
  "password_confirmation": "securePassword123!"
}
```

### 5.2 로그인

```http
POST /api/v1/auth/login
```

**요청 본문**:
```json
{
  "email": "john@example.com",
  "password": "securePassword123!"
}
```

**응답 예시**:
```json
{
  "success": true,
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 1,
      "username": "john_doe",
      "email": "john@example.com"
    }
  }
}
```

### 5.3 로그아웃

```http
POST /api/v1/auth/logout
```

### 5.4 게스트 데이터 마이그레이션

```http
POST /api/v1/auth/migrate-guest-data
```

**요청 본문**:
```json
{
  "session_id": "uuid-v4",
  "tasks": [ ... ],
  "task_lists": [ ... ]
}
```

---

## 6. 에러 코드 목록

| 코드 | HTTP 상태 | 설명 |
|------|-----------|------|
| `VALIDATION_ERROR` | 422 | 유효성 검증 실패 |
| `NOT_FOUND` | 404 | 리소스 없음 |
| `UNAUTHORIZED` | 401 | 인증 실패 |
| `FORBIDDEN` | 403 | 권한 없음 |
| `TASK_ALREADY_COMPLETED` | 409 | 이미 완료된 Task |
| `TASK_NOT_COMPLETED` | 409 | 미완료 Task |
| `INVALID_TASK_TITLE` | 422 | 잘못된 Task 제목 |
| `TASK_TITLE_TOO_LONG` | 422 | Task 제목 초과 |
| `INVALID_TASK_LIST_NAME` | 422 | 잘못된 TaskList 이름 |
| `TASK_LIST_NAME_TOO_LONG` | 422 | TaskList 이름 초과 |
| `SERVER_ERROR` | 500 | 서버 내부 오류 |

---

## 7. 다음 단계

### 7.1 구현 우선순위

1. ✅ Task API 설계 완료
2. ✅ TaskList API 설계 완료
3. 📋 Task API 구현
4. 📋 TaskList API 구현
5. 📋 인증 API 구현
6. 📋 게스트 데이터 마이그레이션 API 구현

### 7.2 추가 예정 API

- SubTask API (Phase 2)
- TaskGroup API (Phase 3)
- 공유 및 협업 API (Phase 4)
- 알림 API (Phase 5)
- 통계 API (Phase 5)

---

**문서 버전**: 1.0
**최초 작성**: 2025-10-30
**최근 업데이트**: 2025-10-30
