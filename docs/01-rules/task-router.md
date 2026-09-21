# Task Router — 작업 유형 판별 알고리즘

> CLAUDE.md **Level 2 Workflow** 의 상세 본문. 명령을 받으면 가장 먼저 이 알고리즘으로 **번들 선택 (UI / DEV / RISK / SERVER)** 을 결정한 뒤, 해당 번들만 로드한다.

## 1. 알고리즘 (1초 안에 결정)

```
입력: 사용자 명령 + 언급된 파일 경로/키워드 + 현재 작업 영역
출력: 적용할 번들 ID 집합 ⊆ {UI, DEV, RISK, SERVER}

1) 파일 경로 매칭 (가장 강한 신호)
   • resources/views/**, resources/scss/**, resources/ts/components/**, resources/ts/admin/components/**  ⇒ UI
   • app/**, routes/**, database/migrations/**, database/seeders/**, config/**, tests/**                ⇒ DEV
   • .env*, docs/00-overview/server-spec.md, 배포 스크립트, Apache vhost, CI 설정                       ⇒ SERVER

2) 키워드 매칭 (경로가 모호하거나 신규 파일일 때)
   • UI    : 마크업·뷰·블레이드·SCSS·컴포넌트·폼 필드·input·select·textarea·버튼·테이블·모달·드로어·헤더·푸터·색·폰트·간격·여백·spacing·padding·margin·rem·px·타이포·반응형·아이콘·시안·디자인·브랜딩·로고
     ※ "간격·여백·spacing·padding·margin·rem·px" 매칭 시 **#18 (Measurement Before Claim) 강제 적용** — 답변에 `[측정 검증]` 블록 의무 포함.
   • DEV   : Controller·Service·Action·Repository·Model·Migration·Seeder·Factory·Policy·Gate·Middleware·Request·DTO·Enum·Route·라우트·컨트롤러·서비스·모델·트랜잭션·정책·권한·기능 토글·feature flag
   • RISK  : destroy·delete·bulk-destroy·logout·sessions.revoke·backups.{delete,restore}·delete-account·취소·환불·결제 취소·세션 강제 종료·권한 박탈·DELETE 라우트·PATCH 가 불가역
   • SERVER: 배포·deploy·서버·인프라·도메인·DNS·SSL·TLS·인증서·.env·환경 변수·backup·복원·rollback·migration 러너·Apache·MySQL/MariaDB·CI·로그·모니터링·SSH

3) 교차 규칙
   • RISK 매칭 시 — 거의 항상 UI 또는 DEV (또는 둘 다) 와 함께 적용
   • SERVER 매칭 시 — 마이그레이션·기능 토글 등은 DEV 와 함께 적용
   • UI ∩ DEV — CRUD 폼/관리자 페이지: 라우트(#4) + 폼 마크업(#8 #1 #6) 동시
   • 어느 매칭도 없으면 — 명령을 사용자에게 1회 되묻는다 (AskUserQuestion). 추측 금지.

4) 과대 적용 우선 (모호할 때)
   • 두 번들이 모두 그럴듯 → 둘 다 로드. 작업 종료 보고에서 "읽었으나 적용 0건" 인 번들은 다음 회 라우터 키워드에서 제거 후보로 §5 에 기록.
```

## 2. 결정 결과 선언 (필수)

#16 의 "[변경 프로세스 단계]" 선언 **바로 다음 줄** 에 다음 1줄을 추가한다.

```
[번들 선택] UI 또는 DEV 또는 RISK 또는 SERVER 중 선택 (복수 가능) — 근거: <키워드 또는 파일 경로 1~2개>
```

예시:
```
[번들 선택] UI + RISK — 근거: resources/views/admin/users/index.php 의 삭제 행 액션 폼 작성
[번들 선택] DEV       — 근거: routes/admin.php 에 PATCH /users/{id}/role 추가
[번들 선택] SERVER + DEV — 근거: database/migrations 신규 + config/platform/features.php 토글 등록
```

선언 누락이거나 근거가 비면 **즉시 작업 중단**.

## 3. 번들 로드 후 의무

1. 해당 번들 파일 (`docs/01-rules/by-task/{ui,dev,risk,server}.md`) §1 트리거 §2 핵심 결정 §3 포함 규칙 표 를 읽는다.
2. §3 표의 규칙 중 **이번 작업과 실제로 닿는 것만** `details/rule-NN-slug.md` 를 펼친다. 닿지 않는 규칙은 펼치지 않는다.
3. §4 PRE 체크 명령을 실행한다.
4. 구현 후 §5 POST 체크 명령을 실행해 0건 (또는 의도 명시) 확인.
5. 완료 보고는 `change-process.md §8` 의 **실행 기반 보고** 양식에 따라 작성 — "적용한 규칙 / 실제 확인한 파일 / 실제 수행한 영향 분석 / 실제 수행한 검증 / 남은 리스크".

## 4. 관련 없는 번들은 읽지 마라

- UI 작업인데 `dev.md` 의 RESTful·migration·trasaction 규칙 본문을 끌어 읽지 않는다.
- 서버 작업인데 `ui.md` 의 typography·lenis·인라인 style 본문을 끌어 읽지 않는다.
- "다 검토했다"는 일반 진술은 #16 §9 금지 사항에 해당. 적용 안 한 규칙을 보고에 적지 않는다.

## 5. 자기 점검 로그 (선택, 누적)

작업 종료 시 라우터가 잘 작동했는지 한 줄 메모를 남기면 다음 회 정확도가 올라간다. (저장은 patch-notes 또는 별도 노트, 본 문서 자체에 누적하지 않는다.)

```
YYYY-MM-DD HH:MM  [번들 선택 X] [실제 적용 Y] [라우터 정확도: 정확/과대/누락]  비고
```

## 6. 빠른 예시

| 사용자 명령 | 선언 |
|---|---|
| "헤더 알림 pill 색상 검증해줘" | `[번들 선택] UI — 근거: 알림 pill = #7 헤더 가독성` |
| "삭제 폼 컴포넌트 만들고 라우트 추가" | `[번들 선택] UI + DEV + RISK — 근거: admin.components.delete-form 신규 + DELETE 라우트` |
| "feature_flags 에 boards.calendar 추가" | `[번들 선택] DEV + SERVER — 근거: config/platform/features.php + 배포 게이트` |
| "Apache vhost 에 도메인 추가" | `[번들 선택] SERVER — 근거: docs/00-overview/server-spec.md 도메인 변수` |
| "Hero 슬로건 카피 변경" | `[번들 선택] UI — 근거: resources/views/pages/home.php` |
| "backups.restore 컨트롤러 작성" | `[번들 선택] DEV + RISK + SERVER — 근거: 백업 복원 = 위험 액션 + 운영` |
