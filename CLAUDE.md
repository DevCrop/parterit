# [REPLACE: 프로젝트명] ([REPLACE: 고객사명])

> [REPLACE: 고객사 영문명]
> **Claude Code 진입점 = 색인.** 명령을 받으면 ① Level 1 항상 적용 → ② Level 2 작업 유형 분류 → ③ Level 3 해당 번들만 로드 → 실행.
> 이 파일은 **짧게** 유지 — 포인터만, 본문은 `docs/01-rules/` · `.claude/agents/`. (길어지면 규칙이 묻힌다.)

---

## 🚨🚨🚨 Level 1 — Global Core (항상 적용, 모든 명령)

### 절대 규칙 #16 — 변경 작업 7단계 프로세스 [최상위]

**코드를 바로 수정하지 않는다. 모든 변경 작업(전 역할 공통)은 예외 없이 아래 7단계를 순서대로 거친다.** 본 규칙은 다른 모든 규칙보다 상위이며, 디자인 4단계 파이프라인(#15)·번들별 규칙은 이 7단계의 "구현" 안에서 작동한다.

```
① 요구사항 재정의 → ② 영향 범위 분석 → ③ 변경 계획 작성
→ ④ 구현 → ⑤ 검증 → ⑥ 문서 업데이트 → ⑦ 작업 히스토리 기록
```

**작업 시작 전 필수 선언 (2줄):**

```
[변경 프로세스 단계] <①~⑦ 중 현재 단계> — 다음으로 넘어가는 조건: <짧게>
[번들 선택]          <UI · DEV · RISK · SERVER 중 선택, 복수 가능> — 근거: <키워드 또는 파일 경로>
```

선언 누락이거나 근거가 비면 **즉시 작업 중단**. 추측 금지.

**완료 보고는 "실행 기반 보고" 양식 (요약 금지)** — 적용한 규칙 / 실제 확인한 파일 / 실제 수행한 영향 분석 / 실제 수행한 검증 / 남은 리스크. 일반 진술("규칙 모두 검토", "체크리스트 통과") 금지. 양식 정본: `docs/01-rules/change-process.md §8`.

상세 절차·양식·금지 9항: **`docs/01-rules/change-process.md`** (외부 도구는 `AGENTS.md` 동일 사본).
요구사항 변경 시 5단계 파급분석: `docs/01-rules/change-impact.md` (이 7단계의 ② 안에서 호출).

### 절대 규칙 #17 — 디자인시스템 유틸 강제 [최상위, 전 역할 공통]

**페이지·섹션·블록 클래스에서 폰트 위계·spacing 을 SCSS 로 직접 지정 금지.** 디자인 시스템 정본의 유틸 클래스를 HTML 마크업에 적용해 표현한다. **동일 의미 컴포넌트는 동일 구현만** 사용, 변형 신규 금지.

**3대 조항 요약:**
- **A. 타이포 위계 = HTML 유틸 클래스만** — `no-display-* / no-heading-* / no-body-*` 만으로 위계 부여. 페이지·섹션 클래스의 `font-size / font-weight / font-family / line-height / letter-spacing` SCSS 직접 지정 금지. 컴포넌트 내부 BEM 자식만 토큰 참조(`var(--*-fs)`) 예외 허용.
- **B. Spacing = 유틸 또는 토큰만** — 섹션 패딩 `.no-section-*`, 일반 `.no-pd-* / .no-mg-*` (HTML) 또는 `var(--section-*)` / `var(--spacing-*)` (컴포넌트 내부). px·rem 하드코딩과 임의 `fluid(...)` 직접 지정 금지.
- **C. 동일 의미 컴포넌트 = 동일 구현** — CTA·카드·버튼은 같은 컴포넌트로. 변형은 modifier(`--ghost`, `--on-dark`, `--sm`) 로만. 새 BEM 블록 신규 작성 금지.

**작업 후 체크 (반드시 0건):**
```bash
# [1] 페이지·섹션 SCSS 가 폰트 위계를 직접 지정
grep -rnE '\b(font-size|font-weight|font-family|line-height|letter-spacing)\s*:' resources/scss/[REPLACE:컨텍스트]/pages --include="*.scss" | grep -v "abstract/" | grep -v "/components/"
# [2] 페이지 SCSS 의 __title 블록에 폰트 위계 잔재
grep -rnE '__title\s*\{[^}]*font-(size|weight|family)' resources/scss/[REPLACE:컨텍스트]/pages/ --include="*.scss"
```

상세·매핑표·시나리오: **`docs/01-rules/details/rule-17-design-system-utility.md`**.

### 절대 규칙 #18 — Measurement Before Claim (rem→px 실제값 검증) [최상위, 전 역할 공통]

**rem 값을 보고할 때 반드시 px 환산을 동봉. 루트 `html { font-size }` 를 사전에 코드로 확인. 사용자의 시각 지적과 토큰 값이 충돌하면 토큰을 정당화하지 말고 실제 렌더 px 를 재측정한다.**

본 프로젝트 확정: **`[REPLACE: font-size 경로 확인 후 기입]` → 1rem = [REPLACE: Npx]**

**3대 조항 요약:**
- **A. rem 보고 시 px 환산 동봉** — 모든 rem 값은 "X rem (= Y px)" 형식.
- **B. 루트 font-size 사전 확인** — 환산 전 `html { font-size }` 코드 확인 의무. 16px 가정 금지.
- **C. 시각 지적 vs 토큰 값 충돌 시 사용자가 옳다** — 즉시 (a) 실제 렌더 px 재계산 또는 (b) DevTools 측정값 요청.

**필수 보고 양식 (rem·px 관련 작업 시 답변 첫머리):**
```
[측정 검증]
- 컨텍스트: [REPLACE: 컨텍스트명]
- 루트 font-size: NN% (확인 경로: resources/scss/.../base/_init.scss:NN)
- 환산 단가: 1rem = NN px
- 보고 값: <셀렉터/속성>: <rem> (= <px>)
```

상세·시나리오·POST 체크 정본: **`docs/01-rules/details/rule-18-measurement-before-claim.md`**.

### 절대 규칙 #19 — 뷰 렌더링 위임 [최상위, 전 역할 공통]

**모든 HTML 덩어리는 `resources/views/components/` 또는 `resources/views/sections/` 에 두고 헬퍼 함수 호출로만 페이지에 연결한다. 페이지 파일에 새 BEM 블록 마크업을 인라인으로 작성하지 않는다.**

**3대 조항 요약:**
- **A. 페이지 = 조립소만** — 데이터 준비 + 레이아웃 상속 + 헬퍼 함수 호출 + 최소 컨테이너만. 새 BEM 블록 인라인 작성 금지.
- **B. 신규 = 무조건 추출 / 수정 = Boy Scout rule** — 새 HTML 덩어리는 처음부터 `components/` 또는 `sections/` 에 만든 뒤 호출.
- **C. 분기 = 크기·역할** — UI 프리미티브 = `components/` → `render_component()`. 페이지 한 덩어리 = `sections/` → `render_section()`.

**작업 후 체크 (반드시 0건):**
```bash
# [1] 같은 의미 BEM 블록이 페이지에 분산 인라인 정의
grep -rnE 'class="[a-z][a-z0-9-]*__(empty|error|loading|placeholder)\b' resources/views/pages --include="*.php"
# [2] 페이지에서 헬퍼 우회 require/include 사용
grep -rnE '\b(require|include)(_once)?\s*\(\s*[^)]*\.php' resources/views/pages --include="*.php" | grep -v "_presets" | grep -v "_helpers"
```

상세·시나리오·POST 체크 정본: **`docs/01-rules/details/rule-19-view-rendering-delegation.md`**.

---

## Level 2 — Workflow (작업 받으면 가장 먼저, 순서대로)

1. **작업 유형 분류** — `docs/01-rules/task-router.md` 알고리즘으로 UI / DEV / RISK / SERVER 중 선택 (복수 가능).
2. **해당 번들만 로드** — `docs/01-rules/by-task/{ui,dev,risk,server}.md`. 관련 없는 번들·규칙 본문은 읽지 않는다.
3. **번들 PRE 체크 실행** → 구현 → 번들 POST 체크 실행 (0건 위반 또는 의도 명시).
4. **실행 기반 보고** (위 Level 1).

역할 분류·병렬 vs 직렬·필수 체크리스트는 그대로 유지:
- `docs/01-rules/workflow.md` — 명령 접수·우선순위·의사결정 권한
- `docs/01-rules/parallel-work.md` — 여러 역할·트랙 병렬 vs 직렬
- `docs/01-rules/must-check.md` — A~H 카테고리 PRE/POST

명확도 판정 (작음·구체적·모호) 과 `/task` `/checklist` `/patch-note` `/change-impact` 명령은 `workflow.md` 정본.

---

## Level 3 — 작업 유형별 규칙 번들 (조건부 로드)

| 유형 | 대표 트리거 | 번들 | 포함 규칙 |
|---|---|---|---|
| **UI / 디자인 / 퍼블** | `resources/views/**`, `resources/scss/**` · 마크업·폼·색·간격·타이포 | `docs/01-rules/by-task/ui.md` | #1 #3 #5 #6 #7 #10 #11 #12 #13 #15 #17 #19 (+ #2 교차) |
| **개발 / 백엔드 / DB** | `app/**`, `routes/**`, `database/**`, Controller·Service·Model | `docs/01-rules/by-task/dev.md` | #2 #4 #8 #9 |
| **위험 액션** | destroy · delete · bulk-destroy · logout · sessions.revoke | `docs/01-rules/by-task/risk.md` | #8 #14 |
| **서버 / 배포 / 인프라** | deploy · `.env` · server-spec · backup · SSL · domain | `docs/01-rules/by-task/server.md` | (절대 규칙 없음 — `server-spec.md` · `conventions.md §git` 인용) |

**관련 없는 번들은 읽지 마라.** 두 번들 이상 매칭되면 모두 적용 (과대 적용 우선). 모호하면 `task-router.md` 가 명시한 키워드 매핑을 따른다.

---

## Level 4 — 개별 규칙 본문 상세

번들이 가리킬 때만 `docs/01-rules/details/rule-NN-slug.md` 를 펼친다.

| # | 한 줄 요지 | 1차 번들 | 상세 |
|---|---|---|---|
| 1 | 컴포넌트 카탈로그 먼저 · raw `<select>` 금지 | UI | `details/rule-01-component-catalog.md` |
| 2 | 서드파티 라이브러리는 래퍼 통해서만 | DEV (UI 교차) | `details/rule-02-third-party-wrapper.md` |
| 3 | UI 색상 60:30:10 | UI | `details/rule-03-color-60-30-10.md` |
| 4 | RESTful HTTP 메서드 | DEV | `details/rule-04-restful.md` |
| 5 | 인라인 `style=` 금지 | UI | `details/rule-05-inline-style.md` |
| 6 | 폼 컴포넌트 완전 매핑 | UI | `details/rule-06-form-component-mapping.md` |
| 7 | 헤더 알림 pill 가독성 | UI | `details/rule-07-header-pill.md` |
| 8 | AJAX `_method` 스푸핑 | DEV (RISK 교차) | `details/rule-08-method-spoof.md` |
| 9 | 기능 제거 = 토글 OFF | DEV | `details/rule-09-feature-removal-toggle.md` |
| 10 | SCSS = 뷰 클래스 1:1 + 라벨 간격 | UI | `details/rule-10-scss-class-1to1.md` |
| 11 | 타이포·컨테이너 유틸리티만 | UI | `details/rule-11-typography-utilities.md` |
| 12 | overflow 요소 `data-lenis-prevent` | UI | `details/rule-12-lenis-prevent.md` |
| 13 | 역할 다른 텍스트 별도 태그 | UI | `details/rule-13-text-role-tag-split.md` |
| 14 | Modal.confirm + AJAX 삭제 5요소 | RISK (UI 교차) | `details/rule-14-confirm-ajax-delete.md` |
| 15 | 디자인 4단계 파이프라인 | UI | `details/rule-15-design-pipeline.md` |
| 16 | 변경 작업 7단계 프로세스 | **Level 1 (always)** | `docs/01-rules/change-process.md` |
| 17 | 디자인시스템 유틸 강제 | **Level 1 (always)** | `docs/01-rules/details/rule-17-design-system-utility.md` |
| 18 | Measurement Before Claim (rem→px 환산) | **Level 1 (always)** | `docs/01-rules/details/rule-18-measurement-before-claim.md` |
| 19 | 뷰 렌더링 위임 — 페이지는 호출만 | **Level 1 (always)** | `docs/01-rules/details/rule-19-view-rendering-delegation.md` |

---

## 1. 프로젝트 정체성

- 프로젝트명: `[REPLACE: 프로젝트명]`
- 고객사: `[REPLACE: 고객사명 (영문)]`
- 분류: `[REPLACE: 기관 유형]`
- 산출물: `[REPLACE: 예) 반응형 웹사이트 + 관리자 CMS]`
- 한 줄: **[REPLACE: 이 프로젝트가 무엇을 달성하는가]** (상세: `docs/00-overview/goals.md`)
- 기술 스택: `[REPLACE: 예) PHP · Apache · MySQL · Vite + SCSS]`
- 배포 환경: `docs/00-overview/server-spec.md`
- 브랜드 토큰 코드 정본: `[REPLACE: 경로]` (요약은 `docs/02-variables/brand-guide.md`)
- 발주: `[REPLACE: 담당자·직책]`

## 2. 역할 (상세: `.claude/agents/<role>.md` · 규칙: `docs/01-rules/roles.md`)

| 역할 | 책임 | 에이전트 |
|---|---|---|
| 브랜딩 | 톤·컬러·로고·타이포 일관성 | `branding.md` |
| 기획 | 요구사항·레퍼런스·IA | `planner.md` |
| 관리자 디자이너 | 시안·레이아웃·비주얼 | `designer-admin.md` |
| 홈페이지 디자이너 | 시안·레이아웃·비주얼 | `designer-brand.md` |
| 퍼블리셔 | 마크업·SCSS·반응형 | `publisher.md` |
| 개발자 | 기능·상태·API·DB | `developer.md` |
| 서버 운영 | 배포·인프라·모니터링 | `server-ops.md` |

## 3. 어디에 뭘 두나 (색인)

| 무엇 | 어디 |
|---|---|
| **처음이면 / 전체 작동 방식** | `docs/00-overview/onboarding.md` |
| 고객 원본 자료 (수정·삭제 금지) | `sources/` |
| 우리가 만든 결과물·코드·시안 | `workspace/` |
| 프로젝트 목표·성공기준·비목표 | `docs/00-overview/goals.md` |
| 배포환경·인프라 | `docs/00-overview/server-spec.md` |
| 고객·프로젝트 변수 / 브랜드 토큰 | `docs/02-variables/{client-info,brand-guide}.md` |
| 명령 접수·작업 순서·우선순위·의사결정 권한 | `docs/01-rules/workflow.md` |
| **변경 작업 7단계 프로세스 (#16 본문)** | `docs/01-rules/change-process.md` |
| **작업 유형 판별 알고리즘 (Level 2)** | `docs/01-rules/task-router.md` |
| **작업 유형별 규칙 번들 (Level 3)** | `docs/01-rules/by-task/{ui,dev,risk,server}.md` |
| **개별 규칙 본문 (Level 4)** | `docs/01-rules/details/rule-NN-slug.md` |
| **HTML 구조 규칙 (섹션 = section>container>inner>title+--cnt, 예외만 다름)** | `docs/01-rules/html-structure.md` |
| 디자인 4단계 파이프라인(#15) 상세 | `docs/01-rules/design-pipeline.md` |
| 여러 역할/작업 병렬 vs 직렬 | `docs/01-rules/parallel-work.md` |
| **컴포넌트 카탈로그 (UI 만들기 전 필독)** | `docs/03-constants/components.md` |
| **코드 배치·SOLID·기능추가 절차** | `docs/03-constants/architecture.md` |
| git 워크플로·코딩 컨벤션 | `docs/03-constants/conventions.md` |
| 필수 체크리스트 (A~H, PRE/POST) | `docs/01-rules/must-check.md` |
| 요구사항 변경 → 파급효과 5단계 | `docs/01-rules/change-impact.md` |
| 보안·접근성·성능·버전관리 표준 | `docs/03-constants/standards.md` |
| 작업 이력 / 요구사항 변경 이력 / 결정 로그 | `docs/04-history/{patch-notes,requirement-changes,decisions}.md` |
| 새 프로젝트 재사용 시 / 템플릿 확장 시 | `docs/02-variables/customizations.md` |
| Claude vs Codex 도구 선택 | `docs/00-overview/claude-vs-codex.md` |

## 4. 변수 vs 상수 (재사용 핵심)

- **변수** (프로젝트마다 다름) = `docs/02-variables/` · `docs/00-overview/` — 재사용 시 **여기만** 수정
- **상수·표준** (재사용, 거의 안 바뀜) = `docs/01-rules/` · `docs/03-constants/` · `.claude/agents/` · `.claude/commands/` — 그대로 복사

## 5. 일반 절대 원칙 (모든 번들에 공통)

- `sources/` 원본 수정·삭제 금지 · `docs/04-history/*`는 **추가만**(기존 항목 수정 금지) · 비밀정보 하드코딩 금지
- 기능 동작 / 디자인·브랜딩 / 의존성 추가·제거 / 배포 = **사용자 확인 필요**
- 요구사항 변경 처리 · `docs/04-history/*` 쓰기 · 레이어 의존 체인 = **병렬 금지(직렬)**
- 자기 역할 밖 임의 수정 금지 · 모호하면 추측 말고 질문 · 무관한 문서·번들 안 읽기
