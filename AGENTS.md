# AGENTS.md — 외부 에이전트 도구용 지침

> 이 파일은 **Cursor·Codex·기타 Claude Code 외 에이전트 도구**가 읽는 표준 진입점입니다.
> Claude Code 는 `CLAUDE.md` 를 진입점으로 사용하며, 그쪽이 정본입니다.
> 두 파일은 동일한 **4-레벨 규칙 시스템**을 강제합니다. 충돌 시 `CLAUDE.md` 가 우선.

## 4-레벨 구조 한눈에 보기

```
Level 1  Global Core (항상 적용) ─ 절대 규칙 #16 변경 작업 7단계 프로세스
Level 2  Workflow              ─ 작업 유형 분류 → 번들 로드 → 실행 기반 보고
Level 3  Task Bundle           ─ UI / DEV / RISK / SERVER 중 해당 번들만
Level 4  Detail                ─ 번들이 가리킬 때만 개별 규칙 본문
```

## 정본 문서

- **Level 1 본문**: `docs/01-rules/change-process.md` — 7단계 + 실행 기반 보고 양식
- **Level 2 알고리즘**: `docs/01-rules/task-router.md`
- **Level 3 번들**: `docs/01-rules/by-task/{ui,dev,risk,server}.md`
- **Level 4 본문**: `docs/01-rules/details/rule-NN-slug.md`
- **부속**: `docs/01-rules/workflow.md` · `parallel-work.md` · `must-check.md` · `change-impact.md` · `design-pipeline.md`

---

# Level 1 — Global Core (항상 적용)

## 절대 규칙 #16 — 변경 작업 7단계 프로세스 [최상위]

**코드를 바로 수정하지 않는다.** 모든 변경 작업은 예외 없이 아래 7단계를 순서대로 거친다.

```
① 요구사항 재정의 → ② 영향 범위 분석 → ③ 변경 계획 작성
→ ④ 구현 → ⑤ 검증 → ⑥ 문서 업데이트 → ⑦ 작업 히스토리 기록
```

### 작업 시작 전 필수 선언 (2줄)

```
[변경 프로세스 단계] <①~⑦ 중 현재 단계> — 다음으로 넘어가는 조건: <짧게>
[번들 선택]          <UI · DEV · RISK · SERVER 중 선택, 복수 가능> — 근거: <키워드 또는 파일 경로>
```

선언 누락이거나 근거가 비면 **즉시 작업 중단**. 추측 금지.

### 각 단계 최소 의무

- ① 요구사항 재정의 → "원하는 것 / 현재 문제 / 기대 동작 / 손대지 말 범위 / 확인된 제약(DB·UI·API·권한·i18n·배포)" 6줄 이상 정리.
- ② 영향 범위 분석 → Backend·Frontend·Data·i18n·Test·Docs **6개 카테고리 전부 점검**.
- ③ 변경 계획 작성 → 수정 파일·연관 파일·호출 흐름·DB 영향·기존 기능 영향·리스크·테스트 계획·문서 업데이트 대상 **8섹션 양식**.
- ④ 구현 → 부분 수정 금지. 연관 의존 파일 동반 확인.
- ⑤ 검증 → 기능·코드 정합성·데이터 안정성·UI·테스트 **5블록 체크리스트** 0건 위반.
- ⑥ 문서 업데이트 → 변경된 기능 관련 부분만.
- ⑦ 작업 히스토리 기록 → 10항목 양식.

### 실행 기반 보고 (요약 금지) — 강제

```md
## 적용한 규칙
- #N (rule-slug): 어떤 결정에 어떻게 적용했는지 1줄.

## 실제 확인한 파일
- path:line — 무엇을 봤나 / 왜 봤나

## 실제 수행한 영향 분석
- Backend / Frontend / Data / i18n / Test / Docs — 각 카테고리별 확인 파일

## 실제 수행한 검증
- 명령어 → 결과 1줄

## 남은 리스크
- 구체 리스크 + 완화 방안
```

**금지:**
- ❌ "절대 규칙 모두 검토했습니다" 같은 일반 진술
- ❌ "체크리스트 통과" 만 적고 항목·파일 미명시
- ❌ 검증 명령어 없이 "확인했습니다" 만 기재
- ❌ 요구사항 재정의 없이 바로 코드 수정
- ❌ 변경 계획 없이 구현 진입
- ❌ 문서·작업 히스토리 갱신 없이 종료

상세 절차·양식·체크리스트 전부: `docs/01-rules/change-process.md`
요구사항 변경 시 5단계 파급분석: `docs/01-rules/change-impact.md`

---

# Level 2 — Workflow (작업 받으면 가장 먼저)

1. **작업 유형 분류** — `docs/01-rules/task-router.md` 알고리즘으로 UI / DEV / RISK / SERVER 중 선택 (복수 가능).
2. **해당 번들만 로드** — `docs/01-rules/by-task/{ui,dev,risk,server}.md`. 관련 없는 번들·규칙 본문은 읽지 않는다.
3. **번들 PRE 체크 실행** → 구현 → 번들 POST 체크 실행 (0건 위반 또는 의도 명시).
4. **실행 기반 보고** (Level 1).

부속: `workflow.md` · `parallel-work.md` · `must-check.md`.

---

# Level 3 — 작업 유형별 규칙 번들 (조건부 로드)

| 유형 | 대표 트리거 | 번들 | 포함 규칙 |
|---|---|---|---|
| **UI / 디자인 / 퍼블** | `resources/views/**`, `resources/scss/**` · 마크업·폼·색·간격·타이포 | `docs/01-rules/by-task/ui.md` | #1 #3 #5 #6 #7 #10 #11 #12 #13 #15 #17 #19 (+ #2 교차) |
| **개발 / 백엔드 / DB** | `app/**`, `routes/**`, `database/**`, Controller·Service·Model | `docs/01-rules/by-task/dev.md` | #2 #4 #8 #9 |
| **위험 액션** | destroy · delete · bulk-destroy · logout · sessions.revoke | `docs/01-rules/by-task/risk.md` | #8 #14 |
| **서버 / 배포 / 인프라** | deploy · `.env` · server-spec · backup · SSL · domain | `docs/01-rules/by-task/server.md` | (절대 규칙 없음 — `server-spec.md` · `conventions.md §git` 인용) |

**관련 없는 번들은 읽지 마라.** 두 번들 이상 매칭되면 모두 적용 (과대 적용 우선).

---

# Level 4 — 개별 규칙 (번들이 가리킬 때만)

| # | 한 줄 요지 | 1차 번들 | 상세 |
|---|---|---|---|
| 1 | 컴포넌트 카탈로그 먼저 · raw `<select>` 금지 | UI | `docs/01-rules/details/rule-01-component-catalog.md` |
| 2 | 서드파티 라이브러리는 래퍼 통해서만 | DEV (UI 교차) | `docs/01-rules/details/rule-02-third-party-wrapper.md` |
| 3 | UI 색상 60:30:10 | UI | `docs/01-rules/details/rule-03-color-60-30-10.md` |
| 4 | RESTful HTTP 메서드 | DEV | `docs/01-rules/details/rule-04-restful.md` |
| 5 | 인라인 `style=` 금지 | UI | `docs/01-rules/details/rule-05-inline-style.md` |
| 6 | 폼 컴포넌트 완전 매핑 | UI | `docs/01-rules/details/rule-06-form-component-mapping.md` |
| 7 | 헤더 알림 pill 가독성 | UI | `docs/01-rules/details/rule-07-header-pill.md` |
| 8 | AJAX `_method` 스푸핑 | DEV (RISK 교차) | `docs/01-rules/details/rule-08-method-spoof.md` |
| 9 | 기능 제거 = 토글 OFF | DEV | `docs/01-rules/details/rule-09-feature-removal-toggle.md` |
| 10 | SCSS = 뷰 클래스 1:1 + 라벨 간격 | UI | `docs/01-rules/details/rule-10-scss-class-1to1.md` |
| 11 | 타이포·컨테이너 유틸리티만 | UI | `docs/01-rules/details/rule-11-typography-utilities.md` |
| 12 | overflow 요소 `data-lenis-prevent` | UI | `docs/01-rules/details/rule-12-lenis-prevent.md` |
| 13 | 역할 다른 텍스트 별도 태그 | UI | `docs/01-rules/details/rule-13-text-role-tag-split.md` |
| 14 | Modal.confirm + AJAX 삭제 5요소 | RISK (UI 교차) | `docs/01-rules/details/rule-14-confirm-ajax-delete.md` |
| 15 | 디자인 4단계 파이프라인 | UI | `docs/01-rules/details/rule-15-design-pipeline.md` |
| 16 | 변경 작업 7단계 프로세스 | **Level 1 (always)** | `docs/01-rules/change-process.md` |
| 17 | 디자인시스템 유틸 강제 | **Level 1 (always)** | `docs/01-rules/details/rule-17-design-system-utility.md` |
| 18 | Measurement Before Claim (rem→px 환산) | **Level 1 (always)** | `docs/01-rules/details/rule-18-measurement-before-claim.md` |
| 19 | 뷰 렌더링 위임 — 페이지는 호출만 | **Level 1 (always)** | `docs/01-rules/details/rule-19-view-rendering-delegation.md` |

---

# 일반 절대 원칙 (모든 번들 공통)

- `sources/` 원본 수정·삭제 금지 · `docs/04-history/*` 추가만 · 비밀정보 하드코딩 금지
- 기능 동작 / 디자인·브랜딩 / 의존성 추가·제거 / 배포 = **사용자 확인 필요**
- 요구사항 변경 처리 · `docs/04-history/*` 쓰기 · 레이어 의존 체인 = **병렬 금지**
- 자기 역할 밖 임의 수정 금지 · 모호하면 질문 · 무관한 번들 안 읽기
