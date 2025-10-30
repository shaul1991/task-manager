# Specs 디렉토리

이 디렉토리는 Task Manager 프로젝트의 전체 명세 문서를 포함합니다.

## 📋 문서 구조

```
specs/
├── README.md                              # 이 파일 - Specs 디렉토리 안내
├── implementation_status.md               # 전체 구현 진행 상황 추적
├── project_specification.md               # 프로젝트 전체 요구사항 및 아키텍처 명세
├── domain_specifications/                 # 도메인별 상세 명세
│   ├── task_domain_spec.md               # Task 도메인 상세 명세 (✅ 완료)
│   ├── task_list_domain_spec.md          # TaskList 도메인 상세 명세 (🚧 진행중)
│   ├── user_domain_spec.md               # User 도메인 상세 명세 (📋 예정)
│   └── future_domains_spec.md            # 향후 도메인 (SubTask, TaskGroup 등)
├── api_specification.md                   # REST API 엔드포인트 명세
└── testing_plan.md                        # 테스팅 전략 및 계획
```

## 📖 문서별 설명

### 1. implementation_status.md
**목적**: 전체 프로젝트 구현 진행 상황 추적

**내용**:
- Feature별 완료율 및 상태
- 레이어별 구현 현황 (Domain, Application, Infrastructure, Presentation)
- 테스트 커버리지 현황
- 완료/진행중/예정 작업 목록

**언제 참조**:
- 프로젝트 전체 진행 상황을 파악할 때
- 다음 작업 우선순위를 결정할 때
- 문서 업데이트 후 구현 상태를 동기화할 때

### 2. project_specification.md
**목적**: 프로젝트 전체 요구사항 및 아키텍처 명세

**내용**:
- 프로젝트 개요 및 비전
- 핵심 비즈니스 요구사항
- DDD 아키텍처 설계 원칙
- Bounded Context 정의
- 계층 구조 로드맵
- 기술 스택 상세

**언제 참조**:
- 프로젝트 전체 구조를 이해할 때
- 새로운 Feature 개발 전 아키텍처 확인할 때
- 기술적 의사결정이 필요할 때

### 3. domain_specifications/ 디렉토리
**목적**: 각 도메인별 상세 명세

**내용** (도메인별):
- Entity 구조 및 비즈니스 규칙
- Value Objects 상세
- Use Cases 목록 및 플로우
- Repository Interface 정의
- Domain Events
- 구현 파일 위치
- 테스트 현황

**언제 참조**:
- 특정 도메인 개발/수정 시
- 도메인 비즈니스 로직 확인 시
- 테스트 작성 시

### 4. api_specification.md
**목적**: REST API 엔드포인트 명세

**내용**:
- API 설계 원칙
- 엔드포인트 목록 및 상세
- 요청/응답 형식
- 에러 처리 규격
- 인증/인가 방식

**언제 참조**:
- API 개발/수정 시
- 프론트엔드 통합 시
- API 문서 작성 시

### 5. testing_plan.md
**목적**: 테스팅 전략 및 계획

**내용**:
- 테스팅 전략 (Unit, Integration, Feature)
- 커버리지 목표
- 테스트 작성 규칙 및 패턴
- 도메인별 테스트 현황
- CI/CD 통합 계획

**언제 참조**:
- 테스트 코드 작성 시
- 테스트 전략 수립 시
- 커버리지 개선 시

## 🔄 문서 업데이트 규칙

### 언제 업데이트하는가?

1. **새로운 Feature 개발 시작 전**
   - 해당 Feature의 요구사항 및 명세를 먼저 작성
   - 관련 도메인 명세 업데이트

2. **구현 완료 후**
   - `implementation_status.md` 업데이트 (완료율 및 상태)
   - 해당 도메인 명세에 구현 파일 경로 추가
   - 테스트 현황 업데이트

3. **아키텍처 변경 시**
   - `project_specification.md` 업데이트
   - 영향받는 도메인 명세 업데이트

4. **API 추가/수정 시**
   - `api_specification.md` 업데이트

### 업데이트 체크리스트

- [ ] 변경된 내용이 모든 관련 문서에 반영되었는가?
- [ ] 완료율 및 상태 표시가 정확한가?
- [ ] 파일 경로 참조가 올바른가?
- [ ] 코드 예시가 최신 상태인가?
- [ ] 테스트 현황이 업데이트되었는가?

## 🎯 상태 표시 규칙

문서 전체에서 일관된 상태 표시를 사용합니다:

- ✅ **완료**: 구현 및 테스트 완료
- 🚧 **진행중**: 현재 개발 중
- 📋 **예정**: 향후 구현 예정
- ⚠️ **주의**: 변경 가능성 있음
- 🔴 **차단됨**: 선행 작업 필요
- ⏸️ **보류**: 의사결정 대기 중

## 📚 관련 문서

- **프로젝트 전체 개요**: `/CLAUDE.md`
- **백엔드 개발 가이드**: `/BACKEND.md`
- **프론트엔드 개발 가이드**: `/FRONTEND.md`
- **README**: `/README.md`

## 🤝 기여 가이드

1. 문서는 **한글로 작성**합니다 (코드/기술용어는 영문)
2. **Markdown 형식**을 준수합니다
3. **코드 예시**를 적극 활용합니다
4. **파일 경로**를 명시합니다
5. **상태 표시**를 명확히 합니다
6. **날짜 기록**을 남깁니다 (주요 변경 시)

---

**최초 작성**: 2025-10-30
**최근 업데이트**: 2025-10-30
