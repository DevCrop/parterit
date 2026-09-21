# 🚨🚨🚨 절대 규칙 #15 — 모든 작업은 "브랜딩 → 디자인 → 컴포넌트 → 실제 적용" 4단계 파이프라인 (전 역할 공통) — 초강력

> 본문 출처: 구 CLAUDE.md 절대 규칙 #15 (원문 보존)
> 소속 번들: `by-task/ui.md` (디자인 단계가 닿는 모든 작업에서 강제)

**이 프로젝트의 모든 명령은 예외 없이 4단계 중앙 집합소를 거친다.** 어느 단계든 건너뛰거나 거꾸로 진행 금지. 상위 단계 근거 없이 하위 단계를 손대지 않는다. 본 규칙은 #1 ~ #14 모든 규칙보다 **상위**이며 (단 #16 변경 프로세스의 "구현" 단계 안에서 작동), 다른 규칙은 이 파이프라인 안에서 작동한다.

```
1) 브랜딩  →  2) 디자인  →  3) 컴포넌트  →  4) 실제 적용
   (톤·         (시안·         (카탈로그·          (페이지·뷰·
    토큰)        레이아웃)       재사용 자산)        기능 코드)
```

**왜 이 규칙이 필요한가:**
- 컴포넌트는 디자인에서 정당화되어야 하고, 디자인은 브랜딩 방향과 일치해야 한다 → 근본부터 검토.
- 인풋·버튼·섹션 추가 시 카탈로그에 동일한 게 있으면 **무조건 재사용**, 없으면 새로 만들어 카탈로그에 등록·재진입(재사용 자산화). 화면마다 새 마크업이 양산되는 사고를 원천 차단.

**모든 명령 작업 시작 전 5줄 선언 필수:**
```
[파이프라인 선언]
- 닿는 단계: [□ 브랜딩  □ 디자인  □ 컴포넌트  □ 적용] (해당 모두 체크)
- 상위 근거: <어느 토큰/시안/문서를 인용했는가 — 파일 경로 또는 시안 페이지>
- 재사용:    <카탈로그에서 무엇을 재사용 / 없으면 신규 컴포넌트 X 등록 계획>
- 환류:      <신규 컴포넌트 시 카탈로그·쇼케이스·SCSS·뷰 4곳 동시 갱신 약속>
- 참고 페이지: <새 페이지·새 BEM 블록이면 기존 어느 페이지/SCSS 를 표준으로 Read 했는지 파일경로 명시. 없으면 작업 중단>
```
선언이 없거나 "상위 근거"·"참고 페이지" 가 비어 있으면 **즉시 작업 중단**하고 사용자에게 보고. 추측으로 단계 건너뛰기 금지.

**"참고 페이지" 가 필수인 이유:**
새 페이지·새 BEM 블록을 짤 때 기존 잘 만들어진 페이지(예: `resources/views/admin/pages/siteinfo/edit.php` + `_form.scss` 또는 `_analytics.scss` 의 KPI 카드 패턴)를 보지 않으면, spacing 토큰·표준 폼 구조·반응형 패턴을 우회해 매번 새로 짜게 된다. 작업 시작 전 **반드시 기존 파일 1개 이상을 Read 한 증거**를 이 필드에 명시한다.

**단계별 정본 위치와 핵심 의무:**

| 단계 | 정본 위치 | 손댈 때 반드시 |
|---|---|---|
| 1. 브랜딩 | `docs/02-variables/brand-guide.md`, `resources/scss/home/abstract/_colors.scss`·`_typography.scss`, `.claude/agents/branding.md` | 토큰만 변경. 페이지·컴포넌트에 색·폰트 하드코딩 금지 (`var(--*)` 만) |
| 2. 디자인 | `.claude/agents/{designer-brand,designer-admin}.md`, 피그마 시안, `workspace/` 시안 PDF | 브랜딩 토큰을 인용해 시안 작성. 토큰에 없는 신규 값 필요 시 **1단계 갱신 먼저** |
| 3. 컴포넌트 | `docs/03-constants/components.md` (카탈로그) + `/console/components` (쇼케이스) + `resources/views/{admin/components,admin/form,components}/` + `resources/scss/.../components/` | **있으면 재사용**. 없으면 신규 작성 + `_<name>.scss` + `components/index.scss @forward` + 카탈로그 한 줄 + 쇼케이스 블록까지 **한 세트로** |
| 4. 적용 | `resources/views/pages/`, `resources/views/admin/`, 컨트롤러, 라우트 | 3단계 컴포넌트 헬퍼(`component(...)` / `include_view(...)`)만. raw `<input>`/`<select>`/`<textarea>` 직접 작성 금지 |

**역방향 검증 (수정·삭제·리팩터링 시):**
- 컴포넌트 변경 → **디자인 근거 인용 필수** (어느 시안/디자이너 결정?). 근거 없는 컴포넌트 변경 금지.
- 디자인 변경 → **브랜딩 방향 일치 확인 필수** (`brand-guide.md`·`branding.md` 톤·토큰 부합?). 부합 안 하면 1단계부터 다시.
- 브랜딩(토큰) 변경 → **그 토큰을 쓰는 모든 디자인·컴포넌트·적용처에 파급효과 분석** (`docs/01-rules/change-impact.md` 5단계 필수 실행).

**금지:**
- ❌ 작업 시작 전 4줄 선언 누락 — 어느 단계인지·상위 근거 무엇인지 안 밝히고 진행
- ❌ 컴포넌트 카탈로그 미확인 후 새 마크업 작성 (= #1 동시 위반)
- ❌ 컴포넌트 신규 작성했는데 카탈로그(`docs/03-constants/components.md`)·쇼케이스(`/console/components`)·SCSS forward 등록 누락 (재사용 자산화 실패)
- ❌ 디자인 변경하면서 브랜딩 토큰 무시 (페이지에 직접 색/폰트 하드코딩, `var(--*)` 미사용)
- ❌ 페이지·뷰에서 컴포넌트 우회해 raw `<input>` / `<select>` / `<textarea>` 작성
- ❌ "단계 건너뛰면 빠르겠지" 추측 — 단 한 명령도 예외 없음

**작업 후 체크 (반드시 0건, 위반 시 즉시 정정):**
```bash
# [1] 페이지·관리자 뷰에서 raw 폼 태그 (쇼케이스·csrf·_method 제외)
grep -rn "<input\|<select\|<textarea" resources/views/pages resources/views/admin --include="*.php" \
  | grep -v "console/components" | grep -v "csrf_field\|_method"

# [2] 뷰에서 브랜딩 토큰 우회 (인라인 색·폰트·배경 하드코딩)
grep -rnE 'style="[^"]*(color|background|font-size|font-family)' resources/views --include="*.php"

# [3] 새 컴포넌트 추가 커밋에 카탈로그(docs/03-constants/components.md) 동반 갱신 여부
git diff --name-only HEAD~1 HEAD | grep -E 'resources/views/(admin/)?(components|form)/' \
  && git diff --name-only HEAD~1 HEAD | grep -q 'docs/03-constants/components.md' \
  || echo "WARN: 새/수정된 컴포넌트가 카탈로그에 반영되지 않음 — 환류 누락"
```

**상세 절차·예시·다이어그램**: `docs/01-rules/design-pipeline.md`

(연관: 절대 규칙 #1 — 컴포넌트 카탈로그 / #2 — 서드파티 래퍼 / #3 — 60:30:10 / #5 — 인라인 style 금지 / #6 — 폼 컴포넌트 매핑 / #11 — 타이포 토큰 / `change-impact.md` 5단계)
