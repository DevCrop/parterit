---
title: 회사 표준 (재사용 가능 상수)
categories: [dev, seo, design]
desc: 보안/접근성/성능/브라우저 지원/백업 등 회사 차원에서 정한 재사용 표준
related: [constants.architecture, constants.conventions, constants.components]
updatedAt: 2026-05-13
---

# 회사 표준 (재사용 가능 상수)

> 회사 차원에서 정한 표준. 프로젝트마다 동일하게 적용.

## UI 색상 60:30:10 법칙

> 이 규칙은 관리자·홈페이지 모든 UI에 적용. 위반 시 CLAUDE.md ⛔ 절대 규칙 #3.

| 비율 | 용도 | 사용 가능 값 |
|------|------|------------|
| **60%** | 배경·캔버스 | `--admin-white`, `--admin-grey-50`, `--admin-grey-100` |
| **30%** | 보조 요소 | `--admin-border`, `--admin-text-secondary`, `--admin-grey-200~400` |
| **10%** | 포인트·CTA | `--admin-primary`, 배지·활성 탭·강조 보더 등 |

**금지 패턴:**
- 테이블 행 배경 전체를 `--admin-primary-light`(파랑)으로 채우기 → 대신 `--admin-grey-50` + 왼쪽 보더 3px primary
- 카드 배경 전체를 primary 계열로 채우기 → 대신 흰색 배경 + border-left 강조
- 섹션·패널 배경에 파랑 계열 사용 → 흰색/회색만 허용

**허용 패턴 (10% 안에서):**
- 버튼 `admin-btn--primary` 배경
- 활성 탭 border-bottom
- 현재 항목 표시용 왼쪽 보더 (`border-left: 3px solid var(--admin-primary)`)
- 뱃지·레이블 텍스트+아웃라인 조합

---

## 보안 표준
- 비밀 정보는 `.env` 또는 비밀 관리 도구. 절대 커밋 금지.
- 의존성은 분기마다 보안 점검.
- 인증/인가 코드는 별도 모듈로 분리.

## 접근성
- 모든 이미지에 `alt` 속성
- 키보드 네비게이션 보장
- 명도 대비 WCAG AA 이상

## 성능 기준
- 초기 로딩 3초 이내 (4G 기준)
- Lighthouse 점수 90+ 목표

## 브라우저 지원
- 최신 Chrome, Safari, Firefox, Edge
- iOS Safari, Android Chrome 최신 2버전

## 백업 / 버전 관리
- main 브랜치는 항상 배포 가능 상태
- 작업은 feature 브랜치
- PR 머지 전 셀프 리뷰 + 1인 이상 리뷰
- **대용량 자산은 git에 넣지 않는다** — `sources/`(고객 원본), 빌드 산출물, 대형 디자인 원본 등은 `.gitignore` 처리. 대신 클라우드 드라이브/NAS 등 **별도 백업** 필수. 버전이 꼭 필요하면 Git LFS.
- 비밀 정보·`*.env`·키 파일은 절대 커밋 금지 (`.gitignore`에 기본 포함)
