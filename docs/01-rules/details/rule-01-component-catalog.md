# 절대 규칙 #1 — UI 작업의 첫 도구 호출 = 컴포넌트 카탈로그

> 본문 출처: 구 CLAUDE.md 절대 규칙 #1 (원문 보존)
> 소속 번들: `by-task/ui.md`

**퍼블·디자인·관리자/공개 UI 작업이면 코드 한 줄 쓰기 전에 반드시 `docs/03-constants/components.md` Read.** 폼 요소(input·textarea·select·checkbox·radio·date·actions)는 `component('<type>', [...])` 헬퍼만, 관리자 UI 블록(nav-tabs·toggle-switch·uploader 등)은 `include_view('admin.components.<name>', ...)` 만. **임의 마크업·인라인 `<input>`·새 form 작성 금지.** 카탈로그에 없으면: `resources/views/{admin/components,admin/form,components}/` 더블체크 → 정말 없으면 신규 생성 + `_<name>.scss` + `components/index.scss` `@forward` + 카탈로그 한 줄 등록 + `/console/components` 쇼케이스 블록까지 한 세트로. 성격이 다르면 prop/modifier 로 기존 컴포넌트 확장. 이 규칙 위반은 반복 지적된 사항. (메모리 `component-reuse-mandatory.md`)

**⛔ 특별 금지 — raw `<select>` 절대 금지 (반복 위반):**
- 폼 select → `component('select', [...])` 필수. `<select>` 태그 직접 작성 즉시 규칙 위반.
- pagination per-page select 포함 모든 select → `admin-select` 클래스 + `data-per-page-nav` 속성 패턴으로 JS 위임.
- `class="admin-select"` 없는 select = SlimSelect 미적용 = 네이티브 브라우저 select 노출 = 금지.
- **예외 없음** — showcase/demo 페이지(`/console/components`) 내 raw select는 유일한 허용 위치.
