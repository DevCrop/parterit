# 절대 규칙 #7 — 헤더 위 알림 pill 및 텍스트 가독성 (퍼블·개발 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #7 (원문 보존)
> 소속 번들: `by-task/ui.md`

**헤더 위 모든 알림 pill·배지·텍스트는 헤더 배경색(그라디언트/단색/검정)에 무관하게 항상 가시해야 한다.**

- pill 기본(0개) 상태: `background: rgba(255,255,255,0.95)` + `color: #111111` 고정 — CSS 변수 사용 금지
- pill 미읽음 상태: `background: #ffffff` + `color: #c81e1e` 고정
- 헤더 배경 토큰(`--admin-header-bg`)이 단색 검정(#000000 계열)인 테마는 border, pill, 아이콘 색상을 별도로 검증해야 함
- 새 테마 추가 시 반드시 `/console/system/theme-audit`에서 헤더 영역 체크
- `--admin-header-notifications-pill-*` 토큰은 테마 참조/감사 목적으로만 유지, pill 실렌더에 `var()` 직접 사용 금지
