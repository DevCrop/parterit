# 절대 규칙 #10 — SCSS 클래스명 = 뷰 클래스명 1:1 일치 + 라벨 위 간격 명시 (퍼블·개발 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #10 (원문 보존)
> 소속 번들: `by-task/ui.md`

**컴포넌트 SCSS를 작성·수정할 때 반드시 뷰 파일의 루트 클래스명을 grep으로 확인하고 그대로 사용한다.** 약어 자의적 사용(`img`, `btn-grp` 등) 금지 — SCSS와 뷰 클래스명이 1자라도 다르면 스타일이 **전혀 적용되지 않는다**.

**라벨 위·아래 간격은 항상 명시적으로 부여한다.** 브라우저 reset 환경에서 모든 요소 `margin: 0`이 기본이다.
- **라벨 위**: 인접 선택자(`desc + label`) 또는 `margin-top`으로 명시
- **라벨 아래 → input/select**: `admin-form__label`과 `admin-form__label--sm`에는 **반드시 `margin-bottom: var(--admin-space-xs)`** 가 정의되어야 한다. 새 라벨 수정자(modifier) 추가 시 동일하게 적용.

"라벨과 input이 붙어 보이니 괜찮겠지" 추측 절대 금지 — reset 후 margin이 0이라 실제로는 붙어 있다.

**금지:**
- SCSS 파일 작성 후 뷰 파일의 실제 클래스명 확인 없이 커밋
- 라벨·필드 위아래 간격을 브라우저 기본값에 의존하는 마크업
- `admin-form__label` 새 modifier 추가 시 `margin-bottom` 누락

**작업 후 체크:**
```bash
# SCSS BEM 루트 클래스가 뷰에도 존재하는지 교차 확인
# (SCSS 파일 작성 시 아래 패턴으로 뷰에서 클래스명 확인)
grep -r "admin-single-image-uploader" resources/views --include="*.php" | head -5
grep -r "admin-single-image-uploader" resources/scss --include="*.scss" | head -5
# 두 결과의 클래스명이 정확히 일치해야 함
```

---

## ⛔ spacing 토큰 강제 검증 (라벨 간격뿐 아니라 전체 spacing)

라벨↔input 간격만 강제하면 카드 내부 폼 gap·리스트 항목 gap 등이 또 하드코딩으로 들어간다 (반복 위반 사례: `gap: 6px;`, `padding: 2px 8px;`). 모든 margin/padding/gap 은 `var(--admin-space-*)` 토큰만 사용.

**허용 예외:**
- `padding: 0` (완전 리셋 의도, BEM 클래스 안에서만)
- `1px solid` 같은 보더 (spacing 아님)
- `--admin-space-*` 토큰 정의 파일 (`abstract/_variables.scss`)
- 아이콘·뱃지·점(dot) 의 width/height 픽셀 값 (spacing 속성 아님)

**작업 후 체크 (3 grep — 결과 0건):**
```bash
# [1] 뷰의 inline spacing 하드코딩 (var(--*) 아닌 것)
grep -rEn 'style="[^"]*(margin|padding|gap)\s*:\s*[^v"]' resources/views/admin --include="*.php"

# [2] SCSS의 spacing 하드코딩 (토큰·0 아닌 것)
grep -rEn '\b(margin|padding|gap)(-(top|bottom|left|right))?\s*:\s*[0-9.]+(px|rem)\b' \
  resources/scss/admin/pages resources/scss/admin/components --include="*.scss" \
  | grep -v '\b0(px|rem)?\b\|var(--admin-space'

# [3] BEM 무력화 (요소 리셋이 클래스 spacing 덮어쓰기)
grep -rEn '\.admin[a-z_-]+\s+\b(th|td|li|p|h[1-6])\b[^{]*\{[^}]*(padding|margin)' \
  resources/scss/admin --include="*.scss"
```
세 명령 모두 0건이어야 통과. `/console/system/ui-audit` 페이지에서도 `inline-spacing-hardcoded`·`scss-spacing-hardcoded`·`bem-reset-override` 카테고리 0건 확인.
