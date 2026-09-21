# 절대 규칙 #13 — 역할이 다른 텍스트는 반드시 별도 태그로 감싼다 (퍼블·개발 공통)

> 본문 출처: 구 CLAUDE.md 절대 규칙 #13 (원문 보존)
> 소속 번들: `by-task/ui.md`

**한 요소 안에서 텍스트의 역할(제목 vs 설명, 라벨 vs 값 등)이 다르면 반드시 각각 태그로 감싸야 한다. 순수 텍스트 노드로 두는 것은 절대 금지.**

**판단 기준 — 위반:**
- 한 태그 안에 raw 텍스트(또는 `<?= ?>` echo)와 자식 태그가 **동시에** 존재하고, 둘의 역할이 다를 때
  - `<a>링크 텍스트<span>설명</span></a>` → 제목 역할 raw 텍스트 + 설명 역할 span → **위반**
  - `<button><?= e($label) ?><span class="loader">...</span></button>` → 라벨 raw echo + 로더 span → **위반**
  - `<p><i class="fa-calendar"></i>31 January 2026</p>` → 아이콘 + raw 날짜 텍스트 → **위반**

**판단 기준 — 허용 (위반 아님):**
- 같은 역할의 문장 안에서 일부 강조: `<p>I'm your <b>father</b> hello</p>` → OK
- `<button>` 안에 아이콘만: `<button><i class="fa-bars"></i></button>` → OK
- `<p>`, `<li>` 안의 `<a>`, `<b>`, `<em>`, `<strong>` → 같은 역할, 인라인 강조
- `<label>` 안의 `<input>` → 구조적 연결
- 이미 역할별로 각각 `<span>`으로 감싼 경우: `<button><span class="btn-label">...</span><span class="btn-loader">...</span></button>` → OK (raw 텍스트 없음)

**올바른 예:**
```html
<!-- ✅ 제목 + 설명 역할 분리 -->
<a href="/research/publications">
    <span class="item-title">Publications</span>
    <span class="item-desc">Peer-reviewed papers and research outputs</span>
</a>

<!-- ✅ 버튼 라벨 + 로더 -->
<button type="submit">
    <span class="btn-label">Send</span>
    <span class="btn-loader" aria-hidden="true"><i class="fa-spinner fa-spin"></i></span>
</button>

<!-- ✅ 아이콘 + 날짜 텍스트 -->
<p class="event-date">
    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
    <span>31 January 2026 14:00~15:00</span>
</p>
```

**태그 선택 가이드:**
- 제목/라벨: `<span class="*-title">`, `<strong>`, 위계상 맞으면 `<h2>`~`<h6>`
- 설명/부가정보: `<span class="*-desc">`, `<p class="*-desc">`
- 버튼 텍스트: `<span class="*-label">`
- 인라인 강조: `<strong>`, `<em>`, `<b>`

**작업 후 체크:**
```bash
# 아이콘(<i>) + raw 텍스트 혼재 패턴
grep -rn "fa-[a-z].*>[^<]" resources/views/ --include="*.php" -A1 | grep -v "aria-"

# 태그 내 raw 텍스트 + 자식 태그 혼재 (홈 뷰 기준)
grep -Pn ">[^<>]{2,}<(?!\/|!--|php|=)" resources/views/components/ resources/views/pages/ -r --include="*.php"
```
결과 0건이면 통과.
