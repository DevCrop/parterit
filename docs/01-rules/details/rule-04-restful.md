# 절대 규칙 #4 — RESTful HTTP 메서드 준수 (개발·퍼블 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #4 (원문 보존)
> 소속 번들: `by-task/dev.md`

**라우트를 추가·수정할 때 반드시 `docs/03-constants/conventions.md §RESTful` 을 따른다.**

핵심 금지 패턴:
- `POST /{id}/update` → **`PATCH /{id}`** 로
- `POST /{id}/destroy` → **`DELETE /{id}`** 로
- `POST /bulk-destroy` → **`DELETE /bulk`** 로
- `POST /order` → **`PATCH /reorder`** 로
- `POST /move` (다중) → **라우트명 `move`, body `{ ids, mode }`** 유지 가능하나 메서드는 POST 허용

**TS 파일 안에 `/console/...` URL 하드코딩 절대 금지** — HTML `data-*` 속성으로 전달한다.

상세 기준: `docs/03-constants/conventions.md` "RESTful API 라우팅 규칙" 절.
