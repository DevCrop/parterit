# Bundle: UI / 디자인 / 퍼블

## 1. 언제 이 번들을 읽나 (트리거)

다음 중 하나라도 해당하면 이 번들을 **반드시** 연다.

- 파일 경로: `resources/views/**`, `resources/scss/**`, `resources/ts/components/**`, `resources/ts/admin/components/**`
- 키워드: 마크업·뷰·블레이드·SCSS·CSS·컴포넌트·폼 필드·input·select·textarea·버튼·테이블·모달·드로어·헤더·푸터·색·폰트·간격·타이포·반응형·아이콘·시안·디자인·브랜딩
- 역할: 퍼블리셔 / 홈페이지 디자이너 / 관리자 디자이너 / 브랜딩

위험 액션 폼(삭제·인증)을 **UI 로** 만드는 경우 → 이 번들 + [`risk.md`](risk.md) 동시 적용.

## 2. 이 번들의 핵심 결정 (요약)

- **컴포넌트 카탈로그 먼저** — 코드 한 줄 쓰기 전에 `docs/03-constants/components.md` 와 `/console/components` 쇼케이스 확인. 있으면 재사용, 없으면 만들고 카탈로그에 등록(4곳 동시: 카탈로그·쇼케이스·SCSS forward·뷰).
- **디자인 4단계 파이프라인** — 브랜딩 토큰(`var(--*)`) 인용 없이 디자인·컴포넌트·적용 단계로 내려가지 않는다. 작업 시작 전 4줄 선언 필수.
- **하드코딩 금지** — 인라인 `style=`, raw `<input>/<select>/<textarea>`, `font-size: Npx/rem` 직접 지정, `imms-container` 사용 — 전부 금지.
- **클래스명·간격 강제** — SCSS BEM 루트 클래스가 뷰의 실제 클래스명과 1:1 일치. 라벨·필드 위아래 간격은 `var(--admin-space-xs)` 등으로 명시.
- **역할 다른 텍스트는 별도 태그** — 아이콘+raw 텍스트, 제목+설명 raw 혼재 금지.

## 3. 포함 규칙 표

| # | 한 줄 요지 | 상세 본문 |
|---|---|---|
| 1 | 컴포넌트 카탈로그 먼저 + raw `<select>` 절대 금지 | [details/rule-01-component-catalog.md](../details/rule-01-component-catalog.md) |
| 2 | 서드파티 라이브러리는 래퍼 통해서만 (외부 코드는 래퍼 타입만 참조) | [details/rule-02-third-party-wrapper.md](../details/rule-02-third-party-wrapper.md) |
| 3 | UI 색상 60:30:10 — primary 는 CTA·활성·강조에만(10%) | [details/rule-03-color-60-30-10.md](../details/rule-03-color-60-30-10.md) |
| 5 | 인라인 `style=` 금지 + `/console/system/ui-audit` 0건 | [details/rule-05-inline-style.md](../details/rule-05-inline-style.md) |
| 6 | 폼 컴포넌트 완전 매핑 (특히 numberStepper, multiSelect, charCounter) | [details/rule-06-form-component-mapping.md](../details/rule-06-form-component-mapping.md) |
| 7 | 헤더 위 알림 pill·텍스트 가독성 (CSS 변수 금지, 고정 RGB) | [details/rule-07-header-pill.md](../details/rule-07-header-pill.md) |
| 10 | SCSS 클래스명 = 뷰 클래스명 1:1 + 라벨 간격 명시 | [details/rule-10-scss-class-1to1.md](../details/rule-10-scss-class-1to1.md) |
| 11 | 홈 타이포·컨테이너는 유틸리티 클래스 / `var(--*-fs)` 만 | [details/rule-11-typography-utilities.md](../details/rule-11-typography-utilities.md) |
| 12 | overflow auto/scroll 요소에 `data-lenis-prevent` 필수 | [details/rule-12-lenis-prevent.md](../details/rule-12-lenis-prevent.md) |
| 13 | 역할 다른 텍스트는 별도 태그 (아이콘+raw 텍스트 금지) | [details/rule-13-text-role-tag-split.md](../details/rule-13-text-role-tag-split.md) |
| 15 | 디자인 4단계 파이프라인 (브랜딩→디자인→컴포넌트→적용) | [details/rule-15-design-pipeline.md](../details/rule-15-design-pipeline.md) |
| 17 | 디자인시스템 유틸 강제 (타이포 위계·spacing·컴포넌트 재사용) | [details/rule-17-design-system-utility.md](../details/rule-17-design-system-utility.md) |
| 18 | Measurement Before Claim — rem 보고 시 px 환산 동봉·root 사전 확인·시각 지적 우선 | [details/rule-18-measurement-before-claim.md](../details/rule-18-measurement-before-claim.md) |
| 19 | 뷰 렌더링 위임 — 페이지는 호출만, 마크업은 `components/` · `sections/` 두 곳에서만 (`render_component` / `render_section`) | [details/rule-19-view-rendering-delegation.md](../details/rule-19-view-rendering-delegation.md) |

## 4. PRE 체크 (작업 전, 모두 실행)

```bash
# [P1] 컴포넌트 카탈로그 — 만들려는 것과 같은/유사한 컴포넌트가 있는지
grep -niE "input|textarea|select|checkbox|radio|date|datetime|number" docs/03-constants/components.md

# [P2] 기존 마크업 패턴 — 같은 화면 영역 다른 페이지에서 어떻게 썼나
ls resources/views/admin/components resources/views/components resources/views/admin/form 2>/dev/null

# [P3] 토큰 — 색·폰트·간격
grep -n "primary\|admin-space\|--heading\|--body" resources/scss/home/abstract/_colors.scss resources/scss/admin/abstract/*.scss 2>/dev/null | head -30
```

## 5. POST 체크 (작업 후, **모두 0건** 이어야 통과)

```bash
# [U1] raw 폼 태그 (showcase·csrf·_method 제외)
grep -rn "<input\|<select\|<textarea" resources/views/pages resources/views/admin --include="*.php" \
  | grep -v "console/components" | grep -v "csrf_field\|_method"

# [U2] 인라인 style — 색·폰트·배경 하드코딩
grep -rnE 'style="[^"]*(color|background|font-size|font-family)' resources/views --include="*.php"

# [U3] 홈 SCSS font-size 하드코딩 (변수 미사용)
grep -rn "font-size" resources/scss/home/ --include="*.scss" \
  | grep -v "var(--" | grep -v "abstract/_typography" | grep -v "base/_common" \
  | grep -v "font-size: inherit" | grep -v "font-size: 0"

# [U4] imms-container 잔재
grep -rn "imms-container" resources/views/ --include="*.php"

# [U5] overflow 요소가 data-lenis-prevent 없이 쓰임
grep -rn "imms-table-wrap\|site-drawer" resources/views/ --include="*.php" | grep -v "data-lenis-prevent"

# [U6] 아이콘+raw 텍스트 혼재 (#13)
grep -rn "fa-[a-z].*>[^<]" resources/views/ --include="*.php" -A1 | grep -v "aria-"

# [U7] 새/수정 컴포넌트 커밋이 카탈로그·쇼케이스 동반 갱신했나
git diff --name-only HEAD~1 HEAD | grep -E 'resources/views/(admin/)?(components|form)/' \
  && git diff --name-only HEAD~1 HEAD | grep -q 'docs/03-constants/components.md' \
  || echo "WARN: 카탈로그 갱신 누락"

# [U8] 감사 페이지
# /console/system/ui-audit 에서 위반 0건 확인 (브라우저)

# [U9] spacing 토큰 미사용 — 인라인 margin/padding/gap 하드코딩 (#10 강화)
grep -rEn 'style="[^"]*(margin|padding|gap)\s*:\s*[^v"]' resources/views/admin --include="*.php"

# [U10] SCSS spacing 하드코딩 (토큰·0 아닌 것)
grep -rEn '\b(margin|padding|gap)(-(top|bottom|left|right))?\s*:\s*[0-9.]+(px|rem)\b' \
  resources/scss/admin/pages resources/scss/admin/components --include="*.scss" \
  | grep -v '\b0(px|rem)?\b\|var(--admin-space'

# [U11] BEM 무력화 (요소 리셋이 클래스 spacing 덮어쓰기)
grep -rEn '\.admin[a-z_-]+\s+\b(th|td|li|p|h[1-6])\b[^{]*\{[^}]*(padding|margin)' \
  resources/scss/admin --include="*.scss"

# [U12] 절대규칙 #17-A — 페이지 SCSS 가 폰트 위계 직접 지정
grep -rnE '\b(font-size|font-weight|font-family|line-height|letter-spacing)\s*:' \
  resources/scss/home/pages --include="*.scss" | grep -v "abstract/" | grep -v "/components/"

# [U13] 절대규칙 #17-C — 구형·일회성 CTA 클래스 신규 사용
grep -rnE 'imms-cta-link|home-[a-z]+__cta\b|__more\b' resources/views/pages --include="*.php"

# [U15] 절대규칙 #19 — 같은 의미 BEM 블록(__empty/__error/__loading)이 페이지에 분산 인라인 정의
grep -rnE 'class="[a-z][a-z0-9-]*__(empty|error|loading|placeholder)\b' resources/views/pages --include="*.php"

# [U16] 절대규칙 #19 — 페이지에서 헬퍼 우회 require/include (데이터 준비 _presets/_helpers 류 제외)
grep -rnE '\b(require|include)(_once)?\s*\(\s*[^)]*\.php' resources/views/pages --include="*.php" | grep -v "_presets" | grep -v "_helpers"
```

**[U14] 절대규칙 #18 — Measurement Before Claim (간격·크기 보고 self-check)**

자동 grep 으로 잡히지 않는 항목이므로 답변 송신 직전 self-check:

1. 답변 내 rem 값에 "(= NN px)" 환산이 모두 동봉되었는가? (admin·home 모두 62.5% → 1rem = 10px)
2. "1rem = 16px" 같은 가정·단정 표현이 본문에 없는가? root font-size 를 코드로 사전 확인했는가?
3. 사용자가 "좁다/넓다/이상하다" 시각 지적을 했는데 "토큰 값은 정상" 으로 정당화한 적이 없는가?
4. 간격·여백·spacing 관련 작업이면 답변 첫머리에 `[측정 검증]` 블록을 포함했는가?

정본: `docs/01-rules/details/rule-18-measurement-before-claim.md`

## 6. 다른 번들과의 교차

- **UI ∩ RISK** — 삭제·로그아웃 등 위험 액션의 폼 UI 를 만드는 경우. [`risk.md`](risk.md) §3 의 5요소 마크업(`data-admin-ajax="1"` + `data-confirm` + `_method` + `csrf_field` + loader span)을 본 번들의 컴포넌트 카탈로그 패턴 안에서 충족시킬 것. 가급적 `admin.components.delete-form` include.
- **UI ∩ DEV** — 컴포넌트가 라우트·컨트롤러와 결합되는 폼/CRUD 화면. [`dev.md`](dev.md) §3 의 RESTful 메서드(`#4`)·`_method` 스푸핑(`#8`) 을 함께 점검.
