# Bundle: 개발 / 백엔드 / DB

## 1. 언제 이 번들을 읽나 (트리거)

다음 중 하나라도 해당하면 이 번들을 **반드시** 연다.

- 파일 경로: `app/**`, `routes/**`, `database/migrations/**`, `database/seeders/**`, `config/**`, `tests/**`
- 키워드: Controller·Service·Action·Repository·Model·Migration·Seeder·Factory·Policy·Gate·Middleware·Request·DTO·Enum·Route·라우트·컨트롤러·서비스·모델·트랜잭션·정책·권한·기능 토글·feature flag
- 역할: 개발자 / 기획(요구사항 변경 영향)

위험 액션(삭제·인증·세션·백업) 컨트롤러/라우트는 [`risk.md`](risk.md) 도 동시 적용.
폼 UI 와 결합되면 [`ui.md`](ui.md) 도 동시 적용.

## 2. 이 번들의 핵심 결정 (요약)

- **RESTful 메서드 준수** — `POST /{id}/update` 같은 비-REST 라우트 금지. PATCH/PUT/DELETE 가 올바른 자리.
- **AJAX 폼은 `_method` 스푸핑 필수** — `data-admin-ajax="1"` 폼이 PATCH/PUT/DELETE 라우트를 향하면 hidden `_method` 빠뜨리면 404.
- **서드파티 라이브러리는 래퍼 통해서만** — 직접 `import`/`new` 금지. `resources/ts/components/*Wrapper.ts` 안에서만 의존.
- **기능 제거 = 코드 삭제 금지** — `feature_flags` 토글 OFF 또는 `config/platform/features.php` `default => false`. 라우트는 유지하고 `FeatureGate` 미들웨어가 게이트.
- **새 admin 페이지 = 8곳 동시 갱신** — 라우트 / 컨트롤러 / 뷰 / `drawer.php` (변수 정의·드릴다운 메뉴·트리 메뉴·**상위 그룹 펼침 조건**) / `config/platform/features.php` / 권한 / `manual.php`. 한 곳이라도 빠지면 active 상태 안 보임 또는 게이트 우회 발생.
- **아키텍처 정본 인용** — 새 코드 배치, 모듈 등록, 마이그레이션 절차는 `docs/03-constants/architecture.md` 가 정본.

## 3. 포함 규칙 표

| # | 한 줄 요지 | 상세 본문 |
|---|---|---|
| 2 | 서드파티 라이브러리는 래퍼 통해서만 (TS 외부 코드 import 금지) | [details/rule-02-third-party-wrapper.md](../details/rule-02-third-party-wrapper.md) |
| 4 | RESTful HTTP 메서드 — `POST /update` 금지, `PATCH /{id}` 등 | [details/rule-04-restful.md](../details/rule-04-restful.md) |
| 8 | AJAX 폼 PATCH/PUT/DELETE 라우트는 `_method` hidden 필수 | [details/rule-08-method-spoof.md](../details/rule-08-method-spoof.md) |
| 9 | 기능 제거는 코드 삭제 아니라 토글 OFF + 라우트 게이트 | [details/rule-09-feature-removal-toggle.md](../details/rule-09-feature-removal-toggle.md) |

## 추가로 항상 참조하는 정본

- `docs/03-constants/architecture.md` — 코드 배치·SOLID·기능 추가 절차·모듈=토글 단위·마이그레이션 러너
- `docs/03-constants/conventions.md` — RESTful 라우팅 규칙·git 워크플로·커밋·파일명 타임스탬프
- `docs/03-constants/standards.md` — 보안·접근성·성능·버전관리 표준

## 4. PRE 체크 (작업 전)

```bash
# [P1] 같은 도메인의 기존 라우트·컨트롤러 패턴
grep -rn "Route::\(get\|post\|patch\|put\|delete\)" routes/admin.php routes/web.php | grep -i "<도메인 키워드>"

# [P2] 영향 받을 모델·서비스·정책·뷰
ls app/Controllers app/Services app/Models app/Policies 2>/dev/null
grep -rln "<모델명>" app resources/views routes

# [P3] 라이브러리/래퍼 — 신규 외부 의존 도입 가능성
ls resources/ts/components/*Wrapper.ts 2>/dev/null

# [P4] 기능 토글 — 새 기능이면 등록 위치 확인
grep -rn "feature_enabled\|FeatureGate" config/ routes/ resources/views/ 2>/dev/null | head -10
```

## 5. POST 체크 (작업 후, 모두 0건 또는 의도 명시)

```bash
# [D1] 비-REST 라우트 잔재
grep -rEn "Route::post\(['\"]/[^'\"]*/(update|destroy|bulk-destroy|order)" routes/

# [D2] _method 누락된 AJAX update 폼
grep -r 'action=.*\.update.*data-admin-ajax' resources/views/admin --include="*.php" -l \
  | xargs grep -L '_method'

# [D3] 컨트롤러/라우트 파일 삭제 — 잔재 정리가 아니면 토글 OFF 로 전환
git diff --stat --diff-filter=D HEAD | grep -E 'routes/|app/Controllers/|app/.*Module'

# [D4] 서드파티 라이브러리 직접 import — 래퍼 우회
grep -rn "from ['\"]\(slim-select\|flatpickr\|quill\|@simonwep/pickr\)" resources/ts --include="*.ts" \
  | grep -v "components/.*Wrapper.ts\|components/DatePicker.ts\|components/SelectWrapper.ts\|components/QuillEditor.ts\|components/PickrWrapper.ts"

# [D5] TS 안에 /console/... URL 하드코딩 (#4 부속)
grep -rn "['\"]/console/" resources/ts --include="*.ts"

# [D6] 새 기능 모듈을 추가했는데 admin/manual.php 섹션 누락
git diff --name-only HEAD~1 HEAD | grep -E 'app/[A-Z][A-Za-z]+Module|app/Controllers/[A-Z][A-Za-z]+Controller' \
  && ! git diff HEAD~1 HEAD -- resources/views/admin/manual.php | grep -q '+' \
  && echo "WARN: 새 기능 모듈인데 manual.php 섹션 추가 누락"

# [D7] drawer 동기화 — 새 admin.<X> 라우트가 drawer.php 에 등록되었는지 + 상위 그룹 펼침 조건에 포함되었는지
php scripts/check-drawer-sync.php
```

### 새 admin 페이지 추가 시 drawer 8곳 체크리스트

| # | 위치 | 갱신 내용 |
|---|---|---|
| 1 | `routes/admin.php` | `Route::get/post/...->name('admin.<domain>.<action>')` |
| 2 | `app/Controllers/Admin/...Controller.php` | 컨트롤러 메서드 |
| 3 | `resources/views/admin/pages/<domain>/...php` | 뷰 파일 |
| 4 | `resources/views/admin/components/drawer.php` 상단 | `$is<Domain><Action> = $current === 'admin.<domain>.<action>';` |
| 5 | `drawer.php` 드릴다운 메뉴 (`admin-drawer__leaf`) | `<a href + active 클래스>` (필요 시 `feature_enabled('<key>')` 조건부) |
| 6 | `drawer.php` 트리 모드 메뉴 (`admin-sidebar__child-link`) | 동일 |
| 7 | `drawer.php` 상위 그룹 펼침 조건 (`$is<Group>Group = ... \|\| $is<New>`) | **가장 자주 빠뜨림 — Tree 모드에서 부모 `<details>` 가 안 열림** |
| 8 | `config/platform/features.php` (필요 시) | feature flag + `route_prefixes` |

선택 (강력 권장): `resources/views/admin/manual.php` 사용자 가이드 섹션.

자동 검증: `php scripts/check-drawer-sync.php` — 라우트명 ↔ drawer 변수·그룹 매핑 자동 교차 검증.

## 6. 다른 번들과의 교차

- **DEV ∩ RISK** — 삭제·인증·세션·백업 컨트롤러/라우트. `_method=DELETE` (`#8`) + Modal.confirm() 마크업(`#14`) 모두 충족. 라우트는 `Route::delete(...)` 또는 `Route::patch(...)`.
- **DEV ∩ UI** — CRUD 폼 화면. 라우트의 메서드(`#4`)와 뷰의 `_method` hidden(`#8`)이 일치하고, 폼 자체는 [`ui.md`](ui.md) §3 의 컴포넌트 헬퍼로 작성.
- **DEV ∩ SERVER** — 마이그레이션·환경 변수·기능 토글 배포. [`server.md`](server.md) 의 마이그레이션 러너·배포 절차 인용.
