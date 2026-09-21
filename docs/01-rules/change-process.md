# 변경 작업 필수 규칙 (7단계 프로세스)

> **이 문서가 CLAUDE.md 절대 규칙 #16 의 본문이다.**
> 모든 변경 작업(브랜딩·기획·디자인·퍼블·개발·서버 운영 전 역할 공통)은 예외 없이 아래 7단계를 순서대로 거친다.
> 보조 절차인 `docs/01-rules/change-impact.md`(요구사항 변경 시 5단계 파급분석)는 이 문서의 §2 "영향 범위 분석" 단계 안에서 호출된다.

---

## 0. 절대 원칙

코드를 바로 수정하지 않는다.

모든 작업은 반드시 아래 순서를 따른다.

1. 요구사항 재정의
2. 영향 범위 분석
3. 변경 계획 작성
4. 구현
5. 검증
6. 문서 업데이트
7. 작업 히스토리 기록

이 순서를 생략하지 않는다.

---

## 1. 작업 시작 전 요구사항 재정의

작업 시작 전에 반드시 아래 형식으로 정리한다.

### 요구사항 요약
- 사용자가 원하는 기능:
- 현재 문제:
- 기대 동작:
- 수정하지 말아야 할 범위:

### 확인된 제약
- 기존 DB 호환:
- 기존 UI 영향:
- 기존 API 영향:
- 권한/보안 영향:
- 다국어 영향:
- 배포 영향:

불명확한 부분이 있으면 추측하지 말고 `가정`으로 명시한다.

---

## 2. 영향 범위 분석 필수

구현 전에 반드시 관련 파일을 탐색한다.

다음 항목을 빠짐없이 확인한다.

### 필수 확인 대상

#### Backend
- Route
- Controller
- Request
- Service
- Action
- Job
- Listener
- Event
- Model
- Repository
- Policy/Gate
- Middleware
- Enum/Constant
- DTO/ViewModel
- Migration
- Seeder
- Factory
- Validation
- Transaction 처리
- Lock/동시성 처리
- Error handling
- Logging

#### Frontend / View
- View
- Component
- JavaScript/TypeScript
- CSS/SCSS
- Admin UI
- User UI
- Form
- Modal
- Table/List
- Detail page
- Button/Action
- Empty state
- Error state
- Loading state

#### Data
- DB 컬럼
- FK
- Index
- Unique constraint
- Nullable 여부
- 기존 데이터 호환성
- 마이그레이션 필요 여부
- Rollback 가능 여부

#### i18n
- ko 언어 파일
- en 언어 파일
- 에러 메시지
- 성공 메시지
- 버튼 라벨
- 페이지 타이틀

#### Test
- 기존 테스트 영향
- 새 테스트 필요 여부
- 수동 테스트 시나리오
- 회귀 테스트 항목

#### Docs
- architecture 문서
- use-case 문서
- class/module 관계 문서
- 작업 히스토리

> 요구사항 변경(이미 결정된 사양을 뒤집는 경우)에 해당하면 이 단계에서 `docs/01-rules/change-impact.md` 5단계 절차를 추가 수행한다.

---

## 3. 구현 전 변경 계획 제출

구현 전에 반드시 아래 형식으로 계획을 작성한다.

```md
## 변경 계획

### 1. 수정할 파일
-

### 2. 연관 파일
-

### 3. 호출 흐름
- Route → Controller → Service → Model → DB

### 4. DB 영향
- 컬럼 추가/수정/삭제:
- FK 영향:
- Index 영향:
- 기존 데이터 영향:

### 5. 기존 기능 영향
- 영향 받는 기능:
- 깨질 수 있는 부분:
- 호환성 유지 방법:

### 6. 리스크
-

### 7. 테스트 계획
- 정상 케이스:
- 실패 케이스:
- 권한 케이스:
- 기존 기능 회귀 테스트:

### 8. 문서 업데이트 대상
-
```

계획 없이 코드를 수정하지 않는다.

---

## 4. 구현 규칙

### 4-1. 부분 수정 금지

사용자가 특정 파일만 언급했더라도, 그 파일과 연결된 의존 파일을 반드시 함께 확인한다.

예:

**Controller 를 수정하면 반드시 확인한다.**
- Route
- Request
- Service
- Model
- Policy
- View
- i18n
- Test

**Model 을 수정하면 반드시 확인한다.**
- Migration
- Relation
- Service
- Controller
- Factory
- Seeder
- Query
- Test

**DB 를 수정하면 반드시 확인한다.**
- Model
- Validation
- Service
- View form
- List/detail 출력
- Export/import
- Seeder
- 기존 데이터

**View 를 수정하면 반드시 확인한다.**
- Controller 전달 데이터
- Route name
- Permission
- Form action
- Validation error
- i18n key
- CSS/JS 영향

---

## 5. 변경 후 검증 규칙

구현 완료 후 반드시 아래 체크리스트를 작성한다.

```md
## 구현 후 검증

### 기능 검증
- [ ] 정상 동작 확인
- [ ] 실패 케이스 확인
- [ ] 권한 체크 확인
- [ ] 기존 기능 영향 확인

### 코드 정합성
- [ ] Route 연결 확인
- [ ] Controller 호출 확인
- [ ] Service 로직 확인
- [ ] Model relation 확인
- [ ] DB schema 정합성 확인
- [ ] i18n key 누락 확인

### 데이터 안정성
- [ ] 기존 데이터 호환 확인
- [ ] nullable/default 확인
- [ ] FK 깨짐 가능성 확인
- [ ] rollback 가능성 확인

### UI 검증
- [ ] 목록 화면 확인
- [ ] 상세 화면 확인
- [ ] 생성/수정 폼 확인
- [ ] 에러 메시지 확인
- [ ] 빈 데이터 상태 확인

### 테스트
- [ ] 기존 테스트 영향 확인
- [ ] 신규 테스트 필요 여부 확인
- [ ] 수동 테스트 시나리오 작성
```

---

## 6. 문서 업데이트 규칙

작업 완료 후 관련 문서를 반드시 업데이트한다.

### 업데이트 대상
- `/docs/architecture/module-map.md`
- `/docs/architecture/use-cases.md`
- `/docs/architecture/class-diagram.md`
- `/docs/architecture/dependency-map.md`
- `/docs/history/CHANGELOG.md`

단, 전체 문서를 매번 크게 수정하지 말고 변경된 기능과 관련된 부분만 업데이트한다.

> 본 프로젝트의 경우 architecture 문서가 미정이면 `docs/03-constants/architecture.md`, 히스토리는 `docs/04-history/patch-notes.md`·`docs/04-history/requirement-changes.md`·`docs/04-history/decisions.md` 를 대상으로 한다.

---

## 7. 작업 히스토리 기록 규칙

모든 작업 완료 후 아래 형식으로 기록한다.

```md
# 작업 히스토리

## 날짜
YYYY-MM-DD

## 작업명
작업 제목

## 작업 목적
왜 수정했는지 작성

## 변경 파일
-

## 연관 파일
-

## 주요 변경 내용
-

## 영향 범위
-

## 리스크
-

## 테스트 결과
-

## 후속 작업
-
```

---

## 8. AI 응답 규칙

AI 는 작업 중 반드시 현재 단계가 무엇인지 표시한다.

예:
- 현재 단계: 영향 범위 분석
- 현재 단계: 변경 계획 작성
- 현재 단계: 구현 완료 후 검증

작업 완료 응답에는 반드시 다음을 포함한다.

```md
## 완료 요약
-

## 변경 파일
-

## 영향 범위
-

## 검증 결과
-

## 문서 업데이트
-

## 남은 리스크
-
```

### 실행 기반 보고 (요약 금지) — 강제

완료 응답은 반드시 **실제 수행한 것만** 기록한다. 일반 진술·요약 진술 금지. 위 6블록은 다음 형식으로 채운다.

```md
## 적용한 규칙
- #N (rule-slug): 어떤 결정에 어떻게 적용했는지 1줄.
  (적용 안 한 규칙은 적지 않는다. "검토함" 같은 표현 금지.)

## 실제 확인한 파일
- path:line — 무엇을 봤나 / 왜 봤나 (구체 경로 필수)

## 실제 수행한 영향 분석
- Backend  : 확인한 파일 N개 — 구체 경로
- Frontend : …
- Data     : …
- i18n     : …
- Test     : …
- Docs     : …
- (해당 카테고리가 0개면 "해당 없음" 으로 명시. 누락 금지.)

## 실제 수행한 검증
- 명령어 → 결과 1줄 (예: `grep -rn "raw <select>" resources/views` → 0건)
- POST 체크 항목별 [U1]·[D2]·[R3] … 결과 명시

## 남은 리스크
- 구체 리스크 + 완화 방안
```

**금지:**
- ❌ "절대 규칙 모두 검토했습니다" 같은 일반 진술
- ❌ "체크리스트 통과" 만 적고 어느 항목·어느 파일인지 미명시
- ❌ 적용하지 않은 규칙을 "검토함" 으로 보고
- ❌ "영향 분석 완료" 만 적고 카테고리별 확인 파일 미명시
- ❌ 검증 명령어 없이 "확인했습니다" 만 적는 보고

---

## 9. 금지 사항

아래 행동은 금지한다.

- 요구사항 분석 없이 바로 코드 수정
- 사용자가 말한 파일만 수정하고 연관 파일 미확인
- DB 변경 후 Model/View/Validation 미확인
- Controller 변경 후 Route/View/Policy 미확인
- i18n key 추가 없이 하드코딩
- 기존 기능 영향 검토 없이 구현
- 테스트 계획 없이 완료 처리
- 문서 업데이트 없이 완료 처리
- 작업 히스토리 없이 완료 처리
- "완료했습니다" 만 말하고 변경 근거 미제시

---

핵심은 **UML 보다 이 규칙이 먼저**다.
UML 은 보조 문서고, 진짜 강제해야 할 건 **영향 범위 분석 체크리스트 + 구현 전 변경 계획 제출**이다.
