---
title: 절대 규칙 #17 — 디자인시스템 유틸 강제
categories: [rule, ui, design-system]
desc: 폰트 위계·spacing·컴포넌트 재사용을 디자인 시스템 유틸(`_common.scss`)·재사용 컴포넌트(`imms-*`)로만 표현. 페이지·섹션 BEM 블록 SCSS 의 직접 지정 금지.
related: [rule-01-component-catalog, rule-11-typography-utilities, rule-15-design-pipeline]
updatedAt: 2026-05-26
---

# 절대 규칙 #17 — 디자인시스템 유틸 강제 (전 역할 공통, Level 1 always)

> 본문 요약은 CLAUDE.md Level 1. 본 파일은 매핑표·시나리오·정정 예시·체크리스트 정본.
> 소속: Level 1 (always) + UI 번들 (`by-task/ui.md`) 의 #11·#1 강화 보완

## 1. 왜 이 규칙이 필요한가

- 같은 의미의 제목·CTA가 10개 페이지에서 10가지 클래스로 갈라지면 톤 일관성이 즉시 무너진다. 사용자가 "한 페이지 안에서도 CTA 스타일이 3가지로 다르다"고 지적한 사건(2026-05-26)이 직접 원인.
- 페이지·섹션 BEM 블록(`.home-events__title`)에 `font-size`·`font-weight`·`line-height`·`letter-spacing`을 SCSS에서 박아넣으면, 위계 조정(예: 메인 섹션 타이틀을 모두 한 단계 줄이기) 시 10곳을 일일이 고쳐야 한다. **유틸 클래스를 HTML에 적용**하면 토큰 값 하나로 전사 파급.
- 신규 일회성 CTA(`home-objects__cta`, `imms-cta-link` 등)를 BEM 블록으로 양산하면 컴포넌트 카탈로그(#1)와 4단계 파이프라인(#15) 의 목적이 무너진다.

## 2. 3대 조항

### A. 타이포 위계 = HTML 유틸 클래스만

페이지·섹션·블록 마크업의 모든 제목·본문은 `_common.scss` 유틸 클래스로만 위계를 부여한다.

| 단계 | 유틸 클래스 | 토큰 (fluid 범위) | 권장 용도 |
|---|---|---|---|
| display-xl | `no-display-xl` | 48 → 148px | Home Hero 슬로건 (1곳) |
| display-lg | `no-display-lg` | 40 → 120px | 시그니처 페이지 Hero |
| display-md | `no-display-md` | 36 → 110px | 캠페인 대제목 (드물게) |
| display-sm | `no-display-sm` | 32 → 84px | 큰 카드 타이틀 |
| display-xs | `no-display-xs` | 28 → 72px | 강조 카드 |
| heading-xl | `no-heading-xl` | 24 → 60px | **메인 페이지 섹션 타이틀 표준** |
| heading-lg | `no-heading-lg` | 22 → 42px | 서브 페이지 섹션 타이틀 |
| heading-bl | `no-heading-bl` | 20 → 36px | 카드 그룹 타이틀 |
| heading-md | `no-heading-md` | 20 → 32px | 일반 섹션 타이틀 |
| heading-sm | `no-heading-sm` | 18 → 28px | 카드 타이틀 |
| heading-xs | `no-heading-xs` | 16 → 24px | 소형 카드·박스 타이틀 |
| body-xl | `no-body-xl` | 16 → 22px | Hero desc, lead text |
| body-lg | `no-body-lg` | 16 → 20px | 강조 본문 |
| body-md | `no-body-md` | 14 → 18px | **기본 본문 (default)** |
| body-sm | `no-body-sm` | 13 → 16px | 캡션·작은 본문 |
| body-xs | `no-body-xs` | 12 → 14px | 메타·라벨 |

**금지 (페이지·섹션 SCSS):**
- ❌ `.home-events__title { font-size: var(--display-md-fs); font-weight: 600; line-height: 1; letter-spacing: var(--tracking-display); }`
- ❌ `.research-tg-card__name { font-size: 1.8rem; }` — px·rem 하드코딩
- ❌ `.about-section__heading { font-family: var(--font-display-family); }` — 페이지 블록에 font-family

**허용 (예외):**
- ✅ `_common.scss` (정본) + `_typography.scss` (토큰 정본)
- ✅ 재사용 컴포넌트(`imms-cta`, `imms-card`, `imms-table` 등) 내부 BEM 자식 요소가 토큰을 참조하는 경우 (`font-size: var(--body-sm-fs)`) — 단 가능하면 마크업에 유틸 적용하는 쪽이 우선
- ✅ 컴포넌트 modifier 전용 weight/line-height 미세 조정 (`.imms-cta--ghost { font-size: var(--body-sm-fs); }`)

### B. Spacing = 유틸 또는 토큰만

| 용도 | 적용 위치 | 클래스/토큰 |
|---|---|---|
| 섹션 상하 패딩 | HTML | `no-section-xs / -sm / -md / -lg / -xl` |
| 컨테이너 가로 | HTML | `no-container-{sm,md,lg,bl,xl}` |
| 일반 padding | HTML 또는 컴포넌트 SCSS | `no-pd-{xs,sm,md,lg,xl}` / `var(--spacing-*)` |
| 일반 margin | HTML 또는 컴포넌트 SCSS | `no-mg-{xs,sm,md,lg,xl}` / `var(--spacing-*)` |
| 컴포넌트 내부 fluid 미세 조정 | 컴포넌트 SCSS만 | `fluid(a, b)` (토큰화 어려운 경우만) |

**금지:**
- ❌ 페이지·섹션 클래스에 `padding: 24px 32px`·`margin: 16px 0` 같은 px 하드코딩
- ❌ `.home-events { padding: fluid(60, 120) fluid(40, 100); }` — 가능하면 `no-section-lg` + `no-container-xl` 조합으로 마크업화. 정말 페이지 특수 레이아웃이면 SCSS에서 `var(--section-lg)` 토큰 사용
- ❌ 같은 의미의 spacing 이 페이지마다 다른 값으로 튐 (`gap: 0.4rem` vs `gap: 0.8rem` vs `gap: fluid(12, 20)`) — 토큰화로 일관

### C. 동일 의미 컴포넌트 = 동일 구현

같은 페이지·다른 페이지를 통틀어 의미가 같은 CTA·카드·버튼·필드는 **반드시 같은 컴포넌트로**. 변형 필요 시 새 BEM 블록이 아니라 **modifier**(`--ghost`, `--on-dark`, `--sm` 등) 또는 prop 으로.

**예시: CTA 통일**
- ❌ 한 페이지 안에서 한 곳은 `imms-cta --ghost`, 다른 곳은 `imms-cta-link`(구형), 또 다른 곳은 `home-objects__cta`(일회성)
- ✅ 모두 `imms-cta` (variant=ghost / primary / secondary, modifier=--on-dark / --sm 조합)

**구형·일회성 CTA 클래스 (마이그레이션 대상):**
| 구형 | 신형 | 비고 |
|---|---|---|
| `imms-cta-link` | `imms-cta` (variant=ghost) | `_imms-section.scss` 의 정의는 @deprecated 주석 후 잔존 사용처만 호환용 유지 |
| `home-objects__cta` | `imms-cta` (variant=ghost) | 일회성 BEM. 신규 작성 금지, 마이그레이션 후 삭제 |
| 페이지 전용 `__more` / `__view-all` | `imms-cta` (variant=ghost) | 동일 |

**금지:**
- ❌ "이 섹션만 살짝 다르게" 새 BEM 블록을 만들어 변형 — modifier 로 표현하거나, 정말 의미가 다르면 새 컴포넌트로 카탈로그(#1) 등록
- ❌ 카탈로그에 등록 안 한 채 일회성 CTA·버튼·카드 클래스 작성
- ❌ `imms-cta-link` (구형) 신규 사용 — 신규 마크업은 `imms-cta --ghost` 만

## 3. 작업 후 체크 (모두 0건 또는 의도 명시)

```bash
# [1] 페이지·섹션 SCSS 가 폰트 위계를 직접 지정 (컴포넌트·정본 제외)
grep -rnE '\b(font-size|font-weight|font-family|line-height|letter-spacing)\s*:' \
  resources/scss/home/pages --include="*.scss" \
  | grep -v "abstract/" | grep -v "/components/"

# [2] 페이지 SCSS 의 __title / __heading / __subtitle 블록에 폰트 위계 잔재
grep -rnE '__(title|heading|subtitle|name)\s*\{[^}]*font-(size|weight|family)' \
  resources/scss/home/pages/ --include="*.scss"

# [3] 구형·일회성 CTA 클래스 신규 사용 (마이그레이션 추적)
grep -rnE 'imms-cta-link|home-[a-z]+__cta\b|__more\b' resources/views/pages --include="*.php"

# [4] 페이지 SCSS 에 px/rem 하드코딩된 padding/margin (토큰화 누락)
grep -rnE '\b(padding|margin)\s*:\s*[0-9]+(\.[0-9]+)?(px|rem)' \
  resources/scss/home/pages --include="*.scss" \
  | grep -v "/components/" | grep -v "abstract/"

# [5] HTML 마크업의 섹션 타이틀에 유틸 클래스 누락 (마이그레이션 추적)
grep -rnE 'class="[^"]*__title[^"]*"' resources/views/pages --include="*.php" \
  | grep -v "no-display\|no-heading"
```

위반이 있으면 정정 후 다시 0건 확인.

## 4. 정정 시나리오 (Before → After)

### 시나리오 1 — 섹션 타이틀

**Before (위반):**
```php
<!-- index.php -->
<h2 class="home-events__title">Events</h2>
```
```scss
/* main.scss */
.home-events__title {
  font-family: var(--font-display-family);
  font-size: var(--display-md-fs);
  font-weight: 600;
  line-height: 1;
  letter-spacing: var(--tracking-display);
  color: var(--clr-gray-900);
  margin: 0;
}
```

**After (정정):**
```php
<!-- index.php — 유틸 클래스 적용 -->
<h2 class="home-events__title no-heading-xl">Events</h2>
```
```scss
/* main.scss — 폰트 위계 제거, 레이아웃 책임만 유지 */
.home-events__title {
  color: var(--clr-gray-900);
  margin: 0;
}
```

### 시나리오 2 — CTA 통일

**Before (위반):**
```html
<a href="/about" class="home-objects__cta">
  About us
  <i class="fa-regular fa-circle-arrow-right"></i>
</a>
```

**After (정정):**
```php
<?= include_view('components.imms-cta', [
    'href'    => '/about',
    'label'   => 'About us',
    'variant' => 'ghost',
]) ?>
```

### 시나리오 3 — 섹션 padding

**Before (위반):**
```scss
.about-section {
  padding: fluid(64, 112) 0;
}
```

**After (정정 — 마크업화 가능하면):**
```php
<section class="about-section no-section-lg">
```
또는 정말 페이지 특수 spacing이라 토큰으로 표현이 어려우면:
```scss
.about-section {
  padding-top: var(--section-lg);
  padding-bottom: var(--section-lg);
}
```

## 5. 페이지별 점진 마이그레이션 체크리스트

본 규칙은 2026-05-26 신설. 기존 코드는 즉시 정정이 어려우므로 페이지별 점진 마이그레이션.

- [x] 메인 페이지 (`pages/index.php`) — 1차 모범 사례 (2026-05-26)
- [ ] Research 페이지군 (`pages/research/**`) — 다음 sprint
- [ ] About 페이지군 (`pages/about/**`)
- [ ] Learning 페이지군 (`pages/learning/**`)
- [ ] News / Events 페이지군 (`pages/news/`, `pages/events/`)
- [ ] Facilities / 기타 (`pages/facilities/`)

각 페이지 마이그레이션 시 §3 grep 체크리스트로 위반 0건 확인 후 patch-note 기록.

## 6. 관련 규칙

- #11 (`details/rule-11-typography-utilities.md`) — SCSS `font-size` 직접 지정 금지의 원형. 본 규칙이 이를 HTML 마크업 차원까지 확장·강화.
- #1 (`details/rule-01-component-catalog.md`) — 컴포넌트 카탈로그 재사용 원칙. 본 규칙 C 조항이 이를 강화.
- #15 (`details/rule-15-design-pipeline.md`) — 디자인 4단계 파이프라인. 본 규칙은 그 안에서 컴포넌트 단계의 강제력 보강.

## 7. 신설 결정 맥락

2026-05-26 사용자 피드백 — 메인 페이지 스크린샷 보고 "About us / All News / All Events 버튼 스타일이 다 다르다", "메인 섹션 타이틀이 너무 크다", "10페이지 모두 다른 클래스로 처리할 거냐, 공통 위계 클래스로 처리하는 게 맞다"는 강력한 지적. 토큰만 정의해두고 강제하지 않은 결과 일관성이 무너졌다는 판단. Level 1 (always) 에 신규 절대 규칙으로 격상.
