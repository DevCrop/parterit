# by-task — 작업 유형별 규칙 번들

> CLAUDE.md 의 **Level 3** 진입점. 작업 유형이 정해진 뒤에만 해당 번들을 열고, 관련 없는 번들은 읽지 않는다.

## 4-레벨 도식

```
Level 1  Global Core (항상 적용, 매우 짧음)
         └─ 절대 규칙 #16 변경 작업 7단계 프로세스 + 실행 기반 보고
            본문: docs/01-rules/change-process.md

Level 2  Workflow (작업 받으면 가장 먼저)
         └─ ① 작업 유형 분류 → ② 해당 번들만 로드 → ③ 실행 기반 보고
            본문: docs/01-rules/task-router.md · workflow.md · parallel-work.md · must-check.md

Level 3  작업 유형별 규칙 번들 (이 디렉터리)
         ├─ ui.md      UI / 디자인 / 퍼블
         ├─ dev.md     개발 / 백엔드 / DB
         ├─ risk.md    위험 액션 (삭제·인증·배포)
         └─ server.md  서버 / 배포 / 인프라

Level 4  개별 규칙 본문 상세 (번들이 가리킬 때만)
         └─ docs/01-rules/details/rule-NN-slug.md  (총 15개)
```

## 번들 선택 결정 트리

```
작업 키워드·파일 경로를 본다
│
├─ resources/views, resources/scss, resources/ts/components,
│  마크업·폼·색·간격·타이포·컴포넌트  ⇒  ui.md
│
├─ app/, routes/, migrations/, Controller·Service·Model·Policy·Request
│                                  ⇒  dev.md
│
├─ destroy·delete·bulk-destroy·logout·sessions.revoke·
│  backups.{delete,restore}        ⇒  risk.md      (+ dev.md 또는 ui.md 동시)
│
└─ deploy·.env·server-spec·backup·SSL·domain·migration runner
                                   ⇒  server.md
```

**복수 매칭 시 모두 적용.** 모호하면 `task-router.md` §3 의 "과대 적용 우선" 규칙을 따른다.

## 각 번들 표준 양식 (6 섹션)

1. 언제 이 번들을 읽나 (트리거 조건)
2. 이 번들의 핵심 결정 (3~5 줄 요약)
3. 포함 규칙 표 (규칙 번호 → 한 줄 요지 → details 링크)
4. PRE 체크 (작업 전, 실행 가능한 명령)
5. POST 체크 (작업 후, 0건 위반 검출 명령)
6. 다른 번들과의 교차 (예: UI ∩ RISK = 삭제 폼 UI)

## 규칙 번호 → 번들 매핑 (전체)

| # | 규칙 한 줄 | 1차 번들 | 2차 번들 |
|---|---|---|---|
| 1 | 컴포넌트 카탈로그 | ui | — |
| 2 | 서드파티 래퍼 | dev | ui |
| 3 | 60:30:10 컬러 | ui | — |
| 4 | RESTful HTTP 메서드 | dev | — |
| 5 | 인라인 style 금지 | ui | — |
| 6 | 폼 컴포넌트 매핑 | ui | — |
| 7 | 헤더 pill 가독성 | ui | — |
| 8 | AJAX `_method` 스푸핑 | dev | risk |
| 9 | 기능 제거 = 토글 OFF | dev | — |
| 10 | SCSS = 뷰 클래스 1:1 | ui | — |
| 11 | 타이포·컨테이너 유틸리티 | ui | — |
| 12 | Lenis prevent | ui | — |
| 13 | 텍스트 역할 태그 분리 | ui | — |
| 14 | Modal.confirm + AJAX 삭제 | risk | ui |
| 15 | 디자인 4단계 파이프라인 | ui | — |
| 16 | 변경 작업 7단계 프로세스 | **Level 1 (always)** | — |
