# 절대 규칙 #8 — AJAX 폼에서 PATCH/PUT/DELETE 라우트는 `_method` hidden 필수 (퍼블·개발 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #8 (원문 보존)
> 소속 번들: `by-task/dev.md` + `by-task/risk.md` (위험 액션 폼에서 재참조)

**`data-admin-ajax="1"` 폼의 action이 PATCH/PUT/DELETE 라우트를 향할 경우, 반드시 `<input type="hidden" name="_method" value="PATCH">` (메서드에 맞게)를 `<?= csrf_field() ?>` 바로 다음 줄에 추가해야 한다.**

- Form.ts는 `_method` 필드를 읽어 실제 HTTP 메서드로 사용 (없으면 POST → 404 발생)
- `routes/admin.php`에서 해당 URL의 메서드를 먼저 확인: PATCH/PUT/DELETE → hidden 추가, POST → 불필요
- update 라우트는 거의 모두 PATCH이므로 edit.php 작성 시 기본으로 추가
- DELETE 라우트 폼 (예: 세션 강제 종료) 도 동일하게 `value="DELETE"`로 추가

**작업 후 체크:**
```bash
# _method 없는 update 폼이 있으면 즉시 추가
grep -r 'action=.*\.update.*data-admin-ajax' resources/views/admin --include="*.php" -l | xargs grep -L '_method'
```
