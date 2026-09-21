---
title: "Claude Code vs Codex — 도구 선택 가이드"
categories: [workflow]
desc: 프로젝트 작업 성격에 따른 Claude Code와 Codex 도구 선택 비교 가이드
related: [overview.onboarding, overview.goals]
updatedAt: 2026-05-13
---

# Claude Code vs Codex — 도구 선택 가이드

> 이 프로젝트의 작업 성격에 어떤 도구가 맞는지 비교. 작업 시작 전 도구 선택 참고용.

## 한 줄 비교

| 측면 | Claude Code | Codex (OpenAI) |
|---|---|---|
| 강점 | 긴 컨텍스트, 문서 기반 작업, 에이전트/서브에이전트 | 빠른 코드 생성, IDE 통합 |
| 약점 | 응답 속도 (모델에 따라) | 긴 문맥 추적, 멀티 파일 일관성 |
| 적합 작업 | 다파일 리팩토링, 문서/규칙 기반 작업, 브랜드/디자인 일관성 | 단발 코드 스니펫, 빠른 자동완성 |

## 작업별 권장

| 작업 종류 | 권장 도구 |
|---|---|
| 브랜딩/기획/문서 작업 | **Claude Code** (긴 문맥, 규칙 준수) |
| 디자인 가이드 일관성 검사 | **Claude Code** |
| 새 컴포넌트 빠르게 1개 작성 | Codex 또는 Claude Code |
| 다파일에 걸친 리팩토링 | **Claude Code** |
| 단순 자동완성 | Codex |
| 요구사항 변경 파급효과 분석 | **Claude Code** (이 템플릿 활용) |

## 작업물 퀄리티 평가 기준

도구 비교 시 다음을 확인:
1. **규칙 준수**: `docs/01-rules/`, `docs/03-constants/`의 규칙을 지켰는가
2. **일관성**: 여러 파일이 서로 잘 맞물리는가
3. **컨벤션**: 코드/네이밍 컨벤션 준수
4. **누락**: must-check.md 항목 빠짐 없는지
5. **사이드이펙트**: 다른 영역 영향 인지하고 작업했는지

## 결론

이 템플릿(문서 기반 + 역할 분담 + 히스토리 추적)은 **Claude Code에 최적화**되어 있다.
다파일/규칙 의존 작업이 많을수록 Claude Code의 강점이 두드러진다.
