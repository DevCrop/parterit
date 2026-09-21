# 🚨🚨🚨 절대 규칙 #14 — 삭제·위험 액션은 Modal.confirm() + Form.ts AJAX 필수 (퍼블·개발 공통) — 초강력

> 본문 출처: 구 CLAUDE.md 절대 규칙 #14 (원문 보존)
> 소속 번들: `by-task/risk.md` (UI 작업에서 위험 액션 폼 작성 시 `by-task/ui.md` 가 교차 인용)

**삭제(`destroy` / `bulk-destroy` / `delete` / `delete-account`), 로그아웃(`auth.logout`), 세션 강제종료(`sessions.revoke`), 복원(`backups.restore`), 백업 삭제(`backups.delete`), 그리고 모든 `Route::delete()` 라우트로 전송되는 폼은 반드시 아래 5요소를 모두 충족해야 한다.**

1. `data-admin-ajax="1"` — Form.ts AJAX 바인딩 (관리자 영역). **이 속성이 없으면 `data-confirm` 도 무시되고 raw submit 으로 JSON 응답 페이지가 그대로 노출된다.**
   - 단, 전용 핸들러가 처리하는 폼 클래스/속성은 예외:
     - `.admin-ops__card-form` ([OpsConsole.ts](resources/ts/admin/pages/OpsConsole.ts))
     - `.admin-toggle-switch-form` ([ToggleSwitchHandler.ts](resources/ts/admin/components/ToggleSwitchHandler.ts))
     - `.admin-segmented-control-form` ([SegmentedControlHandler.ts](resources/ts/admin/components/SegmentedControlHandler.ts))
     - `data-confirm-redirect="…"` ([AdminConfirmRedirectForm.ts](resources/ts/admin/global/AdminConfirmRedirectForm.ts)) — **redirect 응답(`redirect_route`) 컨트롤러용**. 로그아웃처럼 JSON 이 아닌 302 응답을 native submit 으로 처리해야 할 때. `data-admin-ajax="1"` 와 절대 동시 사용 금지.
     - 이들 또한 내부에서 `Modal.confirm()` 호출하므로 `data-confirm` (또는 `data-confirm-redirect`) 만으로 안전.
2. `data-confirm="구체적 메시지"` — `Modal.confirm()` 발동 트리거. 한국어, 불가역성 명시 (예: "이 백업 파일을 삭제할까요? 되돌릴 수 없습니다.")
3. `<input type="hidden" name="_method" value="DELETE">` — DELETE 라우트 메서드 스푸핑 (PATCH/PUT 라우트면 그 메서드)
4. `<?= csrf_field() ?>` — CSRF 토큰
5. 버튼 안 로더 span — `<span class="admin-form__loader" style="display:none" aria-hidden="true"><i class="fa-solid fa-spinner fa-spin"></i></span>` (사용자 더블 클릭 방지)

**왜:**
- `data-confirm` 만 있고 `data-admin-ajax="1"` 누락 → [AdminForm.ts](resources/ts/admin/global/AdminForm.ts) selector `form[data-admin-ajax="1"]` 매칭 실패 → `Modal.confirm()` 미실행 → **모달 없이 raw POST 제출** → 서버 JSON 응답이 그대로 페이지에 노출. (지난 사고: `/console/ops/backups` 삭제 버튼 클릭 시 즉시 처리됨)
- `_method` 누락 → DELETE 라우트가 POST 로 호출되어 404
- `Modal.confirm()` 이 아닌 browser native `confirm()` 호출 → 디자인 토큰 무시·접근성 저하·사이트 톤 깨짐

**금지 패턴:**
- ❌ `<form ... data-confirm="...">` (data-admin-ajax 누락 — 핸들러 클래스 매칭 시에만 허용)
- ❌ `<form data-admin-ajax="1">` 인데 위험 액션이면서 `data-confirm` 없음
- ❌ `<form data-admin-ajax="1" data-confirm="...">` 인데 `_method DELETE` 누락
- ❌ TS / 인라인 onclick 안에서 `if (!confirm('…')) return;` 사용 (browser native — `Modal.confirm()` 으로 교체)
- ❌ 새 삭제 UI 만들 때 [admin/components/delete-form.php](resources/views/admin/components/delete-form.php) 가 있는데도 raw form 작성
- ❌ AJAX 폼인데 button 안에 loader span 누락 → 사용자 더블 클릭

**올바른 정답 마크업 (단일 삭제 — 인덱스 테이블 행 액션):**
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

**컴포넌트 사용 (권장):**
```php
<?= include_view('admin.components.delete-form', [
    'destroyUrl'     => route('admin.…destroy', ['id' => $id]),
    'confirmMessage' => '이 항목을 삭제할까요? 되돌릴 수 없습니다.',
    'buttonLabel'    => '삭제',
]) ?>
```

**작업 후 체크 (반드시 실행, 결과 0건이어야 통과):**
```bash
# [1] data-confirm 이 있는데 data-admin-ajax 없는 폼 (관리자 영역) — 핸들러 클래스/속성 예외 제외
grep -rln 'data-confirm[^-]' resources/views/admin --include="*.php" \
  | xargs grep -L 'data-admin-ajax\|admin-ops__card-form\|admin-toggle-switch-form\|admin-segmented-control-form\|data-confirm-redirect'

# [2] DELETE 라우트 폼인데 _method 누락
grep -rln '\.destroy\|delete-account\|sessions\.revoke\|backups\.delete\|auth\.logout' resources/views --include="*.php" \
  | xargs grep -L '_method'

# [3] 삭제·로그아웃 라우트 폼인데 data-confirm 없음 (위험 액션)
grep -rln '\.destroy\|\.bulk-destroy\|backups\.delete\|auth\.logout\|sessions\.revoke\|delete-account' resources/views --include="*.php" \
  | xargs grep -L 'data-confirm'

# [4] TS/JS 에서 browser native confirm() 호출 — Modal.confirm() 으로 교체
grep -rn '\bconfirm(' resources/ts --include="*.ts" | grep -v 'Modal\.confirm'
```

(연관: 절대 규칙 #8 — `_method` hidden 필수, #4 — RESTful HTTP 메서드)
