---
title: 신입 온보딩 매뉴얼
categories: [workflow]
desc: 프로젝트 템플릿의 전체 구조와 명령 처리 흐름을 5-10분에 파악하는 큰 그림 문서
related: [overview.goals, overview.claude-vs-codex]
updatedAt: 2026-05-13
---

# 신입 온보딩 매뉴얼

> 이 프로젝트(템플릿)가 어떻게 굴러가는지 한 번에 파악하는 문서. 5~10분.
> 여기선 **큰 그림 + "어디로 가야 하나"** 만 — 자세한 규칙은 각 문서로 링크. (이 문서가 길어지면 줄여라.)

---

## 1. 전체 구조

```
CLAUDE.md          ← AI가 매번 가장 먼저 읽는 "색인". 포인터만, 본문 없음.
README.md          ← 사람용 빠른 시작
docs/
  00-overview/     큰 그림: goals(목표)·server-spec(배포환경)·claude-vs-codex·onboarding(이 문서)
  01-rules/        규칙(회사 표준, 재사용): roles(역할표)·workflow(작업 순서·명령 접수)·parallel-work(여러 트랙 병렬 vs 직렬·서브에이전트 디스패치)·must-check(필수 체크 A~H)·change-impact(요구사항 변경 5단계)
  02-variables/    프로젝트마다 다름(재사용 시 여기만 수정): client-info(고객)·brand-guide(브랜드 토큰)·customizations(재사용·확장 가이드 + 전역/프로젝트 배치)
  03-constants/    재사용 상수(거의 안 바뀜): conventions(코딩·커밋·파일명)·standards(보안·접근성·성능)
  04-history/      이력(추가만, 수정 금지): patch-notes(작업)·requirement-changes(요청 변경)·decisions(왜 이렇게)
.claude/
  agents/          역할 전문가 6명: branding·planner·designer·publisher·developer·server-ops
  commands/        슬래시 커맨드: /task·/patch-note·/checklist·/change-impact·/sources-rename
sources/           고객이 준 원본. 절대 수정·삭제 금지. 받으면 바로 여기로.
workspace/         우리가 만든 것(코드·시안·결과물). 가공본만.
~/.claude/         (이 레포 밖) 내 개인·전역 설정 — 모든 프로젝트 공통. 레포엔 안 들어감. 무엇을 여기 vs 레포에 둘지 → customizations.md C
```

**원리 두 개만 기억하면 됨:**
1. **변수 vs 상수 분리** — 프로젝트마다 바뀌는 건 `docs/02-variables/`, 나머지(`01-rules`·`03-constants`·`agents`·`commands`)는 어느 프로젝트나 동일 → 새 프로젝트엔 변수 폴더만 손댐.
2. **색인 → 관련만 깊게 (데이터베이스처럼)** — `CLAUDE.md`가 색인. 명령이 들어오면 "이건 퍼블 일이네" 하고 `agents/publisher.md`만 깊게 읽고 나머진 안 읽음. 다 읽으면 토큰 낭비 + 헷갈림.

---

## 2. 어떻게 작동하나 — 명령 하나의 일생

```
사용자 명령
 │
 ├─① 분류        어느 역할? 브랜딩 / 기획 / 디자인 / 퍼블 / 개발 / 서버 (여러 개면 전부)
 │
 ├─② 명확도 판정
 │     · 명확 + 작음 (오타·문구·로그 추가)  →  바로 실행
 │     · 어느 정도 구체적                    →  "작업 명세" 양식으로 정리 → 빈칸·불확실만 사용자에게 확인 → 실행
 │     · 모호함                              →  추측 금지. 되묻기(AskUserQuestion) 또는 plan 모드(저토큰 탐색)
 │
 ├─③ 선택적 읽기  ← 여기가 핵심
 │     · 깊게(전체)   : ①에서 정한 역할의 .claude/agents/<role>.md  +  그 작업에 직접 관련된 docs
 │     · 훑기(compact): goals.md(목표 충돌?) · client-info.md(현 변수) · patch-notes 최근 3개
 │     · 안 읽음      : 무관한 역할 에이전트, 무관한 docs, 과거 히스토리
 │     · 체크         : must-check.md의 해당 카테고리만 — 작업 전(PRE) 점검
 │
 ├─④ 서브에이전트로 분산
 │     · 탐색량 많으면        →  탐색을 서브에이전트에 위임 (메인 대화를 안 더럽힘)
 │     · 여러 역할/트랙이면    →  각 트랙 쓰기 범위 선언 → 비겹침이면 병렬 디스패치(1트랙=1에이전트)
 │                              겹침·레이어 의존·공유상태·`04-history/*` 쓰기면 직렬 (parallel-work.md)
 │     · 끝나면              →  메인이 `git status`로 겹침 확인 → 통합 → 기록 1회
 │
 ├─⑤ 실행          역할 에이전트 지침 + 규칙(conventions·standards) 적용
 │
 └─⑥ 작업 후       must-check.md POST 점검(`/checklist`) → patch-notes.md에 1줄(`/patch-note`)
                   + 비자명한 결정 했으면 decisions.md
                   + 요구사항이 바뀐 거였으면 requirement-changes.md + change-impact 5단계
```

`/task <대충 설명>` 한 번이면 ①②③을 자동으로 돌려준다.

---

## 3. "~한 경우 → 어디 가서 뭘 하세요" (제일 자주 보게 될 표)

| 상황 | 어디로 | 무엇을 |
|---|---|---|
| **새 프로젝트를 시작한다** | `docs/02-variables/customizations.md` A | "필수 수정 파일" 표대로 채우기(프로젝트명·고객·목표·서버스펙·브랜드), 이력 3개 파일 비우기, `sources/`에 고객 자료 |
| **고객이 자료(PDF·이미지·로고·카피)를 줬다** | `sources/` 하위 폴더 + `must-check.md` E | 그대로 넣고 → `/sources-rename`(파일명을 `YYMMDD-HHMM-제목`으로 일괄 정리). 수정 금지. 가공본은 `workspace/`. 정리한 요구사항은 `client-info.md`에. ⚠️ `sources/`는 `.gitignore` — git에 안 올라가니 **클라우드 등 별도 백업 필수**(`sources/README.md`) |
| **고객 초기 요청사항이 바뀌었다** | `docs/04-history/requirement-changes.md` → `docs/01-rules/change-impact.md` | `/change-impact "무엇이 무엇으로"` → 기록·분류(어느 레이어)·영향 매핑·상위→하위 순서로 수정·통합검증. 상위 레이어(브랜딩 등)면 하위 전부 재검토 |
| **회사 규칙 하나를 바꾸고 싶다** (커밋 규칙·체크 항목 등) | `docs/01-rules/<해당>.md` 또는 `docs/03-constants/<해당>.md` | 그 파일에서 직접 수정. 다른 문서가 그 규칙을 참조하면(보통 `CLAUDE.md` §3 색인) 같이 갱신 |
| **새 규칙/정책을 추가하고 싶다** | `docs/01-rules/<새이름>.md` 새로 생성 | 단일 책임으로 작성 → `CLAUDE.md` §3 색인 + `customizations.md` B 표에 각 1줄 |
| **새 역할(에이전트)을 추가한다** | `.claude/agents/<role>.md` 새로 생성 | 책임·작업전 확인·산출물·금지 작성 → `CLAUDE.md` §2 역할표 + `docs/01-rules/roles.md` 매트릭스 갱신 |
| **자주 쓰는 명령을 커맨드로 만들고 싶다** | `.claude/commands/<name>.md` 새로 생성 | frontmatter(`description`) + 절차 작성. 끝 — 자동으로 `/name`으로 잡힘 |
| **브랜드 컬러/폰트/로고 규칙을 바꾼다** | `docs/02-variables/brand-guide.md` | 토큰 수정 → **브랜딩 변경**이므로 `change-impact.md` 5단계(디자인→퍼블→개발 전부 영향). `decisions.md`에 사유 |
| **마크업/스타일 작업을 한다** | 퍼블리셔 → `workspace/` | `agents/publisher.md`(섹션 골격·BEM·SCSS 유틸 우선) + `must-check.md` C |
| **기능/DB/API 작업을 한다** | 개발자 → `workspace/src/` | `agents/developer.md`(MVC+DDD·Aggregate Root·도메인 이벤트·softDelete·멱등성키…) + `must-check.md` B |
| **관리자 디자인 시안/레이아웃을 만든다** | 관리자 디자이너 → `workspace/design/` | `agents/designer-admin.md`(8pt 그리드·컴포넌트 상태 전부·핸드오프 스펙) |
| **홈페이지 디자인 시안/레이아웃을 만든다** | 홈페이지 디자이너 → `workspace/design/` | `agents/designer-brand.md`(8pt 그리드·컴포넌트 상태 전부·핸드오프 스펙) |
| **배포한다** | `agents/server-ops.md` + `must-check.md` G | 백업 확인·롤백 절차·staging 검증·사용자 승인 필수 |
| **작업을 끝냈다** | `docs/04-history/patch-notes.md` | `/patch-note` — 변경 파일·사유·영향 1줄 (기존 항목은 절대 수정 X) |
| **"왜 이렇게 결정했는지" 남겨야 한다** | `docs/04-history/decisions.md` | append. 코드만 봐선 모를 근거 |
| **뭘 시킬지 정리가 안 됐다 / 두루뭉술하다** | `/task <대충 설명>` | 작업 명세로 정리해주고, 모호하면 되물어줌 |
| **명령이 여러 역할에 걸친다 / 동시에 여러 개 시킨다** | `docs/01-rules/parallel-work.md` | 각 트랙 쓰기 범위 선언 → 비겹침이면 병렬(서브에이전트), 겹침·레이어 의존·공유상태·`04-history/*` 쓰기면 직렬. 끝나면 `git status`로 겹침 확인 후 통합. (`/task`가 판정) |
| **마이그레이션·SQL·일회성 스크립트·로그를 만든다** | 해당 작업 폴더 | 파일명 `YYMMDD-HHMM-제목.확장자` (`conventions.md` 파일명 규칙) |
| **이 템플릿을 다른 프로젝트에 쓴다** | `docs/02-variables/customizations.md` A | 폴더 통째 복사 → 변수 파일들만 교체 → `sources/` 새 자료 |
| **이걸 전역(`~/.claude/`)에 둘지 이 레포에 둘지 모르겠다** | `docs/02-variables/customizations.md` C | "다음 프로젝트에서도 똑같이 원하나?" 예→`~/.claude/`, 아니오→레포. 같은 규칙을 두 곳에 본문으로 쓰지 말 것 |
| **같은 걸 2번 잘못 고쳤다 / 대화가 꼬였다** | (대화 관리) | `/clear` 후 더 구체적인 명령으로 다시 시작 |

---

## 4. 신입이 자주 하는 실수

- ❌ `sources/`의 고객 원본을 수정·덮어쓰기 → 가공은 `workspace/`에서
- ❌ `docs/04-history/*`의 기존 항목 수정 → **추가만**
- ❌ 같은 정보를 두 문서에 → 인덱스 표(`CLAUDE.md` §3)만 갱신, 본문은 한 곳
- ❌ 모호한 명령을 추측으로 진행 → 되묻거나 plan 모드
- ❌ 무관한 문서까지 다 읽기 → 명령 분류 후 관련 역할만 깊게, 나머진 훑거나 패스
- ❌ 병렬로 돌린 두 트랙이 같은 파일/히스토리에 동시에 씀 → 각 트랙 쓰기 범위 선언, 겹치면 직렬, 히스토리는 메인이 1회 (`parallel-work.md`)
- ❌ `CLAUDE.md`에 본문을 길게 쓰기 → 포인터만, 본문은 `docs/`

---

## 5. 더 깊이 알아야 하면

- 명령 접수·작업 순서·우선순위·의사결정 권한 → `docs/01-rules/workflow.md`
- 여러 역할/작업 병렬 vs 직렬·서브에이전트 디스패치 → `docs/01-rules/parallel-work.md`
- 필수 체크리스트 (A~H, 작업 전/후) → `docs/01-rules/must-check.md`
- 요구사항 변경 5단계 → `docs/01-rules/change-impact.md`
- 역할별 전문 지침 → `.claude/agents/<role>.md`
- 코딩 컨벤션·커밋·파일명 → `docs/03-constants/conventions.md`
- 재사용·확장 → `docs/02-variables/customizations.md`
