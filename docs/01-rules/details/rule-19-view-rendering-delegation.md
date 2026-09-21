---
title: 절대 규칙 #19 — 뷰 렌더링 위임 (페이지 = 조립소, 마크업 = components/ · sections/)
categories: [rule, ui, architecture]
desc: 모든 HTML 덩어리는 components/ 또는 sections/ 에 둔 뒤 render_component / render_section / include_view 함수 호출로 페이지에 연결. 페이지 파일에 인라인 마크업 신규 작성 금지.
related: [rule-01-component-catalog, rule-17-design-system-utility, rule-15-design-pipeline]
updatedAt: 2026-05-31
---

# 절대 규칙 #19 — 뷰 렌더링 위임 (전 역할 공통, Level 1 always)

> 본문 요약은 CLAUDE.md Level 1. 본 파일은 조항·시나리오·체크리스트 정본.
> 소속: Level 1 (always) + UI 번들 (`by-task/ui.md`)
> 신설 사유: 2026-05-28 — News 페이지 빈 상태가 4종 layout 마다 인라인 `__empty` 마크업으로 분산되어 한 곳을 고치면 다른 곳이 빠지는 유지보수 사고가 반복. 사용자가 "모든 HTML 은 컴포넌트/섹션 2곳에서만 관리" 원칙을 강력 규칙으로 못박을 것을 요청.

## 1. 왜 이 규칙이 필요한가

- 동일 의미 마크업이 여러 페이지에 인라인으로 복붙되면, 디자인 변경 1건이 N개 파일 손질로 폭증한다. 빈 상태 메시지·일러스트 톤 변경 같이 작아 보이는 작업이 페이지 4곳을 동시에 고치는 작업이 된다.
- 페이지가 데이터 + 마크업 + 조건 분기를 모두 떠안으면 **단일 소스 (single-source) 원칙**이 무너진다. 같은 화면이 두 페이지에서 미세하게 다르게 렌더되는 시각 회귀가 발생한다.
- 컴포넌트 카탈로그(#1)·디자인시스템 유틸(#17)이 강력하더라도, 페이지가 직접 `<div class="...">` 를 새로 만들기 시작하면 두 규칙의 사정권 밖에서 마크업이 늘어난다. 본 규칙은 **렌더링의 위치 자체**를 강제하여 #1·#17 이 작동할 공간을 확보한다.
- 사용자(소장·개발 책임자)가 유지보수의 핵심 원칙으로 "components/ + sections/ 두 곳만 관리하면 끝나도록" 명시적으로 요청 (2026-05-28). 이는 향후 모든 UI 작업의 상위 메타 규칙이다.

## 2. 3대 조항

### A. 페이지 파일 = 조립소만

`resources/views/pages/**` 의 파일은 다음만 포함한다:

1. 레이아웃 상속 (`extend('layouts.main')`)
2. 페이지 전용 데이터 준비 (`$meta = $resolver(...)` 등)
3. **`render_component(...)` / `render_section(...)` / `include_view(...)` 호출**
4. 호출 사이의 최소한의 컨테이너 (`<main class="board-page">`, `<div class="no-container-xl">` 등 레이아웃 래퍼) 와 반복문/조건문

**금지:**
- ❌ 페이지 안에 새로운 BEM 블록 `<div class="some-page__card">` ... `</div>` 인라인 작성
- ❌ 같은 시각 단위(빈 상태, 카드, 알림, 모달 헤더 등) 마크업이 여러 페이지·layout 에 분산
- ❌ 페이지 한정 부분 뷰를 `require dirname(__DIR__) . '/_xxx.php'` 식 raw include 로만 운영 — 부분 뷰가 페이지 전용이라도 `pages.posts._post-card` 같이 `include_view()` 네임스페이스로 호출

**예외 (인라인 마크업 허용):**
- ✅ 페이지 진입부의 1회성 `<?php section('content') ?>` ... `<?php end_section() ?>` 골격
- ✅ 호출들을 묶는 래퍼 (`<main>`, `<div class="no-container-*">`, `<ul>` 등 컨테이너 — 그 안의 카드/아이템은 컴포넌트)
- ✅ 정말로 그 페이지 1회만 등장하고 다른 화면에 절대 재등장하지 않을 헤더 1줄 (`<h1 class="no-display-xs">Page Title</h1>`) — 단 2회 이상 같은 의미 블록이 등장하는 순간 즉시 추출 의무 발생

### B. 신규 = 무조건 추출 / 수정 = 손댄 블록 추출 (Boy Scout rule)

**신규 HTML 덩어리**(`<div class="...">...</div>` 단위로 구분 가능한 시각 블록)는 처음 작성하는 순간 `resources/views/components/` 또는 `resources/views/sections/` 에 둔다. 페이지에는 호출 한 줄만 남긴다.

**기존 페이지 수정**: 손대는 블록이 인라인 마크업이면 **그 자리에서 추출**한 뒤 호출로 바꾼다. 손대지 않은 블록까지 일괄 마이그레이션할 의무는 없으나, "지나가는 길목" 의 한 줄 인라인은 같이 정리한다.

**금지:**
- ❌ 페이지에 새 마크업을 작성한 뒤 "나중에 컴포넌트로 빼겠다" 라는 약속 (= 채무 누적). 처음부터 components/ 또는 sections/ 에 만든다.
- ❌ 컴포넌트로 추출하지 않고 `_partial.php` 같은 일회용 부분 뷰만 만들고 끝 — 부분 뷰여도 `components/` 또는 `sections/` 네임스페이스에 두고 헬퍼 함수로 호출

**허용:**
- ✅ 시안 검증용 prototype 단계에서 임시 인라인 (단 머지 전 추출 필수)

### C. 분기 기준 = 크기·역할

| 종류 | 위치 | 호출 헬퍼 | 예시 |
|---|---|---|---|
| **UI 프리미티브 / 작은 단위 블록** | `resources/views/components/<name>.php` | `render_component('<name>', [...])` 또는 `include_view('components.<name>', [...])` | `empty-state`, `imms-cta`, `pagination`, `img-auto-cover`, `popup`, `breadcrumb` |
| **페이지 한 덩어리 / 도메인 블록** | `resources/views/sections/<group>/<name>.php` | `render_section('<group>.<name>', [...])` | `home.news-latest-4up`, `home.hero-statement`, `home.cta-band` |
| **레이아웃 부분** | `resources/views/layouts/partials/<name>.php` | `include_view('layouts.partials.<name>')` | `favicon`, `head-meta` |
| **관리자 폼 프리미티브 (특수)** | `resources/views/admin/form/<type>.php` | `component('<type>', [...])` | `input`, `select`, `richEditor` — 규칙 #1 정본 |

**판단 시 순서:**
1. 카탈로그(`docs/03-constants/components.md`) 와 쇼케이스(`/guide/components`, `/guide/sections`) 먼저 확인. 있으면 호출.
2. 없으면 크기로 분기 — 작고 재사용 가능한 UI 블록 (버튼·카드·빈 상태·알림 등) = `components/`. 페이지 단위 덩어리 (Hero 영역, News 4-up 그리드, CTA band 등) = `sections/`.
3. 신설하면 **반드시** 동시 4종 작업: 뷰 파일 + SCSS 파일 + `index.scss` `@forward` + 카탈로그(`docs/03-constants/components.md`) 한 줄 + 쇼케이스(`/guide/components` 또는 `/guide/sections`) 데모 블록.

**헬퍼 함수 정본:**
- `render_component(string $key, array $props = []): string` — [framework/Helpers/view.php:73-76](../../../framework/Helpers/view.php#L73) — **공개 사이트** 컴포넌트 전용 (`resources/views/components/`)
- `render_section(string $key, array $props = []): string` — [framework/Helpers/view.php:91-94](../../../framework/Helpers/view.php#L91) — **공개 사이트** 섹션 전용
- `include_view(string $name, array $data = []): string` — [framework/Helpers/view.php:58-62](../../../framework/Helpers/view.php#L58) — 공개/admin 범용
- `render_admin_component(string $key, array $props = []): string` — [framework/Helpers/components.php](../../../framework/Helpers/components.php) — **admin** UI 컴포넌트 전용 (`resources/views/admin/components/`) · `include_view('admin.components.'.$key, $props)` 별칭
- `component(string $type, array $attrs = []): string` — 관리자 폼 프리미티브 전용. [framework/Helpers/components.php](../../../framework/Helpers/components.php)

### Admin 페이지 전용 분기 (2026-05-31 추가)

Admin 페이지(`resources/views/admin/pages/**`)는 공개 사이트와 분리된 컴포넌트 체계를 사용한다.

| 종류 | 위치 | 호출 헬퍼 |
|------|------|-----------|
| **Admin UI 컴포넌트** | `resources/views/admin/components/<name>.php` | `render_admin_component('<name>', [...])` (신규 권장) 또는 `include_view('admin.components.<name>', [...])` |
| **Admin 폼 프리미티브** | `resources/views/admin/form/<type>.php` | `component('<type>', [...])` |

**신규 admin 페이지 작업 흐름:**
1. `/console/guide/sections` 에서 맞는 패턴 확인 (쇼케이스: `resources/views/admin/guide/sections.php`)
2. 있으면 해당 패턴을 복제해서 실제 데이터에 맞게 수정
3. 없으면 `resources/views/admin/guide/sections.php` 에 먼저 패턴 추가 후 페이지에 사용
4. 새 admin UI 컴포넌트는 `resources/views/admin/components/<name>.php` 에 추가 후 `/console/guide/components` 쇼케이스에 데모 블록 추가

**Admin 체크 (반드시 0건) — `admin/` 전체 범위 (pages/ + users/ + roles/ + 기타):**
```bash
# [1] 관리자 인라인 빈 상태/오류 마크업 분산 (admin 전체)
grep -rnE 'class="[a-z][a-z0-9-]*__(empty|error|loading|placeholder)\b' resources/views/admin --include="*.php" \
  | grep -v "/guide/\|/components/"

# [2] page-toolbar / empty-state / table-actions 인라인 마크업 분산 (admin 전체, guide·components 정의 제외)
grep -rn '<div class="admin-page__toolbar"' resources/views/admin --include="*.php" | grep -v "/guide/\|/components/"
grep -rn '<div class="admin-empty"'         resources/views/admin --include="*.php" | grep -v "/guide/\|/components/"
grep -rn 'class="admin-table__actions"'     resources/views/admin --include="*.php" | grep -v "/guide/\|/components/"
```

**중요**: grep 범위는 `resources/views/admin/pages/` 가 아니라 **`resources/views/admin/`** 전체. users/·roles/ 디렉토리는 pages/와 별도이며 이전 작업(2026-05-31 14:00)에서 누락된 적이 있음. 모든 admin 페이지가 검증 대상.

**2026-05-31 admin 전면 컴포넌트화 완료**: page-toolbar(59 인라인 → 0), empty-state(28+4 인라인 → 0), table-actions(25+1 인라인 → 0). 신규 페이지는 이 3종 컴포넌트를 우선 사용. users/·roles/ 보완은 2026-05-31 15:00 patch-notes 참고.

## 3. 작업 후 체크 (반드시 0건)

```bash
# [1] 페이지 파일에서 같은 의미 BEM 블록이 여러 곳에 인라인으로 정의 (대표 사례: __empty / __error / __loading)
grep -rnE 'class="[a-z][a-z0-9-]*__(empty|error|loading|placeholder)\b' resources/views/pages --include="*.php"

# [2] 페이지에서 부분 뷰를 헬퍼 우회로 끌어오는지 (require/include 직접 호출 — _board-page-presets.php 처럼 데이터 준비 PHP 는 예외 OK)
grep -rnE '\b(require|include)(_once)?\s*\(\s*[^)]*\.php' resources/views/pages --include="*.php" | grep -v "_presets" | grep -v "_helpers"

# [3] 신규 PR 에서 페이지 파일 line 증가가 50줄 이상인데 components/ · sections/ 신설이 없는 경우
git diff --stat HEAD~1 HEAD | grep -E 'resources/views/pages/.*\.php' | awk '{ if ($3+0 > 50) print }' \
  && git diff --name-only HEAD~1 HEAD | grep -qE 'resources/views/(components|sections)/' \
  || echo "WARN: 페이지 증가가 큰데 컴포넌트·섹션 신설 없음 — 인라인 마크업 누적 위험"

# [4] 신설 컴포넌트·섹션이 카탈로그·쇼케이스 동시 갱신을 거쳤는지
git diff --name-only HEAD~1 HEAD | grep -qE 'resources/views/(components|sections)/' \
  && git diff --name-only HEAD~1 HEAD | grep -q 'docs/03-constants/components.md' \
  || echo "WARN: 신설/수정 컴포넌트·섹션의 카탈로그 갱신 누락"
```

`[1]` 은 "같은 의미 빈 상태/오류/로딩 마크업이 페이지에 분산" 사고의 직접 감지기. 본 규칙 신설 직후 사례(2026-05-28)는 `board-list__empty / board-gallery__empty / board-webzine__empty` 3종 분산 → `empty-state` 컴포넌트로 통일.

## 4. 기존 규칙과의 관계 (직교 — 모두 동시 적용)

| 규칙 | 무엇을 강제 | 본 규칙(#19)과의 관계 |
|---|---|---|
| #1  | **무엇을 쓸지** — 카탈로그 먼저, raw `<select>` 금지 | #19 는 #1 이 작동할 위치를 보장 (페이지에 새 마크업이 안 들어오면 #1 의 카탈로그가 단일 진실원). |
| #15 | 디자인 4단계 파이프라인 (브랜딩→디자인→컴포넌트→적용) | #19 는 ④ 적용 단계의 형식 = "호출만". 신설 컴포넌트는 ③ 단계에서 components/ · sections/ 에 만든 뒤 ④ 페이지에서 호출. |
| #17 | **어떻게 표현할지** — 토큰·유틸로만, 동일 의미 = 동일 구현 | #19 가 페이지 인라인을 막아 #17 위반(BEM 블록에 인라인 폰트 위계) 의 발생 자체를 차단. #17 의 "동일 의미 컴포넌트 = 동일 구현" 조항을 위치 차원에서 강화. |

## 5. 마이그레이션 정책 (적용 범위)

- **신규 작업**: 무조건 본 규칙 적용. 인라인 마크업 작성 즉시 위반.
- **기존 페이지 수정**: 손댄 블록만 추출 (Boy Scout rule). 그 자리에 있는 인라인 마크업을 컴포넌트/섹션으로 옮긴 뒤 호출로 교체.
- **일괄 리팩토링**: 별도 작업 티켓으로만. 본 규칙 신설(2026-05-28) 시점에 일괄 마이그레이션 의무는 없음 — 손대는 곳마다 정리.

## 6. 시나리오 — 적용 예시

### 시나리오 1: News 페이지 빈 상태 (2026-05-28 본 규칙 신설의 계기)

**Before (인라인 분산):**
```php
// resources/views/pages/posts/layouts/list.php
<?php if (!$hasPosts): ?>
  <li class="board-list__empty">등록된 게시글이 없습니다.</li>
<?php endif; ?>

// resources/views/pages/posts/layouts/gallery.php
<?php if (!$hasPosts): ?>
  <p class="board-gallery__empty">등록된 게시글이 없습니다.</p>
<?php endif; ?>

// resources/views/pages/posts/layouts/webzine.php — 또 다른 클래스명, 같은 의미
```

**After (컴포넌트 호출):**
```php
<?php if (!$hasPosts): ?>
  <?= render_component('empty-state', [
      'message'     => 'No posts yet.',
      'description' => 'Updates will appear here as they are published.',
      'icon'        => 'inbox',
      'tag'         => 'li',   // <ul> 컨텍스트
  ]) ?>
<?php endif; ?>
```

이제 빈 상태 톤·아이콘·간격을 바꾸려면 `resources/views/components/empty-state.php` + `resources/scss/home/components/_empty-state.scss` **두 파일만** 고치면 게시판 4곳·향후 모든 사용처가 동시 갱신.

### 시나리오 2: 페이지 단위 덩어리 신설

**예시: About 페이지에 "Director's message" 영역 신규**

- 잘못된 접근: `resources/views/pages/about/index.php` 안에 `<section class="about-director">...</section>` 인라인 작성
- 올바른 접근:
  1. `resources/views/sections/about/director-message.php` 신설 + `resources/scss/home/sections/_director-message.scss` 신설
  2. `index.scss` `@forward` 추가
  3. 페이지에선 `<?= render_section('about.director-message', ['name' => ..., 'photo' => ...]) ?>` 한 줄
  4. `docs/03-constants/components.md` §3.1 섹션 카탈로그에 한 줄 + `/guide/sections` 쇼케이스에 데모 블록

이렇게 하면 향후 같은 메시지를 People 페이지·Hero 영역에서도 재사용할 때 prop 만 바꿔 호출 가능.
