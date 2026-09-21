# 절대 규칙 #2 — 서드파티 라이브러리 래퍼 패턴 (개발·퍼블 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #2 (원문 보존)
> 소속 번들: `by-task/dev.md` (UI 작업에서도 참조됨 → `by-task/ui.md` 교차 인용)

**SlimSelect·Flatpickr·Quill·Pickr 등 서드파티 라이브러리는 직접 `import`/`new` 금지.** 반드시 `resources/ts/components/<LibName>Wrapper.ts`(또는 `DatePicker.ts`, `SelectWrapper.ts` 등 기존 래퍼)를 통해서만 사용한다. 래퍼 파일 안에서만 라이브러리 import 허용. 외부 코드는 래퍼 타입·메서드만 참조.

현재 래퍼:
- `SelectWrapper.ts` — SlimSelect
- `DatePicker.ts` — Flatpickr
- `QuillEditor.ts` — Quill
- `PickrWrapper.ts` — @simonwep/pickr

새 라이브러리 추가 시 래퍼 파일 먼저 생성 후 코드 작성.
