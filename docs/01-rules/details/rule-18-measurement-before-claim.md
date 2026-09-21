---
title: 절대 규칙 #18 — Measurement Before Claim (rem→px 실제값 검증)
categories: [rule, ui, measurement, communication]
desc: rem 값을 보고할 때 반드시 px 환산을 동봉. 루트 font-size 를 코드로 사전 확인. 사용자의 시각 지적과 토큰 값이 충돌하면 토큰을 정당화하지 말고 실제 렌더 px 를 재측정.
related: [rule-11-typography-utilities, rule-17-design-system-utility, rule-15-design-pipeline]
updatedAt: 2026-05-26
---

# 절대 규칙 #18 — Measurement Before Claim (전 역할 공통, Level 1 always)

> 본문 요약은 CLAUDE.md Level 1. 본 파일은 양식·시나리오·POST 체크 정본.
> 소속: Level 1 (always). #16 (변경 7단계)·#17 (디자인 시스템 유틸) 과 동급.

## 1. 왜 이 규칙이 필요한가

2026-05-26 발생 사례 (정본 기록):

사용자가 admin `/console/site/boards/1/fields` 의 "필드 추가" 버튼 위/아래 간격이 빠듯하다고 질문. Claude 가 SCSS 토큰 `var(--admin-space-md): 1.0rem` 을 읽고 "1.0rem = 16px" 로 환산해 "10px 입니다" 가 아닌 **"16px 입니다"** 라고 잘못 보고. 또한 사용자가 시각적으로 부족함을 지적했음에도 "토큰 값이 X 라서 정상입니다" 류로 정당화함.

실제 환경: [resources/scss/admin/base/_init.scss:13](resources/scss/admin/base/_init.scss#L13) 가 `html:has(body.admin) { font-size: 62.5%; }` 로 admin 의 1rem 을 **10px** 로 만듦. home 도 동일([resources/scss/home/base/_init.scss:16](resources/scss/home/base/_init.scss#L16)). Claude 가 루트 font-size 를 사전 확인하지 않고 브라우저 기본값(16px) 을 가정한 것이 1차 실수. 사용자의 시각 지적을 토큰 값으로 반박한 것이 2차 실수.

본 규칙의 목적은 동일 유형 실수를 **양식 강제**로 차단하는 것. rem→px 환산은 자동 grep 으로 검증할 수 없으므로, **보고 양식 자체를 의무화**해 누락 시 즉시 식별되도록 한다.

## 2. 3대 조항

### A. rem 보고 시 px 환산 동봉 의무

- **모든 rem 값은 "X rem (= Y px)" 형식으로 표기**. 예: `1.0rem (= 10px)`, `2.0rem (= 20px)`.
- 표·코드 블록·인라인 본문 어디든 rem 단독 표기 금지. 단, SCSS 코드 인용 자체(`margin: 1.0rem;` 라인을 그대로 보여줄 때)는 코드이므로 예외 — 그러나 본문 설명에서 그 값을 언급할 땐 환산 필수.
- 사용자가 px 단위로 질문하면 답변도 px 우선, rem 은 부가 정보.

### B. 루트 font-size 사전 확인 의무

- rem 환산 전에 **반드시 해당 컨텍스트의 `html { font-size }` 를 코드로 확인**. 추측·기본값(16px) 단정 금지.
- 본 프로젝트의 확정 사실:
  - **admin (`body.admin` 컨텍스트)**: 62.5% → **1rem = 10px** ([resources/scss/admin/base/_init.scss:10-14](resources/scss/admin/base/_init.scss#L10-L14))
  - **home (전사이트)**: 62.5% → **1rem = 10px** ([resources/scss/home/base/_init.scss:10-16](resources/scss/home/base/_init.scss#L10-L16))
- 추후 외부 라이브러리 wrapper·iframe·Email 템플릿 등 별도 컨텍스트 작업 시에도 동일하게 루트를 먼저 확인.

### C. 사용자의 시각 지적과 토큰 값이 충돌하면 사용자가 옳다

- 사용자가 "이 간격이 좁다/넓다/이상하다" 라고 지적했을 때 "토큰이 X 라서 정상입니다" 로 답변 금지.
- 즉시 다음 둘 중 하나:
  - (a) 실제 렌더 px 를 본 규칙 §A·§B 절차로 재계산해 사용자에게 보고
  - (b) Chrome DevTools 측정값을 사용자에게 요청 ("DevTools 에서 해당 영역의 computed margin/padding 측정값을 알려주실 수 있을까요?")
- 토큰 값이 사용자가 본 결과와 다르다면 **토큰을 정당화하지 말고 토큰을 의심**. 실제 렌더 결과가 진실.

## 3. 보고 양식 (필수, 간격·크기·여백·padding·margin·rem·px 관련 작업 시)

간격·크기 보고 시 답변 본문 첫머리에 반드시 다음 블록을 포함:

```
[측정 검증]
- 컨텍스트: admin / home / (기타)
- 루트 font-size: NN% (확인 경로: resources/scss/.../base/_init.scss:NN)
- 환산 단가: 1rem = NN px
- 보고 값:
  - <셀렉터1 속성>: <원본 rem> (= <환산 px>)
  - <셀렉터2 속성>: <원본 rem> (= <환산 px>)
```

본 블록이 누락된 간격 보고는 **본 규칙 위반**. 보고 직전 자체 점검.

## 4. POST 체크 (보고 직전 자체 검증, 0건 위반)

자동 grep 으로 잡히지 않는 항목이므로 **Claude self-check** 로 다음 3개 항목 점검:

```
# [1] 본 답변에 rem 값이 등장하는가? → 등장한다면 "(= NN px)" 환산이 모두 동봉되었는가?
# [2] "1rem = 16px" 같은 가정·단정 표현이 본문에 있는가? → 있다면 root font-size 를 코드로 확인했는가?
# [3] 사용자가 "X 가 좁다/넓다/이상하다" 라고 지적했는데, 답변에서 "토큰 값은 정상입니다" 류로 정당화한 적이 있는가?
```

위 셋 중 하나라도 위반이면 **답변 송신 전 수정**.

## 5. 정정 시나리오 (Before → After)

### 시나리오 1 — 사용자가 "이 간격 뭐야?" 질문

**Before (위반):**
> 1rem 이니까 16px 입니다.

(루트 font-size 미확인, 환산 단가 가정.)

**After (정정):**
> [측정 검증]
> - 컨텍스트: admin
> - 루트 font-size: 62.5% (resources/scss/admin/base/_init.scss:13)
> - 환산 단가: 1rem = 10px
> - 보고 값: `margin-bottom: var(--admin-space-md)` = 1.0rem (= 10px)

### 시나리오 2 — 사용자가 "여백이 없잖아" 라고 지적

**Before (위반):**
> SCSS 에 `margin-bottom: 1rem` 으로 명시되어 있으니 정상입니다.

(사용자 시각 지적을 토큰으로 반박.)

**After (정정):**
> 확인했습니다 — 1.0rem 은 admin 에서 10px (62.5% base) 로 렌더됩니다. primary 액션 버튼 주변 공간으로 부족하다는 지적이 맞습니다. 목표 간격(예: 20px) 알려주시면 토큰 갱신안 작성하겠습니다.

## 6. 관련 규칙

- **#16 (`change-process.md`)** — 본 규칙 위반 사례 자체가 ④ 구현·⑤ 검증 단계의 보고 실패. #16 의 "실행 기반 보고" 양식 안에서 본 규칙의 [측정 검증] 블록이 함께 출력되어야 함.
- **#17 (`rule-17-design-system-utility.md`)** — spacing 토큰 사용 규칙. 본 규칙은 #17 이 다루는 "토큰을 어떻게 쓰는가" 의 보고·환산 측면.
- **#11 (`rule-11-typography-utilities.md`)** — 타이포 토큰 사용. font-size rem 값 보고 시에도 본 규칙 §A·§B 적용.

## 7. 생성 맥락 (2026-05-26)

사용자 보고 / Claude 실수:

1. 사용자: admin 게시판 fields 화면 "필드 추가" 버튼 위/아래 간격 질문
2. Claude: SCSS 토큰 `--admin-space-md: 1.0rem` 을 읽고 **"1rem = 16px"** 로 잘못 환산해 보고
3. 사용자: "1rem이면 10px로 되어있는거아니야? html에 62.5%하잖아" 지적
4. 재조사 결과: admin·home 모두 62.5% base. Claude 의 환산이 완전히 틀림
5. 사용자: "이런 일 다시는 발생하지 않도록 강력 규칙 추가" 지시 → 본 규칙 신설

본 규칙은 Level 1 always 로 배치 (#16·#17 동급). 신뢰 회복용 최우선 규칙.
