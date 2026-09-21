---
title: 4단계 파이프라인 (브랜딩 → 디자인 → 컴포넌트 → 실제 적용)
categories: [workflow, ui, branding, design, components]
desc: 모든 작업이 통과해야 하는 중앙 집합소. 상위 단계 근거 없이 하위 단계를 손대지 않도록 강제하고, 컴포넌트 재사용·환류를 의무화한다.
related: [rules.workflow, rules.must-check, rules.change-impact, constants.components, variables.brand-guide]
updatedAt: 2026-05-21
---

# 4단계 파이프라인 — 브랜딩 → 디자인 → 컴포넌트 → 실제 적용

> CLAUDE.md **절대 규칙 #15** 의 상세본. 모든 명령은 이 파이프라인을 거친다. 다른 절대 규칙(#1~#14)은 이 파이프라인 안에서 작동한다.

---

## §1. 4단계 개요

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ 1. 브랜딩    │ →  │ 2. 디자인    │ →  │ 3. 컴포넌트  │ →  │ 4. 실제 적용 │
│              │    │              │    │              │    │              │
│ 톤·컬러·     │    │ 시안·레이    │    │ 카탈로그·    │    │ 페이지·뷰·   │
│ 타이포·토큰  │    │ 아웃·플로우  │    │ 재사용 자산  │    │ 컨트롤러·    │
│              │    │              │    │              │    │ 라우트·기능  │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
   정본 = SCSS         정본 = 시안          정본 = 카탈로그      정본 = 뷰·코드
   abstract 토큰       PDF·Figma            + 쇼케이스           (위 3개 인용만)
```

| 단계 | 책임 역할 (`.claude/agents/`) | 정본 위치 | 산출물 |
|---|---|---|---|
| 1. 브랜딩 | `branding.md` | `docs/02-variables/brand-guide.md`, `resources/scss/home/abstract/_colors.scss`, `_typography.scss` | 토큰 정의, 톤 가이드 |
| 2. 디자인 | `designer-brand.md`, `designer-admin.md`, `planner.md` | Figma 시안, `workspace/` 시안 PDF·이미지 | 시안, 레이아웃, 인터랙션 명세 |
| 3. 컴포넌트 | `publisher.md`, `developer.md` | `docs/03-constants/components.md` (카탈로그) + `/console/components` (쇼케이스) + `resources/views/{admin/components,admin/form,components}/` + `resources/scss/.../components/_*.scss` | 재사용 컴포넌트 + 카탈로그 등록 |
| 4. 적용 | `publisher.md`, `developer.md` | `resources/views/pages/`, `resources/views/admin/`, `app/`, `routes/` | 실제 페이지·기능 코드 |

**철칙:**
- 위에서 아래로 흐른다. 토큰 → 시안 → 컴포넌트 → 페이지.
- 아래에서 위는 **근거 인용**만 가능. 페이지가 토큰을 새로 정의하거나, 컴포넌트가 시안 없이 디자인을 결정하면 안 된다.
- 어떤 단계라도 정본을 우회해 페이지에 직접 값을 박는 순간 일관성이 무너진다.

---

## §2. 작업 시작 전 4줄 선언 (모든 명령에 필수)

```
[파이프라인 선언]
- 닿는 단계: [□ 브랜딩  □ 디자인  □ 컴포넌트  □ 적용]
- 상위 근거: <파일 경로 / 시안 페이지 / 카탈로그 항목>
- 재사용:    <카탈로그에서 무엇을 재사용 / 없으면 신규 컴포넌트 X 등록 계획>
- 환류:      <신규 컴포넌트 시 카탈로그·쇼케이스·SCSS·뷰 4곳 동시 갱신 약속>
- 참고 페이지: <새 페이지·새 BEM 블록이면 기존 어느 페이지/SCSS 를 표준으로 Read 했는지 파일경로 명시>
```

**왜 필수인가:**
- 선언 없이 시작하면 단계 인식 자체가 없어, 페이지에 색을 직접 박거나 비슷한 컴포넌트를 또 만드는 사고가 누적된다.
- "상위 근거" 또는 "참고 페이지"가 비어 있으면 → 추측으로 작업 중인 것. **즉시 중단** 후 사용자에게 보고.
- "참고 페이지" 가 없으면 spacing 토큰을 우회한 자체 SCSS 가 매번 작성된다. 기존 잘 만들어진 페이지를 보고 그 패턴을 그대로 사용해야 한다.

**예시 — 좋은 선언:**
```
[파이프라인 선언]
- 닿는 단계: [□ 브랜딩  ■ 디자인  ■ 컴포넌트  ■ 적용]
- 상위 근거: workspace/시안-B-publications.pdf p.3 (Broad 톤 카드 리스트)
            + brand-guide.md §컬러 (primary 사용 위치)
- 재사용:    `card-publication` 컴포넌트 신규 (카탈로그에 유사 항목 없음 확인)
- 환류:      docs/03-constants/components.md 행 추가 + /console/components 블록 추가
            + resources/scss/home/components/_card-publication.scss + index.scss @forward
- 참고 페이지: resources/views/admin/pages/siteinfo/edit.php (admin-form 표준 구조)
              + resources/scss/admin/pages/_form.scss (spacing 토큰 사용 예)
```

**예시 — 나쁜 선언 (즉시 중단 사유):**
```
- 닿는 단계: [■ 적용]                    ← 컴포넌트·디자인 단계 없이 직접 페이지에?
- 상위 근거: (비어 있음)                  ← 무슨 근거로?
- 재사용:    raw <input> 직접 작성        ← #1 위반
- 환류:      (없음)                       ← 자산화 실패
- 참고 페이지: (없음)                     ← 기존 표준을 안 보고 새로 짬 → spacing 위반 반복
```

---

## §3. 단계별 상세

### 3.1 브랜딩 단계

**정본:**
- 컬러 토큰: `resources/scss/home/abstract/_colors.scss`
- 타이포 토큰: `resources/scss/home/abstract/_typography.scss`
- 요약: `docs/02-variables/brand-guide.md`
- 역할: `.claude/agents/branding.md`

**의무:**
- 새 컬러/폰트/간격이 필요하면 **여기에 먼저** 토큰으로 등록. 페이지·컴포넌트에서 새 값 도입 금지.
- 토큰을 바꿀 때는 영향 분석 필수 → `docs/01-rules/change-impact.md` 5단계 실행.
- 외부 코드는 `var(--*)` 로만 참조. `font-size: 1.8rem` / `color: #003E76` 같은 하드코딩 금지.

**금지 패턴 (절대 규칙 #11과 연계):**
```scss
// ❌ 페이지·컴포넌트 SCSS에서 직접
.publication-card { color: #003E76; font-size: 1.6rem; }

// ✅
.publication-card { color: var(--color-primary); font-size: var(--body-md-fs); }
```

### 3.2 디자인 단계

**정본:**
- Figma 시안 (URL 공유 시 본 문서 §6 또는 patch-notes 에 기록)
- `workspace/` 의 시안 PDF·이미지 (예: `workspace/시안-A.pdf`, `시안-B.pdf`)
- 역할: `.claude/agents/{designer-brand,designer-admin}.md`, `.claude/agents/planner.md`

**의무:**
- 시안은 **브랜딩 토큰을 인용**해야 한다. 시안에 새 색·새 간격이 필요하면 → **1단계 갱신 먼저**.
- 시안에 없는 결정(예: 모바일 인터랙션, 빈 상태 UI)은 디자이너와 합의 후 시안에 추가. 그 전에 컴포넌트·페이지 코드 작성 금지.
- 시안 변경 시 영향 받는 컴포넌트를 식별해 3단계로 전파한다.

### 3.3 컴포넌트 단계

**정본 (3곳 동기화 필수):**
1. **카탈로그**: `docs/03-constants/components.md` — 무엇이 존재하는지의 진실
2. **쇼케이스**: `/console/components` 페이지 — 실제 렌더 결과 확인용
3. **코드**:
   - `resources/views/{admin/components,admin/form,components}/` (PHP 뷰)
   - `resources/scss/.../components/_<name>.scss` + `components/index.scss @forward "<name>"`

**재사용 우선 절차 (UI 작업 첫 5분):**
1. `docs/03-constants/components.md` 를 Read 또는 grep.
2. 카탈로그에 비슷한 게 있으면 → **그대로 사용** (`component('<type>', [...])` 또는 `include_view('admin.components.<name>', ...)`).
3. 카탈로그에 없는데 비슷한 게 코드에 있을 수 있음 → `resources/views/{admin/components,admin/form,components}/` 더블체크.
4. 정말 없을 때만 신규 작성. 그리고 **반드시 한 세트로** (아래 환류).

**신규 컴포넌트 작성 시 환류 (4곳 동시 갱신):**
- [ ] PHP 뷰 파일 작성: `resources/views/{admin/components|admin/form|components}/<name>.php`
- [ ] SCSS 파일 작성: `resources/scss/.../components/_<name>.scss`
- [ ] `resources/scss/.../components/index.scss` 에 `@forward "<name>";` 추가
- [ ] `docs/03-constants/components.md` 에 카탈로그 한 줄 추가 (이름·용도·헬퍼 시그니처)
- [ ] `/console/components` 쇼케이스에 블록 추가 (실제 렌더로 확인 가능하도록)

4곳 중 하나라도 빠지면 다음 작업에서 같은 컴포넌트를 또 만들게 된다. **재사용 자산화 실패**.

**기존 컴포넌트 확장 (modifier·prop 추가):**
- 성격이 같은데 형태만 다른 변형이면 → 새 컴포넌트 만들지 말고 modifier 추가 (`admin-btn--danger`, `admin-form__label--sm` 등).
- 카탈로그에 modifier도 명시. 추가된 modifier 가 카탈로그에 없으면 누구도 모른다.

**서드파티 라이브러리는 래퍼 통과 (절대 규칙 #2):**
- SlimSelect → `SelectWrapper.ts`, Flatpickr → `DatePicker.ts`, Quill → `QuillEditor.ts`, Pickr → `PickrWrapper.ts`
- 컴포넌트 안에서 직접 `import`/`new` 금지.

### 3.4 적용 단계

**정본:**
- `resources/views/pages/` (공개 홈)
- `resources/views/admin/` (관리자)
- `app/`, `routes/`

**의무:**
- 3단계 컴포넌트 헬퍼만 사용. `component(...)` / `include_view(...)`.
- raw `<input>` / `<select>` / `<textarea>` 직접 작성 금지 (절대 규칙 #1·#6).
- 색·폰트·여백을 인라인 `style=` 으로 박지 않는다 (절대 규칙 #5). 동적 값은 CSS 변수로 전달.
- DELETE/PATCH/PUT 라우트 폼은 `_method` hidden 필수 (절대 규칙 #8). 위험 액션은 `data-admin-ajax="1"` + `data-confirm` (절대 규칙 #14).
- RESTful 메서드 준수 (절대 규칙 #4).

---

## §4. 역방향 검증 — 수정·삭제·리팩터링 시

작업이 "이미 있는 것을 바꾸는" 성격이면, 흐름이 거꾸로 올라간다. 어느 방향이든 **상위 단계에 근거가 있어야** 한다.

```
4. 적용  →  3. 컴포넌트  →  2. 디자인  →  1. 브랜딩
   수정       바꾸려면        바꾸려면         바꾸려면
              ↑              ↑                ↑
        디자인 근거?    브랜딩 부합?      파급효과 분석?
        (시안 인용)     (브랜드 가이드)   (change-impact.md)
```

### 4.1 컴포넌트를 바꾸려면 (3→2 역검증)
- "이 카드의 패딩을 줄여 줘" 같은 요청도 **시안에 그 결정이 있는지** 먼저 확인.
- 시안에 없는 변경은 → 디자이너와 합의 또는 사용자 확인 후 시안에 반영. 그 다음에 컴포넌트 수정.
- "그냥 보기 좋게" 변경 금지 — 일관성이 무너진다.

### 4.2 디자인을 바꾸려면 (2→1 역검증)
- 시안에 새 컬러·새 폰트·새 스케일이 들어오면 → **브랜딩 토큰과 대조**. 부합하면 진행, 안 부합하면 1단계로.
- `docs/02-variables/brand-guide.md` 와 `.claude/agents/branding.md` 의 톤·룰 위반 여부 확인.

### 4.3 브랜딩(토큰)을 바꾸려면 (1 자체 변경)
- 토큰 변경은 가장 비싼 변경이다. 그 토큰을 쓰는 모든 디자인·컴포넌트·페이지가 영향받는다.
- `docs/01-rules/change-impact.md` 5단계 **반드시 실행** + `docs/04-history/requirement-changes.md` 기록.

---

## §5. 자주 발생하는 위반과 정정 사례

### 위반 1 — 페이지에서 색 하드코딩
```php
<!-- ❌ 페이지에 직접 -->
<div style="background:#003E76; color:white; padding:24px;">
  연구 소개
</div>

<!-- ✅ 토큰 + 컴포넌트 -->
<?= include_view('components.panel', ['variant' => 'primary', 'title' => '연구 소개']) ?>
```
원인: 1·3 단계 모두 우회. 정정: `panel` 컴포넌트 카탈로그 확인 → 없으면 신규 + 환류 4곳.

### 위반 2 — raw `<select>` 작성
```php
<!-- ❌ -->
<select name="category">
  <option value="all">전체</option>
  <option value="paper">논문</option>
</select>

<!-- ✅ -->
<?= component('select', [
    'name' => 'category',
    'options' => ['all' => '전체', 'paper' => '논문'],
]) ?>
```
원인: 절대 규칙 #1 위반 + 3단계 우회. SlimSelect 미적용으로 톤도 깨진다.

### 위반 3 — 비슷한 컴포넌트 중복 생성
- "card-news", "card-event", "card-publication" 을 비슷하게 3개 만들어 SCSS가 분산.
- 정정: 공통 `card-meta` 컴포넌트 + modifier (`--news`, `--event`, `--publication`) 로 통합. 카탈로그에 modifier 명시.

### 위반 4 — 토큰 없이 디자인 진행
- 시안에 새 회색 `#888888` 등장 → 토큰에 없는 값.
- 정정: 1단계로 돌아가 `--color-gray-500` 등으로 정의 → 시안·컴포넌트 모두 토큰 인용.

---

## §6. 다른 규칙·문서와의 관계

| 문서 | 관계 |
|---|---|
| CLAUDE.md 절대 규칙 #15 | 본 문서의 요약·금지·체크. 짧게 유지. |
| CLAUDE.md 절대 규칙 #1 (컴포넌트 카탈로그) | 본 파이프라인의 **3단계 의무**를 분리해서 강조한 하위 규칙. 이 문서가 상위. |
| CLAUDE.md 절대 규칙 #2 (서드파티 래퍼) | 3단계 컴포넌트 내부 규칙. |
| CLAUDE.md 절대 규칙 #3 (60:30:10) | 1·2단계 결정 시 색 배분 룰. |
| CLAUDE.md 절대 규칙 #5 (인라인 style 금지) | 4단계 적용 시 1단계 토큰을 우회하지 못하게 막는 보강 규칙. |
| CLAUDE.md 절대 규칙 #6 (폼 컴포넌트 매핑) | 3단계 컴포넌트 식별 매핑표. |
| CLAUDE.md 절대 규칙 #11 (타이포·컨테이너 유틸리티) | 1단계 토큰을 3단계에서 어떻게 인용해야 하는지의 구체화. |
| `docs/01-rules/workflow.md` | "명령을 받으면" Step 1(분류) 직후 본 파이프라인의 §2 선언이 끼어든다. |
| `docs/01-rules/must-check.md` | PRE/POST 체크리스트와 통합 — 본 문서 §7 의 grep 명령은 must-check 카테고리에 편입 가능. |
| `docs/01-rules/change-impact.md` | 1단계(브랜딩) 변경 시 호출되는 5단계 절차. |
| `docs/01-rules/parallel-work.md` | 4단계는 각각 다른 역할이 담당하므로 병렬·직렬 판정 시 참조. 일반적으로 단계 간은 직렬, 같은 단계 내 다른 컴포넌트는 병렬 가능. |
| `docs/03-constants/components.md` | 3단계의 정본. 본 파이프라인이 카탈로그 동기화를 강제한다. |
| `docs/02-variables/brand-guide.md` | 1단계의 정본. |

---

## §7. PRE/POST 체크리스트

### PRE (작업 시작 전)

- [ ] §2 의 4줄 선언을 작성했는가
- [ ] "상위 근거" 칸이 비어 있지 않은가 (파일 경로 또는 시안 페이지 명시)
- [ ] 손대는 단계가 컴포넌트 또는 적용이면 → `docs/03-constants/components.md` 를 먼저 Read
- [ ] 손대는 단계가 디자인이면 → `brand-guide.md` 와 시안을 먼저 확인
- [ ] 손대는 단계가 브랜딩이면 → `change-impact.md` 5단계를 적용할 준비

### POST (작업 종료 후, 모두 0건이어야 통과)

```bash
# [P-1] 페이지·관리자 뷰에서 raw 폼 태그 (쇼케이스·csrf·_method 제외)
grep -rn "<input\|<select\|<textarea" resources/views/pages resources/views/admin --include="*.php" \
  | grep -v "console/components" | grep -v "csrf_field\|_method"

# [P-2] 뷰에서 브랜딩 토큰 우회 (인라인 색·폰트·배경 하드코딩)
grep -rnE 'style="[^"]*(color|background|font-size|font-family)' resources/views --include="*.php"

# [P-3] 컴포넌트 SCSS 에서 font-size 직접 지정 (토큰 우회)
grep -rn "font-size" resources/scss/home/ --include="*.scss" \
  | grep -v "var(--" | grep -v "abstract/_typography" | grep -v "base/_common" \
  | grep -v "font-size: inherit" | grep -v "font-size: 0"

# [P-4] 새/수정된 컴포넌트가 카탈로그에 반영되었는가
git diff --name-only HEAD~1 HEAD | grep -E 'resources/views/(admin/)?(components|form)/' \
  && git diff --name-only HEAD~1 HEAD | grep -q 'docs/03-constants/components.md' \
  || echo "WARN: 컴포넌트 변경이 카탈로그에 미반영 — 환류 누락"

# [P-5] 새 SCSS 컴포넌트가 index.scss 에 @forward 되었는가
ls resources/scss/home/components/_*.scss 2>/dev/null \
  | sed 's|.*/_||;s|\.scss$||' \
  | while read name; do \
      grep -q "@forward \"$name\"" resources/scss/home/components/index.scss \
        || echo "WARN: $name 컴포넌트가 index.scss @forward 누락"; \
    done
```

POST 체크는 `/checklist` 커맨드 또는 `docs/01-rules/must-check.md` 의 해당 카테고리에 편입해 실행.
