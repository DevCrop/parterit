# Bundle: 위험 액션 (삭제 · 인증 · 세션 · 백업)

## 1. 언제 이 번들을 읽나 (트리거)

다음 중 하나라도 해당하면 이 번들을 **반드시** 연다. 거의 항상 `dev.md` 또는 `ui.md` 와 동시 적용된다.

- 라우트/액션 키워드: `destroy`, `bulk-destroy`, `delete`, `delete-account`, `auth.logout`, `sessions.revoke`, `backups.restore`, `backups.delete`
- HTTP 메서드: `Route::delete(...)`, `Route::patch(...)` 중 의미상 불가역적인 변경
- 동작: 데이터 영구 삭제 / 세션 무효화 / 권한 박탈 / 백업 복원·삭제 / 결제·환불·송장 발행 등 되돌리기 어려운 효과

## 2. 이 번들의 핵심 결정 (요약)

- **5요소 마크업 강제** — `data-admin-ajax="1"` + `data-confirm="…"` + `_method` hidden + `csrf_field` + 버튼 로더 span. 하나라도 빠지면 모달 없는 raw POST·404·이중 클릭 사고 중 하나가 반드시 터진다.
- **`Modal.confirm()` 만 사용** — TS·인라인 onclick 에서 browser native `confirm()` 금지. 디자인 토큰·접근성·톤 보존.
- **권장 컴포넌트** — `admin.components.delete-form` 이 있으면 raw form 작성 금지. include 로 5요소 자동 충족.
- **DELETE 라우트는 `_method=DELETE`** — 폼은 POST 로 보내고 `_method=DELETE` hidden 으로 메서드 스푸핑(`#8`). 없으면 404.
- **사용자 확인 필요 액션** — 기능 동작·디자인·의존성·배포 영향 액션은 자율 실행 금지. CLAUDE.md §5 절대 규칙(사용자 확인 필요) 준수.

## 3. 포함 규칙 표

| # | 한 줄 요지 | 상세 본문 |
|---|---|---|
| 8 | AJAX 폼 PATCH/PUT/DELETE → `_method` hidden 필수 | [details/rule-08-method-spoof.md](../details/rule-08-method-spoof.md) |
| 14 | 삭제·위험 액션 5요소 (Modal.confirm + AJAX) + 컴포넌트 권장 | [details/rule-14-confirm-ajax-delete.md](../details/rule-14-confirm-ajax-delete.md) |

## 정답 마크업 (단일 삭제 — 행 액션)

```php
<form method="POST"
      action="<?= e(route('admin.…destroy', ['id' => $id])) ?>"
      class="admin-form-inline"
      data-admin-ajax="1"
      data-confirm="이 항목을 삭제할까요? 되돌릴 수 없습니다.">
  <?= csrf_field() ?>
  <input type="hidden" name="_method" value="DELETE">
  <button type="submit" class="admin-btn admin-btn--sm admin-btn--danger">
    삭제
    <span class="admin-form__loader" style="display:none" aria-hidden="true">
      <i class="fa-solid fa-spinner fa-spin"></i>
    </span>
  </button>
</form>
```

또는 컴포넌트:

```php
<?= include_view('admin.components.delete-form', [
    'destroyUrl'     => route('admin.…destroy', ['id' => $id]),
    'confirmMessage' => '이 항목을 삭제할까요? 되돌릴 수 없습니다.',
    'buttonLabel'    => '삭제',
]) ?>
```

## 4. PRE 체크 (작업 전)

```bash
# [P1] 기존 동일 도메인 삭제 폼이 어떻게 작성됐나
grep -rln 'data-confirm' resources/views/admin --include="*.php" | head -5

# [P2] 라우트가 DELETE 인지 확인 (POST 면 #4 위반)
grep -rEn "Route::(delete|patch|put)\(['\"][^'\"]*<도메인>" routes/admin.php

# [P3] delete-form 컴포넌트 존재 여부
ls resources/views/admin/components/delete-form.php 2>/dev/null
```

## 5. POST 체크 (작업 후, **모두 0건** 이어야 통과)

```bash
# [R1] data-confirm 있는데 data-admin-ajax 없는 폼 (핸들러 예외 제외)
grep -rln 'data-confirm[^-]' resources/views/admin --include="*.php" \
  | xargs grep -L 'data-admin-ajax\|admin-ops__card-form\|admin-toggle-switch-form\|admin-segmented-control-form\|data-confirm-redirect'

# [R2] DELETE 라우트 폼인데 _method 누락
grep -rln '\.destroy\|delete-account\|sessions\.revoke\|backups\.delete\|auth\.logout' resources/views --include="*.php" \
  | xargs grep -L '_method'

# [R3] 위험 액션 폼인데 data-confirm 없음
grep -rln '\.destroy\|\.bulk-destroy\|backups\.delete\|auth\.logout\|sessions\.revoke\|delete-account' resources/views --include="*.php" \
  | xargs grep -L 'data-confirm'

# [R4] TS/JS 에서 browser native confirm() 호출
grep -rn '\bconfirm(' resources/ts --include="*.ts" | grep -v 'Modal\.confirm'

# [R5] AJAX 폼인데 버튼 로더 span 누락
grep -rln 'data-admin-ajax="1"' resources/views/admin --include="*.php" \
  | xargs grep -L 'admin-form__loader'
```

## 6. 다른 번들과의 교차

- **RISK ∩ UI** — 위험 액션 폼의 마크업 자체. [`ui.md`](ui.md) 의 컴포넌트 카탈로그(`#1`)·인라인 style 금지(`#5`)·역할 다른 텍스트 태그 분리(`#13`)도 동시 충족.
- **RISK ∩ DEV** — DELETE 라우트의 컨트롤러 액션, 트랜잭션, 로깅, 권한 정책. [`dev.md`](dev.md) §3 의 `#4`·`#8`·`#9` 그리고 `architecture.md §3.1 Removal Protocol` 까지 점검.
- **RISK ∩ SERVER** — `backups.{restore,delete}` 같은 인프라 영향 액션은 [`server.md`](server.md) 의 백업·롤백·운영 절차도 인용.
