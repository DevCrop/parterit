---
title: 템플릿 커스터마이징 / 확장
categories: [dev, design]
desc: 템플릿을 새 프로젝트로 재사용하거나 확장할 때 수정해야 할 파일과 절차
related: [variables.client-info, variables.brand-guide]
updatedAt: 2026-06-05
---

# 템플릿 커스터마이징 / 확장

> 이 템플릿을 ① 새 프로젝트로 재사용할 때 ② 확장할 때 — 어디를 건드리는지 한 곳에 모음.

## A. 새 프로젝트로 재사용

### 필수 수정 파일

| 파일 | 수정 내용 |
|---|---|
| `CLAUDE.md` §1 | 프로젝트명, 고객사, 기술 스택, 산출물 형태 |
| `docs/00-overview/goals.md` | 목표, 성공 기준, 비목표 |
| `docs/00-overview/server-spec.md` | 배포환경, 도메인, 인프라 |
| `docs/02-variables/client-info.md` | 고객 정보 전체 |
| `docs/02-variables/brand-guide.md` | 컬러, 타이포, 로고 |
| `docs/04-history/patch-notes.md` | **비우기** (frontmatter + H1 만 남기고 항목 삭제) |
| `docs/04-history/requirement-changes.md` | **비우기** |
| `docs/04-history/decisions.md` | **비우기** |
| `sources/` | 새 고객 자료로 교체 |

### 그대로 두는 것 (재사용 상수)

`docs/01-rules/*` · `docs/03-constants/*` · `.claude/agents/*` · `.claude/commands/*`

### 절차

1. 이 템플릿 폴더를 통째로 복사 → 새 프로젝트명으로 변경
2. `git init` · `.env` 작성 · 의존성 설치
3. 위 "필수 수정 파일" 채우기 (`sources/` 에 고객 자료 넣고 "이거 보고 goals.md·client-info.md 채워줘" 하면 됨)
4. `CLAUDE.md` §1 검토 → 작업 시작

## B. 템플릿 확장 (새 문서·역할·커맨드)

| 추가할 것 | 어디에 만드나 | 같이 갱신 |
|---|---|---|
| 새 규칙 / 정책 | `docs/01-rules/<name>.md` | `CLAUDE.md` §3 색인 |
| 새 프로젝트 변수 | `docs/02-variables/<name>.md` | A의 "필수 수정 파일" 표 |
| 재사용 표준 | `docs/03-constants/<name>.md` | — |
| 새 역할 / 에이전트 | `.claude/agents/<role>.md` | `CLAUDE.md` §2 역할표, `docs/01-rules/roles.md` |
| 새 슬래시 커맨드 | `.claude/commands/<name>.md` | — |
| 새 검증 항목 | `docs/01-rules/must-check.md` 해당 카테고리 | — |

> **원칙**: 한 정보는 **한 곳에만**. 새 문서는 **단일 책임**.

## C. 전역(`~/.claude/`) vs 프로젝트(이 레포) — 어디에 두나

> 판정 한 줄: **"다음 프로젝트에서도 똑같이 원하나?"** — 예 → `~/.claude/` / 아니오 → 이 레포.

| 내용 | 어디 | 왜 |
|---|---|---|
| 개인 작업 습관·전역 권한·전 프로젝트 공통 커맨드 | `~/.claude/` | 어느 레포를 열든 적용 |
| 이 프로젝트의 규칙·역할·에이전트·커맨드 | 이 레포 `.claude/` | 팀이 공유·버전관리 |
| 이 고객 변수(고객정보·브랜드·목표·서버) | 이 레포 `docs/02-variables/`·`docs/00-overview/` | 프로젝트마다 다름 |
