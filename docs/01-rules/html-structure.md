---
title: HTML 구조 규칙 (섹션 마크업 표준)
categories: [ui, rules]
desc: 메인·일반 페이지 섹션은 section>container>inner>title+--cnt 단일 구조로 통일. 특정 섹션만 예외.
related: [rules.html-structure, constants.components]
updatedAt: 2026-06-08
---

# HTML 구조 규칙 — 퍼블 와이어프레임

> **특정 섹션을 제외하고 모든 페이지 섹션은 아래 단일 구조로 통일한다.**
> BEM(`block__a__b`) 금지 — 깔끔한 네스팅 구조 + 유틸 클래스만 사용.

---

## 1. 표준 섹션 구조 (정본)

```html
<section class="<섹션이름>">
  <div class="no-container-xl">      <!-- container: 범용 컨테이너만 (표준폭 1320) -->
    <div class="inner">             <!-- inner: 레이아웃 래퍼 -->
      <h2 class="title f-heading-1">…</h2>   <!-- (선택) 섹션 제목 -->
      <div class="--cnt">           <!-- 콘텐츠 블록 -->
        <!-- grid(2단)이면: -->
        <div class="txt"> … </div>
        <div class="image"></div>   <!-- 또는 <ul class="list"> … </ul> -->
      </div>
    </div>
  </div>
</section>
```

계층: `section` → `container(no-container-xl)` → `inner` → `title` + `--cnt` → (grid면) `txt` + `image`/`list`.

---

## 2. 규칙 (반드시 0 위반)

- **BEM 금지** (페이지 섹션 한정). `__title__text` 같은 중첩 클래스 작성 금지. 클래스명은 `title` / `--cnt` / `txt` / `image` / `list` 처럼 깔끔하게.
- **컨테이너 = 범용 `no-container-*` 만.** 표준폭은 `no-container-xl`(= 1320px). 컴포넌트 전용 컨테이너(예: `*-container`)를 따로 만들지 말고 범용으로 교체. (`2xl` = 1664 는 더 넓은 옵션)
- **타이포 위계 = 마크업 유틸 클래스만.** `f-display-0~3` / `f-heading-1~5` / `f-body-1~4`. 페이지 SCSS 에 `font-size` 직접 지정 금지.
- **SCSS = 섹션 루트 아래 nesting.** 간격은 `fluid()`, 색은 `var(--clr-*)`, 반응형은 `mq("md")` 등. 하드코딩·인라인 `style=` 금지.
- **이미지 = placeholder** (와이어프레임 단계 — 회색 박스 + 마스크 아이콘).
- 브랜드 색은 토큰 `var(--clr-primary-def)`(= `#012758`). 단일 라이트 테마. 폰트는 전부 Pretendard.

---

## 3. 예외 섹션 (구조가 다름)

- **footer** — `--cnt` 사용하지 않는다. `top`(logo + menu) / `mid`(info + link) / `bottom`(copyright) 3단 구조. 위계: menu = title, info-label = desc, info-value = body, copy = desc.
- **visual (swiper)** — 슬라이더 특성상 `.swiper > .swiper-wrapper > .swiper-slide` 가 들어간다. 각 슬라이드 내부는 `inner > title + --cnt` 표준을 유지한다.
- **헤더·푸터 등 공유 컴포넌트** — 기존 `no-*` BEM 클래스 유지 (no-BEM 규칙은 페이지 섹션에만 적용). 단, 컨테이너는 범용 `no-container-*` 로 정렬.

---

## 4. 적용 예 (메인 `views/pages/index.php`)

| 섹션 | 구조 |
|---|---|
| about   | `inner > --cnt(.txt + .image)` (2단 grid, 제목은 .txt 안) |
| business| `inner > title("Our Business") + --cnt(카드 list)` |
| client  | `inner > --cnt(.txt + .image) + list(로고)` |
| visual  | swiper, 슬라이드마다 `inner > title + --cnt(.txt)` |

정본 마크업: `views/pages/index.php` · 정본 스타일: `resource/scss/pages/_main.scss`.
