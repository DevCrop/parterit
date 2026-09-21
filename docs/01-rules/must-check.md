---
title: 필수 체크리스트
categories: [workflow, dev, design]
desc: 작업 전후 무조건 수행해야 하는 카테고리별(A-H) 검증 항목 모음
related: [rules.workflow, rules.change-impact, rules.roles]
updatedAt: 2026-05-13
---

# 필수 체크리스트

> 특정 조건에서 **무조건** 수행해야 하는 검증 항목. **체크 로직은 전부 이 파일에 모은다** (자동 점검은 `/checklist` 커맨드).
> AI는 같은 항목을 **두 번** 점검한다:
> - **작업 전(PRE)**: 해당 카테고리를 읽고 작업 방향이 어긋나지 않는지 확인 → 어긋나면 시작하지 않음
> - **작업 후(POST)**: 같은 항목을 다시 점검 → 미충족이 하나라도 있으면 **작업 종료 보류**, 즉시 해결
>
> 작업이 여러 카테고리에 걸치면 해당 카테고리를 **모두** 점검.

## A. 모든 작업 (예외 없음)

- [ ] (PRE) 관련 역할의 에이전트 파일(`.claude/agents/<role>.md`)을 읽었는가?
- [ ] (PRE) `patch-notes.md` 최근 3개를 확인했는가? (중복·충돌 방지)
- [ ] (POST) 작업 결과를 `patch-notes.md`에 1줄 기록했는가? (병렬로 디스패치했으면 **메인이 한 번만**)

## B. 코드 변경 시 (개발자)

### B-1. 설계 — 작업 전 결정 (PRE) — 코드 배치 정본: `docs/03-constants/architecture.md`
- [ ] **레이어 분리(MVC)**: Controller/Handler = 입출력만, 비즈니스 로직 = 도메인/서비스, View = 표현만. 컨트롤러에 로직 몰빵 금지
- [ ] **DDD 스타일**: 도메인 로직이 모델 안에 있는가? (anemic model 금지 — getter/setter 덩어리 + 서비스에 로직 전부 빼는 구조 금지)
- [ ] **재사용 우선 + 애그리거트 통합**: 같은 일을 하는 클래스가 이미 있는가?(있으면 거기에 추가, 새 파일 금지) 관련 클래스들을 흩뿌리지 않고 **애그리거트 루트로 묶었는가**? 새 "종류"는 `if/switch` 분기 추가가 아니라 **인터페이스 새 구현체 + 레지스트리 등록**인가? (OCP)
- [ ] **루트·새 폴더 금지**: 프로젝트 루트나 새 최상위 폴더에 파일을 만들지 않고 `architecture.md`의 `app/` 맵에 맞는 자리에 두는가? (새 도메인 모듈이면 `app/<Module>/` 하나)
- [ ] **Aggregate Root**: 상태 변경은 애그리거트 루트를 **통해서만** 일어나는가? (불변식 보장 단위, 내부 엔티티 직접 수정 금지)
- [ ] **도메인 이벤트**: 의미 있는 상태 변화는 이벤트로 발행되는가? 부수효과·다른 컨텍스트 연동은 **이벤트 핸들러**로 분리(트랜잭션 경계 분리)
- [ ] **객체지향**: 상속보다 합성, SRP, 의존성 주입(타입힌트는 인터페이스), 도메인 ↔ 인프라 분리
- [ ] **데이터 설계**: 정규화(3NF), 관계(FK + `ON DELETE` 정책), **softDelete**(`deleted_at`), 인덱스, **멱등성 키**(POST 결제·주문 등) 검토했는가? **스키마 변경은 마이그레이션 러너로**(raw `.sql` 직접 실행 금지)
- [ ] **기능 모듈이면 토글 단위인가**: 새 기능을 토글 가능한 모듈로 설계했는가? (라우트 등록·메뉴 렌더가 같은 기능 플래그로 게이트 — `architecture.md` §3)

### B-2. 안전 — 작업 후 확인 (POST)
- [ ] 비밀 정보(API 키·비밀번호)가 코드에 하드코딩되지 않았는가? (`.env` / 시크릿 매니저)
- [ ] 의존성을 새로 추가했다면 사용자에게 확인받았는가?
- [ ] 기존 컨벤션(`docs/03-constants/conventions.md`) + 코드 배치 규칙(`architecture.md`)을 따랐는가? (루트에 새 폴더·파일 안 만들었는가?)
- [ ] 시간순 산출물(마이그레이션·SQL·일회성 스크립트·로그 등)을 새로 만들었다면 파일명에 `YYMMDD-HHMM-제목.확장자` 프리픽스를 붙였는가?
- [ ] **새 기능 모듈을 추가했다면 — 기능 토글 레지스트리에 등록했는가?** (`key`·`label`·`group`·`default`·`routePrefix`·`menu`) 라우트와 사이드바 메뉴가 **같은 플래그로 조건부 처리**되는가? 모듈이 많아지면 그룹→하위항목 구조로 묶었는가? (`architecture.md` §3)
- [ ] **새 CRUD 라우트를 추가했다면 — RBAC 두 곳에 등록했는가?** ① `config/permissions.php` — 새 권한 키(`<group>.<action>`) + label + group + sort 추가, ② `config/route-permissions.php` — 새 라우트명 → 권한 키 매핑 추가. 두 곳을 빠뜨리면 PermissionGate가 해당 라우트를 "매핑 없음"으로 통과시켜 권한 체크가 무력화됨.
- [ ] **새 기능 모듈을 추가했다면 — `docs/05-features/<key>.md` 가 있는가?** (도트는 하이픈으로) 없으면 `/docs-curate` 호출 → docs-curator 가 스켈레톤 생성. 라이브러리(`/console/library`) 에서 분류되도록 frontmatter `categories` 채움. (docs-curator: `.claude/agents/docs-curator.md`)
- [ ] **DB 스키마/데이터를 바꿨다면 — 마이그레이션 파일로 만들었는가?** (`down` 포함, 타임스탬프 규칙) raw `.sql` 을 DB에 직접 실행하지 않았는가?
- [ ] SQL 파라미터 바인딩 / 입력 **서버 검증** / 출력 이스케이프 했는가?
- [ ] 도메인 로직 단위 테스트 + 핵심 플로우 통합 테스트 있는가?
- [ ] API 계약(요청/응답/상태코드)이 바뀌었다면 퍼블리셔·서버운영과 협의했는가?

## C. 마크업/퍼블리싱 시 (퍼블리셔)

### C-1. 컴포넌트 재사용 — 작업 전 (PRE) — 정본: `docs/03-constants/components.md`
- [ ] **`docs/03-constants/components.md`(컴포넌트 카탈로그)와 `/console/components`(렌더 쇼케이스)를 먼저 봤는가?** 만들려는 게 이미 있으면 그걸 **호출만** 한다 — `input` / `button` / `select` / `textarea` / `actions` / `nav-tabs`(페이지 단위 탭) / `breadcrumb` / `pagination` / `sub-visual` / `*-uploader` 등은 **한 번 만들면 재사용**. 화면마다 새 마크업·새 디자인 금지
- [ ] **🚨 폼 컴포넌트 완전 매핑 체크 (반복 위반 최다)** — 폼 필드를 작성할 때마다 아래를 확인:
  - 숫자 입력 → `component('numberStepper', ...)` **(절대로 `component('input', ['type'=>'number'])` 금지)**
  - 여러 줄 입력 → `component('textarea', ['rows'=>N, ...])` — rows 최소값: 요약형 5, 설명형 8
  - 날짜 → `component('date', ...)` / `component('datetime', ...)`
  - 단일 선택 → `component('select', ...)`
  - 복수 선택 → `component('multiSelect', ...)`
  - raw `<input>` / `<textarea>` / `<select>` 태그가 폼 안에 남아 있으면 즉각 위반
- [ ] 기존 걸로 부족하면 → 임의 분기·복붙 변형 금지. (a) 컴포넌트에 **변형(prop/modifier)** 추가로 해결되면 그렇게, (b) 안 되면 디자이너/사용자에게 "**변형 추가 vs 신규 컴포넌트**" 확인
- [ ] 방향·스타일이 근본적으로 달라 **새로 만들어야 하면**: ① 프로젝트 톤·디자인 토큰에 맞춰 만든다(색·간격·폰트·라운드·그림자 하드코딩 금지, 인라인 `style=` 금지), ② 단일 소스로 둔다(뷰 `views/{admin/components,admin/form,components}/<name>.php` + 스타일 `_<name>.scss` + `index.scss`에 `@forward`), ③ **`components.md`에 한 줄 등록**(가능하면 `/console/components` 쇼케이스에도)

### C-2. 일관성·품질 — 작업 후 (POST)
- [ ] **(#18 Measurement Before Claim) 간격·크기 보고 시 rem 값에 "(= NN px)" 환산을 모두 동봉했는가?** 루트 `html { font-size }` 를 코드로 사전 확인했는가? 사용자가 "좁다/넓다/이상하다" 시각 지적을 했는데 "토큰 값은 정상" 으로 정당화한 적이 없는가? — admin·home 모두 62.5% (1rem = 10px). 정본: `docs/01-rules/details/rule-18-measurement-before-claim.md`
- [ ] 섹션 골격 `section > __container > __inner > __head + __body` + **BEM**(`__a__b` 중첩 금지)을 지켰는가?
- [ ] 타이포·간격(margin/padding)을 컴포넌트 CSS에 쓰지 않고 **유틸 클래스**(`no-mg--top`, `no-section` 등 SCSS 반복문 생성)로 처리했는가? SCSS 직접 작성은 특수 케이스만인가?
- [ ] 색·간격·폰트·라운드·그림자 등을 하드코딩하지 않고 **디자인 토큰(CSS 변수 / SCSS 맵)** 으로 썼는가? 인라인 `style=` / `!important` 안 썼는가?
- [ ] ⛔ **SCSS 명세도 충돌 — 요소 타입 리셋이 BEM 클래스를 덮어쓰지 않는가?** `.block th, .block td { padding: 0 }`처럼 **태그를 포함한 리셋 선택자**(명세도 0,1,1)는 `.block__modifier { padding: … }`(0,1,0)보다 항상 우선한다. **규칙: 요소 타입이 섞인 리셋에는 `border: none` 같이 전체 공유 속성만 선언하고, `padding`·`font-size`·`color` 등 BEM 하위 클래스가 개별 지정해야 할 속성은 절대 선언하지 않는다.** 새 컴포넌트에서 `table th, td {}` / `ul li {}` 등 리셋을 쓸 때마다 이 규칙을 점검할 것. (위반 사례: `_roles-permissions.scss`의 `th,td{padding:0}`이 `__th-feature` 패딩을 무력화 — 2026-05-14 발견)
- [ ] 같은 역할의 UI가 화면마다 다르게 보이지 않는가? (변형은 prop/modifier로, 복붙 변형 금지)
- [ ] 새로 만들거나 손본 공용 컴포넌트를 `docs/03-constants/components.md`에 반영했는가? (반대로 — 특정 화면 전용 마크업/스타일을 컴포넌트인 양 잘못 만들지 않았는가? 그런 건 페이지 SCSS에 둔다)
- [ ] 시맨틱 마크업 + 접근성 WCAG AA(`alt`·키보드·포커스 표시·명도 대비 4.5:1) 충족했는가?
- [ ] CLS 방지(이미지 `width/height` 또는 `aspect-ratio`) 확인했는가?
- [ ] **4단계 반응형(Mobile ≤767 / Tablet 768-991 / Laptop 992-1279 / PC ≥1280) 전부 대응했는가?** (`$bp-mobile / $bp-tablet / $bp-laptop / $bp-pc`)
- [ ] **`.admin__main` 수평 패딩이 모든 뷰포트에서 최소 `var(--admin-space-2xl)` (2rem) 이상인가?** 모바일에서 좌우 여백이 10px 이하이면 위반.
- [ ] **테이블이 있다면 모바일(≤767px)에서 가로 스크롤 없이 카드 형태로 변환됐는가?** (`thead` 숨김 + `data-label` + 행 → flex-wrap 카드. `overflow-x: auto` 금지)
- [ ] **기능 라벨·행 텍스트 폰트가 최소 `var(--admin-fs-body-lg)` 이상인가?** (열 헤더·그룹 타이틀은 `--admin-fs-sm` 이상. `--admin-fs-xs`는 배지·메타 전용)
- [ ] **기능명(좌측 열) `padding-left` 가 최소 1.5rem인가?** 텍스트가 경계에 바짝 붙어 있으면 위반.
- [ ] SEO 기본(`title`·`meta description`·canonical·OG·JSON-LD·heading 위계) 빠진 것 없는가?
- [ ] **[Lenis] 홈 SCSS에 `overflow: auto/scroll` 추가했다면 — 대응 HTML 태그에 `data-lenis-prevent` 속성이 있는가?** (`SmoothScrollManager.ts`가 홈 전체에 Lenis를 적용하므로 누락 시 내부 스크롤 불가)
  ```bash
  # overflow auto/scroll 선언된 클래스 목록
  grep -rn "overflow" resources/scss/home/ --include="*.scss" | grep -E ":\s*(auto|scroll)"
  # 주요 overflow 클래스가 뷰에서 data-lenis-prevent 없이 쓰이는지 검출
  grep -rn "imms-table-wrap\|site-drawer" resources/views/ --include="*.php" | grep -v "data-lenis-prevent"
  ```

## D. 디자인/브랜딩 변경 시

- [ ] 브랜드 가이드(`docs/02-variables/brand-guide.md`)와 일치하는가?
- [ ] 컬러/타이포가 정의된 **토큰**을 사용했는가? (색만으로 의미 전달 금지)
- [ ] 컴포넌트 상태(hover/focus/disabled/loading/empty/error)와 콘텐츠 극단값을 다뤘는가?
- [ ] 변경 사유를 `decisions.md`에 기록했는가?

## E. 고객 자료 받았을 때

- [ ] 원본 파일을 **`sources/`** 에 그대로 보관했는가?
- [ ] 원본을 수정/덮어쓰지 않았는가?
- [ ] 가공본은 `workspace/`에 별도 저장했는가?

## F. 요구사항 변경 발생 시

- [ ] `requirement-changes.md`에 변경 내용을 기록했는가?
- [ ] `change-impact.md` 5단계 절차로 파급효과를 분석했는가?
- [ ] 영향받는 모든 역할의 작업을 재검토했는가?

## G. 배포 작업 시

- [ ] `server-spec.md`의 환경을 정확히 따랐는가?
- [ ] 백업이 있는가? (그리고 실제 복구 가능한가?)
- [ ] 롤백 절차가 명확한가?
- [ ] staging에서 검증했는가?
- [ ] 사용자 확인을 받았는가?

## H. 병렬 디스패치 시 (여러 트랙 동시 실행) — 상세: `docs/01-rules/parallel-work.md`

- [ ] (PRE) 각 트랙의 **쓰기 범위(경로)를 선언**했고, 트랙끼리 겹치지 않는가? 레이어 의존이 없는가? 공유 상태(DB 스키마·`brand-guide.md` 토큰·설정·컴포넌트)를 동시에 안 건드리는가?
- [ ] (PRE) 어떤 트랙도 `docs/04-history/*`에 쓰지 않는가? (히스토리 기록은 메인이 마지막에 1회)
- [ ] (PRE) 요구사항 변경 처리면 → **병렬 금지**, `change-impact.md` 직렬 절차로 전환했는가?
- [ ] (POST) `git status`로 실제 변경 파일이 선언한 쓰기 범위 안이고 트랙끼리 안 겹쳤는지 확인했는가? 겹쳤으면 직렬로 재작업했는가?
- [ ] (POST) 트랙 산출물을 통합하고 경계 일관성을 확인했는가? (해당 카테고리 재점검)

---

> 자동화된 검사(ESLint/Stylelint/타입체크/CI 등)는 이 체크리스트의 보완재다 — 설정 파일은 프로젝트 루트에 두고, 어떤 규칙을 켰는지는 `docs/03-constants/conventions.md`에 적는다. 단, 사람·AI가 봐야 할 "판단" 항목은 위 체크리스트가 정본.
