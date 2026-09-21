# Bundle: 서버 / 배포 / 인프라

## 1. 언제 이 번들을 읽나 (트리거)

다음 중 하나라도 해당하면 이 번들을 **반드시** 연다.

- 키워드: 배포·deploy·서버·인프라·도메인·DNS·SSL·TLS·인증서·`.env`·환경 변수·backup·복원·rollback·migration 러너·feature flag 배포·로그·모니터링·SSH·Apache·MySQL/MariaDB
- 파일 경로: `.env*`, `config/**`, `docs/00-overview/server-spec.md`, 배포 스크립트, CI 설정, Apache vhost
- 역할: 서버 운영

## 2. 이 번들의 핵심 결정 (요약)

- **이 번들에 직접 귀속되는 절대 규칙은 현재 없다.** 본문은 모두 표준 문서(아래 §3)를 정본으로 인용한다.
- **마이그레이션 러너 사용** — raw `.sql` 직접 실행 금지. `docs/03-constants/architecture.md §4` 의 러너를 통해서만.
- **기능 토글로 배포 게이트** — 새 기능 모듈은 `feature_flags` 또는 `config/platform/features.php` 로 ON/OFF. 라우트·메뉴는 같은 플래그로 동시 게이트(반쪽 게이트 금지 — `#9` 정본).
- **백업·롤백 액션은 RISK** — `backups.restore`, `backups.delete` 는 [`risk.md`](risk.md) 5요소 마크업 동시 적용.
- **비밀정보 하드코딩 금지** — `.env` 또는 secret store 사용. 코드·뷰·문서·커밋에 절대 노출 금지.
- **git 워크플로 준수** — pull / commit / push 타이밍과 자동화 훅은 `docs/03-constants/conventions.md §git` 정본.

## 3. 인용할 정본 문서

| 영역 | 정본 |
|---|---|
| 배포 환경·인프라 사양·도메인·SSL·서버 스펙 | `docs/00-overview/server-spec.md` |
| 코드 배치·기능 추가 절차·모듈=토글 단위·마이그레이션 러너·Removal Protocol §3.1 | `docs/03-constants/architecture.md` |
| git 워크플로·커밋·파일명 타임스탬프·RESTful 라우팅 | `docs/03-constants/conventions.md` |
| 보안·접근성·성능·버전관리 표준 | `docs/03-constants/standards.md` |
| 기능 제거 = 토글 OFF + 라우트 게이트 | `details/rule-09-feature-removal-toggle.md` |
| 백업·복원·삭제 위험 액션 마크업 | [`risk.md`](risk.md) §3 |

## 4. PRE 체크 (작업 전)

```bash
# [P1] 현재 서버 사양·도메인 상태
grep -nE "도메인|DNS|SSL|TLS|Apache|MySQL|PHP" docs/00-overview/server-spec.md 2>/dev/null | head -20

# [P2] 환경 변수 — 새 변수 추가 시 .env.example 동반 갱신 약속
ls .env .env.example 2>/dev/null

# [P3] 기능 토글 — 새 기능 배포 게이트
grep -rn "feature_flags\|FeatureGate\|feature_enabled" config/ routes/ resources/views/ 2>/dev/null | head -10

# [P4] 마이그레이션 러너 위치·사용법
grep -rn "migration" docs/03-constants/architecture.md | head -10
```

## 5. POST 체크 (작업 후, 모두 0건 또는 의도 명시)

```bash
# [S1] 비밀정보 하드코딩 검출
grep -rEn "(api[_-]?key|secret|password|token)\s*=\s*['\"][^'\"]{8,}" \
  app/ resources/ config/ routes/ docs/ --include="*.php" --include="*.ts" --include="*.md" 2>/dev/null \
  | grep -v ".env"

# [S2] raw .sql 직접 실행 흔적 (러너 우회)
grep -rn "exec.*\.sql\|mysql.*<.*\.sql" deploy/ scripts/ 2>/dev/null

# [S3] .env 사본·dump 가 커밋에 포함됐는지
git diff --name-only HEAD~1 HEAD | grep -E '^\.env(\..*)?$' | grep -v '\.env\.example'

# [S4] 라우트·컨트롤러·모듈 파일 deletion — 잔재 정리가 아니면 토글 OFF 로 전환 (#9)
git diff --stat --diff-filter=D HEAD | grep -E 'routes/|app/Controllers/|app/.*Module'

# [S5] 새 기능 토글이 있는데 메뉴·뷰 게이트 누락 — feature_enabled 조건부 렌더 확인
# 수동: 새 토글 키를 grep 했을 때 메뉴·푸터·헤더 등 노출 지점에서 함께 등장하는지
```

## 6. 다른 번들과의 교차

- **SERVER ∩ RISK** — backup·rollback·삭제 운영 액션은 [`risk.md`](risk.md) 의 5요소 마크업 + Modal.confirm 패턴 필수.
- **SERVER ∩ DEV** — 새 기능 배포·마이그레이션·feature flag 등록은 [`dev.md`](dev.md) §3 의 `#9` 토글 게이트 정본 인용.
- **SERVER ∩ UI** — 운영 대시보드(`/console/ops/*`) UI 변경은 [`ui.md`](ui.md) 도 동시 적용.
