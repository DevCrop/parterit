# 절대 규칙 #5 — 인라인 style= 금지 + UI 감사 페이지 필수 확인 (퍼블·개발 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #5 (원문 보존)
> 소속 번들: `by-task/ui.md`

**HTML/PHP 파일에 `style=` 속성 직접 작성 절대 금지.** 동적 값(컬러 칩 등)은 CSS custom property(`--var-name`)로 전달한다.

핵심 금지 패턴:
- `style="display:none"` → HTML `hidden` 속성 또는 `.admin-form__loader` CSS 제어
- `style="margin-bottom:.5rem"` → `admin-form__hint--below` 등 기존 CSS 클래스
- `style="max-width:10rem"` → `admin-form__field--narrow` 등 기존 CSS 클래스
- `style="background:#ff0000"` → `style="--chip-color:#ff0000"` + CSS `background: var(--chip-color)`
- component() 파라미터 `'style' => '...'` → `'fieldClass'`/`'inputClass'` + CSS 클래스

**퍼블·개발 작업 후 `/console/system/ui-audit` 에서 위반 항목 확인 필수.** Super Admin 전용 페이지. 위반 0건이 될 때까지 수정한다.
