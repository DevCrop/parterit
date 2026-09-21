# 절대 규칙 #11 — 홈페이지 타이포그래피·컨테이너는 유틸리티 클래스만 (퍼블·개발 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #11 (원문 보존)
> 소속 번들: `by-task/ui.md`

**SCSS 컴포넌트 파일에서 `font-size` 직접 지정 절대 금지.** HTML에서 타이포그래피 유틸리티 클래스(`no-display-*`, `no-heading-*`, `no-body-*`)를 적용하거나, SCSS에서 참조 시 반드시 `font-size: var(--heading-md-fs)` 형태의 CSS 변수 사용.

**`imms-container` 클래스 사용 절대 금지.** 반드시 `no-container-{breakpoint}` 형태 사용 (예: `no-container-bl`, `no-container-xl`).

**매핑표 (fluid() → CSS 변수):**
| fluid() 인자 | CSS 변수 |
|---|---|
| `fluid(12,14)` | `var(--body-xs-fs)` |
| `fluid(13,16)` | `var(--body-sm-fs)` |
| `fluid(14,18)` | `var(--body-md-fs)` |
| `fluid(16,20)` | `var(--body-lg-fs)` |
| `fluid(16,22)` | `var(--body-xl-fs)` |
| `fluid(16,24)` | `var(--heading-xs-fs)` |
| `fluid(18,28)` | `var(--heading-sm-fs)` |
| `fluid(20,32)` | `var(--heading-md-fs)` |
| `fluid(20,36)` | `var(--heading-bl-fs)` |
| `fluid(22,42)` | `var(--heading-lg-fs)` |
| `fluid(24,60)` | `var(--heading-xl-fs)` |
| `fluid(28,72)` | `var(--display-xs-fs)` |
| `fluid(32,84)` | `var(--display-sm-fs)` |
| `fluid(36,110)` | `var(--display-md-fs)` |
| `fluid(40,120)` | `var(--display-lg-fs)` |
| `fluid(48,148)` | `var(--display-xl-fs)` |

**금지:**
- `font-size: 1.8rem` — 하드코딩 rem 직접 지정
- `font-size: fluid(14, 18)` — fluid() 직접 호출 (CSS 변수에 이미 정의됨)
- `font-size: 14px` — px 고정값
- `<div class="imms-container">` — 삭제된 커스텀 컨테이너

**예외 허용:**
- `font-size: inherit` / `font-size: 1em` — 상속 표현
- 아이콘(`i`, `.icon`) 요소의 font-size — 아이콘 크기 제어 목적
- `resources/views/guide/` — 시연 페이지

**작업 후 체크:**
```bash
grep -rn "font-size" resources/scss/home/ --include="*.scss" | grep -v "var(--" | grep -v "abstract/_typography" | grep -v "base/_common" | grep -v "font-size: inherit" | grep -v "font-size: 0"
grep -rn "imms-container" resources/views/ --include="*.php"
```
