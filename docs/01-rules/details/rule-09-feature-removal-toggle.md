# 절대 규칙 #9 — 기능 제거 = 코드 삭제 금지, 토글 OFF + 라우트 게이트 (개발·기획 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #9 (원문 보존)
> 소속 번들: `by-task/dev.md`

**한 번 빌드된 기능은 제거할 때 코드를 삭제하지 않는다.** `config/platform/features.php` 의 해당 키를 `default => false` 로 두거나 DB `feature_flags` 에 `enabled = 0` 으로 기록해 **토글 OFF** 로 비활성화한다. 라우트는 등록을 유지하되 `FeatureGate` 미들웨어가 `route_prefixes` 기준으로 자동 404 처리한다. 메뉴·푸터·헤더 링크는 `feature_enabled('<key>')` 조건부 렌더로 숨긴다.

**금지:**
- 컨트롤러·뷰·마이그레이션 파일 통째 삭제 (`git rm`)
- `routes/web.php` / `routes/admin.php` 에서 라우트 라인 통째 삭제
- 기능 모듈 폴더(`app/<Module>/`) 통째 제거
- 사이드바·푸터 메뉴 링크만 빼고 라우트는 살려두는 반쪽 게이트 (둘 다 같은 플래그로 처리)

**예외 (삭제 허용):** 잘못 들여온 **타프로젝트 잔재** 코드 — 이 프로젝트 정식 기능이 아니므로 보존 가치가 없다. patch-notes 에 "타프로젝트 잔재 정리 — 토글 보존 대상 아님" 명기 후 삭제 가능.

**작업 후 체크:**
```bash
# 라우트/컨트롤러/모듈 파일 deletion 검출 — 의도된 잔재 정리가 아니면 토글 OFF 로 전환
git diff --stat --diff-filter=D HEAD | grep -E 'routes/|app/Controllers/|app/.*Module'
```

상세 절차: `docs/03-constants/architecture.md` §3.1 Removal Protocol.
