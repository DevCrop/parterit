---
title: 코딩 컨벤션 (재사용 가능 상수)
categories: [dev, ops]
desc: 네이밍, 파일명 타임스탬프, 코드 스타일, git 워크플로 등 모든 프로젝트 공통 컨벤션
related: [constants.architecture, constants.standards, constants.components]
updatedAt: 2026-05-13
---

# 코딩 컨벤션 (재사용 가능 상수)

> **모든 프로젝트 공통**. 거의 변하지 않는다. 다른 프로젝트로 그대로 복사 가능.

## 네이밍
- 파일명: `kebab-case`
- 컴포넌트: `PascalCase`
- 변수/함수: `camelCase`
- 상수: `UPPER_SNAKE_CASE`
- CSS 클래스: `kebab-case` 또는 BEM

### 클래스명 — 프로젝트/브랜드 prefix 금지, 도메인 전문어로 작명

**클래스명에 프로젝트명·브랜드명·고객사명을 prefix로 붙이지 않는다.** 코드베이스가 다른 프로젝트로 재사용될 때 prefix만 남는 짐이 된다. 클래스명은 **해당 도메인 안에서의 책임**을 드러내는 전문어로 작명한다 — 어느 도메인에 속하는지는 **네임스페이스(폴더)** 가 담당.

| ❌ 금지 | ✅ 권장 | 위치 |
|---|---|---|
| `PichiHomeController` | `HomeController` | `App\Controllers\Site\Marketing\` |
| `PichiEvent` | `Event` | `App\Models\Site\` |
| `PichiNotice` | `Notice` | `App\Models\Site\` |
| `ImmsResearchController` | `ResearchController` | `App\Controllers\Site\Research\` |
| `EwhaFacultyMember` | `FacultyMember` | `App\Models\People\` |

**도메인 경계 표현은 폴더(네임스페이스)에서 하고, 클래스명은 짧고 도메인 전문어를 쓴다.**
- `App\Controllers\Site\Marketing\HomeController` ← 공개 사이트의 마케팅 영역 Home
- `App\Controllers\Admin\Site\HomeController` ← 같은 이름이라도 네임스페이스로 구분 가능

**예외 — 모호성이 생기는 경우만 도메인 전문어를 prefix로 허용**:
- 같은 네임스페이스 안에 두 종류의 "Event"가 공존해 충돌하면 `CalendarEvent`, `AuditEvent` 식으로 도메인 전문어를 붙일 수 있다. 단 이때도 prefix는 프로젝트명이 아니라 **도메인 전문어** 여야 한다.

**테이블명은 별도** — 기존 DB 호환을 위해 테이블명에 프로젝트 prefix가 남아 있을 수 있다(`pichi_events` 등). 모델 클래스에서는 `protected static $table = 'pichi_events';` 로 명시해 클래스명과 분리한다.

## 파일명 타임스탬프 규칙 (전역) ⏱️

**시간순으로 쌓이는 산출물**은 파일명 앞에 생성 시각을 붙인다 — 정렬만으로 순서가 잡히고, 디버깅·추적이 쉬워진다.

**형식**: `YYMMDD-HHMM-kebab-제목.확장자`
- 예: `260512-2338-add-orders-table.sql`, `260512-0930-backfill-prices.ts`, `260512-1400-deploy-outage-postmortem.md`
- 24시간제, 프로젝트 합의 타임존(미정이면 로컬). 같은 분에 여러 개면 뒤에 `-1`, `-2`
- 제목은 `kebab-case`, 무엇을 하는 파일인지 한눈에

**붙이는 경우** (순서·시점이 의미 있음):
- DB 마이그레이션 / SQL 스크립트
- 일회성 스크립트·데이터 작업(backfill, 마이그레이션 외 batch 등)
- 로그·스냅샷·덤프·디버그 산출물
- 단독 히스토리 문서(예: 길어진 포스트모템·리뷰 기록 — `docs/04-history/`에 추가할 때)
- `sources/`에 받는 고객 자료(받은 날짜 기준, 시간 모르면 `YYMMDD-` 까지만)

**붙이지 않는 경우** (이름으로 참조되는 안정적 단일 파일):
- 코드 모듈·컴포넌트(`button.tsx`, `user-service.ts`), 설정 파일, `index.{ext}`
- `docs/`의 규칙·변수 문서, `README.md`, `CLAUDE.md` 등
- → 이런 것의 변경 이력은 **git** 과 **append-only 단일 파일**(`docs/04-history/patch-notes.md` 등 — 항목 머리에 타임스탬프)로 남긴다. 파일을 새로 만들지 않는다

> **`workspace/` 안에서도 동일하게 적용** — "여기 들어가는 모든 파일을 타임스탬프로" 가 아니다. `workspace/src/`의 코드·컴포넌트는 **이름 그대로**(히스토리는 git이 관리). 타임스탬프 프리픽스는 마이그레이션(`workspace/.../migrations/260513-2332-…​.sql`)·시안 버전 스냅샷·로그·일회성 스크립트처럼 **쌓이는** 산출물에만.

## 코드 스타일
- 들여쓰기: 2 spaces
- 세미콜론: `[사용 / 미사용 선택]`
- 따옴표: 싱글 `'` 우선, JSX는 더블 `"`
- 한 줄 최대 길이: 100자

## 주석
- 기본은 **주석 없음**. 코드 자체가 설명되도록.
- 비자명한 **WHY**만 주석. WHAT은 금지.
- TODO 주석은 이슈로 옮기고 제거.

## 커밋 메시지
```
<type>: <한 줄 요약>

<상세 설명 - 선택>
```

타입: `feat`, `fix`, `chore`, `docs`, `refactor`, `style`, `test`

## git 워크플로 (자동화 포함)

> 정본 운영 규칙. 자동 실행은 **Claude Code 훅**(`.claude/settings.json`)이 담당하고, 운영콘솔(관리자)에도 같은 동작을 버튼으로 둔다.

- **작업 시작 시 `pull`** — 새 작업/세션을 시작하면 먼저 `git pull --rebase --autostash`(또는 fetch + 상태 확인). 훅으로 자동. 충돌이 나면 멈추고 사람에게.
- **커밋은 작은 단위·자주** — 한 논리적 변경 = 한 커밋. 메시지는 위 `<type>: 요약` 형식. (기존 `YYMMDD HHMM` 형태 스냅샷 커밋은 자동 훅이 만드는 시점 커밋 — 사람이 직접 만들 땐 `<type>:` 형식 우선.)
- **`push` 는 사람 확인 후** — 자동 푸시 금지. 훅·버튼은 push 직전 확인을 받거나 commit까지만 하고 push는 분리. 외부로 나가는 동작이므로 신중히.
- **브랜치** — `main` 은 항상 배포 가능 상태. 기능 작업은 feature 브랜치 권장(소규모 변경은 main 직접 허용하되 위 규칙 준수). 머지 전 셀프 리뷰.
- **`.gitignore`** — 비밀정보·`*.env`·키, `sources/`(고객 원본), 빌드 산출물·대형 자산, `vendor/`, `node_modules/`, `storage/logs/*` 등 커밋 금지. 대용량은 클라우드/NAS 별도 백업, 꼭 버전 필요하면 Git LFS. (회사 표준: `docs/03-constants/standards.md`)
- **DB 마이그레이션** — 스키마 변경은 raw `.sql` 직접 실행이 아니라 **마이그레이션 러너**로. 마이그레이션 파일은 위 "파일명 타임스탬프 규칙" 적용. 상세: `docs/03-constants/architecture.md` §4.

## 코드 배치 · 아키텍처

→ 정본: **`docs/03-constants/architecture.md`** — 루트에 새 폴더·파일 금지(전부 `app/` 아래), SOLID, 기존 클래스 재사용·관련은 애그리거트 루트로 통합, 상속보다 합성, 인터페이스 우선, 새 기능 모듈은 기능 토글 레지스트리에 등록.

## 폴더 구조 원칙
- 역할별 분리 (`assets/`, `components/`, `styles/`) — 코드 배치 상세는 `architecture.md` 의 `app/` 맵
- 깊이 4단계 이하 권장
- `index.{ext}`로 진입점 통일 (PHP는 클래스 네임스페이스)

## RESTful API 라우팅 규칙

> **모든 HTTP 엔드포인트(관리자·공개 사이트 공통)에 적용.** 새 라우트를 추가하거나 기존 라우트를 수정할 때 이 규칙을 먼저 확인한다.

### 1. 리소스 표준 7동사 (RFC 7231 기준)

| 동사 | 메서드 | URL 패턴 | 의미 |
|------|--------|----------|------|
| index | GET | `/resources` | 목록 |
| create | GET | `/resources/create` | 생성 폼 |
| store | POST | `/resources` | 생성 저장 |
| show | GET | `/resources/{id}` | 단건 조회 |
| edit | GET | `/resources/{id}/edit` | 수정 폼 |
| update | PUT/PATCH | `/resources/{id}` | 수정 저장 |
| destroy | DELETE | `/resources/{id}` | 삭제 |

HTML form은 `PUT`/`PATCH`/`DELETE`를 지원하지 않으므로 `_method` 필드로 터널링한다. (JS fetch는 실제 메서드 사용)

### 2. 비표준 액션 명명 규칙

표준 7동사 외 동작은 **동사 + 명사** 형태로 URL 세그먼트를 붙인다.

```
POST   /resources/bulk-destroy        # 대량 삭제
PATCH  /resources/reorder             # 순서 일괄 변경
POST   /resources/move                # 복수 항목 페이지 경계 이동 (body: ids[], mode, per_page)
POST   /resources/{id}/toggle         # 상태 토글
GET    /resources/{id}/preview        # 미리보기(읽기)
POST   /resources/{id}/duplicate      # 복제
```

**금지 패턴:**
```
❌ POST /resources/{id}/update    → PATCH/PUT /resources/{id}
❌ POST /resources/{id}/destroy   → DELETE /resources/{id}
❌ POST /resources/order          → PATCH /resources/reorder
❌ POST /resources/bulk-destroy   → DELETE /resources/bulk (body: ids[])
```

### 3. URL 설계 원칙

- **명사·복수형**: `/menus`, `/work-categories`, `/work-templates`
- **kebab-case**: 두 단어 이상은 하이픈 연결 (`work-sections`, `site-meta`)
- **계층 최대 2단계**: `/menus/{id}/info` ✅ `/menus/{id}/info/origin/sub` ❌
- **쿼리스트링**: 필터·정렬·페이지네이션. URL 세그먼트에 필터값 금지
- **버전 접두사**: 외부 API 분리 시 `/api/v1/` 사용. 관리자·공개 라우트는 버전 없음

### 4. HTTP 메서드 & 상태코드

| 상황 | 메서드 | 성공 코드 | 실패 코드 |
|------|--------|----------|----------|
| 생성 | POST | 201 Created | 422 Unprocessable |
| 수정 | PATCH | 200 OK | 404 / 422 |
| 삭제 | DELETE | 200 OK (JSON) | 404 |
| 대량 삭제 | DELETE | 200 OK | 422 |
| 순서 변경 | PATCH | 200 OK | 422 |
| 토글 | POST | 200 OK | 422 |

AJAX 응답은 항상 JSON. 성공: `{ result: "success", ... }`, 실패: `{ result: "error", msg: "..." }`.

### 5. PHP 라우트 선언 순서

같은 prefix 그룹 안에서 항상 이 순서를 유지한다:
```
GET    /              index
GET    /create        create
POST   /              store
PATCH  /reorder       reorder (있을 때)
GET    /{id}          show (있을 때)
GET    /{id}/edit     edit
PATCH  /{id}          update
DELETE /{id}          destroy
DELETE /bulk          bulkDestroy (있을 때)
POST   /move          move (있을 때, body: ids[])
POST   /{id}/toggle   toggle (있을 때)
```

### 6. TypeScript / JS 클라이언트 규칙

- URL은 **HTML data 속성**(`data-action-url`, `data-move-url`, `data-bulk-url`)으로 전달. TS 파일 안에 URL 하드코딩 금지.
- 삭제: `DELETE` 메서드 사용 (`_method: DELETE` 폼 터널링 또는 fetch DELETE).
- 수정: `PATCH` 메서드 사용 (부분 수정). 전체 교체는 `PUT`.
- 순서 변경: `PATCH /reorder`, body `{ items: [{id, sort_order}] }`.
- 대량 삭제: `DELETE /bulk`, body `{ ids: number[] }`.

---

## 뷰 파일 구조 규칙 — 공개 페이지 (⛔ 위반 시 파일 위치 교정 필수)

`resources/views/pages/` 아래에 **라우트 prefix와 동일한 폴더 구조**로 배치한다.

| 케이스 | 경로 패턴 | 예시 |
|--------|----------|------|
| 단순 페이지 (상세 없음) | `pages/{section}/{page}.php` | `/about/vision` → `pages/about/vision.php` |
| 목록 페이지 | `pages/{section}/{sub}/index.php` | `/about/people` → `pages/about/people/index.php` |
| 상세 페이지 | `pages/{section}/{sub}/show.php` | `/about/people/{slug}` → `pages/about/people/show.php` |
| 중첩 라우트 | 폴더 중첩 그대로 | `/facilities/reservation/equipment/bookings` → `pages/facilities/reservation/equipment/bookings/index.php` |

**규칙 요약:**
- 상세 페이지(`{slug}`, `{id}` 파라미터)가 있는 리소스 → 폴더 안에 `index.php` + `show.php`
- 상세 페이지가 없는 단순 페이지 → 폴더 바로 아래 `{page-name}.php`
- 섹션 최상위 랜딩은 항상 `폴더/index.php`

**컨트롤러 render() 경로**: 폴더 구분자 `/` → `.` 으로 변환.  
예: `pages/about/people/index.php` → `render('pages.about.people.index', ...)`

---

## 금지 사항
- 비밀 정보 하드코딩
- 미사용 코드 방치
- 의미 없는 변수명 (`a`, `temp`, `data1`)
- `// 나중에 정리` 류 주석
- TS 파일 안에 `/console/...` URL 하드코딩 (→ HTML data 속성으로)
