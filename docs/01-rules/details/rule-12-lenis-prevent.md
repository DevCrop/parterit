# 절대 규칙 #12 — Lenis 환경에서 overflow auto/scroll 요소에 data-lenis-prevent 필수 (퍼블·개발 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #12 (원문 보존)
> 소속 번들: `by-task/ui.md`

**홈 SCSS에서 `overflow: auto/scroll`, `overflow-x: auto/scroll`, `overflow-y: auto/scroll`이 지정된 요소의 대응 HTML 태그에는 반드시 `data-lenis-prevent` 속성을 추가해야 한다.**

Lenis smooth scroll이 홈 전체에 적용되어 있어(`SmoothScrollManager.ts`), 속성이 없으면 해당 요소 내부 스크롤을 Lenis가 가로채 스크롤이 동작하지 않는다.

**TS에서 동적으로 생성하는 요소**(Select/MultiSelect 드롭다운 등)는 JS에서 `"data-lenis-prevent": ""` 속성 주입으로 대체 가능.

**현재 적용 현황:**
| 요소 | SCSS 파일 | overflow 속성 | data-lenis-prevent |
|---|---|---|---|
| `.site-drawer` | `scss/home/layouts/_drawer.scss` | `overflow-y: auto` | ✅ `components/drawer.php` 적용됨 |
| `.imms-table-wrap` | `scss/home/components/_imms-table.scss` | `overflow-x: auto` | ⚠️ 뷰에서 사용 시 반드시 추가 |

**금지:**
- `overflow: auto/scroll` 요소에 `data-lenis-prevent` 없이 마크업하는 것

**작업 후 체크:**
```bash
# [1] 홈 SCSS에서 overflow auto/scroll 선언된 클래스 목록 확인
grep -rn "overflow" resources/scss/home/ --include="*.scss" | grep -E ":\s*(auto|scroll)"

# [2] 주요 overflow 클래스가 뷰에서 data-lenis-prevent 없이 쓰이는지 검출
grep -rn "imms-table-wrap\|site-drawer" resources/views/ --include="*.php" | grep -v "data-lenis-prevent"
```
두 번째 결과가 0건이면 통과. (1건 이상이면 해당 태그에 `data-lenis-prevent` 추가)
