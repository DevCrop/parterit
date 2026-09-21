# 🚨🚨🚨 절대 규칙 #6 — 폼 컴포넌트 **완전 매핑 체크** (퍼블·개발 공통) — 반복 위반 최다

> 본문 출처: 구 CLAUDE.md 절대 규칙 #6 (원문 보존)
> 소속 번들: `by-task/ui.md`

**폼 필드를 작성하기 전·후 반드시 아래 매핑표를 보고 올바른 컴포넌트인지 확인한다. 임의 `<input type="number">` 등 raw 태그 또는 틀린 타입 사용은 즉각 위반.**

| 입력 유형 | 올바른 컴포넌트 | 자주 잘못 쓰는 패턴 (금지) |
|---|---|---|
| 한 줄 텍스트 | `component('input', [...])` | raw `<input>` |
| 여러 줄 텍스트 | `component('textarea', ['rows'=>N, ...])` — **rows 최소값: 요약 5·설명 8·기타 5** | raw `<textarea>`, rows=2~3 |
| 숫자 (+/- 버튼) | `component('numberStepper', [...])` | `component('input', ['type'=>'number'])` ← **이게 바로 반복 실수** |
| 날짜/일시 | `component('date', [...])` / `component('datetime', [...])` | raw `<input type="date">` |
| 단일 선택 | `component('select', [...])` | raw `<select>` ← 절대 금지 |
| 복수 선택 | `component('multiSelect', [...])` | raw `<select multiple>` |
| 체크박스 | `component('checkbox', [...])` | raw `<input type="checkbox">` |
| 라디오 그룹 | `component('radioGroup', [...])` | raw `<input type="radio">` |
| 글자수 제한 textarea | `component('charCounter', [...])` | 일반 textarea |
| 색상 입력 | `colorPickerField` (TS) | raw `<input type="color">` |

**작업 전(PRE) 체크:**
1. 이 매핑표와 `docs/03-constants/components.md` 섹션 1 대조
2. `component('input', ['type'=>'number'])` → `numberStepper` 인지 확인
3. textarea rows 값 — 요약형 최소 5, 설명형 최소 8

**작업 후(POST) 체크:**
1. 파일 내 `<input`, `<textarea`, `<select` 를 Grep → 허용된 위치(showcase 페이지 `/console/components` 제외) 외 raw 태그 있으면 즉시 컴포넌트로 교체
2. `/console/system/ui-audit` 위반 0건 확인
