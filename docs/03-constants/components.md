---
title: 컴포넌트 카탈로그
categories: [design, dev]
desc: UI 생성 전 필독 — 기존 컴포넌트 재사용 또는 신규 생성과 카탈로그 등록 절차
related: [constants.architecture, constants.conventions, constants.standards]
updatedAt: 2026-09-30
---

# 컴포넌트 카탈로그

- 2026-10-07 공통 spacing: root의 sm/md/lg/xl/2xl은 fluid(10,12)/(12,16)/(20,24)/(24,32)/(32,40), xs는 최소8px 유지. brochure/partners에서 재사용하며 dialog 닫기는 최소40px. 목록 전역 초기화는 RESET에만 ol/ul/li 적용, INIT 중복 제거. 상세: `04-history/261007-fluid-reset-qa.md`.

- 2026-10-07 `partnerit-brochure-modal`/`partnerit-form-field`: 공통 native dialog, 기존 CTA·ScrollManager·타이포 재사용, AJAX 성공 후 소개서 다운로드. 브로슈어 feature flag로 전역 활성화. 필수 4항목+동의/선택 문의, 서버 검증·중복 방지·오류 복구. `partnerit-hero-preview`: Figma 노트북 외곽 원본+HTML 대시보드/메일/분석 카드. `partnerit-partners`: Figma 원본 14종을 공통 container 내 7/4/2열로 전체 표시. 상세 검증·배포는 `04-history/261007-customer-feedback-qa.md`.

- 2026-09-30 히어로 eyebrow 대비 확장: 공고 매칭·AI 보고서·요금정책·고객사례 상단 배지는 공통 SCSS에서 `text-navy` 배경/`ui-white` 글씨로 통일. 본문 eyebrow와 가격·사진 후기 배지는 변경하지 않는다. 기존 타이포·정렬·모션 selector 유지.

- 2026-09-30 `partnerit-ai-report-context`: 조건 비교는 단일 플랫폼 목업으로 표현. ‘기업 맞춤 분석’ 헤더→요구조건3항목/기업정보4항목→합류선·AI분석→네이비 판단안내. 가짜 점수·기능 없음. 모바일에서도2열입력 유지, 아이콘/제목은 세로정렬. `NarrativeAos`는 개별카드 대신 전체패널에 visual1회 적용. AIhero eyebrow만 네이비/화이트로 pale배경과 구분.

- 2026-09-30 공통 eyebrow 배지: 기존 라벨7종(공고 hero/profile, AI hero/context, subhero, 메인 services, company vision, legal)은 `_partnerit-eyebrow-badge.scss`의 단일 선언을 공유. Surface Section 배경·Primary Hover 글씨·8px radius·6/12px padding, `f-body-3 --semibold`. 기존 모션용 클래스·내용·정렬 유지. 가격 플랜·사진 후기 배지는 의미가 달라 제외. 참고/공개 예시: `/matching`, `/ai-report`, `/pricing`, `/partners`.

- 2026-09-30 최종 QA 보정: `header.php` 하위 NAV는 내부 현재 경로 일치도 active로 인정해 상위/하위 URL이 같은 `/matching`에서도 활성 표시한다. `HeaderManager`는 드로어 열림 완료 후 실제 표시·활성화된 요소에만 포커스를 이동하고 Tab/Shift+Tab을 순환한다. `partnerit-metrics`의 92.4% 캡션은 소개서 원문인 ‘유료 전환 시 지속 이용 의향’이다.

- 2026-09-30 `partnerit-announcement-profile`: 왼쪽 설명부를 eyebrow·2단 강조 제목·핵심 설명·보안 안내 카드로 재구성. 기존 오른쪽 3단계 카드와 문구 의미는 유지하며 공통 announcement 타이포 유틸을 사용한다.

- 2026-09-17 `partnerit-swiper-controls`: 후기·인증서·대표 고객사례의 48px 이전/다음 버튼과 6px/24px 점 pagination을 통일하는 공용 컨트롤. 컴포넌트별 클래스는 JS selector로 유지하고 공용 클래스를 함께 사용한다.

- 2026-09-17 `partnerit-stories-featured`: CMS의 대표 여부 `extra2=Y` 전체를 1열 Swiper로 렌더링. 2건 이상일 때 공용 controls와 6초 autoplay를 노출하고, 1건이면 Swiper 래퍼만 유지해 불필요한 조작부를 숨긴다. 대표 전체는 일반 갤러리에서 제외한다.

- 2026-09-17 메인 인터랙션 정리: `partnerit-impact`의 막대·도넛 차트는 viewport 최초 진입 시 GSAP으로 1회 재생하고 reduced-motion에서는 정적 표시. 고객 후기 Swiper는 5.2초, 회사 인증서 Swiper는 3.6초 autoplay하며 hover·키보드 focus 중 일시 정지하고 조작 후 계속 재생.

- 2026-09-17 모바일 헤더/드로어: `partnerit-header__actions`가 무료 체험 CTA와 햄버거를 하나의 우측 액션 그룹으로 묶는다. 모바일 submenu의 좌측 세로선·점 표시는 제거하고 현재 메뉴를 Primary 9% 배경으로 표시한다.

- 2026-09-16 공통 헤더 active: MenuItem::isActive() 활성 체인 재사용. PC 상위 메뉴 블루 밑줄/하위 메뉴 블루 배경, 모바일 현재 메뉴 블루. aria-current 적용. 360px 이하 로고120px로 CTA 여유 확보.

- 2026-09-16 비전 고도화: `partnerit-company-vision`은 제목/본문 2열, 1024px 이하 1열. V3 배경·원문 유지, 연결/데이터/기술 강조 및 결론 구분선. 기존 Figma 350:11782를 참고하되 사용자 요청으로 배치를 개선.

- 2026-09-16 Soft Blue V3: 회사소개/공고 매칭/AI 보고서 배경6·아이콘25, 컨소시엄 협업 목업2 최신화. 적용표 및 원본 노드는 `260916-assets-v3.md` 참조.

- 2026-09-16: `partnerit-company-certificates` — 회사소개 인증서 Swiper. 중앙 이전/페이지 점/다음 조작부, Primary blue 활성 상태, 끝단 disabled 처리. 별도 하단 안내 문구 제거; 각 서류의 출원 상태 캡션은 유지.

## Partnerit public-site components

- AI report context/capability 목업 패널: 첨부 Frame17371 색감 참고, 좌측 소프트블루→우측 아이스블루 그라디언트 통일. 외곽·목업 프레임·툴바 테두리 제거, 라운드·목업 그림자·비율 유지.

- company-vision 최신: 기업 대표 협업 콘셉트 생성 사진을 배경으로 사용(실제 고객 사진 아님). 중앙 화이트 오버레이로 가독성 확보. 본문3개는 한 그룹으로 등장; 설명16~20px, 결론28~36px 간격 차등. 기존 문단별 stagger 대체.

- company-vision 최신: 단일 카드 사양을 텍스트 중심 100svh GSAP pin 장면으로 대체. CompanyVisionController가 제목→본문3개와 원본 PNG 배경을 scrub reveal. 768px 미만/높이700px 미만/reduced-motion/내용 높이 초과는 정적 표시. 기본 CSS는 텍스트를 숨기지 않음.

- company-hero: 중앙 정렬, 상단 패딩에 공통 --header-height 반영. 고정 헤더 아래 표시 영역 기준 상하 균형, 모바일 동일 원칙.

- company-vision 최신: 연결·데이터·기술 문장은 분할 없이 하나의 화이트 라운드 카드 안에서 자연 줄바꿈. 3행 개별 카드 사양 대체, 키워드 블루 강조 유지.

- company-history: 연도별 마지막 항목은 하단 패딩·구분선·최소 높이 제거, 불릿은 첫 줄 기준 정렬.

- company-flow 제목의 01~04 접두 번호 제거. 아이콘·설명·카드 순서는 유지.

- company-vision: 원문과 V3 배경 유지, 연결/데이터/기술 3행 화이트 카드 및 비전 결론 블루 패널. company-flow 아이콘 64px 슬롯에 surface-section 배경·radius16px, 그림 48px 중앙 배치.

- announcement-profile: 오른쪽 카드의 시각적 01·02·03 번호 제거. 아이콘·제목·설명 및 순서 목록 의미는 유지.

- context 최신: 정보 카드 solid surface, 결과 문구 위 border 제거. 보고서 프리뷰 제목/캡션 삭제, 그라디언트 패널 중앙에 목업만 유지.

- capability 이미지 패널: Primary/white/surface 토큰 그라디언트, 흰 브라우저 프레임과 은은한 그림자. 이미지 원본 비율·좌우 stretch 유지.

- ai-report hero 제목 bold700. context 입력 카드에 흰 아이콘 박스, 결과 문장2행/Primary 강조. 카드·프리뷰만 토큰 기반 은은한 그라디언트 적용, 확정 PNG 섹션 배경은 유지.

- capability 최신: 제목+카드+CTA 왼쪽 copy, 오른쪽 이미지 패널의 2열 stretch. 이전 제목/CTA 전체행 결정 대체. 900px 이하 자연높이 세로배치.

- ai-report-context 프리뷰: 설명과 같은 높이의 파스텔 패널 안에 제목/브라우저 프레임/원본 이미지/캡션 배치. 모바일 자연 높이, 이미지 비율 유지.

- 컨테이너 통일: consortium-collaboration의 920px/872px 폭 제한 제거, no-container-xl 최대1320px/양측24px 사용. 그리드100%, 카드 이미지449:253 비율. announcement 모바일 양측20px 재정의 제거하여24px 통일.

- consortium-matching-system: 핵심 기능 배지4개 복원. 모집/참여 설명은 구분선+라벨 배지+요약으로 축약. PC 좌우 stretch, 고정 섹션 최소높이 제거.

- 최신 결정: 서비스3페이지 요금 compact 안내 삭제. partners 다음 final-cta로 연결. pricing compact 옵션/스타일 제거, 메인·요금정책 전체 가격표 유지.

- ai-report-capability: 제목·CTA는 공통 전체행, 설명 카드와 목업은 가운데2열 stretch. 900px 이하 세로 배치. 이미지 비율 유지.

- ai-report-context 개선: intro 2열(설명/조건+기업정보), 모바일 세로. walkthrough 상단 구분선 및 여백, 읽기 항목별 구분선. AOS fade 650ms, 0/100/200ms 순차 지연, reduced-motion 정지.

- 서비스 콘텐츠 보강: announcement-profile 이용 단계, ai-report-context 보고서 해설, consortium-matching-system 양방향 이용 안내. 기존 컴포넌트 확장 및 기존 목업/V3 아이콘 재사용.
- partnerit-pricing의 compact 옵션: 서비스3페이지에서 요금 비교 안내, 미지정 시 기존 전체 가격표 유지. final-cta는 서비스 경로에 따라 카피 분기. 상세: 260916-service-content.md.

- 260916 원본 재대조 결과: `260916-publishing-migration.md` 참조. 특허·공통 CTA 배경 원본 복원, 최신 블루 Hero 카드 SVG, 성과 수치/설명 정정. 공통 컬러와 1320px 실내용 컨테이너 반영.

- 플로팅 전환: TOP absolute 배치 및 opacity/transform 240ms, 숨김 visibility 지연. 보미는 has-top에 따라 같은 시간으로 이동. display:none 방식 대체, reduced-motion 지원.

- 보미 플로팅 링크: 무료 체험 로그인 새 탭 이동. TOP 비표시 시 display:none으로 자리도 제거하여 보미가 하단으로 내려옴.

- 메인 12개 섹션의 본문/헤더 컨테이너는 `no-container-xl`로 통일. impact/patent 별도 폭과 solutions/services 여백 예외 제거. 리뷰 슬라이더·파트너 마퀴의 전체폭 트랙은 유지.

- `partnerit-metrics__inner`: 메인과 동일한 `no-container-xl` 사용. 별도 980px 폭/좌우 여백 제거.

- `floating-button`: Frame17644(350:9345) 원본 보미 PNG 상시 고정 표시. TOP은 300px 초과 스크롤 시 표시, #3c82f6. 보미 클릭 기능 미지정. 모바일 safe-area 유지.

- 무료 체험 CTA 목적지: `https://app.partnerit.kr/account/signin` (사용자 확정). 헤더/메인 Hero/AI 보고서 Hero/공통 하단에서 새 탭 이동(`noopener noreferrer`). 공통 CTA의 `newTab` 기본값은 false.

- 메인 `partnerit-metrics`: 숫자는 `f-display-metrics`(24~40px), 1024px 이하 2열. SlotCounter 종료 후 최종 문자열로 치환하여 릴 DOM을 남기지 않음.

### 260916 요금정책 / 고객사례 (Figma 350:15049 / 350:15399)

- `partnerit-subhero`: kind=pricing/stories, 두 페이지의 원본 타이틀·설명과 전용 타이포 변형.
- `partnerit-story-card`: story 데이터와 featured 플래그로 대표 후기/갤러리 카드 재사용.
- `partnerit-stories-featured`, `partnerit-stories-gallery`: CMS 대표 전체 Swiper와 일반 후기 갤러리. 대표 다중 선택을 지원하며 대표 게시물은 갤러리에서 중복 제외. 갤러리 숫자 pagination은 서버 페이지 이동으로 유지.
- 원본 PNG 3개와 화살표 SVG 2개: `resource/images/stories/`. PHP/SCSS 분리, 인라인 스타일 없음.
- `/pricing`은 기존 가격/리뷰/파트너/CTA, `/partners`는 파트너/CTA 재사용. 실제 공개 페이지에서 반응형 검증.

### 260916 AI 보고서 (Figma 350:13649)

- `partnerit-ai-report-{hero,context,preparation,diagnosis,capability}`: 원본 전용 5개 섹션. PHP와 동명 SCSS 분리, `_partnerit-ai-report.scss`에서 범위 토큰/컨테이너/목업 규격 관리.
- `partnerit-cta-link`의 `gradient` 변형: 66px 그라디언트 무료 체험 CTA. 기존 primary/secondary 기본값 유지.
- 원본 에셋: `resource/images/business/ai-report/` PNG 4개, SVG 6개. mockup node 350:13673/14077/14496/14581.
- 타이포는 `_headings.scss`의 ai-report-hero/label/eyebrow, cta-large 및 기존 본문 토큰 사용. 하단은 공통 리뷰/파트너/가격/CTA.
- 현재 저장소에 console/components 및 ui-audit 경로가 없어 실제 공개 경로 `/business/ai-report`에서 검증.

### 260916 공고 매칭 (Figma 350:12186 / master 374:676)

- 전용 7개: `partnerit-announcement-hero`, `partnerit-announcement-problem`, `partnerit-announcement-collection`, `partnerit-announcement-profile`, `partnerit-announcement-selection`, `partnerit-announcement-management`, `partnerit-announcement-report`.
- 페이지는 include 조합만 담당. 각 PHP와 동명의 SCSS를 분리하고 `_partnerit-announcement.scss`에 페이지 범위 토큰·컨테이너·공통 목업 규격을 둔다.
- 타이포: `abstract/_headings.scss`의 `announcement-*` 명명 토큰 사용.
- CTA 및 하단 리뷰·파트너·요금제·최종 CTA는 기존 공통 컴포넌트 재사용.
- 에셋: `resource/images/business/announcement/`의 primary-*.svg, collect/normalize/attachment.png, profile-*.png, industry.png, hero-wave.png, hero/selection/report-dashboard.png. 외곽 그림자는 SCSS에서 적용.

| Component | View | Purpose |
|---|---|---|
| Main hero | `views/components/hero.php` | 100vh-class homepage hero with Figma 01_Hero copy, CTA pair, and four independently staged visual layers. Styles: `resource/scss/components/_hero.scss`; Figma exports: `resource/images/main/hero/`. |
| Partnerit metrics | `views/components/partnerit-metrics.php` | Homepage proof-point cards; the first three values use one-shot viewport-triggered slot reels, with accessible static text. Styles: `resource/scss/components/_partnerit-metrics.scss`; controller: `resource/js/animation/SlotCounter.js`. |
| Partnerit patent | `views/components/partnerit-patent.php` | Homepage patent-application proof band with the official KIPO horizontal SVG mark and application number. Styles: `resource/scss/components/_partnerit-patent.scss`. |
| Partnerit problem | `views/components/partnerit-problem.php` | Homepage problem statement with a desktop sticky copy column and three Figma-exported PNG cards. Styles: `resource/scss/components/_partnerit-problem.scss`; assets: `resource/images/main/problem/`. |
| Partnerit solutions | `views/components/partnerit-solutions.php` | Homepage solution overview with a responsive four-card grid and high-resolution Figma visual exports. Styles: `resource/scss/components/_partnerit-solutions.scss`; assets: `resource/images/main/solutions/`. |
| Partnerit services | `views/components/partnerit-services.php` | Homepage services overview with three alternating feature rows, dedicated detail links, responsive mockups, and softly layered Figma backgrounds. Styles: `resource/scss/components/_partnerit-services.scss`; assets: `resource/images/main/services/`. |
| Partnerit service details | `views/components/partnerit-service-details.php` | Homepage Services Details section with centered introduction and a four-column divided layout. Each card renders its own 2× Figma-exported PNG with `object-fit: contain`; the shared background uses soft blue/mint/lilac glows plus transparent generated `obj_1` sparkle and `obj_2` AI assets. Styles: `resource/scss/components/_partnerit-service-details.scss`; assets: `resource/images/main/service-details/service-*-v2.png`, `service-details-obj-*-v2.png`. |
| Partnerit impact | `views/components/partnerit-impact.php` | Homepage proof section after Service Details. Three responsive metric cards emphasize search-time, partner-setup, and decision-time reductions; each includes an accessible HTML/CSS comparison chart. `ImpactChartController` animates bars and the donut once on viewport entry and respects reduced-motion. Styles: `resource/scss/components/_partnerit-impact.scss`. |
| Partnerit reviews | `views/components/partnerit-reviews.php` | Homepage customer-story carousel after Partnerit impact. Uses the exact three unique photo layers, copy, badge, arrows, dimensions, offsets, and five pagination positions from Figma node `225:1461`; off-canvas placeholder positions reuse only the exported preview layers until the admin feed replaces the array. `SwiperController` provides keyboard, clickable pagination, card-next, previous/next navigation, and 5.2-second autoplay with hover/focus pause. Styles: `resource/scss/components/_partnerit-reviews.scss`; Figma assets: `resource/images/main/reviews/`. |
| Partnerit partners | `views/components/partnerit-partners.php` | Homepage partner-logo proof section after customer reviews. Uses the two Figma logo frames as local PNG assets, in equal logo cards and opposing seamless CSS marquee rows. Styles: `resource/scss/components/_partnerit-partners.scss`; assets: `resource/images/main/partners/`. |
| Partnerit pricing | `views/components/partnerit-pricing.php` | Homepage pricing and add-on service grid after the partner marquee. Uses Figma node `225:1563` copy, three plan cards, four add-on cards, local exported service icons, and responsive CSS grids. Styles: `resource/scss/components/_partnerit-pricing.scss`; assets: `resource/images/main/pricing/`. |
| Partnerit CTA link | `views/components/partnerit-cta-link.php` | Shared Partnerit primary/secondary CTA link with the Figma 52px button geometry. `download` prop을 전달하면 동일 출처 자료 파일의 다운로드 링크로 렌더링한다. Styles: `resource/scss/components/_partnerit-cta-link.scss`. Used by the final CTA and available for later section reuse. |
| Partnerit final CTA | `views/components/partnerit-final-cta.php` | Homepage closing CTA from Figma node `225:1761`; responsive copy/actions over the exact exported background. Styles: `resource/scss/components/_partnerit-final-cta.scss`; asset: `resource/images/main/footer/final-cta-bg.png`. |
| Partnerit consortium hero | `views/components/partnerit-consortium-hero.php` | Consortium matching page hero from Figma node `350:11114`; contains responsive title/copy and a clipped dashboard export with a bottom-up entrance. Styles: `resource/scss/components/_partnerit-consortium-hero.scss`; asset: `resource/images/business/consortium/consortium-dashboard.png`. |
| Partnerit consortium problem | `views/components/partnerit-consortium-problem.php` | Problem-definition section following the consortium hero from Figma node `350:10875`; two-column context header and three responsive problem cards. Styles: `resource/scss/components/_partnerit-consortium-problem.scss`; icons: `resource/images/business/consortium/problem/`. |
| Partnerit consortium matching system | `views/components/partnerit-consortium-matching-system.php` | Two-column matching-system feature section from Figma node `350:10899`; CSS gradient visual, exported dashboard panel, explanatory copy, and staged process badges. Styles: `resource/scss/components/_partnerit-consortium-matching-system.scss`; asset: `resource/images/business/consortium/matching-system/matching-panel.png`. |
| Partnerit consortium features | `views/components/partnerit-consortium-features.php` | Four-card AI matching feature grid from Figma node `350:10960`; exact icon exports, responsive 4/2/1-column layout, and staggered reveal. Styles: `resource/scss/components/_partnerit-consortium-features.scss`; icons: `resource/images/business/consortium/features/`. |
| Partnerit consortium collaboration | `views/components/partnerit-consortium-collaboration.php` | Integrated collaboration-tool section from Figma node `350:10988`; four exported product UI visuals in a responsive 2x2 card grid. Styles: `resource/scss/components/_partnerit-consortium-collaboration.scss`; assets: `resource/images/business/consortium/collaboration/`. |
| Partnerit company introduction | `views/components/partnerit-company-*.php` | Company page from Figma node `350:11731`; hero, service flow, vision, history and certificate Swiper. Styles: matching `resource/scss/components/_partnerit-company-*.scss`; assets: `resource/images/about/company/`. |
| Partnerit announcement matching | `views/components/partnerit-announcement-*.php` | Announcement matching page from Figma node `350:12186`; AI announcement hero dashboard, qualification analysis, profile reuse, eligible-opportunity selection and report CTA. Styles: matching `resource/scss/components/_partnerit-announcement-*.scss`; assets: `resource/images/business/announcement/`. |
| Partnerit global footer | `views/components/footer.php` | Global public footer from Figma node `225:1772`; exact white Partnerit lockup, company information, service navigation and policy row. Included automatically by `views/layouts/main.php`; styles: `resource/scss/layouts/_footer.scss`. |

> **⛔ 강력 규칙 — UI 컴포넌트 작업 시 첫 번째 행동은 반드시 `/console/components` 확인.**
> - 있으면 **재사용** (새 마크업 절대 금지).
> - 없으면 만들고 **이 파일에 한 줄 + `/console/components` 쇼케이스 블록 추가** (세트).
> - 디자인 토큰만 사용. 색·간격·폰트 하드코딩·인라인 스타일 금지.
>
> 실제 렌더 미리보기: `/console/components` (= `resources/views/admin/components.php`).
> 관련 규칙: `docs/01-rules/must-check.md` C-1 · `.claude/agents/publisher.md` "컴포넌트 재사용".

## 0. 절차 (퍼블/디자인/관리자 화면 작업 시)

1. **먼저 검색** — 이 문서 + `/console/components` 쇼케이스 + `resources/views/{components,admin/components,admin/form,sections}/` 에 비슷한 게 있나? 있으면 **그걸 쓴다** (호출만, 새 마크업 금지).
2. **변형이 필요하면** — 임의 분기·복붙 금지. 컴포넌트에 **prop / modifier**(`--sm`, `--ghost` 등)를 추가해서 처리. 한 컴포넌트 = 한 소스.
3. **없거나, 방향/스타일이 근본적으로 달라 새로 만들어야 하면**:
   - (a) 프로젝트 톤·간격·색에 **유사하게** — 색·간격·폰트·라운드·그림자는 디자인 토큰(`abstract/_variables.scss`의 `--admin-*`, 공개 쪽은 `resources/scss/home/abstract/*`)만 사용. 하드코딩·인라인 스타일 금지.
   - (b) 단일 소스로: 뷰는 `resources/views/{admin/components,admin/form,components}/<name>.php`, 스타일은 `resources/scss/admin/components/_<name>.scss`(또는 `home/components/_<name>.scss`)에 두고 `index.scss`에 `@forward`.
   - (c) **이 문서에 한 줄 추가** + 가능하면 `/console/components` 쇼케이스에도 한 블록 추가.
4. 작업 후 `must-check.md` C 카테고리 재점검.

> 페이지 전용(특정 화면에서만 쓰는) 마크업/스타일은 컴포넌트가 아니다 — `resources/scss/admin/pages/_<page>.scss` 같은 페이지 파일에 둔다. 단 "다른 화면에서도 또 쓰겠다" 싶으면 컴포넌트로 승격하고 여기 등록.

---

## 1. 관리자 폼 프리미티브 — `component('<type>', [...])` (뷰: `resources/views/admin/form/*.php`)

`component()` 헬퍼(`framework/Helpers/components.php`)로 호출. `admin-form__*` 클래스 / 토큰 사용.

| type | 파일 | 용도 / 주요 옵션 |
|---|---|---|
| `input` | `admin/form/input.php` | 텍스트/이메일/숫자 등 단일 입력. `name,id,label,value,placeholder,hint,required,type,inputClass,wrap` |
| `textarea` | `admin/form/textarea.php` | 여러 줄 입력. 위 + `rows`, `textareaAttrs`(추가 속성 배열), `wrap=false`/`noFieldWrapper=true`(래퍼 없이 label+textarea만) |
| `select` | `admin/form/select.php` | Slim Select 단일 선택. `options`(`[value=>label]`), `selected`, `placeholder` |
| `multiSelect` | `admin/form/multiSelect.php` | 커스텀 다중 선택 + 검색 + 태그 칩. `options`, `selected`(배열), `placeholder`, `searchPosition`(`'top'`=검색창 드롭다운 상단 고정), `addable`(새 항목 추가) |
| `checkbox` | `admin/form/checkbox.php` | 단일 체크박스(스위치 아님) |
| `radio` | `admin/form/radio.php` | 라디오 1개(`.admin-radio-wrap`). 여러 개는 ↓ |
| `radioGroup` | `admin/form/radioGroup.php` | 라디오 묶음. `name`, `options`(value=>label), `selected`, `hint`(하단 안내). **아이콘 카드 변형**: `icons`(value=>FA 클래스, 예: `'fa-solid fa-list'`)를 넘기면 자동으로 카드형(아이콘 위/라벨 아래, 선택 시 강조)으로 렌더. `variant:'card'` 로 강제 가능. 카드는 순수 CSS(`:has`)·JS 불필요 |
| `date` / `datetime` / `time` | `admin/form/{date,datetime,time}.php` | Flatpickr — 입력 영역 클릭 시 선택기 |
| `actions` | `admin/form/actions.php` | 폼 하단 버튼 행(`.admin-form__actions`). `buttons`=[`{type:'submit',label,class,form?}` / `{type:'link',href,label,class}` / `{type:'delete',action,confirm,label,class}`] — submit엔 로더 자동 |
| `tagInput` | `admin/form/tagInput.php` | 자유 입력 태그 칩. Enter/쉼표로 추가, × 제거. `value`(초기배열), `maxTags`, `maxLength`, `placeholder`, `stacked`(세로 한 줄씩 풀폭), `validate`('email'=이메일 형식만 칩 추가 허용). JS: `initTagInputs()` |
| `numberStepper` | `admin/form/numberStepper.php` | ± 버튼 숫자 입력. `value`, `min`, `max`, `step`, `suffix`(단위). JS: `initNumberSteppers()` |
| `charCounter` | `admin/form/charCounter.php` | 글자 수 카운터 textarea. `maxlength`(필수), `rows`. 80%↑ warn·100% limit 강조. JS: `initCharCounters()` |
| `richEditor` | `admin/form/richEditor.php` | **리치 에디터 (Quill 2 + quill-table-better)**. 이미지·동영상 드래그앤드롭 업로드 + 테이블 삽입(행/열 추가·셀 병합) + 코드 블록·인용·서식. `name`(필수), `id`, `label`, `hint`, `value`(초기 HTML), `placeholder`, `required`, `uploadUrl`(기본 `admin.uploads.attachment`), `enableTable`(기본 true), `enableVideo`(기본 true), `minHeight`(기본 `24rem`), `wrap`, `fieldClass`. **JS 자동 마운트**는 `RichEditor.initAll()` (`resources/ts/components/RichEditor.ts`)이 `[data-rich-editor]` 를 탐지. 마운트되면 hidden input 과 양방향 sync. **절대규칙 #2** — Quill 은 본 컴포넌트 또는 `QuillEditor.ts` 래퍼만 사용 |

> AJAX 폼은 `<form ... data-admin-ajax="1">` 로 표시하면 `AdminForm.ts`(공통)가 fetch 제출·로더·`{result,msg,redirect}` 처리. 직접 fetch 코드 짜지 말 것.

## 2. 관리자 UI 컴포넌트 (뷰: `resources/views/admin/components/*.php` · `include_view('admin.components.<name>', [...])`)

| view | 파일 | 용도 |
|---|---|---|
| `page-header` | `admin/components/page-header.php` | **페이지 헤더** — title·desc를 세로로 묶는 header-info + 선택적 actions. `title`(필수), `desc`?(raw HTML — `<code>`,`<strong>` 허용), `actions`?(버튼 HTML). SCSS: `pages/_page.scss` `.admin-page__header-info`. 사용처: features, system/logs, project-docs, library |
| `nav-tabs` | `admin/components/nav-tabs.php` | **페이지 단위 탭 바**(각 탭 = 별도 라우트 `<a>`). `tabs`=[`{label,url,active?,badge?}`], `ariaLabel`, `navClass`. SCSS: `components/_nav-tabs.scss`. 사용처: site-meta, project-docs, 접속 통계(analytics — `pages/analytics/_tabs.php` 가 위임, navClass=`analytics-tabs`; 필터 쿼리는 컨트롤러가 url 에 부착) |
| `toggle-switch` | `admin/components/toggle-switch.php` | **POST 폼 기반 ON/OFF 스위치** (iOS 스타일 트랙+썸). `action`(필수), `enabled`, `confirm?`, `disabled?`, `srLabel?`, `hint?`(기본 현재상태 "켜짐"/"꺼짐"), `hidden?`, `formClass?`. SCSS: `components/_toggle-switch.scss`. **비동기 처리는 `ToggleSwitchHandler.init(root)` 로**. 사용처: 기능 토글 페이지 |
| `segmented-control` | `admin/components/segmented-control.php` | **POST 폼 기반 radio 선택** (iOS 스타일 세그먼트). `action`(필수), `options`(필수, `[value=>label]`), `value`(필수, 현재 선택), `name?`(기본 `value`), `confirm?`, `disabled?`, `srLabel?`, `hidden?`, `formClass?`, `method?`(get/post/patch — patch면 `_method` hidden 자동). SCSS: `components/_segmented-control.scss`. **비동기 처리는 `SegmentedControlHandler.init(root)` 로**. 사용처: 기능 토글 enum (예: `admin.drawer.mode`) |
| `breadcrumb` | `admin/components/breadcrumb.php` | 빵부스러기. `breadcrumb`=[`{label,url|null}`] |
| `header` | `admin/components/header.php` | 관리자 상단 바 (레이아웃이 자동 포함). 알림: `data-admin-notifications*` + `AdminNotificationsDropdown` / `AdminNotificationsUnreadPoll`. 드롭다운에 **그룹 탭 + 개별/모두 읽음 처리** (그룹 ≥ 2일 때만 탭 노출, `config/notifications.php`의 `group_key` 기반 자동 생성). 폴링은 pill 개수만, 드롭다운 콘텐츠는 열 때마다 fresh fetch. 미읽음 pill은 **0 포함 항상 표시**, 1건 이상만 강조(`is-unread`). 폴링 주기·끄기: `config/admin.php`의 `notifications_poll_interval_seconds`(env `ADMIN_NOTIFICATIONS_POLL_SECONDS`, `0` = 폴링 끔). 라우트: `admin.notifications.{index,recent,unread-count,read,read-all}` (PATCH) |
| `sidebar` | `admin/components/sidebar.php` | 관리자 좌측 메뉴 (메뉴 추가 시 여기 수정) |
| `flash` | `admin/components/flash.php` | 세션 플래시 메시지 (`->with('success'|'error', ...)`) |
| `index-toolbar` / `index-filter` / `search-bar` | `admin/components/{index-toolbar,index-filter,search-bar}.php` | 목록 페이지 상단: 일괄작업·페이지이동 툴바 / 필터 / 검색. **`index-filter` 2행 레이아웃(2026-06-03)**: 검색 = 전용 풀폭 행(`.admin-index-filter__search`), 정렬·셀렉트·기간·보기 = 아래 행(`.admin-index-filter__filters`, flex-wrap 자연 줄바꿈). search 필드가 없으면 적용/초기화 버튼이 필터 행에 붙음. SCSS: `pages/_page.scss` `.admin-index-filter*`. |
| `index-table` (2026-06-03 신설) | `admin/components/index-table.php` | **목록 반응형 테이블 — 데스크탑 `<table>` + 모바일 카드(indexCard) 단일 소스 렌더**. 한 벌의 `columns`(`key`·`label`·`render?(closure)`·**`cardRender?(closure)`**(모바일 카드 전용 셀 — 데스크탑 인라인편집 input 과 분리해 중복 id/form 방지)·`compact?`·`primary?`·`th/tdClass?`) + `rows` 정의로 두 뷰를 함께 그려 어긋남 방지(#19). props: `columns`(필수), `rows`(필수), `rowId?(closure)`, `bulk?`(master+행 체크박스), `drag?`(정렬 핸들+`sortUrl`), `actions?(closure → table-actions)`, **`cardActions?(closure)`**(모바일 카드 전용 액션), `bulkUrl`/`bulkResource`/`sortUrl`/`tableAriaLabel?`. ⚠ `render`/`actions` 클로저는 데스크탑·모바일 2회 호출되므로 부수효과·중복 id 주의(번호 카운터 금지 — 호출측 맵 사용). **≤lg(1024px) 폭에서 테이블 숨김 + 카드 노출** (SCSS: `components/_index-card.scss` `.admin-index-card*`, 래퍼 `.admin-index-listing[data-bulk-rows-scope]`). 대량선택은 테이블·카드 양쪽 체크박스를 한 `BulkSelectManager`/`PageMoveTool` 가 추적(`[data-bulk-rows-scope]` 루트, 같은 id 미러링·중복 제거). 쇼케이스: `/console/guide/sections` §Index — Standard Table. **`bulk-checkbox`·`table-actions` 재사용** — 신규 행 마크업 페이지 인라인 금지(#19). 레퍼런스 사용처: `admin/pages/posts/index.php`. (나머지 index 페이지는 단계적 마이그레이션.) |
| `bulk-checkbox` | `admin/components/bulk-checkbox.php` | 목록 행 선택 체크박스 (일괄 삭제·이동). `index-table` 이 테이블·카드 양쪽에 자동 렌더. |
| `pagination` | `admin/components/pagination.php` | 관리자 페이지네이션 |
| `delete-form` | `admin/components/delete-form.php` | 단일 삭제 폼(확인 모달 포함) |
| `page-toolbar` (2026-05-31 신설) | `admin/components/page-toolbar.php` | **페이지 상단 보조 버튼 행**. page-header 아래에 ← 목록 버튼·관련 페이지 링크·인라인 폼을 묶음. `backUrl`(필수), `backLabel?`(기본 '← 목록'), `extraActions[]`(`['type'=>'link'\|'html', ...]`). 59개 create/edit/index 페이지 일괄 통일 (인라인 마크업 0건). |
| `empty-state` (2026-05-31 신설) | `admin/components/empty-state.php` | **목록 0건 빈 상태 표시**. `message`(필수), `description?`, `createUrl?`, `createLabel?`, `createClass?`. <code>if $total === 0</code> 분기에서 호출. 25개 index 페이지 일괄 통일. **공개 사이트의 `components/empty-state.php`와 다른 별도 컴포넌트** (admin 톤·구조). |
| `table-actions` (2026-05-31 신설) | `admin/components/table-actions.php` | **테이블 행 액션 묶음** (편집 + 삭제 + 추가 액션). `editUrl?`, `editLabel?`(기본 '편집'), `deleteUrl?`, `deleteConfirm?`, `deleteWithLoader?`, `extraActions[]`(`position`: 'before'/'after'). 삭제는 DELETE 메서드 form + data-confirm + admin-form-inline 자동 생성. 25개 index 페이지 일괄 통일. |
| `detail-card` (2026-05-31 신설) | `admin/components/detail-card.php` | **show 페이지 상세 카드** — 제목 + dt/dd 정의 목록 또는 자유 슬롯. `title`(필수), `rows[]`(`['label','value']`, value는 raw HTML — 호출 측 e() 책임), `slot?`(rows와 택일, 자유 HTML), `emptyText?`(둘 다 비었을 때 안내). SCSS 클래스: `admin-detail-card / __title`, `admin-detail-list`. |
| `detail-grid` (2026-05-31 신설) | `admin/components/detail-grid.php` | **detail-card 그리드 컨테이너**. `cards[]`(각 카드 = detail-card props), `columns?`(1-4, 기본 2 → `admin-detail-grid--cols-N` modifier). 페이지에서는 호출 1줄로 N개 카드를 N열로 배치. show 페이지 통일. |
| `entity-picker` (2026-06-03 신설) | `admin/components/entity-picker.php` | **버튼 + 모달 단일 선택 피커** (raw `<select>` 대체, #1). 트리거 버튼 클릭 → `<template>`(서버 신뢰 HTML)을 `Modal.content()`(#2)로 띄움 → 검색(client-side) + 카드 리스트, 선택 시 `baseUrl?{name}={id}&{extraParams}` 로 GET 이동(컨트롤러 기존 흐름 유지). props: `name`(필수), `items`[{id,label,sub?,thumb?,icon?}], `selectedId?`, `baseUrl`(필수), `extraParams?`, `placeholder?`, `title?`, `searchPlaceholder?`. SCSS `components/_entity-picker.scss`(`.admin-entity-picker`). TS `admin/global/AdminEntityPicker.ts`(AdminLayout.init 등록, `[data-entity-picker]` 훅, 인라인 스크립트 금지 #2). 쇼케이스 `/console/guide/components` §Selection. 사용처: 예약 슬롯 장비 선택. |
| `status-legend` (2026-06-03 신설) | `admin/components/status-legend.php` | **상태 범례(점+라벨)**. 캘린더·예약 등 상태색(pending=warn/approved=info/completed=success) 설명. props `items`[{status,label}], `class?`. SCSS `components/_status-legend.scss`(`.admin-status-legend`, `__dot--{status}`). 재사용(다른 캘린더·신청). 쇼케이스 §Feedback. |
| `calendar-filter` (2026-06-03 신설, 마크업+TS) | `admin/global/AdminCalendarFilter.ts` | **캘린더 상태 칩 토글(client-side)**. `[data-status-filter]` 안 `[data-status-toggle="{status\|all}"]` 칩 클릭 → 연결 `.imms-calendar` 의 `.imms-calendar__chip--{status}` 가시성 토글. 컨트롤러·board-calendar 변경 0. SCSS `components/_calendar-filter.scss`(`.admin-cal-filter`). 보통 board-calendar `controlsExtra` 슬롯에 둠. 쇼케이스 §Feedback. |
| `single-image-uploader` / `single-video-uploader` / `single-audio-uploader` | `admin/components/single-*-uploader.php` | 단일 파일 업로더(첨부 테이블 연동, 미리보기). SCSS: `components/_single-*-uploader.scss` |
| `service-item-deep-dsl-field` | `admin/components/service-item-deep-dsl-field.php` | Work 서비스 항목 DSL 입력기(특수) |
| `footer` | `admin/components/footer.php` | 관리자 푸터 |

관련 TS 컴포넌트(`resources/ts/admin/components/*`): `AdminIndexTable`, `BulkSelectManager`, `SortableList`, `PageMoveTool`, `FileUploader`, `colorPickerField`(Pickr + 그라데이션 — 색상 입력 필드는 이거 재사용), `ToggleSwitchHandler`(비동기 토글 — 아래). 전역 초기화는 `admin/global/*` (`AdminNotificationsDropdown`, `AdminNotificationsUnreadPoll` 등).

### ToggleSwitchHandler — 비동기 ON/OFF 토글

`ToggleSwitchHandler.init(root)` 로 `root` 내 `.admin-toggle-switch-form` 전체에 바인딩.

| 흐름 단계 | 동작 |
|---|---|
| 클릭 | `data-confirm` 있으면 `Modal.confirm` → 거부 시 중단 |
| 요청 중 | 버튼 `disabled` + hint 텍스트 "처리 중…" |
| 성공 | 클래스(`is-on`/`is-off`), hint("켜짐"/"꺼짐"), `aria-pressed`, 부모 `li` 상태 업데이트 → `Toast.success(msg)` |
| 실패 | 버튼 복원 → `Toast.error(msg)` (criticalError=true 이면 `Modal.alert`) |

> **규칙 — 상태변경·CRUD 작업의 피드백은 Toast 패턴으로 통일.** 페이지 새로고침 없이 인라인으로 반영하고, 성공은 `Toast.success`, 실패는 `Toast.error`, 확인이 필요한 파괴적 작업은 `Modal.confirm` 선처리. (`resources/ts/utils/Toast.ts` · `Modal.ts`). **직접 fetch 코드 짜지 말고 `ToggleSwitchHandler` (토글) 또는 `data-admin-ajax="1"` 폼 (AJAX 폼) 재사용.**

## 3. 공개 사이트 컴포넌트 (뷰: `resources/views/components/*.php`)

| view | 파일 | 용도 |
|---|---|---|
| `header` / `footer` | `components/{header,footer}.php` | 공개 페이지 공통 헤더·푸터. Footer는 Partnerit Figma node `225:1772` 기준으로 전 페이지에서 자동 포함. |
| `drawer` | `components/drawer.php` | 모바일 메뉴 드로어 |
| `floating-cta` | `components/floating-cta.php` | 우하단 고정 빠른연락(전화·카카오·SNS) — siteinfo 링크 + icon() 헬퍼, 스크롤 시 표시 |
| `sub-visual` | `components/sub-visual.php` | 서브페이지 상단 비주얼(타이틀 영역) |
| `marquee` | `components/marquee.php` | 흐르는 텍스트 띠 |
| `pagination` | `components/pagination.php` | 공개 목록 페이지네이션 |
| `popup` | `components/popup.php` | 메인 팝업 |
| `portfolio-modal` | `components/portfolio-modal.php` | Work 모달 |
| `email-verify-modal` | `components/email-verify-modal.php` | 비로그인 폼(문의·가맹) 이메일 인증 모달 — `purpose`(contact_inquiry·contact_history·franchise), `modalId`. JS는 `resources/ts/components/EmailVerificationModal.ts` 래퍼로만 초기화(인라인 스크립트 금지). |
| `section-prev-next-nav` | `components/section-prev-next-nav.php` | 이전/다음 글 내비 |
| `img-auto-cover` / `video-auto-cover` | `components/{img,video}-auto-cover.php` | 비율 유지 cover 미디어(LQIP/플레이스홀더) — 이미지·영상은 직접 `<img>` 쓰지 말고 이걸로 |
| `board-calendar` | `components/board-calendar.php` | 월별 캘린더 그리드(공개·관리자 공용). `year`, `month`, `cellsByDay`(`[day=>[['title','url','chipClass?'],...]]`), `mode`(`'public'`\|`'admin'`), `createUrlBase`(admin: 빈 셀 + 버튼 prefill `?date=YYYY-MM-DD`), `prevMonthUrl`, `nextMonthUrl`, `todayUrl?`, `monthLabel?`, `controlsExtra?`(controls 행 우측 raw HTML — 상태필터·범례 등). 클래스: `.imms-calendar` (SCSS: home `_imms-calendar.scss`, admin `_board-calendar.scss`). **상태칩 모디파이어**(admin) `.imms-calendar__chip--pending/--approved/--completed` + `[hidden]`(calendar-filter 연동, 2026-06-03 추가). 사용처: `pages/posts/layouts/calendar.php`(공개), `admin/pages/posts/calendar.php`·`sections/admin/slots-calendar.php`(관리자). |
| `search-overlay` | `components/search-overlay.php` | **사이트 통합 검색 오버레이** — 헤더 돋보기(`.site-header__search`) 클릭 시 풀폭 슬라이드 다운. 큰 입력창 + AJAX 자동완성 (`/search/suggest` → 카테고리별 상위 3건 그룹) + Suggested/Recent(localStorage) 칩. ARIA dialog, ESC/backdrop/X 닫힘, ↑↓ Enter 키보드 네비. TS: [SearchOverlayController](../../resources/ts/home/global/SearchOverlayController.ts) (HomeLayout.init에서 호출). SCSS: `_search-overlay.scss`. **layouts.main 이 자동 포함** — 페이지에서 호출 불필요. |
| `search-result-item` | `components/search-result-item.php` | 검색 결과 1행 (카테고리 배지 + 메타 + 제목 + 스니펫). `hit` props: `{category,title,url,snippet,meta}`. 사용처: `pages/search/index.php`. SCSS: `_imms-search-hit.scss`. |
| `imms-cta` | `components/imms-cta.php` | **표준 CTA 컴포넌트 (Phase 2 / Sprint A)**. `href,label,variant`(`primary`\|`secondary`\|`ghost`)`,size`(`sm`\|`md`\|`lg`)`,onDark`(다크 배경 위)`,external,icon`. SCSS: `_imms-cta.scss`. Primary/Secondary hover = 화살표 6px 우측 slide. **Ghost hover** = 좌측 precision line `::before` 12→28px 확장 + 라벨 navy→accent teal + 화살표 6px slide (IMMS 정밀 도구 톤, `var(--dur-base) var(--ease-out-quart)`). 모든 variant 의 아이콘 박스 baseline 정렬 픽스 적용 (2026-05-26). **페이지당 Primary 1개 룰**. raw 링크/버튼 새로 작성 금지. **절대규칙 #17-C**: 같은 의미 CTA 는 본 컴포넌트만, 변형은 modifier(`--ghost/--on-dark/--sm`)로. 구형 `imms-cta-link` 는 2026-05-26 전면 제거 완료. |
| `share-button` | `components/share-button.php` | **공유하기 버튼 + 공유 모달 (2026-06-02 신설)**. 상세 헤더 썸네일 위. `url`(공유 절대 URL), `title`, `label`(기본 `posts.share`). 버튼 클릭 시 같은 파일의 `<template data-share-template>`(링크 복사 + X/Facebook/LinkedIn)을 `Modal.content()` 로 띄움. TS: `ShareModal.ts`(HomeLayout.init 호출, clipboard + execCommand 폴백). SCSS: `_imms-share-btn.scss`. **절대규칙 #2** — 모달은 `Modal` 유틸만. |
| `reservation-equipment-item` | `components/reservation-equipment-item.php` | **장비 목록 1행 (2026-05-29 신설)**. facilities/reservation 전용. `name`(필수), `sub`(팀/한글분류), `contact`, `phone`, `thumb`(이미지 URL), `active`(is-active 상태), `href`. SCSS: `_reservation-equipment-item.scss`. 완전 독립 BEM (`rsv-eq-item`). 쇼케이스: `/guide/components` §08. |
| `reservation-notices-panel` | `components/reservation-notices-panel.php` | **예약 공지 박스 (2026-05-29 신설)**. facilities/reservation 우측 사이드. Notices / Education·Seminars 탭 + 리스트 + 페이저. `items`(`[{tag,body}]`), `activeTab`, `pagerCurrent`, `pagerTotal`. SCSS: `_reservation-notices-panel.scss`. BEM: `rsv-notices`. 쇼케이스: `/guide/components` §08. |
| `reservation-schedule-calendar` | `components/reservation-schedule-calendar.php` | **예약 월간 스케줄 달력 (2026-05-29 신설)**. board-calendar와 완전 독립(BEM: `rsv-cal`). 슬롯 칩 3종(pending/approved/completed). `year`, `month`, `monthLabel`, `activeView`(`schedule`\|`status`), `cellsByDay`(`[day=>[{label,status}]]`), `prevUrl`, `nextUrl`, `todayDay`, `footnote`. SCSS: `_reservation-schedule-calendar.scss`. 쇼케이스: `/guide/components` §08. |
| `event-card-wide` | `components/event-card-wide.php` | **가로형 이벤트 카드 (2026-06-04 신설)**. Events 보드 전용. 좌측 큰 이미지(`image`, 없으면 `imms-placeholder--16x9`) + 날짜 오버레이(`dateTop`/`dateBottom`) + 우측 일시(`datetime`)·제목(`title`)·설명(`excerpt`, line-clamp 2)·CTA. `href`, `concluded`(true→이미지 grayscale+opacity·텍스트 secondary·`__status` 라벨), `concludedLabel`. CTA 는 `imms-cta --ghost` 재사용(#17-C). 타이포·spacing 은 `.no-*` 유틸만(#17). SCSS: `_event-card-wide.scss` `.event-card-wide`(sm↓ 세로 전환). 사용처: `sections/events/events-board.php`. **#19**: 카드 마크업 페이지/섹션 인라인 금지 — 본 컴포넌트만. |
| `search-input` | `components/search-input.php` | **범용 검색 입력 (2026-06-02 신설)**. 돋보기 아이콘 + `<input type="search">` + 커스텀 × 클리어 버튼(값 있을 때만 노출, 클릭 시 값 삭제+포커스 유지). props: `name`(기본 `search`), `id`(기본 `name`), `value`, `placeholder`, `label`(sr-only), `clearable`(기본 true). 브라우저 기본 search 클리어 UI 는 `appearance:none` + `::-webkit-search-cancel-button{display:none}` 로 제거. HTML/SCSS/TS 완전 분리 — SCSS: `_search-input.scss` `.imms-search-input`, TS: [SearchInput.ts](../../resources/ts/components/SearchInput.ts)(`Application.boot()` 에서 `initAll()`, `[data-search-input]` 훅·인라인 스크립트 금지 #2). 사용처: `components/filter-sidebar.php`. **#19**: 검색 input 페이지/섹션 인라인 작성 금지 — 본 컴포넌트만. |
| `filter-sidebar` | `components/filter-sidebar.php` | **목록 필터 사이드바 (2026-06-02 신설, 모바일 시트·커스텀필드 확장)**. 검색(search-input 위임) + 정렬 + 카테고리 + 태그 + **커스텀필드 필터**(`customFilters[]`: select=라디오 / multiselect=체크박스, 옵션 = field_options). props: `action`, `resetUrl`, `search`, `searchPlaceholder`, `sort`/`sortOptions`, `categories`/`categoryId`, `tags`/`tagId`, `customFilters`, `locale`. **모바일(lg↓)**: 별도 `.imms-filter-trigger`("Filters") 버튼 → 하단 시트(드로어, backdrop+슬라이드업), `FilterSidebarController.ts`(HomeLayout.init, `[data-filter-sidebar]`/`[data-filter-trigger]`/`[data-filter-close]` 훅, Lenis stop/start). 라디오 변경 즉시 GET 제출 = `Application.ts::initNewsFilter`. SCSS: `_imms-filter.scss`. **하위호환**: 구형 인라인 `.imms-filter-sidebar:not([data-filter-sidebar])` 는 시트 변환 없이 정적 노출(통합검색 등). 사용처: `sections/home/news-list-paged.php`, `pages/research/publications/index.php`. **#19**: 인라인 작성 금지. (이전 단순 항목은 본 항목으로 갱신.) |
| `publication-detail-meta` | `components/publication-detail-meta.php` | **논문 상세 메타 블록 (Publications 전용, 2026-06-02 신설)**. Publications 게시판(`board.code='publications'`) 상세에서만 호출. `post`,`board`,`locale`. `Post::getFieldValue()`로 Authors·Journal·Published Date·Volume/Issue·Page·ISSN·DOI·Link·Google Scholar·PubMed + Keywords(태그 칩 `?tag_id=` 링크) + Abstract(=content) 를 `<dl>` 라벨 행으로 렌더. 빈 행 생략, DOI/Scholar/PubMed/external_link 는 `target="_blank" rel="noopener"`. SCSS: `_imms-pub-meta.scss`(`.imms-pub-meta`) + 키워드 칩 `_imms-keyword-chip.scss`(`.imms-keyword-chip`). 타이포 = `no-body-sm/xs` 유틸, 색·간격 토큰만(#17). **#19**: 페이지 인라인 금지. |
| `empty-state` | `components/empty-state.php` | **빈 상태 표시 (미니멀 Broad 톤, 2026-05-28 신설)**. 목록 0건·결과 없음 등을 중앙 정렬 + 라인 아이콘 + 1줄 메시지 + 1줄 보조 설명으로 통일. `message`(필수), `description?`, `icon`(`inbox`\|`document`\|`calendar`\|`null`, 기본 `inbox`), `variant`(`default`\|`compact`, 기본 `default`), `tag`(`div`\|`li`\|`p`, 기본 `div` — `<ul>` 내부면 `'li'`). SCSS: `_empty-state.scss`. 타이포는 `no-body-md` / `no-body-sm` 유틸, 색상은 `--clr-text-base` / `--clr-text-aux`, spacing 은 `--spacing-*` / `--section-*` 토큰만. 사용처: `pages/posts/layouts/{list,gallery,webzine}.php`, `components/notice-list.php`. **절대규칙 #19**: 빈 상태 마크업 페이지에 인라인으로 또 만들지 말 것 — 본 컴포넌트만 사용. |
| `person-card` | `components/person-card.php` | **인물 카드 (People 페이지, 2026-06-04 신설)**. People 2-col grid 1칸. props: `name`(필수), `slug`, `position`, `affiliation`, `photo`(빈 문자열이면 아바타 placeholder), `email`, `homepage`, `scholar`, `tags`(`Person::researchTags($locale)` 결과 `[{code,slug,name,url,is_leader}]`). **TG/CP 칩**: `<a class="imms-card__tag">` 코드(TG3·CP2) + hover title=풀네임(localized) + `research.tg.show`/`cp.show` 상세 링크, 리더는 `--leader`+★. 데이터는 정규화 관계 `person_research_tags`(pivot, i18n 라벨은 technical_groups/core_projects `t()` 재사용) — freeform `tg_tags` 문자열 대체(컬럼은 폴백 보존). **레이아웃(2026-06-04 개편)**: 좌측 **3:4 세로 이미지**(`flex 0 0 fluid(110,140)` + `align-self:stretch` → 카드 높이 100% cover) + 우측 info(padding 만 info 에). 카드 `height:100%`+`align-items:stretch` 로 같은 행 높이 정렬, 우측 내용 길어지면 카드·이미지 함께 확장. sm 이하 세로 전환(상단 풀폭 3:4). **사진 없을 때**: `.imms-card__thumb--placeholder`(IMMS 블루 그라데이션 + `fa-solid fa-user`, 아이콘 `fluid(40,56)`=40~56px). 데이터 가공은 호출 섹션(`about.people-list`)에서, 컴포넌트는 순수 표현(Person 모델 비의존). SCSS: `_imms-card.scss` `.imms-card--person`. 타이포·색·간격 토큰만(#17). **#19**: 인물 카드 페이지/섹션 인라인 금지 — 본 컴포넌트만. |
| `person-related-list` | `components/person-related-list.php` | **인물 상세 "관련 논문/수상" 리스트 (2026-06-04 신설)**. People 상세에서만 호출. props: `title`, `items`(`[{title, url?, meta?}]`). url 있으면 링크, 없으면 텍스트(Awards 는 상세 라우트 없어 텍스트). `items` 빈 배열이면 미렌더. 데이터: `Person::relatedPublications()`/`relatedAwards()`(pivot `person_research_related`). SCSS: `_about.scss` `.people-related`. 타이포 `no-*` 유틸·토큰만(#17). **#19**: 페이지 인라인 금지. |
| `newsletter-form` | `components/newsletter-form.php` | **뉴스레터 구독 form (Footer, 지시서 §3.6, 2026-06-03 신설)**. 이메일 `<input type="email">` + Subscribe `<button type="submit">`. props: `action`(제출 URL, 기본 `''` 미연결), `label`(안내문, 기본 'Sign up for our newsletter'), `placeholder`(기본 'Your email address'), `btnLabel`(기본 'Subscribe'), `onDark`(기본 false). SCSS: `_newsletter-form.scss` `.newsletter-form`. 타이포 = `no-body-*` 유틸, 색·간격은 `--clr-*`/`--spacing-*` 토큰만(#17). **UI 전용** — 제출 시 DB 저장·메일링 연동은 개발 범위(라우트·마이그레이션) 별도. 사용처: `components/footer.php`. **#19**: 뉴스레터 form 페이지/Footer 인라인 작성 금지 — 본 컴포넌트만. |

**Hero 인터랙션 마크업** (Phase 2 / Sprint A — `resources/scss/home/components/_imms-hero-fx.scss`):
- `.imms-hero` — 풀폭 Hero 컨테이너 (Display Serif headline + sub + actions + scroll)
- `.imms-gradient-mesh` + `__blob--a/b/c` — I6 Gradient Mesh 배경 (다층 blur blob, Three.js 미사용)
- `.marquee-whisper` + `--hero-foot` + `__track` + `__set` — I3 Marquee Whisper (CSS animation 기반, `jquery.marquee`와 별개)
- 트리거 속성: `data-anim="gradient-mesh"` / `data-anim="marquee-whisper"` / `data-anim="type-mask"` / `data-anim="magnetic"` — 각각 [`MotionTokens.ts`](../../resources/ts/home/global/MotionTokens.ts) 헬퍼로 reduced-motion·모바일 가드.

## 2.1 관리자 UI 컴포넌트 렌더링 헬퍼

admin 컴포넌트 호출에는 두 가지 동일한 방법이 있다. 신규 페이지는 `render_admin_component()` 형식을 권장.

```php
// 방법 A — 전통 호출 (기존 파일과 호환)
<?= include_view('admin.components.page-header', [...]) ?>

// 방법 B — 짧은 별칭 헬퍼 (신규 파일 권장)
<?= render_admin_component('page-header', [...]) ?>
// = include_view('admin.components.'.$key, $props) 의 wrapper
```

**정의:** `framework/Helpers/components.php` `render_admin_component(string $key, array $props = []): string`

## 2.2 관리자 페이지 섹션 패턴 카탈로그

Admin 페이지를 구성하는 재사용 가능한 패턴 단위. 신규 페이지 작업 시 이 카탈로그에서 맞는 패턴을 선택해 복제·확장하고, 없는 패턴은 `/console/guide/sections`에 먼저 추가 후 사용.

**라이브 쇼케이스:** `/console/guide/sections` (= `resources/views/admin/guide/sections.php`)

**4개 그룹 × 10개 패턴:**

| 그룹 | 패턴 키 | 제목 | 구성 요소 |
|------|---------|------|-----------|
| **List Pages** | `index-table-standard` | Index — Standard Table | `page-header` + `index-filter`(검색 풀폭 행 + 필터 줄바꿈 행) + `index-toolbar` + `index-table`(데스크탑 테이블 + 모바일 카드 단일 소스, 드래그정렬·체크박스·액션) + `pagination` |
| | `admin.index-list` (섹션, 2026-06-03) | Index — One-call Section | **위 전체 조립을 단일 `render_section('admin.index-list', [...])` 호출로**. 페이지는 데이터·`columns`·`filter.fields` 만 준비 → 헤더·필터·툴바·반응형 테이블/카드·페이지네이션·빈상태 자동 렌더. props: `title`/`subtitle`/`desc`/`createUrl`·`createLabel`(또는 `headerActionsHtml`), `pageClass`/`pageAttrs`(**`data-controller` 보존=드래그정렬 init**), `backToolbar`, `beforeFilterHtml`/`beforeListHtml`/`afterListHtml`(슬롯), `filter`, `rows`/`rowId`/`total`/`currentPage`/`lastPage`/`perPage`, `columns`/`actions`/`cardActions`, `bulk`/`drag`/`sortUrl`/`bulkResource`, `toolbar`, `pagination`, `emptyState`, `autoNo`(`'asc'`=$i+1 / `'desc'`=`admin_index_row_no` 내림차순; NO 컬럼 자동 주입, 데스크탑·모바일 동일값 보장 위해 rows 선주행 [id=>번호] 맵). 빈/목록 분기는 `count(rows)` 자동. **~27개 표준 index 페이지 통일** (예외: menu-categories·menu-tags = 행 구조적 인라인편집, 별도 유지). 뷰: `sections/admin/index-list.php`, 등록: `config/admin-sections.php`. 쇼케이스: `/console/guide/sections` §Index — One-call Section. |
| | `index-card-grid` | Index — Card Grid | `page-header` + `.admin-card-grid` + `.admin-card--media` |
| | `index-empty-state` | Index — Empty State | `page-header` + `.admin-empty` + `.admin-empty__text` + CTA |
| **Forms** | `form-single-column` | Form — Single Column | `page-header` + `<form>` + `component(*)` × N + `component('actions')` |
| | `form-two-column` | Form — Two Column | `.admin-form-2col__main`(2/3) + `.admin-form-2col__side`(1/3) + `admin-card` |
| | `form-with-locale-tabs` | Form — Locale Tabs | `locale-tabs` + 다국어 폼 필드 |
| **Detail / Show** | `show-readonly` | Show — Read-only | `page-header` + `.admin-card` + `.admin-detail` (dt·dd 쌍) |
| | `show-with-actions` | Show — With Actions | `.admin-show-layout__main` (상세) + `.admin-show-layout__aside` (액션 패널) |
| **Sub-resource Panels** | `inline-crud-panel` | Inline CRUD Panel | `.admin-list-section` + 제목 행 + AJAX 추가 버튼 + 정렬 테이블 |
| | `nested-settings-tabs` | Nested Settings — Tabs | `nav-tabs` (라우트 별 탭) + 각 탭 독립 폼 |

**규칙 — 신규 admin 페이지 작업 흐름:**
1. `/console/guide/sections` 에서 맞는 패턴 확인
2. 있으면 해당 패턴을 복제해서 실제 데이터에 맞게 수정
3. 없으면 `resources/views/admin/guide/sections.php` 에 먼저 패턴 추가 후 사용
4. 새 패턴은 이 카탈로그 표에도 한 줄 추가

## 3.1 공개 사이트 페이지 섹션 카탈로그 (뷰: `resources/views/sections/home/*.php`)

페이지를 구성하는 재사용 가능한 큰 단위 블록들. 컴포넌트(`imms-*`)와 유틸 클래스(`no-*`)로 조립되며, 각 섹션은 임시 데이터로 자급자족 렌더된다. 호출은 `render_section('home.<key>', $props)`. 쇼케이스 페이지: [/guide/sections](../../resources/views/guide/sections.php) (라이브 렌더 30종 × 5그룹).

**호출 규약:**
- 뷰: `resources/views/sections/home/<key>.php` (`render_section('home.<key>', $props)`)
- SCSS: `resources/scss/home/sections/_sec-<key>.scss` (`.sec-<key>` 단일 BEM 블록)
- props 없이 호출 가능 — 파일 상단에 `$x = $x ?? <demo>` 패턴으로 데모 데이터 자급
- 절대규칙 #17 준수: 컴포넌트(`imms-*`) + 유틸(`no-*`) 우선, 신규 BEM 은 layout/grid/position/background 만

**Group ① Hero / Page Header (5):** `hero-mesh`, `hero-video-split`, `page-header-center`, `page-header-left`, `section-intro-band`
**Group ② Content Grids (7):** `disease-areas-grid`, `feature-grid-3`, `feature-grid-2-bigimg`, `centers-leadership-3col`, `buildings-grid-4col`, `stats-band`, `tab-switch-content`
**Group ③ News / Events / Publications (9):** `news-latest-4up`, `news-featured-carousel`, `news-list-paged`, `events-grid`, `events-calendar`, `events-board` (`events.events-board`), `publications-list`, `publications-list-paged`, `related-publications`
- `events-board` (2026-06-04 신설, `render_section('events.events-board')`) — Events 페이지 보드. 상단 툴바(Past/Upcoming `imms-tabs--pill` + `search-input`) + `event-card-wide` 가로형 카드 세로 리스트, `concluded` 항목은 어둡게+"Event Concluded". **2-모드**(news-list-paged 패턴): (A)게시판 모드 — `$board`+`$paginator` 주어지면 events 보드 실제 Post 를 `event_start_at`·`location`·썸네일·요약으로 카드 매핑, `event_(end|start)_at < 오늘` 자동 종료판정, Past/Upcoming 탭=실제 `?filter=` 링크, 검색=GET search, 빈목록=`empty-state`, `pagination`. (B)디자인타임 모드 — 값 없으면 더미 배열(쇼케이스). **렌더 경로**: `/events` → `EventsController` → `PublicBoardRenderer::index('events')` → `board.layout='list'`(마이그 `260604-1108`) → `pages/posts/index.php` 가 `$listSection='events.events-board'` 호출. Past/Upcoming 필터·정렬은 `PublicBoardRenderer`(events 가드, `eventDateFieldId()` 상관 서브쿼리). BEM: `sec-events-board`(layout만). 뷰: `sections/events/events-board.php`. ⚠️ `pages/events/index.php` 는 미사용(board 이관 잔재).
- `publications-list-paged` (2026-06-02 신설) — Publications 게시판 공개 목록. `news-list-paged` 게시판 모드 미러링 + 좌측 `filter-sidebar`(검색 placeholder "e.g keyword, inventor name, etc" + 정렬 + 카테고리 + 태그) + 우측 본문을 `published_at` 연도(내림차순) `.imms-year-group` 헤딩으로 그룹핑, `.imms-card--publication` 카드(journal·date·title·authors·venue·DOI + 키워드 칩 `?tag_id=`) + `pagination`. `PublicBoardRenderer::index` 가 `boardCode==='publications'` 일 때 `pages/posts/index.php` 의 `$listSection` 으로 선택.
- `related-publications` (2026-06-02 신설) — Publications 상세 하단 "Related Publications". 키워드(태그) 교집합 상위 N건(`PublicBoardRenderer::relatedByTags`, 현재 글 제외, 공유 태그 수 desc)을 `.imms-card--publication` 카드(title·authors·journal·date)로 나열. 빈 배열이면 미렌더.
**Group ④ People / CTA / Detail (7):** `people-grid`, `directory-search`, `contact-form-band`, `locations-map`, `newsletter-footer-band`, `cta-callout-navy`, `article-body`
- `about.people-list` (2026-06-04 신설, `render_section('about.people-list')`) — People 페이지 본문(총계 toolbar + 카테고리 탭 + 인물 grid + 검색 JS). 인물 카드는 `person-card` 컴포넌트에 위임. props: `people[]`(Person 모델), `activeRole`, `roleLabels`(advisory_board 제외된 맵 — `PeopleController` 가 `array_diff_key` 로 필터). **Advisory Board 는 People 공개 목록에서 제외**(전용 섹션 `about.advisory-board-list` 별도). 뷰: `sections/about/people-list.php`. **#19**: People 본문 페이지 인라인 금지 — 본 섹션만.
**Group ⑤ Detail page utilities (5):** `article-header-meta`, `video-embed`, `related-prev-next`, `sticky-sidenav-layout`, `long-text-2col`

**Group ⑦ Facilities / Instrumentation (1, 2026-05-29 신설):**
- `facilities-reservation-layout` — 예약 페이지 전체 콘텐츠 섹션. 툴바 + 카테고리 pill 탭 + 3-col(장비 목록 | 장비 상세 | 신청 사이드) + 하단 스케줄 달력. `reservation-equipment-item`, `reservation-notices-panel`, `reservation-schedule-calendar`, `imms-cta` 조합.

**Group ⑧ Research — IMMS Platforms (3, 2026-06-04 신설, 260504 docx 3):**
- `research.platforms-list` (`render_section('research.platforms-list', $props)`) — IMMS Platforms 인덱스 목록. 1줄 1개 카드: 좌 사진 / 우 title·intro·Learn more(`imms-cta --ghost`). props: `heading`, `lead`, `learn_more`, `items[]`(title·intro·href·image?). 뷰: `sections/research/platforms-list.php`. **#19**: platforms 인덱스 인라인 금지 — 본 섹션만.
- `research.platform-detail` (`render_section('research.platform-detail', $props)`) — 플랫폼 상세(좌 소개 본문 / 우 이미지) + Key Features / Access 2-up 목록. IMMS Analytical Facility 페이지 전용. props: `eyebrow`, `title`, `body[]`, `image?`, `features_heading`, `features[]`, `access_heading`, `access[]`. 뷰: `sections/research/platform-detail.php`.
- `research.intro-bullets` (`render_section('research.intro-bullets', $props)`) — 소개 문단 + 리드 + 불릿 목록 + 마무리. "콘텐츠 추후 추가" 안내 페이지(Resources/Collaborations/Awards) 공용. props: `intro[]`, `list_lead`, `bullets[]`, `outro`. 뷰: `sections/research/intro-bullets.php`. **#19**: 3개 페이지 인라인 금지 — 본 섹션 단일 소스.

**Group ⑥ IMMS Identity (12, 2026-05-28 신설):**
- `research-framework-infinity-sigma` — Infinity-Σ SVG 다이어그램. TG1~5 + CP1~4 노드 arc 배치 + Shared Platforms band. CSS `:has()` hover dim. **인터랙티브** (vanilla SVG hover).
- `tg-spotlight-carousel` — 5 TG Swiper carousel (fade + autoplay + fraction). 슬라이드 = 좌 hero + 우 leader/tagline/대표논문 3건/Explore CTA. **Swiper v12**.
- `cp-roadmap-timeline` — 4 CP horizontal connector line (modifier status: active/upcoming). **GSAP ScrollTrigger** connector draw + node stagger.
- `multiscale-zoom-pyramid` — Atom→System 5계층 stair (다크 navy) + sticky panel. **GSAP ScrollTrigger** pin + scrub (desktop only).
- `research-themes-mosaic-masonry` — 12 토픽 CSS columns masonry (1/2/3 col 반응형). hover overlay precision line slide. **Pure CSS**.
- `oa-sdl-showcase-live` — 좌 60% 미디어 + 우 40% 3-stat + features + LIVE pulse pill. **CSS keyframe pulse**.
- `equipment-carousel-coverflow` — 8 장비 Swiper coverflow + 중앙 강조. **Swiper EffectCoverflow + Mousewheel**.
- `lab-floor-plan-interactive` — SVG 평면도 + zone hover/click + 사이드 상세 패널. **Vanilla DOM**.
- `faculty-spotlight-feature` — Broadie 톤. 좌 portrait (3:4) + 우 eyebrow/name/pull-quote/papers + prev/next nav. **GSAP fade + vanilla rotate**.
- `alumni-pathways-grid` — IMMS Fellows pathways 3-up grid (사진 + name + IMMS role + NOW affiliation + arrow). hover lift + arrow translate.
- `timeline-milestones-horizontal` — IMMS milestone Swiper free-mode horizontal scroll + dashed rail. **Swiper FreeMode + Mousewheel**.
- `awards-recognition-marquee-wall` — 3-stat count-up + 2-row infinite marquee (좌/우 다른 속도). **GSAP count-up + 기존 `marquee-whisper` 패턴 재사용**.

신규 BEM 블록 (섹션 레이아웃 한정): `sec-hero-mesh`, `sec-hero-video-split`, `sec-page-header-left`, `sec-section-intro-band`, `sec-disease-areas-grid`, `sec-feature-2-bigimg`, `sec-centers-leadership-3col`, `sec-buildings-grid-4col`, `sec-stats-band`, `sec-tab-switch-content`, `sec-news-latest-4up`, `sec-news-featured-carousel`, `sec-news-list-paged`, `sec-events-board`, `event-card-wide`, `sec-events-grid`, `sec-events-calendar`, `sec-publications-list`, `sec-people-grid`, `sec-directory-search`, `sec-contact-form-band`, `sec-locations-map`, `sec-newsletter-band`, `sec-cta-callout-navy`, `sec-article-body` + `sec-article-aside`, `sec-article-header-meta`, `sec-video-embed`, `sec-related-prev-next`, `sec-sticky-sidenav-layout`, `sec-long-text-2col`, `sec-platforms-list`, `sec-platform-detail`, `sec-research-intro`.

**헬퍼:** [framework/Helpers/view.php](../../framework/Helpers/view.php) — `render_component($key, $props)` = `include_view('components.'.$key, $props)`, `render_section($key, $props)` = `include_view('sections.'.$key, $props)`.

## 4. Work 상세 섹션 템플릿 (뷰: `resources/views/sections/*.php`)

관리자에서 조립/순서변경하는 Work 상세 섹션 블록들. 새 섹션 타입 추가 = 여기 파일 추가 + DB 등록 + `work-sections` 동기화(`docs/work-section-sync.md`). 공용 슬롯: `_cms-image-slot.php` / `_cms-video-slot.php` / `_media-cover-from-url.php`.
현재: `identity-text-intro`, `full-bleed-media`, `fullbleed-wide-sequence`, `media-dual-row`, `media-two-up-wide`, `split-brand-media-text` / `split-brand-text-media`, `impact-head-duo-media`, `impact-media-grid-text`, `impact-text-media-grid`, `text-cards-quad`, `triple-column-story`, `carousel-centered`, `wide-caption-slide`, `staggered-gallery`, `pinned-wide-gallery`, `parallax-stack-wide`, `horizontal-scroll-media`, `bands-three-stripes`, `brand-callout-navy`.

## 4.1 관리자 섹션 (뷰: `resources/views/sections/admin/*.php` · `render_section('admin.<key>', [...])`)

페이지 한 덩어리(캘린더·복합 위젯 등)를 페이지 인라인으로 두지 않고 sections로 추출. 페이지는 데이터만 준비해 호출.

| key | 파일 | 용도 / props |
|---|---|---|
| `admin.slots-calendar` (2026-05-31 신설) | `sections/admin/slots-calendar.php` | **시설·예약 슬롯 월별 캘린더** (장비 선택 form + 월 네비 + 7열 그리드 + 상태 칩). props: `equipment`(선택된 장비\|null), `allEquipment[]`(옵션), `year`,`month`,`monthLabel`, `cellsByDay`(`[day=>[['id','label','status','time_start','time_end'], ...]]`), `prevUrl`,`nextUrl`. equipment 가 null 이면 안내 empty-state. `slots/index` 에서 사용. |

## 5. 레이아웃 (뷰: `resources/views/layouts/*.php`, `resources/views/admin/layouts/*.php`)

| layout | 용도 |
|---|---|
| `layouts.main` | 공개 사이트 기본 레이아웃 (헤더/푸터/SEO·JSON-LD `seo()` 헬퍼) |
| `layouts.admin` | 관리자 기본 레이아웃 (사이드바·헤더·플래시). 페이지에서 `extend('layouts.admin')` + `section('content')`. `section('admin_style')` / `section('admin_script')` 로 페이지별 CSS/JS 주입 |
| `layouts.admin-auth` | 관리자 로그인/회원가입 |
| `layouts.admin_embed` | iframe 임베드용 관리자 레이아웃 (Work 캔버스 프리뷰 등) |
| `layouts.error` | 에러 페이지 |

## 6. 스타일 토큰·구조 (하드코딩 금지)

- 관리자 토큰: `resources/scss/admin/abstract/_variables.scss` — `--admin-{space*, fs-*, text*, grey-*, border*, primary, radius*, duration, ease, elevation*, ...}`. `$admin-breakpoint`(992px).
- 공개 토큰: `resources/scss/home/abstract/{_colors,_typography,...}.scss` (브랜드 토큰 정본). 요약 = `docs/02-variables/brand-guide.md`.
- 간격/타이포는 가능하면 **유틸 클래스**(`no-mg--top`, `no-section` 등 SCSS 반복문 생성)로. 컴포넌트 CSS에 `font-size` 직접 지정·인라인 스타일·`!important` 금지(`publisher.md` 참조).
- 새 컴포넌트 SCSS: `components/_<name>.scss` 만들고 `components/index.scss`에 `@forward`. 페이지 전용은 `pages/_<page>.scss` + `pages/index.scss`. 빌드: `npm run build:admin-css`(관리자) / `npm run build:css`(공개) / `npm run build`(JS).

---

## 7. 다크 테마

`[data-theme="dark"]`를 `<html>`에 적용하면 전환. `localStorage('admin-theme')` = `system | light | dark`.

- FOUC 방지: `layouts/main.php` `<head>` 인라인 스크립트 (JS 로드 전 즉시 적용).
- 토큰: `resources/scss/admin/abstract/_variables.scss` `[data-theme="dark"]` 블록.
- 전환 UI: 헤더 `data-admin-theme-menu` 드롭다운 (`header.php`). TS: `ThemeToggle.ts` → `initAdminThemeToggle()`.

## 변경 이력 (이 카탈로그 자체)

- 2026-09-16 — 헤더 하위 NAV active는 primary 글자·surface-section 배경·700 굵기로 표시하며 hover/focus에도 유지.

- 2026-09-16 — header/drawer 상위 메뉴는 첫 공개 하위 URL로 이동. /about·/business는 첫 하위 경로로 302 이동. PartnerIT CTA·서비스 링크·리뷰/인증서 버튼 hover 이동 제거, primary-hover·surface-section 토큰으로 배경 전환. 스크롤 등장·TOP 표시 모션 유지.

- 2026-05-13 — 최초 작성. `admin.components.nav-tabs`(site-meta 탭을 일반화) 신규 등록.
- 2026-05-13 — `admin.components.toggle-switch` 신규 등록 (기능 토글 페이지에서 사용. POST 폼 기반 iOS 스타일 스위치).
- 2026-05-18 — `admin.components.page-header` 신규 등록 (features·system/logs·project-docs·library 4개 페이지 헤더 통일. title+desc 세로 묶음 + 선택적 actions).
- 2026-05-18 — **강력 규칙 강화**: `/console/components` 확인 의무화. `admin-btn--xs` 사이즈 추가. 다크 테마 토큰 + `ThemeToggle.ts` 추가. `SelectSearch`/`MultiSelectSearch` 쇼케이스 추가 (기존 `SelectWrapper` + `Select` + `MultipleSearchSelect` 래퍼 — 별도 파일 없음, `initAdminSelects()` 공통 초기화).
- 2026-05-18 — `admin.components.segmented-control` 신규 등록 (기능 토글 enum 용. POST/PATCH 폼 기반 iOS 스타일 세그먼트, `SegmentedControlHandler` 비동기).
- 2026-05-19 — **`admin-btn--outline` 변형 추가** (`_ui.scss`). 중립 회색 테두리, 배경 없음. 용도: 툴바 유틸 버튼(전체 선택·페이지 이동 등). `--secondary`는 primary 아웃라인으로 재정의(배경 채움→테두리만). 기능별 버튼 색 계층: `--primary`(CTA, 채움) > `--secondary`(보조 링크, primary 테두리) > `--outline`(유틸, 중립 테두리) > `--ghost`(최소 강조) > 기본 white(편집 등 행 액션).
- 2026-05-27 — `components.search-overlay` + `components.search-result-item` 신규 등록 (사이트 통합 검색). 라우트 `search.index` / `search.suggest`, 페이지 `pages/search/index.php`, 서비스 `App\Support\Search\SearchService` (Post DB LIKE + SearchCatalog in-memory). 헤더 돋보기 버튼이 트리거.
- 2026-05-22 — `components.board-calendar` 신규 등록. 월별 캘린더 그리드(공개·관리자 공용). `layout=calendar` 게시판에서 공개 화면 + 관리자 CRUD 친화 캘린더 양쪽에 사용. 라이브러리 미도입(순수 PHP+CSS). 관리자 모드는 빈 셀 + 버튼으로 그 날짜 prefill 된 create 폼으로 이동, 이벤트 칩 클릭은 edit 폼으로 이동.
- 2026-06-04 — `imms-tabs` **swiper rail** 도입. default 탭에 `data-tabs-swiper` 를 붙이면 `ImmsTabsSwiper` 가 런타임에 `.imms-tabs-rail`(바깥 셸)로 감싸 넘치는 탭을 **컨테이너 바깥 별도 칸의 좌우 화살표**로 스크롤(항목과 겹치지 않음). 기존 내부 absolute 화살표(`imms-tabs__arrow`) 제거 → `imms-tabs-rail__arrow`. SCSS/TS 단일 소스(`_imms-tabs.scss`+`ImmsTabsSwiper.ts`) — 적용처는 속성만 추가(검색·about.people·facilities·guide). `<li>` 래퍼/평면 `<a>` 구조 모두 지원. 쇼케이스: `/guide/components` Tabs 섹션 "Swiper rail".
- 2026-06-04 — `components.empty-state` 아이콘에 `search`·`help` 추가(통합검색 빈 상태/플레이스홀더에서 사용). 검색 페이지의 인라인 `__placeholder`/`__empty` 블록을 `render_component('empty-state', …)` 호출로 위임(Rule #19) — `_search.scss` 의 대응 SCSS 제거.
- 2026-09-16 — AI report context/capability: Figma 350:14496·350:14581 PNG 3x 추출본 사용. Context는 종합진단+역량분석 세로 연결, 제목 f-heading-announcement-section. 두 목업은 --report-mockup-shadow 공용 약한 그림자 사용.
- 2026-09-16 — Company vision: CSS gradient background + four decorative photo cards. CompanyVisionController staggers cards upward during desktop pin, then reveals grouped text. Mobile uses static two-column gallery; reduced-motion keeps content visible.
- 2026-09-16 — AI report motion: ReportFlowController owns grouped copy/visual reveals. No per-item AOS. Copy starts earlier with short fade; supporting visuals use restrained desktop rise. Mobile fades only; reduced-motion remains static. Shared footer/reviews and other routes unchanged.
- 2026-09-16 — Supersedes report-only scope: ContentFlowController now provides explicit copy/visual groups across home, company, announcement, AI report, consortium and pricing. Interactive Swiper/Marquee/SlotCounter and CompanyVisionController remain independent. Removed four legacy per-card AOS declarations and unused consortium AOS overrides.
- 2026-09-16 — `components.partnerit-legal-document`: kind privacy/terms, JSON 원문 기반 공통 법률 문서 UI. 정책 탭·접이식 목차·조항·반응형 표. 실제 공개 예시 /legal/privacy 및 /legal/cookies. SCSS partnerit-legal-document 등록. 원문 확인사항은 legal-documents.md.
