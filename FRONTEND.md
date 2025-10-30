# FRONTEND.md - 프론트엔드 개발 문서

Vite + Tailwind CSS 4.0 + Laravel Blade Components 기반 프론트엔드 개발 가이드

## 기술 스택

- **빌드 도구**: Vite
- **CSS 프레임워크**: Tailwind CSS 4.0
- **템플릿 엔진**: Laravel Blade
- **컴포넌트**: Laravel Blade Components
- **상태 관리 (게스트 모드)**: LocalStorage API
- **HTTP 클라이언트**: Axios (전역 설정)

## Tailwind CSS 4.0 통합

### 새로운 `@source` 디렉티브

Tailwind CSS 4.0에서는 `@source` 디렉티브를 사용하여 스캔할 파일 경로를 지정합니다.

**설정 파일 (`resources/css/app.css`):**

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

**특징:**
- Tailwind CSS 클래스를 사용하는 모든 파일 경로를 명시적으로 지정
- Laravel Pagination 뷰 파일도 포함하여 페이지네이션 스타일 지원
- Blade 템플릿과 JavaScript 파일에서 사용되는 모든 클래스를 자동 감지

### Vite 네이티브 플러그인

Vite 설정에서 `@tailwindcss/vite` 플러그인을 사용합니다.

**설정 파일 (`vite.config.js`):**

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

**이점:**
- 최적의 성능과 빠른 HMR (Hot Module Replacement)
- 별도의 PostCSS 설정 불필요
- Vite와 Tailwind CSS의 네이티브 통합

## Blade Components 작성 규칙

### 단일 책임 원칙

각 Blade Component는 하나의 명확한 책임을 가져야 합니다.

```php
// ✅ 명확한 단일 책임
namespace App\View\Components\Task;

use Illuminate\View\Component;
use Src\Application\Task\DTOs\TaskDTO;

class TaskItem extends Component
{
    public function __construct(
        public TaskDTO $task
    ) {}

    public function render()
    {
        return view('components.task.task-item');
    }
}
```

### Props 타입 힌팅

모든 Props는 명확한 타입 힌팅을 사용해야 합니다.

```php
// ✅ 명확한 타입 힌팅과 기본값
public function __construct(
    public TaskDTO $task,
    public bool $showActions = true,
    public ?string $variant = null,
    public string $size = 'medium'
) {}
```

**타입 힌팅 가이드:**
- 필수 Props: 기본값 없이 타입만 지정
- 선택적 Props: 기본값 제공
- Nullable Props: `?Type` 형식 사용

### Slot 활용

재사용성을 높이기 위해 Slot을 적극 활용합니다.

```blade
{{-- task-card.blade.php --}}
<div class="task-card rounded-lg border p-4">
    <div class="task-header">
        <h3 class="text-lg font-semibold">{{ $task->title }}</h3>
    </div>

    <div class="task-body mt-2">
        <p class="text-gray-600">{{ $task->description }}</p>
    </div>

    @isset($actions)
        <div class="task-actions mt-4 flex gap-2">
            {{ $actions }}
        </div>
    @endisset

    @isset($footer)
        <div class="task-footer mt-4 border-t pt-4">
            {{ $footer }}
        </div>
    @endisset
</div>
```

**사용 예시:**

```blade
<x-task.task-card :task="$task">
    <x-slot:actions>
        <button class="btn-primary">완료</button>
        <button class="btn-secondary">수정</button>
    </x-slot:actions>

    <x-slot:footer>
        <span class="text-sm text-gray-500">
            생성일: {{ $task->createdAt }}
        </span>
    </x-slot:footer>
</x-task.task-card>
```

### 컴포넌트 네이밍 규칙

**디렉토리 구조:**
```
resources/views/components/
├── layout/
│   ├── app.blade.php           # 앱 레이아웃
│   ├── guest.blade.php         # 게스트 레이아웃
│   ├── header.blade.php        # 헤더
│   └── sidebar.blade.php       # 사이드바
├── task/
│   ├── task-list.blade.php     # 할 일 목록
│   ├── task-item.blade.php     # 할 일 항목
│   ├── task-form.blade.php     # 할 일 폼
│   └── task-filter.blade.php   # 할 일 필터
├── task-list/
│   ├── task-list-list.blade.php    # TaskList 목록
│   ├── task-list-card.blade.php    # TaskList 카드
│   └── task-list-form.blade.php    # TaskList 폼
└── ui/
    ├── button.blade.php        # 버튼
    ├── input.blade.php         # 입력 필드
    ├── modal.blade.php         # 모달
    └── toast.blade.php         # 토스트 알림
```

**네이밍 컨벤션:**
- kebab-case 사용 (task-item, task-list-card)
- 도메인별로 디렉토리 분리 (task/, task-list/, ui/)
- 명확한 목적을 나타내는 이름 사용

### 컴포넌트 클래스 위치

**PHP 클래스 위치:**
```
app/View/Components/
├── Layout/
│   ├── App.php
│   ├── Guest.php
│   ├── Header.php
│   └── Sidebar.php
├── Task/
│   ├── TaskList.php
│   ├── TaskItem.php
│   ├── TaskForm.php
│   └── TaskFilter.php
├── TaskList/
│   ├── TaskListList.php
│   ├── TaskListCard.php
│   └── TaskListForm.php
└── Ui/
    ├── Button.php
    ├── Input.php
    ├── Modal.php
    └── Toast.php
```

## LocalStorage 관리 (게스트 모드)

### 데이터 구조

게스트 모드에서는 LocalStorage를 사용하여 클라이언트 측에 데이터를 저장합니다.

**LocalStorage 키 구조:**
```javascript
{
  "guest_tasks": [
    {
      "id": "uuid-v4",
      "title": "우유 사기",
      "description": "저지방 우유 2L",
      "completed_datetime": null,
      "task_list_id": "uuid-v4",
      "created_at": "2025-10-30T10:00:00Z",
      "updated_at": "2025-10-30T10:00:00Z"
    }
  ],
  "guest_task_lists": [
    {
      "id": "uuid-v4",
      "name": "쇼핑 목록",
      "description": "장보기",
      "created_at": "2025-10-30T09:00:00Z",
      "updated_at": "2025-10-30T09:00:00Z"
    }
  ],
  "guest_session_id": "uuid-v4"
}
```

### LocalStorage 유틸리티 함수

**권장 구조 (`resources/js/storage/guestStorage.js`):**

```javascript
// LocalStorage 키 상수
const STORAGE_KEYS = {
    TASKS: 'guest_tasks',
    TASK_LISTS: 'guest_task_lists',
    SESSION: 'guest_session_id'
};

// UUID 생성
function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

// Task 저장소
export const guestTaskStorage = {
    // 모든 Task 가져오기
    getAll() {
        const tasks = localStorage.getItem(STORAGE_KEYS.TASKS);
        return tasks ? JSON.parse(tasks) : [];
    },

    // Task 추가
    add(task) {
        const tasks = this.getAll();
        const newTask = {
            id: generateUUID(),
            ...task,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        tasks.push(newTask);
        localStorage.setItem(STORAGE_KEYS.TASKS, JSON.stringify(tasks));
        return newTask;
    },

    // Task 업데이트
    update(id, updates) {
        const tasks = this.getAll();
        const index = tasks.findIndex(t => t.id === id);
        if (index !== -1) {
            tasks[index] = {
                ...tasks[index],
                ...updates,
                updated_at: new Date().toISOString()
            };
            localStorage.setItem(STORAGE_KEYS.TASKS, JSON.stringify(tasks));
            return tasks[index];
        }
        return null;
    },

    // Task 삭제
    delete(id) {
        const tasks = this.getAll();
        const filtered = tasks.filter(t => t.id !== id);
        localStorage.setItem(STORAGE_KEYS.TASKS, JSON.stringify(filtered));
    },

    // Task 완료 토글
    toggleComplete(id) {
        const task = this.getAll().find(t => t.id === id);
        if (task) {
            const completed = task.completed_datetime
                ? null
                : new Date().toISOString();
            return this.update(id, { completed_datetime: completed });
        }
        return null;
    }
};

// TaskList 저장소
export const guestTaskListStorage = {
    // 모든 TaskList 가져오기
    getAll() {
        const taskLists = localStorage.getItem(STORAGE_KEYS.TASK_LISTS);
        return taskLists ? JSON.parse(taskLists) : [];
    },

    // TaskList 추가
    add(taskList) {
        const taskLists = this.getAll();
        const newTaskList = {
            id: generateUUID(),
            ...taskList,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };
        taskLists.push(newTaskList);
        localStorage.setItem(STORAGE_KEYS.TASK_LISTS, JSON.stringify(taskLists));
        return newTaskList;
    },

    // TaskList 업데이트
    update(id, updates) {
        const taskLists = this.getAll();
        const index = taskLists.findIndex(tl => tl.id === id);
        if (index !== -1) {
            taskLists[index] = {
                ...taskLists[index],
                ...updates,
                updated_at: new Date().toISOString()
            };
            localStorage.setItem(STORAGE_KEYS.TASK_LISTS, JSON.stringify(taskLists));
            return taskLists[index];
        }
        return null;
    },

    // TaskList 삭제
    delete(id) {
        const taskLists = this.getAll();
        const filtered = taskLists.filter(tl => tl.id !== id);
        localStorage.setItem(STORAGE_KEYS.TASK_LISTS, JSON.stringify(filtered));
    }
};

// 세션 관리
export const guestSession = {
    // 세션 ID 가져오기 (없으면 생성)
    getId() {
        let sessionId = localStorage.getItem(STORAGE_KEYS.SESSION);
        if (!sessionId) {
            sessionId = generateUUID();
            localStorage.setItem(STORAGE_KEYS.SESSION, sessionId);
        }
        return sessionId;
    },

    // 모든 게스트 데이터 가져오기 (회원 전환용)
    getAllData() {
        return {
            session_id: this.getId(),
            tasks: guestTaskStorage.getAll(),
            task_lists: guestTaskListStorage.getAll()
        };
    },

    // 모든 게스트 데이터 삭제
    clearAll() {
        localStorage.removeItem(STORAGE_KEYS.TASKS);
        localStorage.removeItem(STORAGE_KEYS.TASK_LISTS);
        localStorage.removeItem(STORAGE_KEYS.SESSION);
    }
};
```

### 게스트 → 회원 전환 플로우

**1. 게스트 데이터 수집:**
```javascript
import { guestSession } from './storage/guestStorage';

function prepareGuestDataForMigration() {
    return guestSession.getAllData();
}
```

**2. 서버로 데이터 전송 (회원 가입 후):**
```javascript
async function migrateGuestData(userId) {
    const guestData = prepareGuestDataForMigration();

    try {
        const response = await axios.post('/api/migrate-guest-data', {
            user_id: userId,
            guest_data: guestData
        });

        if (response.data.success) {
            // 마이그레이션 성공 시 LocalStorage 정리
            guestSession.clearAll();
            console.log('Guest data migrated successfully');
        }
    } catch (error) {
        console.error('Failed to migrate guest data:', error);
    }
}
```

## JavaScript 설정

### Axios 전역 설정

`resources/js/bootstrap.js`에서 Axios가 전역으로 설정되며, 모든 요청에 CSRF 토큰과 `X-Requested-With` 헤더가 자동으로 포함됩니다.

```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF 토큰 자동 포함
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}
```

### 모듈 구조

**권장 디렉토리 구조:**
```
resources/js/
├── app.js                  # 메인 진입점
├── bootstrap.js            # Axios 설정
├── storage/
│   └── guestStorage.js     # LocalStorage 유틸리티
├── components/
│   ├── task/
│   │   ├── taskList.js     # Task 목록 인터랙션
│   │   └── taskForm.js     # Task 폼 인터랙션
│   └── task-list/
│       ├── taskListList.js    # TaskList 목록 인터랙션
│       └── taskListForm.js    # TaskList 폼 인터랙션
└── utils/
    ├── validation.js       # 폼 검증 유틸리티
    └── toast.js            # 토스트 알림 유틸리티
```

## 프론트엔드 컴포넌트 구조

### 레이아웃 컴포넌트

**앱 레이아웃 (`resources/views/components/layout/app.blade.php`):**
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gray-100">
        <x-layout.header />

        <div class="flex">
            <x-layout.sidebar />

            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
```

**게스트 레이아웃 (`resources/views/components/layout/guest.blade.php`):**
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>

        <!-- 회원 가입 유도 배너 -->
        <div class="fixed bottom-4 right-4">
            <div class="rounded-lg bg-blue-600 p-4 text-white shadow-lg">
                <p class="mb-2 text-sm font-semibold">
                    회원 가입하고 데이터를 안전하게 보관하세요!
                </p>
                <a href="{{ route('register') }}" class="text-sm underline hover:text-blue-100">
                    회원 가입하기 →
                </a>
            </div>
        </div>
    </div>
</body>
</html>
```

### UI 컴포넌트

**버튼 컴포넌트 (`resources/views/components/ui/button.blade.php`):**
```blade
@props([
    'variant' => 'primary',
    'size' => 'medium',
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        'outline' => 'border border-gray-300 text-gray-700 hover:bg-gray-50',
    ];

    $sizes = [
        'small' => 'px-3 py-1.5 text-sm',
        'medium' => 'px-4 py-2 text-base',
        'large' => 'px-6 py-3 text-lg',
    ];

    $classes = implode(' ', [
        'rounded-lg font-medium transition-colors duration-200',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['medium'],
    ]);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
```

**사용 예시:**
```blade
<x-ui.button variant="primary" size="medium">
    저장
</x-ui.button>

<x-ui.button variant="danger" size="small" onclick="deleteTask()">
    삭제
</x-ui.button>
```

## UI/UX 가이드라인

### 색상 체계

Tailwind CSS의 기본 색상 팔레트를 사용하되, 프로젝트 특성에 맞게 커스터마이징합니다.

**주요 색상:**
- **Primary**: Blue (파란색) - 주요 액션, 링크
- **Secondary**: Gray (회색) - 보조 액션, 배경
- **Success**: Green (초록색) - 완료, 성공 메시지
- **Danger**: Red (빨간색) - 삭제, 경고 메시지
- **Warning**: Yellow (노란색) - 주의 메시지

### 반응형 디자인

모든 컴포넌트는 모바일 우선(Mobile First) 원칙으로 설계합니다.

**Breakpoints:**
- `sm`: 640px (모바일)
- `md`: 768px (태블릿)
- `lg`: 1024px (데스크톱)
- `xl`: 1280px (대형 데스크톱)

**예시:**
```blade
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
    {{-- 모바일: 1열, 태블릿: 2열, 데스크톱: 3열 --}}
</div>
```

### 접근성 (Accessibility)

- **시맨틱 HTML**: 적절한 HTML 태그 사용 (`<button>`, `<nav>`, `<main>`)
- **ARIA 속성**: 스크린 리더를 위한 `aria-label`, `aria-describedby` 추가
- **키보드 탐색**: 모든 인터랙티브 요소는 키보드로 접근 가능
- **색상 대비**: WCAG AA 기준 (4.5:1 이상) 준수

## 개발 명령어

### 프론트엔드 개발 서버

```bash
npm run dev                      # Vite 개발 서버 (HMR 활성화)
npm run build                    # 프로덕션 빌드
```

### 올인원 개발 서버

백엔드와 프론트엔드를 동시에 실행:

```bash
composer dev
```

이 명령어는 다음 서비스를 동시에 실행합니다:
- Laravel 개발 서버 (포트 8000)
- 큐 워커
- 로그 모니터링 (Laravel Pail)
- Vite 개발 서버 (HMR)

## 참조

이 문서는 프론트엔드 개발에 특화된 문서입니다. 백엔드 개발은 @BACKEND.md를 참조하세요.

프로젝트 전체 개요와 개발 환경 설정은 @CLAUDE.md를 참조하세요.
