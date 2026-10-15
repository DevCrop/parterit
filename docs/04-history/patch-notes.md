# Partnerit 작업 이력

## 2026-09-30 — 나머지 히어로 배지 대비 통일

- 날짜: 2026-09-30
- 작업명: 공고 매칭 및 subhero 배지 네이비/화이트 적용
- 작업 목적: 밝은 히어로 배경에서 카테고리 라벨이 묻히는 문제 개선
- 변경 파일: `resource/scss/components/_partnerit-eyebrow-badge.scss`, `resource/dist/style.css`, `resource/dist/style.css.map`, 컴포넌트 카탈로그 및 이력
- 연관 파일: `partnerit-announcement-hero.php`, `partnerit-ai-report-hero.php`, `partnerit-subhero.php`, `_colors.scss`
- 주요 변경 내용: AI 보고서에 적용했던 네이비/화이트 규칙을 공고 매칭·요금정책·고객사례 hero에도 적용. 기존 배지 크기·타이포·정렬·모션과 본문 배지 유지.
- 영향 범위: Frontend CSS만. Backend·Data·i18n 변경 없음. Test는 아래 12개 조합, Docs는 카탈로그/이력 갱신.
- 리스크: 운영 서버 업로드 및 실제 Safari/Android 검수는 별도. CSS 캐시 갱신 필요.
- 테스트 결과: npm run build 0 errors/0 warnings, git diff --check 정상. Playwright Chromium에서 matching/ai-report/pricing/partners 각 320/390/1440px HTTP200, 네이비 배경/흰 글씨, 페이지와 배지 가로 넘침 없음, pageerror 없음 확인.
- 후속 작업: 운영 서버의 `resource/dist/style.css` 교체. map은 선택. HTML/PHP/JS 추가 업로드 불필요.

## 2026-09-30 — AI 보고서 조건 비교 플랫폼 목업 고도화

- 날짜: 2026-09-30
- 작업명: 조건/기업정보 통합 분석패널 및 hero배지 대비
- 작업 목적: 두 장의 큰 카드보다 분석흐름이 선명한 플랫폼 표현, pale배경에 묻히던 AIhero 배지 구분
- 변경 파일: `views/components/partnerit-ai-report-context.php`, `_partnerit-ai-report-context.scss`, `_partnerit-eyebrow-badge.scss`, `resource/js/animation/NarrativeAos.js`, dist app/style 및 maps
- 연관 파일: `partnerit-ai-report-hero.php`, `_partnerit-ai-report.scss`, 실제hero보고서PNG, `HeroMotionController.js`
- 주요 변경: 단일 UI패널·입력3/4항목·합류선/AI분석·네이비 결과안내. 기존 필드와 지원판단문구 유지. raster 생성 대신 HTML/CSS목업으로 선명도·접근성·모바일 확보. AIhero배지 네이비/화이트, 본문배지 기존SoftBlue 유지. 개별card AOS 제거하고 전체panel visual로 통일.
- 영향 범위: `/ai-report` context와hero배지, 기존라우트/DB/가격/실제분석기능 변경없음.
- 리스크: 실서버배포 및 실제Safari/Android 기기검수 별도. 목업은 분석흐름 설명이지 실시간 결과화면이 아님.
- 테스트 결과: BUILD0errors/0warnings, PHP lint정상, diffcheck오류0. 320/390/430/768/1440에서 페이지/패널내부넘침0·JS오류0·HTTP200·hero배지네이비/화이트 assert. 390/1440 일반모션 hero배지opacity1, panelopacity1·개별card AOS없음 확인. 데스크톱/모바일intro·hero캡처 직접확인.
- 후속 작업: `views/components/partnerit-ai-report-context.php`, `resource/dist/style.css`, `resource/dist/app.js` 운영업로드. maps선택. 디자인워크스페이스 `final-review/20260930-ai-context/`에 증거보관.

## 2026-09-30 — 공개 페이지 상단 라벨 배지 통일

- 날짜: 2026-09-30
- 작업명: 텍스트 eyebrow의 공통 Soft Blue 배지 적용
- 작업 목적: 제목 위 라벨이 단순히 파란 글씨로 나열되던 인상을 개선하고 역할별 표현 일관성 확보
- 변경 파일: eyebrow를 가진 PHP8개(`partnerit-announcement-hero/profile`, `partnerit-ai-report-hero/context`, `partnerit-subhero`, `partnerit-company-vision`, `partnerit-legal-document`, `partnerit-services`), 해당 SCSS7개의 중복 라벨 표현 제거, `_partnerit-eyebrow-badge.scss` 신규 및 index forward, `resource/dist/style.css/map`
- 연관 파일: `HeroMotionController.js`, `CompanyVisionController.js`, `_colors.scss`, `_init.scss`, 기존 서비스/고객사례 배지
- 주요 변경: 기존 메인서비스 Soft Blue 배지와 고객사례 둥근사각 라벨을 레퍼런스로 사용. 현재 컬러 토큰의 연블루 배경·짙은 블루 글씨, radius8px, padding6/12px, f-body-3/600 위계로 통일. 그림자·아이콘·점·그라디언트 없음. selector 유지로 GSAP 연계 보존.
- 영향 범위: 메인·회사소개·공고매칭·AI보고서·요금·고객사례·legal의 기존 라벨만. 컨소시엄/회사 히어로에 새 라벨을 추가하지 않고 가격·후기 배지를 보존. DB·라우트·API·실제 서비스 기능 변경 없음.
- 리스크: 배지 높이가 기존 텍스트보다 늘지만 타이틀 여백 및 모바일 넘침을 실측 확인. 실제 Safari/Android는 별도 검수.
- 테스트 결과: BUILD0errors/0warnings, PHP8개 lint 정상, git diff --check 오류0. 9페이지×320/390/1440=27조합에서 HTTP200·JS오류0·가로넘침0, badge색상/600굵기/8px radius/그림자없음/내부넘침없음 assert 통과. 390px 일반 모션에서 공고 배지 opacity1/transform none 확인. matching·AI·pricing 데스크톱/모바일 캡처 시각 확인.
- 후속 작업: 운영 반영시 PHP8개와 `resource/dist/style.css` 업로드 필요. `.map`은 선택. 증거는 디자인 워크스페이스 `final-review/20260930-badges/`에 보관.

## 2026-09-30 — 최종 QA 잔여 3건 수정

- 날짜: 2026-09-30
- 작업명: 지표 표현·하위 NAV active·모바일 드로어 포커스 보정
- 작업 목적: 최종 검증에서 발견한 콘텐츠 정확성·활성 표시·키보드 접근성 결함 해결
- 변경 파일: `views/components/partnerit-metrics.php`, `views/components/header.php`, `resource/js/ui/header/HeaderManager.js`, `resource/dist/app.js`, `resource/dist/app.js.map`
- 연관 파일: `bootstrap/menu.php`, `src/Menu/Menu.php`, `views/components/drawer.php`, `resource/scss/components/_modal.scss`
- 주요 변경: 92.4%는 실제 이용 현황이 아닌 지속 이용 의향으로 정정. 공개 헤더 하위 메뉴에 내부 경로 일치 판정을 추가. 드로어는 GSAP 열림 완료 후 포커스를 옮기고 숨김·disabled·inert·음수 tabindex 요소를 제외한 실제 포커스 대상만 순환.
- 영향 범위: 공개 헤더·모바일 메뉴·메인 지표. 공통 메뉴 엔진·라우트·DB·CMS·가격·SCSS는 변경 없음.
- 리스크: 실서버 캐시·실제 Safari 기기 반영은 별도 검수 필요. 첫 프레임 포커스 이동은 메뉴 visibility 전환과 충돌하여 열림 완료 시점으로 보완.
- 테스트 결과: BUILD 오류0·경고0, 변경 PHP2개 lint 정상, `git diff --check` 오류0. Chromium 7페이지×320/390/430/768/1440=35조합에서 HTTP200·JS예외0·가로넘침0·현재 메뉴 단일 active·지표 문구 정합 확인. 320/390/768에서 각2회 드로어 열기→최초 포커스→Shift+Tab 마지막→Tab 처음→Escape 닫기 및 토글 포커스 복귀 통과. `/matching?qa=1` active 유지 확인.
- 후속 작업: 서버에는 `views/components/header.php`, `views/components/partnerit-metrics.php`, `resource/dist/app.js` 업로드. `app.js.map`은 디버깅용 선택 사항. 로컬 증거는 디자인 워크스페이스 `final-review/20260930-fixed/`에 보관.

## 2026-09-30 — AI 보고서 capability 패널 대비 보정

- 날짜: 2026-09-30
- 작업명: 역량분석 목업 외부 패널 색상 분리
- 작업 목적: 섹션 배경과 유사했던 목업 외부 배경의 시각적 경계 확보
- 변경 파일: `resource/scss/components/_partnerit-ai-report-capability.scss`, `resource/dist/style.css`, `resource/dist/style.css.map`
- 연관 파일: `views/components/partnerit-ai-report-capability.php`, `resource/scss/components/_partnerit-ai-report.scss`
- 주요 변경: Primary Blue 28%·16% radial layer와 18%→6%→14% linear layer 조합으로 Soft Blue 패널 강화
- 영향 범위: `/ai-report` capability visual 배경
- 리스크: 저채도 디스플레이에서는 색상 차이가 다소 약하게 보일 수 있음
- 테스트 결과: `npm run build` 성공(0 errors, 0 warnings), `/ai-report` HTTP 200, watch 프로세스 유지, headless 데스크톱 캡처로 대비 확인
- 후속 작업: 운영 반영 후 실제 디스플레이에서 블루 농도 최종 확인

## 2026-09-30 — 공고 매칭 프로필 콘텐츠 위계 정리

- 날짜: 2026-09-30
- 작업명: 기업정보 활용 섹션 타이포·정보 구조 고도화
- 작업 목적: 단순 나열로 보이던 설명을 서비스 흐름과 데이터 관리 안내가 구분되는 구조로 개선
- 변경 파일: `views/components/partnerit-announcement-collection.php`, `views/components/partnerit-announcement-profile.php`, `resource/scss/components/_partnerit-announcement-profile.scss`, `resource/dist/style.css`, `resource/dist/style.css.map`
- 연관 파일: `views/pages/business/index.php`, `resource/scss/components/_partnerit-announcement.scss`, `resource/scss/abstract/_headings.scss`
- 주요 변경: 소개서 기준 주석 제거, eyebrow·강조 제목·설명·보안 안내 카드 적용, 기존 오른쪽 이용 단계 유지
- 영향 범위: 공개 `/matching` collection/profile 섹션의 콘텐츠와 스타일
- 리스크: 실제 모바일 브라우저별 줄바꿈은 운영 기기에서 최종 확인 필요
- 테스트 결과: Docker 4개 서비스 기동, `/matching` GET 200, Sass build 0 errors/0 warnings, PHP lint 통과, 데스크톱·모바일 폭 headless 캡처 확인
- 후속 작업: 운영 반영 후 실제 iOS Safari/Android Chrome에서 제목·보안 안내 줄바꿈 최종 확인

## 2026-09-30 — 고객자료 기준 콘텐츠·전환 경로 정리

- 날짜: 2026-09-30
- 작업명: 고객자료 정합성 보정 및 컨택 페이지 비활성화
- 작업 목적: 고객 서비스 소개서·법적 문서·외부 무료체험 흐름과 공개 사이트 콘텐츠를 일치시킴
- 변경 파일: `config/project_profile.php`, `routes/web.php`, `views/components/partnerit-metrics.php`, `views/components/hero.php`, `views/components/partnerit-cta-link.php`, `views/components/partnerit-final-cta.php`, `resource/data/legal-documents.json`
- 연관 파일: `views/pages/contact/index.php`, `resource/files/partnerit-service-brief.pdf`
- 주요 변경: 기관 수치 400개 통일, 실제 소개서 다운로드 연결, 법적 연락처·조 번호 정리, `/contact` 외부 로그인 리다이렉트
- 영향 범위: 공개 메인 콘텐츠, 공통 CTA, 법적 문서, `/contact` 접근 동작
- 리스크: 운영 연락처 변경은 고객 제공 법적 PDF 원문과 달라지므로 향후 법무 확정본 수령 시 재동기화 필요
- 테스트 결과: `npm run build` 성공(0 errors, 0 warnings), 변경 PHP 6개 lint 통과, JSON 파싱 및 개인정보 조 번호 1~12 연속 확인, 소개서 PDF 20쪽·원본 SHA-256 일치, 링크/구문/삭제 정책 검사 통과. 로컬 HTTP 검증은 현재 PHP 런타임의 PDO MySQL 드라이버 부재로 부팅 전 단계에서 제한됨.
- 후속 작업: 고객 승인 법적 문서가 갱신되면 `legal-documents.json`과 원본 PDF를 함께 교체

## 2026-09-29 — 홈 서비스 비주얼 블루 그라디언트 복원

- 날짜: 2026-09-29
- 작업명: AI 분석 리포트·컨소시엄 매칭 배경 이미지 노출 복원
- 작업 목적: 회색으로 보이던 서비스 목업 뒤 배경을 기존 파트너잇의 블루 계열 분위기로 복원
- 변경 파일: `resource/scss/components/_partnerit-services.scss`, `resource/dist/style.css`, `resource/dist/style.css.map`
- 연관 파일: `views/components/partnerit-services.php`, `resource/images/main/services/services-report-bg.png`, `resource/images/main/services/services-consortium-bg.png`
- 주요 변경: 배경 PNG 자체의 블루 그라디언트를 희석하던 `opacity: 0.22`를 제거해 원본 이미지를 정상 노출
- 영향 범위: 메인 `Our Services`의 AI 분석 리포트·컨소시엄 매칭 이미지 영역만 변경
- 리스크: 원본 PNG 색상이 매우 저채도라 디스플레이 환경에 따라 블루 농도가 은은하게 보일 수 있음
- 테스트 결과: `npm run build` 성공(0 errors, 0 warnings), PHP lint 통과, CSS selector 생성 및 diff 정책 검사 통과
- 후속 작업: 로컬 또는 배포 서버 기동 후 실제 화면에서 블루 농도와 목업 대비 최종 육안 검수

## 2026-09-21 — AI 보고서 shadow·CTA 위계 정리

- 변경 파일: `resource/scss/components/_partnerit-ai-report.scss`, `_partnerit-ai-report-hero.scss`, `_partnerit-ai-report-diagnosis.scss`.
- 주요 변경 내용: 히어로의 고불투명도 다중 shadow를 제거하고 보고서 목업 공통 shadow 토큰 적용. 무료 체험 버튼을 52px 높이로 축소하고 900px 이하에서 full width 적용.
- 검증: `npm run build` 0 errors/0 warnings. 로컬 `/ai-report`에서 hero shadow, 1px border, CTA 52px 렌더링 확인.

## 2026-09-21 — 컨소시엄 협업 카드 이미지 업스케일

- 변경 파일: `views/components/partnerit-consortium-collaboration.php`, `resource/images/business/consortium/collaboration/*-hd.png`.
- 주요 변경 내용: 메신저·AI 회의록·문서관리·프로젝트 추적 목업을 1796×1012 HD 자산으로 교체하고 카드 비율과 크롭을 통일.
- 검증: 로컬 `/consortium`에서 네 이미지가 모두 1796×1012로 로드되고 카드 2×2 렌더링이 유지되는 것을 확인.

## 2026-09-21 — 서비스 서브페이지 히어로 공통 인터랙션

- 변경 파일: `resource/js/app.js`, `resource/js/animation/HeroMotionController.js`, `resource/js/animation/NarrativeAos.js`, `resource/scss/components/_partnerit-announcement-hero.scss`, `resource/scss/components/_partnerit-consortium-hero.scss`.
- 주요 변경 내용: 회사소개 전용 모션을 공개 서브페이지 공통 히어로 컨트롤러로 확장하고, copy와 visual의 단계별 GSAP 진입을 적용. 기존 공고·컨소시엄 CSS keyframes와 히어로 AOS 등록은 제거해 중복 transform을 방지.
- 검증: `npm run build` 0 errors/0 warnings. `/matching`, `/ai-report`, `/consortium`, `/pricing`, `/partners`, `/about/company`에서 진행 중 opacity/transform과 완료 후 원상 복구를 확인하고 히어로 내부 `data-aos` 중복이 없음을 확인.

## 2026-09-21 — 전 페이지 AOS·회사소개 히어로 stagger

- 변경 파일: `resource/js/app.js`, `resource/js/animation/NarrativeAos.js`, `resource/js/animation/CompanyHeroController.js`.
- 주요 변경 내용: 공개 페이지의 누락된 제목·목업·카드 그룹에 AOS를 확장하고, 회사소개 히어로를 핵심 제목과 설명 행 단위 GSAP stagger로 구성.
- 영향 범위: 공개 페이지 등장 모션만. Swiper와 기존 개별 모션 로직은 유지.
- 검증: `npm run build` 0 errors/0 warnings. 홈·공고·AI 보고서·컨소시엄·요금·고객사례·개인정보 페이지에서 AOS 등록 확인(각 47/34/24/28/16/13/13개), 회사소개 히어로의 kicker → headline → 설명 행 진행 중 프레임 확인.

## 2026-09-21 — 페이지 전환 제거·AOS 단일화

- 변경 파일: `resource/js/app.js`, `resource/js/animation/NarrativeAos.js`, `resource/scss/base/index.scss`, `resource/scss/components/_partnerit-aos.scss`.
- 주요 변경 내용: cross-document View Transition 비활성화, ContentFlow GSAP 중복 제거, 공통 제목·비주얼·카드 그룹을 AOS로 통합. 적용되지 않던 비정규 duration·delay를 AOS 지원 50ms 단위로 바로잡고 스크롤 진입 강도와 stagger 보정.
- 영향 범위: 공개 페이지의 일반 콘텐츠 등장 모션. 특수 목적 GSAP·Swiper 모션은 유지.
- 검증: `npm run build` 0 errors/0 warnings. 로컬 공고 매칭에서 전환명 `none`, 제목 `0.8s`, 카드 `0.7s` 및 `0/0.1/0.2s` stagger가 스크롤 중간 프레임에서 순차 적용되는 것 확인.

## 2026-09-21 — 페이지 전환·스크롤 모션 동기화

- 변경 파일: `resource/js/app.js`, `resource/js/animation/ContentFlowController.js`.
- 주요 변경 내용: cross-document view transition 종료 후 AOS/ScrollTrigger refresh, AOS·GSAP 중복 적용 방지, 공통 copy/visual reveal의 이동 거리·투명도·진입 시점 강화.
- 영향 범위: 공개 페이지 스크롤 등장 모션. 별도 인터랙션을 가진 히어로·회사 비전·차트·Swiper는 변경하지 않음.
- 검증: `npm run build` 0 errors/0 warnings, 로컬 회사소개에서 진입 전 `aos-init` 유지 및 스크롤 진입 후 `aos-animate` 전환 확인.

## 2026-09-15 — 요금제·부가 서비스 Figma 그리드

- 날짜: 2026-09-15
- 작업명: Partnerit pricing — Figma grid
- 작업 목적: 성장 단계별 구독 플랜과 부가 서비스를 비교 가능한 공개 홈 grid로 제공.
- 변경 파일: `views/pages/index.php`, `views/components/partnerit-pricing.php`, `resource/scss/components/_partnerit-pricing.scss`, `resource/scss/components/index.scss`, `resource/images/main/pricing/*`, `docs/03-constants/components.md`.
- 연관 파일/호출 흐름: `views/pages/index.php` → `components.partnerit-pricing` → 플랜/부가서비스 배열 → CSS grid.
- 주요 변경 내용: Figma node `225:1563`의 3개 플랜·4개 서비스 카피, 카드 우선순위 border, 연한 배경, 원본 체크/서비스 아이콘, 3→2→1열 반응형을 구현.
- 영향 범위: 공개 홈 표시 UI 및 정적 이미지 자산만. Backend/Data/i18n/권한/의존성 변경 없음.
- 리스크: 가격·포함 기능은 정적 시안 값이다. 실제 판매 정책 확정 시 서버 데이터나 CMS 연결이 필요.
- 테스트 결과: `node build.js` 0 errors/0 warnings, `php -l` 통과, 로컬 HTTP 200과 플랜/부가서비스 렌더 확인.
- 후속 작업: 결제/문의 흐름이 확정되면 각 플랜의 CTA와 정책 페이지 연결.

## 2026-09-15 — 파트너 마퀴 카드 shadow 보정

- 날짜: 2026-09-15
- 작업명: Partnerit partners — card elevation correction
- 변경 파일: `resource/scss/components/_partnerit-partners.scss`
- 주요 변경 내용: 마퀴 clip 컨테이너의 상하 shadow 여유를 확보하고 카드 border·elevation을 강화.
- 영향 범위: 공개 홈 파트너 로고 카드의 시각 표현만 변경. PHP, 로고 자산, 애니메이션 로직, DB/API/i18n 변경 없음.
- 테스트 결과: `node build.js`, 로컬 HTTP 응답 확인.

## 2026-09-15 — 파트너 로고 무한 마퀴

- 날짜: 2026-09-15
- 작업명: Partnerit partners — Figma marquee
- 작업 목적: 고객 후기 다음에 Figma 원본 기관·기업 로고 두 행을 무한 마퀴로 표시.
- 변경 파일: `views/pages/index.php`, `views/components/partnerit-partners.php`, `resource/scss/components/_partnerit-partners.scss`, `resource/scss/components/index.scss`, `resource/images/main/partners/*`, `docs/03-constants/components.md`.
- 연관 파일/호출 흐름: `views/pages/index.php` → `components.partnerit-partners` → 두 개의 반복 로고 목록 → CSS keyframes.
- 주요 변경 내용: Figma PNG 14개를 local asset으로 사용하고, 동일 카드 비율의 두 트랙을 반대 방향으로 무한 반복. motion preference가 reduce면 이동 정지.
- 영향 범위: 공개 홈 UI·정적 이미지 자산만. Backend/Data/i18n/권한/의존성 변경 없음.
- 리스크: 현재 로고 목록은 Figma 정적 원본이다. 관리자 연동이 필요해지면 배열을 관리 데이터로 치환해야 함.
- 테스트 결과: PHP lint, `node build.js`(0 errors/0 warnings), 로컬 HTTP 200 및 렌더된 섹션/자산 경로 확인.
- 후속 작업: 로고 노출 순서·추가/삭제를 관리해야 할 때 데이터 소스를 별도 설계.

## 2026-09-15 — 고객 후기 카드 화살표 hover 노출

- 날짜: 2026-09-15
- 작업명: Partnerit reviews — arrow visibility state
- 변경 파일: `resource/scss/components/_partnerit-reviews.scss`
- 주요 변경 내용: active 카드의 배지/본문 노출은 유지하고, 우측 상단 화살표는 active 카드 hover·키보드 focus 시에만 노출되도록 상태를 분리.
- 영향 범위: 후기 카드의 시각 상태만 변경. PHP, Swiper JS, 데이터/관리자 연동, DB/API/i18n 변경 없음.

## 2026-09-15 — 고객 후기 Swiper Figma 원본 교정

- 날짜: 2026-09-15
- 작업명: Partnerit reviews — Figma source correction
- 작업 목적: 임의 생성했던 후기 샘플을 폐기하고 Figma 원본 node `225:1461`의 실제 자산·카피·레이아웃으로 교체.
- 변경 파일: `views/components/partnerit-reviews.php`, `resource/scss/components/_partnerit-reviews.scss`, `resource/js/ui/SwiperController.js`, `resource/images/main/reviews/*`, `docs/03-constants/components.md`, `docs/04-history/requirement-changes.md`, `design-qa.md`.
- 연관 파일/호출 흐름: `views/pages/index.php` → `components.partnerit-reviews` → 기존 `SwiperController` → Figma PNG/SVG 로컬 자산.
- 주요 변경 내용: 실제 이미지 3장과 SVG 3개, 실제 한드허그 후기, 중앙/좌우 카드 규격, 배지·그라디언트·컨트롤을 원본 수치로 반영. 관리자 연결용 `$reviews` prop 구조는 유지.
- 영향 범위: 공개 홈 후기 UI와 후기 전용 Swiper 초기화만. Backend/Data/i18n/권한/의존성 변경 없음.
- 리스크: Figma 시안의 페이지네이션은 5칸이지만 고유 사진은 3장만 제공되어, 화면 밖 2개 초기 슬롯은 export preview 자산을 재사용. 관리자 연동 시 전달 건수와 이미지에 맞춰 자동 변경됨.
- 테스트 결과: `php -l`, `node --check`, `npm run build`, HTTP 응답 검사 통과. 1920px 렌더에서 중앙 872×519px, 좌우 646×400px 및 119px 오프셋, 5개 페이지네이션 확인; 390px 모바일 overflow 확인.
- 후속 작업: 관리자 후기 데이터가 준비되면 `$reviews`에 category/quote/client/role/image/image_alt/image_position을 전달.

## 2026-09-15 — 고객 후기 Swiper 섹션

- 날짜: 2026-09-15
- 작업명: Partnerit reviews carousel
- 작업 목적: 첨부 시안의 중앙 강조형 후기 슬라이더를 홈페이지에 추가하고, 추후 관리자 데이터 연결이 가능하도록 카드 콘텐츠를 데이터 기반으로 분리.
- 변경 파일: `views/pages/index.php`, `views/components/partnerit-reviews.php`, `resource/scss/components/index.scss`, `resource/scss/components/_partnerit-reviews.scss`, `resource/js/ui/SwiperController.js`, `resource/images/main/reviews/review-sample-01.jpg` ~ `review-sample-05.jpg`, `docs/03-constants/components.md`, `docs/04-history/requirement-changes.md`, `design-qa.md`.
- 연관 파일/호출 흐름: `views/pages/index.php` → `partnerit-reviews` 컴포넌트 → `[data-partnerit-reviews-swiper]` → 기존 `SwiperController`; CSS는 공개 컴포넌트 bundle `@forward` 연결.
- 주요 변경: 시안에 맞춘 5장 중앙 정렬/옆 카드 미리보기, 카테고리·샘플 배지, 페이지네이션, 이전/다음 버튼, 키보드 제어, 모바일 단일 중심 카드. 후기 사진 5장은 각각 1672×941 JPEG 품질 92로 저장(총 915,485 bytes); 관리자 데이터 전달 시 샘플 배지 자동 비표시.
- 영향 범위: 공개 홈 UI, Swiper 초기화 및 샘플 이미지뿐. Backend/DB/API/권한/i18n 불변; 신규 라이브러리 없음. 실제 후기 정보가 아닌 샘플 콘텐츠임을 명확히 표기.
- 리스크: 가상 인물 사진과 문구는 관리자 연동 전 화면 구성용 샘플이며 실제 고객 후기·보증으로 사용하면 안 됨. 브라우저 시각 QA는 CUA 경로 오류로 미완료.
- 테스트 결과: PHP lint 2개 파일 통과, `node --check resource/js/ui/SwiperController.js` 통과, `npm run build` 0 errors/0 warnings/4 outputs. 홈/CSS/JS HTTP 200, 5개 슬라이드와 샘플 배지 HTML 응답 확인.
- 후속 작업: 관리자 후기 관리 데이터 공급 후 샘플 콘텐츠/자산 교체, 인앱 브라우저에서 데스크톱·모바일 비교 검수.

## 2026-09-15 — Partnerit impact 성과 섹션 추가

- 날짜: 2026-09-15
- 작업명: Partnerit impact
- 작업 목적: 첨부 디자인을 따라 주요 시간 절감 수치와 비교 차트를 메인 하단에 추가.
- 변경 파일: `views/pages/index.php`, `views/components/partnerit-impact.php`, `resource/scss/components/index.scss`, `resource/scss/components/_partnerit-impact.scss`, `docs/03-constants/components.md`, `docs/04-history/requirement-changes.md`, `design-qa.md`.
- 연관 파일/호출 흐름: `views/pages/index.php` → `partnerit-service-details` → 신규 `partnerit-impact`; 스타일은 공개 컴포넌트 bundle의 `@forward`로 연결.
- 주요 변경: 소개 문구, 98.7%/67.5%/83.3% 성과 카드, 대비 막대와 도넛 차트; 3열, 태블릿 2열, 모바일 1열.
- 영향 범위: 공개 홈 UI·SCSS·컴포넌트 카탈로그만; backend/DB/API/권한/i18n 불변.
- 리스크: 차트의 축소 표현이 실제 기간/수치 관계로 오해되지 않도록 visible labels와 accessible descriptions를 함께 제공. 현재 프로젝트에 `/console/components` 쇼케이스 뷰는 없음.
- 테스트 결과: `php -l views/components/partnerit-impact.php` 및 `php -l views/pages/index.php` 통과. `npm run build` 0 errors/0 warnings/4 outputs. 홈과 `resource/dist/style.css` GET 200, 응답 HTML에 impact 제목/83.3% 존재.
- 후속 작업: 로컬 브라우저 CUA 초기화가 OS 경로 오류로 실패해 데스크톱/모바일 화면 비교는 보류; `design-qa.md`에 blocked 기록.

## 2026-09-15 — Service Details 여백·배경 보정

- 요청: 카드의 공백을 줄이고 Figma처럼 요소 주변 및 배경을 은은하게 구성.
- 변경 파일: `resource/scss/components/_partnerit-service-details.scss`, `docs/04-history/requirement-changes.md`, `design-qa.md`.
- 변경 내용: 카드 최소 높이 54rem→45rem(540→450px), 헤더-카드 간격 상한 96px→64px. 섹션 바깥에는 낮은 농도의 블루/라벤더 radial 배경, 카드 하단에는 옅은 블루 틴트 적용.
- 영향: 공개 홈 Service Details SCSS만. Backend/DB/API/권한/i18n 불변.
- 검증: `npm run build` 0 errors/0 warnings, PHP lint 성공, 홈/CSS/이미지 HTTP 200. CUA 브라우저 캡처는 OS 경로 오류로 막힘.

## 2026-09-15 — Service Details 카드 레이아웃 정정

- 요청: 첨부 #2처럼 텍스트를 카드 상단에, 첨부 #1의 비주얼을 카드 하단에 배치하고 네 패널 크기를 맞춤.
- 변경 파일: `views/components/partnerit-service-details.php`, `resource/scss/components/_partnerit-service-details.scss`, `docs/03-constants/components.md`, `docs/04-history/requirement-changes.md`, `design-qa.md`.
- 레이아웃: 동일한 네 열/세로 분할 카드, 카드별 두 줄 제목과 세로 해시태그 목록, 하단 동일 비율(350:275) 목업 크롭. 중간/모바일에서는 2열/1열.
- 자산: `service-details-strip.png` 네 영역을 각각 같은 방식으로 표시.
- 영향: 공개 홈 Service Details UI만. Backend/DB/API/권한/i18n 불변.
- 검증: PHP lint와 최신 Sass bundle 빌드 확인. 브라우저 픽셀 검증은 CUA OS 경로 오류로 막힘.

## 2026-09-15 — Service Details 완성형 카드 적용

- 요청: 첫 번째 이미지처럼 비주얼 조각만 두지 말고, 두 번째 이미지의 완성형 섹션으로 구성하며 이미지 규격을 동일하게 맞춤.
- 변경 파일: `views/components/partnerit-service-details.php`, `resource/scss/components/_partnerit-service-details.scss`, `docs/03-constants/components.md`, `docs/04-history/requirement-changes.md`, `design-qa.md`.
- 레이아웃: 중앙 타이틀/소개 문구 아래 4개 동일 규격 카드. 이미지 뷰포트는 공통 312:259 비율, 데스크톱 4열·태블릿 2열·모바일 1열.
- 콘텐츠: 네 개 Figma PNG 목업, 한국어 제목 두 줄, 기능 태그 3개를 각각 HTML 구조로 표시.
- 영향: 공개 홈 Service Details 컴포넌트에 한정. Backend/DB/API/권한/i18n 불변.
- 검증: PHP 문법 및 Sass watch 빌드 확인 예정; 실제 픽셀 QA 상태는 `design-qa.md`에 기록.

## 2026-09-15 — Service Details 이미지 구성 정정

- 수정 사유: 기존 해석과 달리 제목 아래에는 카드 설명/태그가 아니라 첨부 두 번째 이미지의 비주얼 네 조각만 필요함.
- 변경 파일: `views/components/partnerit-service-details.php`, `resource/scss/components/_partnerit-service-details.scss`, `resource/images/main/service-details/service-details-strip.png`.
- 문서 갱신: `docs/04-history/requirement-changes.md`, `docs/03-constants/components.md`, `design-qa.md`.
- 레이아웃: 제목만 남기고 이미지 스트립을 4개 독립 crop 영역으로 표시. 넓은 화면 4열, 중간 폭 2열, 모바일 1열.
- 자산: 첨부 2번 1400×275 PNG 원본을 그대로 보존; 기존 개별 Figma 목업 파일은 페이지에서 더 이상 참조하지 않음.
- 영향: 홈의 Service Details 컴포넌트 및 스타일만. Backend/DB/API/권한/i18n 불변.
- 검증: PHP lint 성공, Sass watch 오류/경고 0건, 홈·CSS·스트립 PNG HTTP 200, 원본/복사본 SHA-256 동일 확인.
- 접근성: 각 crop 영역에 해당 목업을 설명하는 접근 가능한 이름 제공.
- 브라우저 확인: 인앱 브라우저 CUA 오류로 실제 페이지 시각 비교는 보류할 수 있음.
- 후속: 인앱 브라우저가 복구되면 데스크톱/모바일 캡처를 비교해 QA 상태 갱신.

## 2026-09-15 — 메인 Service Details 섹션

- 요청: 첨부한 4개 서비스 이미지의 정보 구조를 이미지 한 장이 아닌 반응형 HTML로 구현.
- 상위 디자인: Figma `Service Details` frame `225:1164` 및 사용자 제공 이미지.
- 변경 파일: `views/pages/index.php`, `views/components/partnerit-service-details.php`, `resource/scss/components/_partnerit-service-details.scss`, `resource/scss/components/index.scss`.
- 문서 파일: `docs/03-constants/components.md`, `design-qa.md`, `docs/04-history/patch-notes.md`.
- 자산: Figma child frames `225:1179`, `225:1224`, `225:1270`, `225:1306`에서 각각 3x PNG를 export해 `resource/images/main/service-details/`에 저장.
- 핵심 변경: 헤더/소개/제목/키워드 태그를 HTML 텍스트로 분리하고 제품 일러스트만 별도 이미지로 배치. 4열→태블릿 2열→모바일 1열 구성.
- 호출 흐름: `views/pages/index.php`에서 `partnerit-services` 다음 `partnerit-service-details` 호출.
- 영향 범위: 공개 홈 UI 및 전역 SCSS bundle에 한정. Backend·DB·API·권한·i18n 변경 없음.
- 검증: PHP lint 성공, Sass watch 0 errors/0 warnings, 홈 HTTP 200, PNG dimensions 936×759 또는 936×777 확인.
- 미완료/후속: 선택된 인앱 브라우저 자동화 연결이 OS 경로 오류로 실패해 실제 픽셀 시각 QA는 blocked로 남김(`design-qa.md`).

## 2026-09-15 — 메인 서비스 GRID 섹션

- Figma `Our Services` 섹션의 소개와 서비스 3종(공고 매칭, AI 분석 리포트, 컨소시엄 매칭)을 홈에 GRID로 구성하고 중앙 항목은 좌우 순서를 교차.
- Figma 비주얼 배경·화면 목업을 `resource/images/main/services/`에 PNG로 추가하고 그림자는 CSS에만 적용.
- 모바일은 세로 흐름으로 전환하고 CTA를 기존 `/business/announcement-matching`, `/business/ai-report`, `/business/consortium-matching` 경로에 연결.
- 독립 PHP/SCSS 컴포넌트, SCSS forward, 컴포넌트 카탈로그 갱신.
- 검증: PHP lint, watch 컴파일, 홈·대상 URL HTTP 상태, desktop/mobile DOM 크기·이미지 로드·가로 overflow·콘솔 확인. 외부 headless 캡처는 흰 화면으로 출력되어 시각 QA는 보류(`design-qa.md`).

## 2026-09-15 — 메인 솔루션 GRID 섹션

- Figma `260911`의 `Frame 17584` (`225:744`)를 문제 인식 섹션 뒤 홈에 연결.
- 타이틀과 4개 솔루션 카드를 반응형 GRID로 구성(데스크톱 4열, 태블릿 2열, 모바일 1열).
- Figma 이미지 영역 `225:749/755/760/765`를 각각 3x PNG(936×600px)로 export해 저장.
- 독립 PHP 컴포넌트, SCSS forward, 컴포넌트 카탈로그를 갱신.
- 검증: PHP lint, watch 결과, PNG 크기/로드, 1440/768/390 렌더 확인.

## 2026-09-15 — 메인 문제 인식 sticky 섹션

- Figma `260911`의 `03_Problem` 프레임을 홈 특허 섹션 다음에 연결.
- 왼쪽 메시지·설명을 sticky로 두고 오른쪽에 문제 카드 3개를 배치, 모바일에서는 일반 스크롤 흐름으로 변경.
- Figma 카드 아이콘 프레임 `225:727`, `225:733`, `225:739`를 PNG로 export해 `resource/images/main/problem/`에 저장.
- 독립 PHP 컴포넌트와 BEM SCSS를 추가하고 컴포넌트 카탈로그 및 SCSS forward 등록.
- 검증: PHP lint, 실행 중인 npm watch 산출, 로컬 HTTP·데스크톱/모바일 시각 확인.

## 2026-09-15 — 메인 통계·특허 섹션 퍼블리싱

- 작업 목적: 홈 히어로 뒤에 서비스 통계와 알고리즘 특허 출원 증빙을 배치.
- 변경 파일: `views/pages/index.php`, `views/components/partnerit-metrics.php`, `views/components/partnerit-patent.php`, `resource/js/animation/SlotCounter.js`, `resource/scss/components/_partnerit-metrics.scss`, `resource/scss/components/_partnerit-patent.scss`, `resource/scss/components/index.scss`, `resource/images/main/proof/kipo-logo-horizontal.svg`.
- 연관 파일: `docs/03-constants/components.md`, `docs/04-history/patch-notes.md`, `design-qa.md`.
- 주요 변경: 4개 성과 카드와 특허 배너를 별도 컴포넌트화하고, 수치 3개에 진입 시 1회 슬롯 애니메이션 추가.
- 영향 범위: 공개 홈 화면 및 전역 JS/Sass 번들에 한정. 백엔드·DB·API·i18n 변경 없음.
- 리스크: 첨부 시안의 특허청 CI는 2016–2025 구 CI이며, 현재 기관명은 지식재산처.
- 테스트: PHP lint, Node 구문 검사, watch 컴파일, 데스크톱·모바일 렌더 및 슬롯 동작 확인.
- 후속 작업: 통계 문구·수치와 출원번호의 대외 공개 문구를 최종 확정하고, 현행 지식재산처 MI의 SVG 배포 여부를 확인.
- 히어로 다음에 데이터 통계 카드 4개와 특허 출원 기술 배너를 각각 독립 컴포넌트로 추가.
- 숫자 3개는 화면 진입 시 슬롯 릴 애니메이션을 한 번 재생하고, 모션 축소/미지원 브라우저는 정적 숫자로 노출.
- 참고 시안의 특허청 공식 가로형 CI SVG 및 출원번호를 배너에 반영.
- 컴포넌트 카탈로그와 SCSS forward 등록.
- 검증: npm watch Sass 산출, PHP lint, 로컬 HTTP 렌더 확인. 시각 확인은 `design-qa.md`에 기록.
- 자산 출처: [대한민국 특허청 공식 MI 가이드에 수록된 과거 CI(2016–2025) 가로형 SVG](https://commons.wikimedia.org/wiki/File:Korean_Intellectual_Property_Office_Logo_(horizontal).svg); 2025-10 이후 현행 지식재산처 MI는 공식 자료실에서 SVG가 아닌 AI/PNG로 배포됨.

## 2026-09-15 — 메인 히어로 퍼블리싱

- Figma `260911`의 `01_Hero` 카피·CTA와 Frame 17571 시각 구성을 메인 페이지에 적용.
- 중앙 노트북 목업은 단일 PNG export로 교체하고, 네 시각 요소의 그림자는 투명 export 위에 SCSS filter로 적용.
- Figma 캔버스 배경을 제외한 투명 PNG로 다시 내보내 사각형 그림자 번짐을 제거하고, 미리보기 캐시도 갱신.
- 맞춤 공고 메일·분석 리포트·컨소시엄 매칭 레이어를 독립 배치하고 순차 페이드 처리.
- Figma PNG export를 `resource/images/main/hero/`에 저장하고 모바일 그리드 및 모션 감소 설정 추가.
- 검증: npm watch Sass 산출 성공, PHP 문법·로컬 HTTP 렌더 확인.

## 2026-09-15 — Partnerit 와이어프레임 초기화

- 공개 페이지의 기존 전용 카피·페이지별 디자인을 공통 와이어프레임 구조로 교체.
- 관리자 화면은 기존 CMS 동작을 유지하고 회색조 스타일과 Partnerit 텍스트 워드마크 적용.
- 프로젝트 프로필에서 기존 고객사 연락처·영상·태그 설정을 비우거나 비활성화.
- DB, 계정, 인증키, 배포 설정, sources/ 원본 자료는 보존.
- 다음 단계: 새 메뉴 구조, 브랜드 자료, 페이지별 콘텐츠를 받은 뒤 실제 디자인/퍼블로 교체.

## 2026-09-16 — 메인 Final CTA 및 공통 푸터 적용

- 작업명: Figma `14_Final_CTA` 및 `15_Footer` 퍼블리싱.
- 작업 목적: 메인 마지막 섹션과 전 공개 페이지 공통 푸터를 Partnerit 실제 디자인으로 교체.
- 변경 파일: `views/components/partnerit-final-cta.php`, `views/components/partnerit-cta-link.php`, `views/components/footer.php`, `views/pages/index.php`, 관련 SCSS와 타이포 유틸.
- 연관 파일: `views/layouts/main.php`, `resource/scss/components/index.scss`, `docs/03-constants/components.md`.
- 주요 변경 내용: Figma 원본 배경 PNG·흰색 로고 SVG 적용, CTA 링크 공통화, 회사정보·서비스 메뉴·정책 행 구성, 데스크톱/모바일 반응형 처리.
- 영향 범위: Final CTA는 메인에만 추가되고, Footer 외형은 `layouts/main.php`를 사용하는 모든 공개 페이지에 적용.
- 데이터 영향: DB·스키마·API·권한 변경 없음. 기존 footer site-tag 출력 로직 유지.
- 리스크: Figma의 `이용약관` 전용 라우트가 없어 현재 준비된 `/legal/cookies` 페이지에 연결했으며, 약관 페이지 확정 시 경로 교체 필요.
- 테스트 결과: PHP lint 3개 파일 통과, Sass/JS 번들 0 errors·0 warnings, 홈/PNG/SVG HTTP 200, 1440×900 및 390×844 실렌더 확인.
- 후속 작업: 실제 이용약관 콘텐츠와 전용 라우트 확정.
## 2026-09-16 — 컨소시엄 매칭 Hero 적용

- 대상: `/business/consortium-matching`
- Figma `350:11114` 기준으로 제목, 설명, 1192px 대시보드 배치와 878px Hero 높이를 구현했습니다.
- 대시보드는 Figma 완성본을 2배 PNG로 추출하고, 첫 진입 시 하단에서 올라오는 모션과 `prefers-reduced-motion` 대체 동작을 적용했습니다.
- 변경 파일: `views/components/partnerit-consortium-hero.php`, `views/pages/business/index.php`, `resource/scss/components/_partnerit-consortium-hero.scss`, 컴포넌트 인덱스와 카탈로그.
- 검증: PHP lint, SCSS/JS build, 대상 URL 및 에셋 HTTP 응답, 데스크톱·모바일 렌더링 확인.

## 2026-09-16 — 컨소시엄 매칭 문제 정의 섹션 적용

- 대상: `/business/consortium-matching` Hero 하단.
- Figma `350:10875` 기준으로 577px 배경 섹션, 2열 카피, 3열 문제 카드 구조를 구현했습니다.
- 카드 아이콘 3개는 Figma 원본 PNG를 추출했고, 콘텐츠는 접근 가능한 HTML 목록 구조로 구성했습니다.
- 반응형에서는 2열 후 1열 카드로 전환되도록 처리했습니다.

## 2026-09-16 — 컨소시엄 매칭 시스템 섹션 적용

- 대상: `/business/consortium-matching` 문제 정의 섹션 하단.
- Figma `350:10899` 기준 983px 섹션과 2열 Grid를 구현했습니다.
- 비주얼 배경은 CSS 다중 그라디언트로 구성하고, 중앙 대시보드 패널은 Figma 2배 PNG를 사용했습니다.
- 대시보드의 하단 진입 모션과 처리 단계 배지의 순차 노출을 AOS로 적용했으며 모션 감소 환경을 지원합니다.

## 2026-09-16 — 컨소시엄 핵심 기능 Grid 적용

- 대상: `/business/consortium-matching` 매칭 시스템 섹션 하단.
- Figma `350:10960` 기준 678px 그라디언트 섹션과 312px 카드 4열 구조를 구현했습니다.
- 기능 아이콘 4개는 Figma 원본 PNG를 추출하고, 카드 콘텐츠는 접근 가능한 목록 구조로 구성했습니다.
- 반응형 4/2/1열 전환과 카드 순차 노출, 모션 감소 환경을 적용했습니다.

## 2026-09-16 — 컨소시엄 협업툴 Grid 적용

- 대상: `/business/consortium-matching` 핵심 기능 섹션 하단.
- Figma `350:10988` 기준 1356px 섹션과 424×425 카드 2×2 구조를 구현했습니다.
- 채팅, AI 회의록, 문서관리, 프로젝트 추적 비주얼을 각각 Figma 2배 PNG로 추출해 정확한 카드 상단 포지션으로 배치했습니다.
- 반응형 2/1열 전환, 순차 노출, 모션 감소 환경을 적용했습니다.

## 2026-09-16 — 컨소시엄 공통 하단 섹션 연결

- 대상: `/business/consortium-matching` 협업툴 섹션 하단.
- 메인과 동일한 고객 리뷰, 파트너 마퀴, 요금제, 최종 CTA 컴포넌트를 공통 include로 연결했습니다.
- 리뷰 Swiper와 파트너 마퀴를 포함한 기존 반응형·인터랙션 로직을 중복 구현 없이 그대로 사용합니다.

## 2026-09-16 — 회사소개 페이지 전체 적용

- 대상: `/about`, `/about/company`.
- Figma `350:11731` 기준 회사소개 Hero, 서비스 흐름, 비전, 주요 연혁, 기술자산·기업 인증 섹션을 컴포넌트 단위로 구현했습니다.
- 히어로 배경, 기능 아이콘 4종, 인증서 원본 5종과 화살표는 Figma 원본 에셋을 로컬에 저장했습니다.
- 인증서 Swiper의 이전·다음·페이지네이션과 모바일 대응을 추가하고, 하단 고객 후기부터 CTA까지 기존 공통 컴포넌트를 연결했습니다.

### 260916 마스터 규칙 반영

- `374:676` 마스터 보드 기준으로 인증 문서 타이포를 문서명 24/34 SemiBold, 메타 16/24 Regular로 분리했습니다.
- 인증 슬라이더는 1320px viewport·344px slide·40px gap·원본 5장·비순환 이동으로 보정했고, 버튼 포커스와 실제 묶음 기준 페이지네이션을 적용했습니다.
- 모바일 390px에서 회사소개 컨테이너를 `min(1320px, calc(100% - 48px))`로 고정해 가로 넘침 및 한글 타이틀 잘림을 제거했습니다.

## 2026-09-16 — 공고 매칭 고객자료 기반 와이어프레임 적용

- 대상: `/business/announcement-matching`, Figma `350:12186`.
- 전용 컴포넌트: Hero·공고 조건 분석·기업 프로필 활용·지원 가능 공고 선별/일정 관리·AI 분석 리포트 CTA를 BEM PHP/SCSS 단위로 구성했습니다.
- 에셋: Figma 원본 공고 추천 대시보드와 분석 마스코트·배경을 `resource/images/business/announcement/`에 저장해 사용합니다.
- 공통 구성: 고객 후기, 파트너 마퀴, 요금제, Final CTA는 기존 공통 컴포넌트를 재사용했습니다.
- 영향: 공개 공고 매칭 페이지와 SCSS bundle만 변경하며 DB·API·권한·다국어 로직 변경은 없습니다.

## 2026-09-16 — 공고 매칭 원본 재대조 및 에셋 교정

- ① 요구사항: 260916 공고 매칭 원본과 START HERE 마스터(374:676)를 우선해 임의 문구·누락 섹션·잘못된 보고서 이미지를 교정.
- ② 영향 분석: UI 번들만 변경. 공개 페이지 → 7개 include → 컴포넌트 SCSS → 기존 watch 흐름. 공통 하단 및 다른 사업 페이지의 동작은 유지.
- ③ 계획: 섹션별 design context와 스크린샷 확보 → 원본 SVG/PNG 추출 → 명명 타이포·범위 토큰 → 각 PHP/SCSS 수정 → 브라우저 검증.
- ④ 구현: 지원 조건 4행, 수집·분석 3카드, 기업정보 활용 3항목, 선별 조건 4항목, 관리 기능 4카드, 실제 AI 리포트 목업을 복원. 1320px 컨테이너, 본문 섹션 상하 112px, 원문·배경 그라디언트 적용.
- 에셋 근거: Hero 350:12210, 선별 목업 350:12577, 리포트 목업 350:12929. 원본 아이콘과 배경은 각 섹션 design context 제공 자산 사용. 바깥 그림자는 이미지 부모 SCSS로 분리.
- ⑤ 검증: PHP 7개 컴포넌트 및 페이지 lint 정상. 기존 watch 재컴파일 오류/경고 0. Edge headless 1920/1440/1024/768/390/360px에서 가로 넘침 0, 모든 전용 이미지 decode 정상, JS pageerror 0. 1920px 본문 섹션 높이 648/528/620/718/554/718px 확인. 데스크톱·모바일 실제 스크린샷 확인.
- ⑥ 기록: 컴포넌트 카탈로그와 본 이력 갱신. DB/API/권한/다국어/신규 의존성 변경 없음.
- ⑦ 인계: /business/announcement-matching. AI 보고서 CTA는 기존 /business/ai-report 라우트를 사용하며 목적지 페이지 신규 구현은 이번 범위가 아님. 모바일은 별도 승인 시안이 없어 콘텐츠 순서 유지 및 단일 열 재배치.

## 2026-09-16 — 요금정책 및 고객사례 페이지

- 요구사항/원본: Figma 350:15049, 350:15399. 기존 마스터 규칙과 UI 번들 적용.
- 영향/계획: 기존 pricing/partners 라우트를 유지하고 페이지 include 조합, 전용 PHP/SCSS, 명명 타이포 토큰을 추가. DB/API/관리자/다국어 변경 없음.
- 구현: 요금정책 전용 상단 및 공통 가격표·후기·파트너·CTA. 고객사례 전용 상단, 대표 후기, 3열 갤러리 5건, 공통 파트너·CTA. 원본 PNG 3개/SVG 2개 저장.
- 검증: PHP 신규 컴포넌트/페이지 lint 정상. watch 오류0 경고0. Edge 1920/1024/768/390/360px 두 경로 HTTP200, h1 각각1, 가로 넘침0, JS pageerror0. 전용 데스크톱/모바일 레이아웃 스크린샷 확인.
- 제약/인계: 사진 시안 표기 유지. 추가 후기 데이터가 없어 페이지2~4 및 이동버튼은 비활성 UI. 관리자 연동은 미구현. 공통 하단의 일부 지연 이미지가 전체 페이지 자동 캡처에서 미표시되어 해당 캡처로 로딩 완료를 확정하지 않음.
- 기록: 컴포넌트 카탈로그 갱신. console/components 미구현으로 실제 공개 경로를 사용.

## 2026-09-16 — AI 보고서 고객자료 기반 와이어프레임 구현

- 작업 목적/요구사항: Figma 350:13649 전용 5개 섹션을 원문과 원본 에셋으로 구현. 기존 빈 본문을 대체하고 공통 하단 연결.
- 제약: 기존 메인/공고/컨소시엄 유지. DB/API/권한/i18n/배포 변경 없음. 모바일은 별도 시안 없이 콘텐츠 순서 유지.
- 변경 계획 및 호출 흐름: 기존 routes/web.php의 business.ai-report → pages/business/index.php 분기 → AI 보고서 PHP 5개 → 동명 SCSS 및 components/index.scss. 기존 CTA에 gradient 변형만 확장.
- 변경 파일: views/components/partnerit-ai-report-*.php, resource/scss/components/_partnerit-ai-report*.scss, resource/scss/abstract/_headings.scss, components/index.scss, views/pages/business/index.php, partnerit-cta-link.php/SCSS, 원본 이미지 디렉터리.
- 연관 파일/영향 분석: Backend routes/web.php 읽기만; Frontend 공통 CTA 및 announcement-report 패턴 참조; Data 해당 없음; i18n 한국어 원문 유지; Test 브라우저 검증; Docs components.md 및 본 이력.
- 주요 구현: 좌우 Hero, 분석 입력정보 2카드, 지원 준비 4항목, 종합진단 이미지와 지표 해설 4카드, 역량/파트너 2카드. PNG 4개와 SVG 6개를 Figma에서 직접 다운로드.
- 적용 규칙: #1 기존 CTA/하단 재사용, #5 inline style 없음, #10 동명 BEM/PHP/SCSS, #11/#17 타이포 맵, #19 페이지는 include 조합, #18 Edge 실측.
- 검증 명령/결과: docker-compose exec -T web php -l(전용5개/CTA/페이지) 모두 정상. 기존 watch 643ms 오류0 경고0. Edge Playwright 1920/1440/1024/768/390/360에서 scrollWidth=viewport, 이미지 decode 누락0, pageerror0. 메인/공고/컨소시엄 HTTP200 및 h1 각각1 확인. 1920/390 스크린샷 육안 확인.
- 리스크: 무료 체험은 기존 사이트와 동일한 /contact 연결. 실제 플랫폼 URL 확정 필요. 모바일 보고서 목업은 원본 비율 축소이므로 작은 글자는 장식적 예시이며 핵심 설명은 HTML로 별도 제공.
- 후속/문서: 카탈로그와 본 이력 갱신. console/components 및 ui-audit 경로는 현 저장소 미구현으로 실제 공개 페이지 검증을 사용. 신규 의존성 없음.

## 2026-09-16 — 메인 통계 숫자 넘침 수정

- 목적/요구: 기존 수치와 슬롯 효과를 유지하면서 카드 밖 숫자 넘침 제거.
- 원인: 공용 f-display-3 최대58px가 좁은 카드 내용 폭에 비해 큼.
- 변경 파일: partnerit-metrics.php, _partnerit-metrics.scss, _headings.scss, SlotCounter.js.
- 연관 흐름: 메인 통계 include → 전용 타이포/슬롯 SCSS → IntersectionObserver → Web Animations.
- 구현: 전용 24~40px 토큰, 1024px 이하 2열, 릴 셀 수축 방지, 애니메이션 종료 후 숫자 문자열 복원.
- 영향: Frontend 통계만. Backend/Data/i18n/권한/API 변경 없음. 기존 reduced-motion 최종값 경로 유지.
- 검증: 기존 watch 오류0/경고0. Edge 1920/1440/1024/768/390/320px에서 4개 값 모두 카드 내부 가용폭 이내. 종료 후 값40/35,000/92.4 및 reel DOM0 확인. 390px 스크린샷 확인.
- 규칙: #10 BEM 일치, #11/#17 폰트 맵, #18 실제 너비 측정. inline style/신규 라이브러리 없음.
- 문서: 컴포넌트 카탈로그 갱신. 감사/쇼케이스 미구현으로 실제 메인에서 검증.
- 리스크/후속: 실제 데이터 수치는 변경하지 않음. 폰트 또는 통계 자릿수 변경 시 폭 재검증 필요.

## 2026-09-16 — 무료 체험 외부 로그인 연결

- 사용자 확정 URL: https://app.partnerit.kr/account/signin.
- 변경: header.php, hero.php, partnerit-ai-report-hero.php, partnerit-final-cta.php의 무료 체험 href만 교체. 같은 창 이동 유지.
- 영향: 공개 CTA 4개 원본 컴포넌트 및 공통 포함 페이지. 일반 문의/contact 라우트, DB/API/권한/디자인 변경 없음.
- 문서: 컴포넌트 카탈로그에 확정 목적지 기록. 외부 로그인 기능 자체는 이번 수정 범위 아님.

## 2026-09-16 — 무료 체험 새 탭 전환

- 사용자 후속 요청에 따라 무료 체험 4곳에 target=_blank 및 rel=noopener noreferrer 적용.
- 공통 partnerit-cta-link는 newTab 선택값 추가, 기본 false로 기존 내부 링크 동작 유지.
- 헤더/메인/AI 보고서/공통 하단만 변경. URL 및 DB/API/디자인 변경 없음. 카탈로그 갱신.

## 2026-09-16 — 보미 플로팅 요소 및 TOP 표시 수정

- Figma Frame17644(350:9345) design context와 원본 PNG 추출. 공통 aside TOP 위에 흰 원형/보미 상시 배치.
- floating-button.php, _floating-button.scss, FloatingButtonController.js 변경. TOP 숨김 기본값과 is-visible 상태 구현, 프라이머리 블루 #3c82f6 적용.
- 300px 초과 스크롤 조건 유지, 푸터에서도 TOP 사용 가능. 보미는 연결 목적지가 없어 표시 요소만 구현.
- PHP lint 정상. 모바일 브라우저에서 초기 숨김/스크롤 표시/TOP 클릭 복귀 확인. DB/API/라우트 영향 없음. 카탈로그 갱신.

## 2026-09-16 — 메인 통계 공통 컨테이너 적용

- 사용자 요청: 통계 섹션 폭을 다른 메인 섹션과 통일.
- partnerit-metrics.php에 no-container-xl 적용, _partnerit-metrics.scss의 독립 980px 폭과 패딩 제거.
- 슬롯/수치/카드 반응형 유지. DB/API/라우트 영향 없음. 컴포넌트 카탈로그 갱신.

## 2026-09-16 — 메인 전체 컨테이너 정렬

- 요구: 성과(단순한 기능 소개보다) 포함 메인 섹션의 컨테이너/좌우 여백 통일.
- 변경: impact의 1038px 제한 제거, patent에 공통 컨테이너 적용 및 1080px 제한 제거, solutions/services의 데스크톱 패딩0 예외 제거, services/service-details 중복 max-width 제거.
- 기준: 전역 no-container-xl 유지. 내부 텍스트/목업 규격 및 풀폭 리뷰/마퀴 트랙 유지. DB/API/라우트/콘텐츠 영향 없음.
- 검증: Edge Playwright에서 메인 12개 본문/헤더 컨테이너 모두 동일한 좌표/내용폭. 1920px에서 x324/1272px, 1440px에서 x83/1274px, 1024px에서 x22/981px, 390px에서 x20/350px(반올림).
- 문서: 컴포넌트 카탈로그 갱신. 서브 전용 디자인 컨테이너는 이번 메인 수정 범위에서 제외.

## 2026-09-16 — 보미 체험 링크 및 하단 배치

- 사용자 요청에 따라 보미 표시 요소를 무료 체험 외부 링크로 전환(target=_blank, noopener noreferrer).
- TOP 숨김 시 display:none으로 레이아웃 공간도 제거. 스크롤 시 TOP 표시 및 보미 상단 배치.
- floating-button.php/SCSS만 수정. 키보드 포커스 표시 추가. PHP lint 및 모바일 초기/스크롤 위치 확인. DB/API 변경 없음.

## 2026-09-16 — 플로팅 버튼 양방향 트랜지션

- TOP의 display:none 전환을 absolute/opacity/visibility 방식으로 대체하여 사라질 때도 240ms 페이드·이동 적용.
- 보미는 has-top 상태에 따라 240ms 위아래 이동. 숨겨진 TOP은 클릭 비활성. reduced-motion 시 전환 제거.
- _floating-button.scss/FloatingButtonController.js 수정. 브라우저에서 표시/숨김 중간 opacity/숨김 완료 및 보미 복귀 확인. URL/DB/API 변경 없음.

## 2026-09-16 — 규칙판 기반 공통 토큰 및 메인 배경 재마이그레이션

- 요구/계획: 374:676과 텍스트 가이드 읽기 → 현재 원본 노드와 코드 대조 → 누락 배경/컬러/문구 수정 → 공개7개 경로 검증.
- 확인한 원본: 350:9347, 350:9354, 350:10809, 350:9282, 350:10419. Figma 파일 변경 없음.
- 변경: _colors.scss의 임시 회색 브랜드를 Primary/본문/제목 토큰으로 교체. body400. no-container-xl 실내용1320px.
- 특허: 원본 배경PNG 추가, 잘못된 행정 문구 정정, 제목/본문 역할 토큰 적용. CTA 배경PNG 최신 추출 및 plain img/picture 스타일 호환.
- Hero: 최신 블루 알림 카드 SVG를 원본 export로 추가. 기존 이미지 보존.
- 성과: 원본 배경SVG 추가, 최신 조사 설명과 수치/차트 비율 정정. 97.9/66.7/83.3.
- 영향: Frontend 공통 토큰과 메인/공통 CTA. Backend/Data/i18n/권한/API 변경 없음. 기존 무료체험/aside 동작 유지.
- 검증: Edge 7개 경로 ×1920/390, HTTP200, overflow0, pageerror0. 배경/알림 에셋 decode 정상. 특허/CTA 스크린샷 확인. watch 오류0/경고0.
- 문서: 카탈로그 및 260916-publishing-migration.md에 원본 노드/토큰/미완료 범위 명시.
- 남은 범위: 전7페이지 모든 개별 노드의 시각/문구/타이포 완전 재대조는 미완료. 이를 픽셀퍼펙트 완료로 보고하지 않음.

## 2026-09-16 — 활성 후기 및 LOGO_01 마퀴 수정

- 원인: 기본 후기 데이터 5개 중 4개의 quote/client/role이 비어 있었음.
- Figma 350:10513 및 350:15399의 원문을 사용해 빈 후기를 채우고 익명 작성자와 사진 시안 표시를 유지. 이미지/음영/문구 레이어 순서를 명시함.
- 별도 LOGO_01 16개(395:678~694, 395:711~723)를 각각 export해 figma-logo-01~16.png로 저장. 원본 202×92 영역을 표시하고 외곽 캔버스는 CSS에서 제외함.
- 마퀴는 화면 폭에 맞춰 각 그룹의 반복 수를 확보하며 resize에도 재계산. 이미지 사전 로딩, 두 그룹의 동일 길이 유지.
- 성과 차트의 사용 라벨 줄바꿈 방지, 단축 시간 막대의 불필요한 회색 트랙 제거.
- 검증: PHP lint 2개 통과. Edge 390/1440/2560에서 후기 5개 전환 시 문구 opacity1, 페이지 overflow0, JS 오류0. 2560에서 각 마퀴 그룹3360px로 빈 꼬리 방지. 기존 watch 재빌드 성공.
- 영향 범위: 공개 공통 후기/파트너 및 메인 성과 차트. DB/API/관리자/무료체험 링크 변경 없음.

## 2026-09-16 — 회사소개 인증서 Swiper 조작부

- 목적: 이전 화살표의 흰색/방향 오류 수정 및 요청한 하단 설명 2줄 제거.
- 변경 파일: partnerit-company-certificates.php, _partnerit-company-certificates.scss, SwiperController.js.
- 연관 파일: views/pages/company/index.php, about/company/arrow-prev.svg·arrow-next.svg, _colors.scss.
- 계획/호출: 회사소개 페이지 → 기존 인증서 컴포넌트 → SwiperController. 기존 SVG를 올바른 색상으로 재사용하고 이전 방향 회전; 가운데 pagination 폭 고정; 한 칸씩 이동.
- 주요 변경: Primary 토큰으로 다음 버튼/활성 점 통일, 버튼 축소 방지 및 페이지 점 키보드 포커스. 요청한 notice와 대응 SCSS 삭제.
- 영향: Frontend만. Backend/Data/i18n/API/권한 변경 없음. 특허 및 상표 출원 캡션과 실제 서류 파일은 보존.
- 규칙: #2 기존 SwiperController 사용, #3 활성 색상 토큰, #5 inline style 추가 없음, #10 BEM 일치.
- 검증: PHP lint 통과. Edge 1440/390에서 이전/다음/페이지 점 클릭 및 notice 미출력 확인. 다음 버튼 rgb(60,130,246). watch 오류0/경고0.
- 문서: components.md 갱신. 관리 콘솔 감사 기능은 본 공개 퍼블 범위에 해당 없음.
- 리스크/후속: 기존 인증서 원본 해상도는 변경하지 않음. 고해상도 원본 교체는 별도 작업.

## 2026-09-16 — 현재 Figma Soft Blue V3 에셋 적용

- 목적: 사용자가 지정한 배경6곳·아이콘25곳·협업 목업2곳을 현재 시안으로 교체.
- 요구: 기존 문구/가격/순서/폰트/레이아웃 유지. 실제 서비스 기능 추가 금지.
- 변경 파일: company hero/vision/flow, announcement hero/problem/profile/selection/management, ai-report hero/diagnosis/preparation/capability의 PHP/SCSS 및 consortium-collaboration PHP. shared/soft-blue-v3 PNG17개, collaboration PNG2개 추가.
- 연관: routes/web.php, views/pages/business/index.php, 기존 이미지 헬퍼 및 watch 파이프라인.
- 계획/영향: 원본 README3개와 Figma 디자인 컨텍스트 대조 → 개별 파일 복사/추출 → 기존 컴포넌트 경로/SCSS 교체 → 4페이지×2뷰포트 검증. Backend/Data/i18n/API/권한 변경 없음.
- 주요 변경: PNG cover만 사용, 이전 overlay 제거. 아이콘 슬롯 contain, 4개만 white plate. 회의·프로젝트 현재 visual frame export로 크롭 유지.
- 위험 관리: 원본/기존 에셋 삭제하지 않음. 메인/컨소시엄의 확정 배경과 기능성 아이콘 유지. 실제 Hover/Active 서비스 기능은 추가하지 않음.
- 검증: PHP lint, watch 오류0/경고0, Edge 1920/390 HTTP200·overflow0·broken images0·pageerror0.
- 문서: 260916-assets-v3.md 및 components.md 갱신.
- 후속: 현재 랜딩 목업은 정적 이미지. 실제 서비스 UI 구현은 별도 범위.

## 2026-09-16 — 회사소개 비전 메시지 위계 개선

- 목적: 사용자 지적에 따라 분리되어 보이던 제목/오른쪽 하단 본문 구도 개선.
- 원본 확인: 현재 Figma 350:11782 디자인 컨텍스트/스크린샷. 기존 원문과 V3 PNG 유지.
- 변경: partnerit-company-vision.php 및 _partnerit-company-vision.scss.
- 연관: _headings.scss의 기존 heading-2/vision-lead/company-hero 토큰 재사용.
- 계획: 2열 편집형 구도, 핵심 메시지 강조, 연결/데이터/기술 행 구분, 결론 구분선. 1024px 이하 순차 1열.
- 영향: Frontend 비전 컴포넌트만. Backend/Data/i18n/API 변경 없음.
- 검증: php -l 통과. Edge 1920/390 가로넘침0, PC/모바일 섹션 캡처 시각 확인.
- 문서: components.md 업데이트.
- 리스크: 원본 Figma와 의도적으로 다른 개선안이며 현재 요청에 따른 웹 배치 변경. Figma 파일 자체는 수정하지 않음.
- 후속/캡처: handoff/verification-v3/vision-1920.png, vision-390.png.

## 2026-09-16 — 헤더 active 및 공개 페이지 반응형 점검

- 목적: 현재 경로를 공통 헤더·드로어에 표시하고 공개7개 페이지 반응형 점검.
- 변경: views/components/header.php, drawer.php, resource/scss/components/_header.scss.
- 연관: src/Core/Application.php 활성 체인, src/Menu/MenuItem.php, HeaderManager.js, routes/web.php.
- 구현: 기존 isActive 재사용, 상위/하위 aria-current, Primary blue 활성 스타일. 320px에서 로고/CTA 사이 여유를 위해 360px 이하 로고120px.
- 범위: Frontend만 변경. Backend 메뉴 판정 재사용, Data/i18n/DB/API 변경 없음.
- 검증: 7경로 ×320/390/768/1024/1440. document 가로넘침0, 헤더 로고/CTA 겹침0, viewport 밖 본문 후보0, JS 오류0. 의도적 swiper/marquee 범위는 제외.
- 메뉴: 모바일 열림 및 Escape 닫힘 확인, 컨소시엄 상위/하위 computed Primary blue 확인. PC dropdown 노출 확인.
- PHP header/drawer lint 통과. 카탈로그 갱신. 임시 검증 스크립트 삭제.
- 리스크: 자동 경계 검사는 모든 내부 텍스트의 시각 품질까지 보증하지 않음. PC/320px 헤더·메뉴 직접 캡처 추가 확인.

## 2026-09-16 — 상위 메뉴 첫 하위 이동 및 버튼 hover 정리

- 목적: 주요기능 등 상위 메뉴 클릭 시 첫 하위 페이지 활성화, 버튼 hover 위치 이동 제거.
- 변경: header.php, drawer.php, routes/web.php 및 header/CTA/services/reviews/company-certificates SCSS.
- 구현: 첫 공개 하위 URL 연결, /about·/business 302 별칭. Primary/navy/surface 토큰 재사용, hover 배경·색 전환만 적용.
- 유지: 리뷰 슬라이드/내용 등장, TOP 및 aside 표시 전환. 고객 문구·가격·에셋 변경 없음.
- 검증: /business → announcement-matching 및 /about → company 최종 URL과 상·하위 aria-current 확인. CTA hover 전후 y=589, transform=none, 배경 blue → primary-hover 확인. PHP lint 정상, watch 빌드 오류0/경고0.
- 카탈로그 갱신. 신규 의존성·DB·API 변경 없음.

## 2026-09-16 — 하위 NAV active 시각 상태 보강

- 요청: 공고매칭 등 하위 NAV 현재 페이지 표시.
- 원인: active 규칙 뒤의 동일 우선순위 hover 규칙이 색을 덮음.
- 범위: _header.scss dropdown만 변경, 기존 PHP aria-current 및 메뉴 활성 체인 재사용.
- 구현: hover/focus 토큰 통일, active 규칙 후순위 배치 및 font-weight 700 적용. 위치 이동 없음.
- 검증: Playwright로 공고매칭·AI 보고서·컨소시엄 매칭에서 현재 하위 링크 hover 확인. 모두 글자 rgb(60,130,246), 배경 rgb(236,243,255), 굵기700.
- Backend/Data/i18n/API 변경 없음. 카탈로그 갱신. 메뉴 링크/문구 유지.

## 2026-09-16 — 서비스 상세 이용 흐름 및 콘텐츠 보강

- 목적: 기능 설명을 사용자 이용 과정·보고서 해석·양방향 추천 관점으로 보강.
- 변경 파일: announcement-profile, ai-report-context, ai-report-capability, consortium-matching-system, final-cta, pricing PHP 및 business/index.php. profile/context/matching-system/pricing SCSS.
- 연관 파일: 기존 CTA, routes/web.php, V3 아이콘, diagnosis-report.png. 변경 불필요.
- 내용: 3단계 안내, 보고서 읽기, 서비스 간 CTA, 모집/참여 흐름, 요금 compact, 서비스별 최종 CTA.
- 영향: 공개 서비스3페이지. 메인/요금표 가격 및 기능 보존. DB/API/권한 영향 없음.
- 리스크: 미확정 가격 및 실제 기능 세부 기준은 고객 확인 필요.
- 테스트: PHP lint7개 통과, 3경로×4viewport 넘침0/이미지 오류0/JS 예외0.
- 문서: components.md 및 260916-service-content.md.
- 캡처: 디자인 워크스페이스 handoff/verification-v3/content-* 6개.
- 후속: 고객 콘텐츠 승인 및 미확정 다운로드·요금 기준 확인. 배포 수행 안 함.

## 2026-09-16 — AI 보고서 분석 구도·fade 정리

- 목적: 기업별 맞춤 분석의 정보 관계를 명료화하고 보고서 안내 밀도 완화.
- 변경: partnerit-ai-report-context.php 및 동일 SCSS. 기존 AOS 재사용.
- 구현: 설명/정보 결합 패널2열, 원본 document/building 아이콘. 모바일1열.
- 간격: 상단/보고서 사이96px(모바일56px), 보고서 상단64px(모바일48px), 안내 항목32px(모바일28px) 상하 여백. root10px 기준.
- 모션: 이동 없는 fade650ms, 0/100/200ms 지연. 일회 노출, reduced-motion 즉시 표시.
- 검증: 320/390/768/1440 가로넘침0, 안내3항목 도달 후 opacity1. PHP lint 정상, watch 오류0/경고0.
- 문서: 카탈로그 갱신. Backend/Data/i18n/API 변경 없음.
- 캡처: handoff/verification-v3/report-context-refined-390.png 및 -1440.png.

## 2026-09-16 — 역량 분석 좌우 정렬

- 목적: CTA 포함 왼쪽518px와 오른쪽348px 높이 불균형 해소.
- 변경: partnerit-ai-report-capability.php 및 동일 SCSS.
- 구현: 제목/CTA 전체행 분리, 카드와 목업2열 stretch, 모바일1열.
- 검증: 1440px에서 양측348px, 390px 가로넘침0. 이미지 비율 유지, PHP lint 정상, watch 오류0/경고0.
- 영향: 해당 섹션만. 링크/문구/이미지/Backend/Data/API 변경 없음. 카탈로그 갱신.
- 캡처: handoff/verification-v3/capability-aligned-{390,1440}.png.

## 2026-09-16 — 서비스 요금 안내 제거

- 사용자 요청에 따라 서비스3페이지 compact 요금 안내 및 비교 버튼 삭제.
- business/index.php 호출3개, pricing.php compact 분기, pricing SCSS 전용 스타일 제거.
- 공통 파트너 영역 다음 CTA 직접 연결, 별도 빈 래퍼 없음. 기존 파일 이력으로 복원 가능.
- 검증: PHP lint 통과. 서비스3페이지 해당 문구0/CTA1, 메인·요금정책 가격표3개 유지.
- 카탈로그 업데이트. 데이터·가격·API 변경 없음.

## 2026-09-16 — 컨소시엄 배지 복원 및 밀도 정리

- 목적: 설명 증가로 사라진 배지 UI 및 이미지 아래 빈 공간 개선.
- 변경: consortium-matching-system PHP/SCSS. 핵심 배지4개, 모집/참여 라벨 배지, 긴 번호 목록을 요약문으로 대체.
- 레이아웃: 좌우 stretch, 불필요한 섹션 최소높이와 마지막 중복 문구 제거. 기존 목업/배경 유지.
- 검증: 1440px 양측530px. 390px 가로 넘침0. watch 오류0/경고0.
- 카탈로그 갱신. DB/API/가격 영향 없음. 캡처 consortium-badges-{390,1440}.png.

## 2026-09-16 — 협업툴 및 페이지 컨테이너 통일

- 목적: 협업툴만 좁은 폭으로 보이는 문제 및 공고매칭 모바일 좌우 여백 차이 수정.
- 변경: _partnerit-consortium-collaboration.scss, _partnerit-announcement.scss.
- 연관: base/_root.scss 공통 no-container-xl, collaboration.php의 기존 컨테이너 클래스.
- 구현: 컨테이너/그리드 개별 폭 제거, 고정 카드 높이 대신 이미지 비율·내용 높이 적용. 공고매칭 모바일24px.
- 검증: 공개7페이지 ×390/1440/1920에서 공통 컨테이너 실측 기준 min(1320,viewport-48) 불일치0, 가로넘침0. PC/모바일 캡처 확인. watch 오류0/경고0.
- 유지: 후기 슬라이드·마퀴의 의도적인 전체폭, 제목 및 목업 자체의 최대폭. Data/API/문구/가격 변경 없음.
- 캡처: handoff/verification-v3/collaboration-container-{390,1440}.png. 카탈로그 갱신.

## 2026-09-16 — 보고서 프리뷰 패널 보강

- 목적: 짧은 이미지가 단독으로 떠 보이는 문제 해소.
- 변경: ai-report-context PHP/SCSS. 외부 패널과 정적 브라우저 프레임 추가, 이미지 중앙 정렬 및 원본 비율 유지.
- 검증: 1440px 프리뷰/설명 모두529px. 390px 가로넘침0. PHP lint 및 watch 오류0/경고0.
- 영향: 해당 프리뷰만, 기존 fade/데이터/이미지 보존. 카탈로그 갱신.
- 캡처: handoff/verification-v3/report-preview-shell-{390,1440}.png.

## 2026-09-16 — 역량 섹션 좌측 콘텐츠 묶음

- 사용자 표시대로 제목/카드2개/CTA를 왼쪽 copy로 묶고 오른쪽 이미지는 동일 높이 패널 안에 배치.
- 변경: capability PHP/SCSS. 원본 이미지 비율 유지, 900px 이하 세로 자연높이.
- 검증: 1440px 양측534px/상단동일. 320/390/768/1440 넘침0. PHP lint 및 watch 오류0/경고0.
- 카탈로그 갱신. 링크/문구/데이터 변경 없음.

## 2026-09-16 — AI 보고서 패널 톤·강조 개선

- 요청: 히어로 bold, 입력 아이콘 흰박스, 결과 문장 강조, 프리뷰 그라디언트.
- 변경: ai-report-hero.php, ai-report-context.php/SCSS. 색상 토큰 재사용, 확정 섹션 배경 PNG 변경 없음.
- 검증: 390/1440 가로넘침0, h1 computed700. watch 오류0/경고0. 캡처 report-gradient-{390,1440}.png.
- 카탈로그 갱신. 데이터/API 영향 없음.

## 2026-09-16 — 정보 카드 및 보고서 프리뷰 단순화

- 변경: ai-report-context PHP/SCSS. 카드 단색, 결과 위 border 제거, 프리뷰 제목/캡션 삭제 및 미사용 heading 스타일 제거.
- 검증: 390/1440 overflow0, 카드 backgroundImage none, 결과 border0px, 프리뷰 텍스트 없음. watch 오류0/경고0.
- 유지: 목업 프레임·그라디언트·fade·이미지 alt. 카탈로그 갱신.

## 2026-09-16 — 역량 목업 그라디언트

- 요청: 단색 이미지 패널을 그라디언트 위 목업 느낌으로 변경.
- 변경: capability PHP/SCSS. 정적 브라우저 프레임 및 토큰 기반 블루 그라디언트/그림자.
- 검증: PC 양쪽534px 유지, 390/1440 가로넘침0. PHP lint 및 watch 오류0/경고0.
- 카탈로그 갱신. 이미지·링크·데이터 변경 없음. 캡처 capability-gradient-{390,1440}.png.
## 2026-09-16 — 공고 매칭 기업정보 카드 번호 제거

- 날짜: 2026-09-16
- 작업명: announcement-profile 번호 표시 제거
- 목적: 사용자 요청에 따라 오른쪽 01·02·03만 제거, 카드 내용 유지.
- 변경 파일: views/components/partnerit-announcement-profile.php, resource/scss/components/_partnerit-announcement-profile.scss
- 연관 파일: views/pages/business/index.php:38 컴포넌트 호출 유지, docs/03-constants/components.md 갱신.
- 주요 변경: 번호 데이터·span·미사용 __number 스타일 삭제. 규칙 #10에 따라 뷰/SCSS 동시 정리.
- 영향 범위: Frontend 카드 표시만. Backend 호출 변경 없음. Data/DB/API/권한/i18n 해당 없음. Test는 PHP lint·Playwright, Docs는 카탈로그·본 기록.
- 리스크: 목록 기본 번호 재노출 가능성을 computed listStyleType으로 검증; none 확인.
- 테스트 결과: php -l 문법 오류 없음. Playwright 390/1440에서 번호0·카드3·목록 marker none·문서 가로 넘침0. 인라인 스타일·새 폼·타이포·컨테이너 변경 없음.
- 후속 작업: 없음.
## 2026-09-16 — 회사소개 비전 및 아이콘 배경 정돈

- 날짜: 2026-09-16
- 작업명: company vision/flow 시각 위계 보완
- 목적: 비전 가독성 개선, 플랫폼 아이콘에 은은한 블루 라운드 배경 적용.
- 변경 파일: resource/scss/components/_partnerit-company-vision.scss, _partnerit-company-flow.scss
- 연관 파일: views/components/partnerit-company-vision.php 및 partnerit-company-flow.php 확인, 문구·구조 변경 없음.
- 주요 변경: 비전 3행 화이트 카드, 마지막 문장 블루 패널, 플랫폼 아이콘 그림48px/슬롯64px/radius16px. 기존 토큰 사용, 인라인 스타일 없음.
- 영향 범위: Frontend 두 섹션만. Backend/Data/i18n/API/권한 변경 없음. Docs 카탈로그 갱신, Test Playwright 및 watch.
- 리스크: 모바일 긴 문구 줄바꿈 — 390px 캡처 및 overflow 검증.
- 테스트 결과: 390/1440 문서 넘침0, 아이콘 배경 rgb(236,243,255), radius16px, 비전3행. watch 오류0/경고0.
- 후속 작업: 없음. 기존 PNG 배경·공통 컨테이너·타이포 유지.
## 2026-09-16 — 회사소개 플랫폼 카드 제목 번호 제거

- 사용자 요청에 따라 views/components/partnerit-company-flow.php 제목 4개의 01~04 접두 번호만 삭제. 스타일·설명·아이콘·순서 및 Backend/Data/API 영향 없음. 카탈로그 동반 갱신.
## 2026-09-16 — 연혁 목록 마지막 항목 마감 정리

- resource/scss/components/_partnerit-company-history.scss: 연도별 last-child padding-bottom/border-bottom/min-height 0. 불릿 첫 줄 정렬. 문구·연도·나머지 항목 변경 없음. 카탈로그 갱신.
## 2026-09-16 — 비전 문장 단일 카드 통합

- 목적: 하나의 문장을 세 카드로 나누지 않고 자연스럽게 읽도록 사용자 수정 요청 반영.
- 변경 파일: views/components/partnerit-company-vision.php, resource/scss/components/_partnerit-company-vision.scss. 분할 span 제거, 단일 화이트 카드·은은한 그림자 적용.
- 유지: 원문, 세 키워드 강조, 타이포 유틸리티, 배경 PNG, 주변 내용, 공통 컨테이너.
- 영향: Frontend 비전만. Backend/Data/API/권한/i18n 변경 없음. Docs 카탈로그·본 기록 갱신.
- 검증: php -l 정상. Playwright 390/1440에서 분할 span0, strong3, 가로 넘침0. 모바일 캡처 확인. 행간 기존 유틸리티 약1.667배 유지.
- 리스크/후속: 없음. 기존 3행 카드 사양은 본 변경으로 대체.
## 2026-09-16 — 회사소개 히어로 상하 여백 보정

- 요청/원인: 상하 여백 불균형. 기존 flex-start·최소높이로 잔여 공간이 하단에 몰리고 고정 헤더가 상단 공간 차지.
- 수정: resource/scss/components/_partnerit-company-hero.scss 중앙 정렬 및 상단 공통 헤더 높이 보정. 모바일 적용. 이미지·문구·타이포·컨테이너 유지.
- 영향: Frontend 해당 히어로만, Backend/Data/API/i18n 변경 없음. 카탈로그 갱신.
- 검증: Playwright fonts.ready 후 390/1440/2048 확인. 헤더 아래~제목/설명~섹션끝 각각 약145/144px,145/149px,125/124px. overflow0. 공통 헤더 실제 높이와 fluid 토큰 차이에 따른 최대4px 편차 유지.
## 2026-09-16 — 회사소개 비전 GSAP pin 장면

- 목적: 사용자 요청에 따라 카드 대신 100vh 텍스트 중심 스크롤 후킹.
- 변경 파일: partnerit-company-vision PHP/SCSS, resource/js/animation/CompanyVisionController.js 신규, resource/js/app.js 초기화.
- 주요 변경: 원문 유지, 중앙 텍스트 구성, 기존 PNG 배경 opacity reveal, 제목 및 세 문단 순차 등장. 기존 GSAP/ScrollTrigger/Lenis 재사용, 의존성 추가 없음.
- 영향: 회사소개 비전 전용. Backend/Data/API/i18n 변경 없음. 카탈로그 갱신. 스크롤 구간은 viewport1.5배, 끝에는 모든 내용 노출 후 다음 섹션 이동.
- 안전장치: JS 미동작 시 원문 표시; 모바일/낮은 창/reduced-motion은 pin·숨김 없음. matchMedia 전환 시 GSAP 컨텍스트 복원.
- 검증: 1440x900 pin true·높이900·overflow0; 진행0/0.5/1에서 문단 opacity [0,0,0]/[1,.8483,0]/[1,1,1], 역스크롤 정상. 모바일390x844 pin false·문단 모두1·overflow0. 캡처 verification-v3/vision-pin-*.
- 리스크: 화면에 내용이 들어가지 않는 확대 환경은 pin을 생략하여 읽기 보장. 기존 문구/타이포/배경 유지.
## 2026-09-16 — 비전 본문 그룹화 및 협업 사진

- 요청: 대표 협업 사진 분위기 및 문단 연결감. imagegen 스킬의 built-in image_gen으로 신규 생성, 실제 고객 증언 사진으로 사용하지 않음.
- 에셋: resource/images/shared/soft-blue-v3/company-founders-vision.png (원본 생성 파일을 프로젝트로 복사).
- 생성 프롬프트: photorealistic-natural, fictional Korean SME founders men/women35–55 candid collaborative meeting in bright Seoul office; outer left/right subjects, bright empty central copy space; cool white/pale blue daylight; no logos/text/watermarks/handshake/holograms. 실제 인물 지정 없음.
- 변경: _partnerit-company-vision.scss 배경 사진 및 중앙 오버레이, 본문 간격 차등. CompanyVisionController.js 본문 그룹 단위 fade, 기존 pin/모바일/reduced-motion 안전장치 유지.
- 영향: Frontend 해당 섹션만. Backend/Data/API/i18n 변경 없음. 카탈로그 갱신.
- 검증: Playwright1440/390 overflow0, 배경 빌드에셋 연결, 종료 시 본문 opacity1. PC 설명18.76px/결론33.51px, 모바일16.04px/28.08px. 캡처 vision-founders-*.
## 2026-09-16 — AI 보고서 목업 그라디언트 통일

- 대상: _partnerit-ai-report-context.scss, _partnerit-ai-report-capability.scss.
- 변경: 제공한 Frame17371 배경 느낌에 맞춰 좌측 블루/우측 밝은 그라디언트, 패널·목업·툴바 테두리 제거. 본문/원본 이미지/높이/반응형은 유지.
- 검증: watch 빌드 확인. in-app browser 도구가 kernel assets 경로 오류로 열리지 않아 최신 렌더 시각 검증 불가. 기존 4사진 회사비전 요청은 이번 변경에 포함하지 않음.

## 2026-09-16 — AI 보고서 고해상도 목업과 제목 위계

- 목적: 목업 그림자 완화, 짧은 좌측 이미지 개선, 제목 위계 복구.
- 변경 파일: partnerit-ai-report-context.php, partnerit-ai-report-capability.php 및 대응 SCSS, _partnerit-ai-report.scss, images/business/ai-report/*-3x.png.
- 연관 파일: config/functions.php image_picture, abstract/_headings.scss, components.md.
- 주요 변경: 종합진단과 역량분석을 세로 연결. Figma 원본 350:14496(3960×1190), 350:14581(2040×1044) 3배 PNG. 제목 기존 section 유틸 사용. 목업 그림자 6px 18px -8px /9%로 통일.
- 영향: Frontend AI 보고서 두 영역만. Backend는 image_picture 호출 계약 유지; Data/DB/i18n 변경 없음. Test는 handoff/verify-report-refine.cjs, Docs는 카탈로그 및 이 기록.
- 규칙: #5 inline style 미추가, #10 기존 BEM 수정, #11/#17 기존 타이포 및 컨테이너 사용, #18 브라우저 실측, #19 components 내부 구현.
- 검증: PHP lint 두 파일 정상. watch 851ms, 0 errors/warnings. Playwright 390/1440/1920 가로 넘침0. 실제 PNG naturalWidth3960/2040 확인. 제목26.14/35.65/40px. PC 좌측 본문 이미지 합계451.6px+툴바36px. PC 두 영역 및 모바일 캡처 육안 확인.
- 리스크: 모바일에서 보고서 내부 작은 텍스트는 시각 예시이며, 핵심 설명은 옆/하단 HTML로 제공. 다른 페이지 이미지는 이번 범위에 포함하지 않음.
- 후속 작업: 없음. 캡처 handoff/verification-v3/report-refine-*.

## 2026-09-16 — 회사 비전 4사진 스크롤 카드
- 목적: 이전 단일 사진 배경 대신 요청한 4장 순차 상승 장면 구현.
- 변경 파일: company-vision PHP/SCSS, CompanyVisionController.js, company-vision-meeting-1/2/3.png.
- 연관 파일: app.js 기존 초기화 유지. 카탈로그 및 handoff/company-vision-photo-cards.md 기록.
- 주요 변경: 은은한 그라디언트/카드/텍스트 레이어 분리. 4장 stagger, 중앙 문구 그룹 유지, desktop pin2.5vh배, 모바일 2열 정적 배치.
- 영향: Frontend 비전 한 영역. Backend/Data/i18n 변경 없음.
- 검증: PHP lint 정상; Playwright1440/390 overflow0, 4이미지 로드, desktop 진행0/.4/1 순차 등장 확인, 모바일 pin false. 캡처 육안 확인.
- 리스크: 생성 사진은 장식이며 실제 고객 사례로 표기하지 않음. reduced-motion·낮은 화면 안전장치 유지.
- 후속: 없음.
- 후속 줄바꿈 보정: 비전 본문 최대폭680px, 연결/데이터 문장과 기술 문장을 2행으로 묶고 결론도 균형 있는 2행 처리. 모바일에서는 강제 행을 풀고 text-wrap:balance 적용. PHP lint·watch 오류0, 1440/390 overflow0 및 pin 동작 유지 확인.

## 2026-09-16 — AI 보고서 전달 흐름 모션 정리
- 목적: 무분별한 항목별 AOS 대신 정보 그룹별 강약 적용.
- 변경 파일: ReportFlowController.js 신규, app.js 초기화, context PHP AOS 제거, 대응 SCSS dead override 제거.
- 연관: 기존 GSAP/ScrollTrigger, AI report 5컴포넌트. 의존성 추가 없음.
- 주요 변경: 히어로/CTA 즉시 표시. 문제 이해→보고서 읽기→준비→지표→파트너 연결 순서의 설명과 목업별 viewport trigger. Copy 450ms, visual850ms/24px 상승, mobile350ms/이동0. 반복/스크롤고정/문자별 애니메이션 없음.
- 영향: Frontend 해당 페이지 전용. Backend/Data/i18n 변경 없음. 문구/가격/레이아웃/공통영역 유지.
- 안전장치: JS 미지원 기본 표시, reduced-motion 무효과, matchMedia revert, pagehide 정리, 이미 화면에 있거나 지나간 내용 애니메이션 생략.
- 검증: handoff/verify-report-flow.cjs. 1440 기본모션 AOS0, trigger11, JS오류0, 가로넘침0, 스크롤 완료 후 숨겨진 요소0. 모바일 및 reduced-motion 추가 실행 기록은 스크립트 출력 참조.
- 문서: components.md 갱신. 후속: 없음.

## 2026-09-16 — 메인·서브페이지 콘텐츠 흐름 모션 확장
- 목적: AI 보고서에 한정됐던 적용 범위를 사용자 확인 후 메인·서브로 확대.
- 변경: ReportFlowController를 ContentFlowController로 이동·개명, app.js 초기화 갱신. 명시적인 제목/설명/시각자료 그룹 설정으로 기존 엔진 재사용.
- 뷰: company-flow, consortium-features/collaboration/matching-system 개별 AOS 제거. 대응 consortium SCSS의 미사용 AOS override 삭제.
- 보존: Hero, 숫자 카운터, swiper, marquee, 회사 비전 pin, CTA 기능, 문구와 레이아웃.
- 영향: Frontend 전용. Backend/Data/i18n 무변경. 신규 의존성 없음.
- 검증: handoff/verify-content-flow.cjs. /, /about/company, /business/announcement-matching, /business/ai-report, /business/consortium-matching, /pricing 각각1440/390에서 HTTP200, AOS0, JS오류0, 가로넘침0, 스크롤 후 숨은 콘텐츠0. 메인 trigger20개 확인. /stories는 별도 페이지 확인 근거로 사용하지 않음.
- 문서: components.md 갱신. reduced-motion·matchMedia 정리·한번만 재생 정책은 기존 엔진 유지.
- 리스크/후속: 실제 서비스 데이터 변경 없음. 향후 섹션 추가 시 명시적 selector 목록에만 추가.

## 2026-09-16 — PDF 원문 기반 이용약관·개인정보처리방침 게시
- 목적: 빈 법률 문서 페이지에 제공된 두 PDF 내용 반영.
- 변경: legal/privacy.php·cookies.php, partnerit-legal-document.php/SCSS 신규, SCSS forward, resource/data/legal-documents.json.
- 관련: footer.php의 기존 privacy/cookies 링크 유지, routes/web.php 무변경. 카탈로그 및 legal-documents.md 갱신.
- 원문: 이용약관9쪽/개인정보4쪽. PDF 스킬, pdfplumber+PDFium 사용. CID 문자 복원, 조항별 분할 및 표3개 구성. 법적 의미·날짜·가격·항번호 변경 없음.
- 영향: Frontend 두 페이지 및 공통 렌더러. Backend는 정적JSON 읽기만 추가, DB/API/i18n 변경 없음.
- 검증: 원문 대비 공백 제외 문자계수 차이0. Playwright 두 페이지1440/390 본문 일치, overflow0, JS오류0, 목차 이동 정상. PHP lint 및 watch 오류0.
- 리스크: 원문 개인정보 제10조 중복, 약관 제13조 항7 누락 유지. 실제 연락처·위탁업체의 최신성 확인 필요; 법률 검토로 표방하지 않음.
- 후속: 사용자 확인 후 원문 변경이 승인되면 데이터 갱신.

## 2026-09-16 — Console 관리자 인증 스키마 복구

- 요청/원인: `/console/lib/login/login.process.php` 로그인 시 `nb_admin` 미존재로 PDOException 발생.
- 영향 분석: LoginProcessor의 `nb_admin` 조회 필드와 `nb_roles` 역할 매핑, console 계정 페이지 쿼리, DB 연결 설정(nineonelabs)을 확인. 프론트엔드/공개 페이지/데이터 i18n은 변경하지 않음.
- 변경 파일: `database/migrations/260916-0845-create-admin-schema.php`. `migrations` 이력 테이블, `nb_roles`(기본 3권한), `nb_admin`(sitekey+uid unique, 비밀번호 255자, active/role/email/phone/created·updated) 생성. 계정·비밀번호는 임의 시드하지 않음.
- 적용: Docker web 컨테이너에서 마이그레이션 실행, batch1 기록 확인.
- 검증: `SHOW TABLES`에서 nb_admin/nb_roles/migrations 확인, `SHOW CREATE TABLE nb_admin` 컬럼·인덱스 확인, `GET /console/index.php` HTTP200·Fatal error 없음, PHP lint 정상.
- 보안: 원문 덤프의 다른 사이트 관리자 계정을 복사하지 않음. 실제 관리자 등록은 콘솔의 계정 등록 화면에서 승인된 자격으로 수행.
- 후속: 관리자 계정 최초 등록/권한 부여 시 운영 담당자 승인 필요.

## 2026-09-17 — 실서버 배포 전 생성물 정리

- 목적: 운영 업로드 전 개발 과정에서 생성된 브라우저 프로필·검수 캡처·OS 메타 파일 제거.
- 변경 범위: 프로젝트 루트 `C:/Users/USER/Projects/파트너잇/퍼블/260915` 내부만 점검.
- 정리 대상: `.tmp-*` 브라우저 프로필 6개, `footer-preview*.png` 6개, 하위 경로 포함 `.DS_Store` 5개.
- 처리 방식: 영구 삭제 명령이 실행 정책에 차단되어 프로젝트 밖 `.cleanup-backup-260917`로 이동.
- 보존: `.env`, 업로드 데이터, DB, `vendor`, `node_modules`, `sources`, 문서, 서버 설정 및 전체 실행 코드.
- Backend 영향: 없음. 라우트·인증·CMS·DB 쿼리 변경 없음.
- Frontend 영향: 없음. 뷰·SCSS·JS·배포 산출물 내용 변경 없음.
- Data/i18n 영향: 없음. 업로드 실파일과 언어 리소스 보존.
- 검증: 잔존 `.DS_Store`·`Thumbs.db`·임시·로그·백업 파일 0건, junk 유사 디렉터리 0건.
- 빌드: `npm run build` 완료, 오류 0·경고 0. 실제 운영 전송은 서버 접속 정보와 백업 절차 확정 후 별도 수행.

## 2026-09-17 — 운영 서버 업로드 대상 정리

- 목적: 개발 원본과 검사 자료를 제외하고 웹 실행에 필요한 파일만 전달.
- 런타임 포함: bootstrap/captcha/config/console/inc/lang/module/routes/src/uploads/vendor/views, 빌드 CSS·JS, 이미지·폰트·외부 라이브러리, 루트 진입·서버 설정 파일.
- 제외: node_modules, docs, sources, database 덤프, scripts, Docker, IDE 설정, SCSS·번들링 JS 원본, 소스맵, 로컬 검사 파일.
- 보안 보정: `config/setup.php`의 과거 운영 DB 하드코딩을 제거하고 서버 환경변수 또는 루트 `.env`에서만 운영 DB 정보를 읽도록 변경.
- 배포 환경: 사용자 확인에 따라 HTTP 운영 설정을 유지하고, 운영 루트 `.env`에서 DB 정보를 읽도록 구성. `.htaccess`에서 `.env` 직접 접근 차단.
- Backend 영향: DB 연결값 공급 방식만 변경. 쿼리·CMS·라우트·인증 로직 변경 없음.
- Frontend 영향: 없음. 최신 `resource/dist/app.js`, `style.css`를 재빌드해 포함.
- Data/i18n 영향: 스키마·데이터·언어 리소스 변경 없음. DB 덤프는 웹 루트 패키지에서 제외.
- 검증: PHP lint 정상, npm build 오류0·경고0. 필수 런타임 경로와 제외 경로를 구분해 확인.
- 결과: ZIP 생성 요청이 아니라는 사용자 정정에 따라 임시 패키지는 격리하고, FTP 업로드 경로 목록과 서버 설정 절차만 전달.

## 2026-09-17 — 운영 DB Import용 최신 덤프 생성

- 목적: 현재 로컬 CMS 상태를 운영 DB `Pit0917D`에 즉시 Import할 수 있는 SQL로 제공.
- 원본: Docker MySQL 8.0의 `nineonelabs` 현재 데이터.
- 포함: 테이블 17개, 관리자 계정 3개, 고객사례 게시물 3개 및 관련 게시판·설정 데이터.
- 형식: UTF-8 BOM 없음, utf8mb4, single-transaction, trigger/event/routine 포함, add-drop-table 적용.
- 호환: `CREATE DATABASE`와 `USE` 문을 제외해 phpMyAdmin에서 선택한 운영 DB에 바로 Import 가능.
- Backend 영향: 없음. 원본 DB 읽기 전용 덤프이며 애플리케이션 코드·스키마를 별도로 변경하지 않음.
- Frontend/Data/i18n 영향: 공개 화면 변경 없음. 현재 데이터 스냅샷만 파일로 생성.
- 검증: 별도 임시 DB로 전체 Import 성공, 테이블17·nb_admin3·nb_board3 일치 확인 후 검증 DB 제거.
- 결과 파일: `release/Pit0917D-20260917.sql`, 49,754 bytes.
- SHA-256: `1AE67C4CDEA9FA4396E8BAEE5064A32875A51505DF56AB0ED0B5796E7DB95FB1`.

## 2026-09-17 — 메인 히어로 모바일 비주얼 position 유지

- 목적: 900px 이하에서 기능 목업이 2열 grid로 분리되던 동작을 제거하고 데스크톱과 같은 중첩형 비주얼 유지.
- 변경 파일: `resource/scss/components/_hero.scss` 및 재빌드된 `resource/dist/style.css`.
- 구현: 중앙 브라우저 목업을 기준으로 mail/report를 좌측, consortium match를 우측 absolute position으로 배치.
- 반응형: 900px 이하와 560px 이하 좌표·크기를 분리하고 모바일에서는 비주얼 폭을 확장해 양옆 카드가 일부 노출되도록 구성.
- overflow: 기존 hero `overflow:hidden`을 유지해 페이지 가로 스크롤 없이 가장자리 목업만 잘리도록 처리.
- 보존: 히어로 문구·버튼·이미지 에셋·등장 애니메이션·데스크톱 위치 변경 없음.
- 영향: Frontend hero 한 영역. Backend/Data/i18n/API/DB 영향 없음.
- 검증: npm build 오류0·경고0, `/` HTTP200, SCSS grid 잔존0건, dist 반영 확인.
- 리스크: 작은 화면에서 보조 목업은 의도적으로 가장자리가 일부 잘림.
- 후속: 없음.

## 2026-09-17 — 차트·Swiper autoplay 및 모바일 헤더 정돈

- 목적: 메인 성과 차트의 정보 전달력을 높이고 고객 후기·회사 인증서가 자동 순환되도록 하며, 모바일 헤더와 drawer의 위계를 정돈.
- 변경 파일: `resource/js/animation/ImpactChartController.js` 신규, `resource/js/app.js`, `resource/js/ui/SwiperController.js`, `resource/scss/components/_partnerit-impact.scss`, `resource/scss/components/_header.scss`, `resource/scss/layouts/_drawer.scss`, `views/components/header.php`.
- 차트: 성과 섹션 최초 진입 시 막대 scaleX, 도넛 0→83.3%, 범례 순차 표시. GSAP/ScrollTrigger 미지원 또는 reduced-motion에서는 완성 상태를 그대로 표시.
- Swiper: 고객 후기 5.2초, 회사 인증서 3.6초 autoplay. loop, 마우스 hover 정지, 키보드 focus 정지/이탈 시 재개, 조작 후 계속 재생 적용.
- 헤더: CTA와 햄버거를 `partnerit-header__actions` 안에 묶어 우측 정렬하고 모바일 gap을 6px로 제한. 로고와 액션 그룹 사이만 가변 여백 사용.
- drawer: 모바일 submenu 좌측 선·점·active scale 제거. 현재 항목은 Primary 9% 혼합 배경과 Primary 텍스트, hover는 7% 배경으로 표현.
- 영향: Frontend 인터랙션과 모바일 navigation만 변경. 문구·링크·이미지·Backend·DB·API·i18n 변경 없음.
- 검증: `npm run build` 오류0·경고0, header/drawer PHP lint 정상, `/` 및 `/about/company` HTTP200. Chrome CDP 390×844 실측에서 가로 넘침0, actions x239.8~366px, CTA/햄버거 gap6px. 후기 realIndex 0→1/5.6초, 인증서 0→1/4초 및 autoplay running/loop true. drawer submenu padding-left0·pseudo none·active Primary 혼합 배경 확인. 성과 차트는 진입 전 bar scale0/도넛0에서 진입 후 transform 정리/도넛83.3으로 완료. reduced-motion에서 인증서 autoplay disabled·speed0 확인.

## 2026-09-17 — 대표 고객사례 다중 Swiper 및 pagination 통일

- 원인: `/partners` 대표 영역이 `partnerit_featured_customer_story()`의 첫 1건만 렌더링해 CMS에서 여러 게시물을 대표로 지정해도 누락됨. 후기와 인증서 Swiper의 동일한 모양도 각각 별도 SCSS로 중복 관리됨.
- 실서버 비교: `http://partnerit.nineonelabs.co.kr/partners`도 대표 단일 article 구조이며 별도 고도화 버전이 아님을 HTML과 배포 app/style 자원으로 확인.
- 데이터: `partnerit_featured_customer_stories()` 추가. `extra2=Y` 전체를 반환하고, 지정값이 없을 때만 첫 게시물 1건을 fallback. 기존 단건 함수는 첫 대표 반환 호환 레이어로 유지.
- 대표 Swiper: `partnerit-stories-featured`에 항상 swiper/wrapper/slide 구조 적용. 대표 2건 이상일 때 prev/pagination/next와 6초 autoplay·loop·keyboard·hover/focus pause를 활성화하고 1건에서는 조작부를 렌더링하지 않음.
- 갤러리: 대표 ID 전체를 일반 목록에서 제외해 중복 카드 방지.
- 공통 UI: `_partnerit-swiper-controls.scss` 신규. 후기·인증서·대표에 동일한 48px 원형 버튼, 16px gap, 6px 기본 점, 24px active 점, Primary 컬러와 focus 상태 적용. 숫자형 갤러리 pagination은 서버 페이지 이동이므로 별도 유지.
- 변경 파일: stories data/featured/gallery, reviews/certificates markup, `SwiperController.js`, stories/reviews/certificates SCSS, 공용 control SCSS와 index.
- 영향: Frontend 및 고객사례 조회 정규화만 변경. DB 스키마·관리자 저장 계약·라우트·상세 페이지·i18n 변경 없음.
- 검증: build 오류0·경고0, 변경 PHP 5개 lint 정상, `/partners` HTTP200. 더미 배열 대표2건 반환 확인. Chrome 실측 후기/인증서 control gap16·button48×48·active24×6·Primary rgb(60,130,246) 동일. 현재 DB 대표1건은 Swiper wrapper1·controls 없음·가로 넘침0.

## 2026-09-17 — 고객사례 증분 데이터 및 CRUD 검증

- 목적: 다중 대표 Swiper 운영 검증을 위해 고객사례 대표2건·일반2건 추가, 전체 덤프 대신 운영 반영용 증분 SQL 제공.
- 데이터: 브릿지플랜/커넥트웍스 대표 사례, 데이터포지/넥스트웨이브 일반 사례. 기존 업로드 썸네일 파일을 재사용해 신규 파일 업로드 없음.
- SQL: `release/Pit0917D-customer-stories-update-20260917.sql`. `sitekey+board_no+title` 조건으로 중복 삽입 방지, 최초 실행에만 기존 sort_no +4, ko locale 동시 삽입.
- CRUD: 임시 게시물 INSERT→SELECT 1→UPDATE 제목/본문/대표Y→SELECT 1→locale/board DELETE→SELECT 0 순서로 검증.
- 결과: 로컬 고객사례7건·대표3건·ko locale7건. SQL 2회 실행 후에도 7/3/7 유지.
- Frontend: `/partners` 대표 원본3건(Loop clone 포함5), 일반 카드4건, controls 노출, 상세 `/partners/11`~`/14` HTTP200 및 제목/본문 확인.
- Browser: Chrome 390px에서 대표 autoplay running/loop true, realIndex0→1/6.3초, button48px/active24px, 가로 넘침0.
- Import 검증: 기존 `Pit0917D-20260917.sql`을 임시 DB에 복원한 뒤 증분 SQL 적용 결과 7건·대표3건·locale7건 확인 후 임시 DB 제거.
- 영향: 고객사례 데이터만 추가. 스키마·카테고리·관리자 계정·기존 업로드 파일·다른 게시판 변경 없음.

## 2026-09-21 — Services Details 피그마 에셋 재추출 및 카드별 렌더링

- 작업명: 메인 `Services Details` 이미지·배경 고도화.
- 작업 목적: 단일 1400×275 스트립을 400% 확대한 뒤 위치 이동으로 잘라 쓰던 구조를 제거하고, 현재 피그마 `457:1082`의 카드 그래픽과 주변 `obj_1`·`obj_2` 인상을 반영.
- 변경 파일: `views/components/partnerit-service-details.php`, `resource/scss/components/_partnerit-service-details.scss`, `resource/images/main/service-details/service-*-v2.png`, 빌드 산출물, 컴포넌트 카탈로그.
- 연관 파일: 메인 페이지의 기존 컴포넌트 호출 구조와 `resource/scss/components/index.scss`는 유지.
- 주요 변경 내용: 카드 그래픽 4종을 Figma에서 2× PNG로 개별 추출하고 실제 픽셀 크기를 마크업에 기록. `object-fit: contain`으로 카드별 비율을 보존하고 배경을 blue/mint/lilac 저채도 그라디언트로 재구성. `obj_1`은 라벤더 글래스 스파클, `obj_2`는 아이스블루 3D AI 형태의 알파 투명 PNG로 새로 생성해 카드 외곽 좌우에 배치. 오브젝트는 카드 경계 밖으로 완전히 이동하고 공간이 부족한 1100px 이하에서는 숨겨 콘텐츠 중첩을 방지.
- 영향 범위: 공개 메인 페이지의 서비스 상세 섹션만 변경. Backend·DB·API·i18n 영향 없음.
- 리스크: 생성형 장식은 피그마 원본과 픽셀 단위 동일 에셋이 아니지만, 원본의 색감·질감·역할을 유지하며 흰 사각 배경 문제를 제거.
- 테스트 결과: `npm run build` 오류 0·경고 0, `/` HTTP 200, 데스크톱 렌더에서 4개 개별 이미지와 대체텍스트 확인.
- 데이터 안정성: DB·업로드 데이터·CMS 조회 로직 변경 없음.
- 후속 작업: 실제 기기 모바일 QA에서 카드 2열 가독성만 최종 확인.
- 2026-10-07 — 고객 피드백 3건: 히어로 고해상도 원본/실텍스트 전환, 파트너 로고 원본 재추출 및 그리드 잘림 수정, 소개서 공통 모달/AJAX/DB/관리자 접수 연결. 기존 문의 데이터 보존, 접수 실패 다운로드 금지, 모바일·Chromium·WebKit·실제 POST/관리자 조회/권한 검증 및 빌드 완료. 상세: `261007-customer-feedback-qa.md`.
- 2026-10-07 — 신규 spacing 5개 fluid 토큰화, RESET/INIT 목록 초기화 단일화, 중복 backdrop/hidden 정리. 기존 적용값·40px 닫기 버튼 유지. 7페이지×4폭=28건 Chromium QA 및 읽기 전용 재캡처, 빌드 오류0/경고0. 상세: `261007-fluid-reset-qa.md`.
- 2026-10-07 — 최종 검수: 변경 PHP28개 lint 실패0, 최종 build 오류0/경고0, diff check 정상. DevCrop/parterit 공개 원격과 동기화 상태 확인 후 codex/final-qa-20261007 게시. 비밀정보·QA·덤프·generated dist 제외, 실서버 배포/운영 DB 변경 없음. 실행 근거: `261007-fluid-reset-qa.md`.

## 2026-10-08 — 메인 히어로 1400px fluid 폭

### 요구사항과 영향 분석
- 원하는 것: 히어로 비주얼 최대 1400px, 좁은 화면에서 유동 축소.
- 현재 문제: visual 1280px 및 부모 공통 컨테이너 폭 제한.
- 기대 동작: 기존 좌우 여백을 유지하며 부모와 visual 모두 1400px까지 확장.
- 유지 범위: 원본 PNG 4개, 비율·position·모바일 배치·타이포·인터랙션.
- 제약: 공통 컨테이너와 다른 섹션은 변경하지 않음.
- 배포: 변경된 빌드 CSS만 제공하며 서버 업로드는 수행하지 않음.
- Backend/Data/i18n: PHP·DB·문구 변경 없음. Frontend: `_hero.scss`만 수정. Test: viewport 실측 및 build. Docs: 본 이력과 components 카탈로그.

### 변경 계획
1. 수정 파일: `resource/scss/components/_hero.scss`.
2. 연관 파일: `base/_root.scss`, `views/components/hero.php`, `partnerit-hero-preview.php` 읽기 확인.
3. 호출 흐름: 메인 hero → inner 공통 컨테이너 → PNG visual; hero 범위의 폭만 override.
4. DB 영향: 없음.
5. 기존 기능 영향: 이미지 소스·링크·애니메이션 유지.
6. 리스크: 부모 max-width에 막히거나 모바일 overflow 발생 가능, 실제 DOM 측정으로 검증.
7. 테스트 계획: 1920/1440/1280/768/390/320px, 이미지 로딩·경계·문서 가로 넘침 검사, production build와 diff check.
8. 문서: 카탈로그 및 본 append-only 이력에 검증 결과 기록.

### 규칙 적용
- #1/#10: 기존 hero 컴포넌트와 동일 BEM SCSS 재사용.
- #11/#17: 기존 no-container-xl 유지, 사용자 지정 1400px 예외는 hero 내부만 적용; 기존 spacing과 타이포 유지.
- #18: 계산값이 아니라 브라우저 width·root font-size 실측으로 검증.
- 디자인 단계: 기존 브랜드 변수 유지 → 폭만 조정 → hero SCSS 재사용 → build CSS 적용.

### 검증 및 작업 이력
- 작업명/목적: 메인 히어로 최대 1400px fluid 요청 반영.
- 변경/연관 파일: 위 계획과 동일, PHP·이미지·JS 변경 없음.
- 주요 내용: inner `min(1400px, calc(100% - 4.8rem))` 및 max-width 1400px, visual 상한 1400px.
- 영향 범위: 메인 hero, 공통 컨테이너 파일은 수정하지 않음.
- 기능 검증: 원본 PNG4개 로드 및 visual 경계 내 배치 6개 viewport 모두 통과.
- 코드 정합성: `npm run build` 오류0·경고0, `git.exe diff --check` 정상.
- UI/테스트: `node hero-fluid-qa.cjs` 성공. root10px, viewport1920/1440/1280/768/390/320에 visual1400/1392/1232/720/342/272px, 가로 overflow0. 1920·390 캡처 시각 확인.
- 데이터 안정성: DB/API 요청 로직 수정 없음, QA는 GET만 수행.
- 리스크/환경: 최초 QA는 localhost 서버 중지로 실패; 해당 프로젝트 기존 DB/web 컨테이너 시작 후 통과. 실서버 검증 및 배포는 하지 않음.
- 후속: `resource/dist/style.css`만 서버 동일 경로 덮어쓰기; 사용자 요청 범위상 Git push 없음.

## 2026-10-08 — 파트너 로고 마퀴 복원

- 요구: 고객 요청에 따라 정적 grid에서 양방향 자동 흐름 복원.
- 현재 문제: 최신 로고 교체 시 marquee wrapper가 제거되어 기존 controller가 작동하지 않음.
- 기대 동작: 14개 최신 로고를 7개씩 두 줄로 반복, 각 로고의 contain/캔버스 보정 유지.
- 유지 범위: 로고 파일·이름·제목·타이포·hero1400px 변경 보존.
- 제약: 이동하며 화면 경계로 출입하는 동작과 로고 이미지 자체 잘림은 구분, 과거 음수 inset 크롭은 복원하지 않음.
- 배포: 변경 PHP와 빌드 CSS만 제공 예정. JS는 기존 MarqueeController 재사용.
- 영향 점검: Backend는 component 마크업만, Frontend는 partners SCSS, Data/i18n 불변, Test는 브라우저 이동 및 반응형 확인, Docs는 카탈로그/이력.
- 계획1 수정: `views/components/partnerit-partners.php`, `_partnerit-partners.scss`.
- 계획2 연관: `MarqueeController.js`, `NarrativeAos.js`, app 초기화 확인.
- 계획3 호출: component rows → 기존 controller resize fill/aria-hidden duplicate → CSS seamless translateX(-50%).
- 계획4 DB: 변경 없음.
- 계획5 기존 기능: 최신 이미지 보정 재사용, 다른 marquee 불변.
- 계획6 리스크: 반복 빈틈·전체 문서 overflow·감소 모션에서 누락. CSS fallback 및 브라우저 확인.
- 계획7 검증: PHP lint/build/diff, PC·모바일 자동 이동/복제 그룹 크기·이미지 로드·overflow 확인.
- 계획8 문서: 본 이력과 components 카탈로그 갱신, runtime-only ZIP 제공.
- 규칙: #1/#10 기존 컴포넌트·controller 재사용, #5 인라인 style 없음, #17 기존 색/spacing 변수 유지, #19 PHP component 내 렌더링 유지.
- 디자인 단계: 기존 브랜드 토큰 → 2줄 자동 흐름 → 기존 BEM/JS 재사용 → component와 SCSS 적용.
- 완료/기능: 14개 최신 로고, 두 줄 반대 방향 42초(모바일32초) 무한 흐름. 로고 contain/개별 보정 보존, 예전 음수 inset 없음.
- 검증/코드: `npm run build` 오류0·경고0, Docker PHP lint 성공, `git.exe diff --check` 공백 오류 없음.
- 검증/UI: 내장 브라우저1280/1440/1920/390px에서 document 가로 overflow 없음. 1280px track X -195→-394 / 역방향 -1280→-1081로 실제 이동 확인. 390px 2개 그룹 각1148px, 1440px 각1605.625px, 1920px 각3220px로 viewport보다 넓고 양쪽 반복 크기 동일. 이미지 로드 모두 성공, PC·모바일 시각 확인.
- 접근성: 각 행 원본7개, 복제 aria-hidden=true 확인. reduced-motion은 CSS 정적 grid/no animation 적용(시스템 설정 실측은 미수행).
- 데이터/리스크: DB·접수·개인정보·이미지 파일 변경 없음. 마퀴가 화면 경계로 출입할 때의 일부 카드 노출은 정상 이동 동작이며 이미지 내부 잘림과 별개. 운영 서버 직접 배포 미수행.
- 패키지/후속: PHP component 및 style.css 두 파일만 제공. 기존 배포 app.js의 MarqueeController 재사용. 오늘 hero1400px CSS도 포함, Git push 없음.
- Git 게시 후속 요청: 사용자가 GitHub 업로드를 승인함. 원격 `DevCrop/parterit`, 기존 `codex/final-qa-20261007` 브랜치에 hero/partners 소스3개와 문서2개만 게시 대상으로 확정. 직전 원격 HEAD `1539f46` 일치 확인, 기존 빌드·UI·PHP lint 근거 재사용. generated dist/ZIP/환경정보 제외, Docker 중지 상태 유지, main 머지·운영 서버 배포는 범위 밖.

## 2026-10-15 — PHP 8.4 호환성 마이그레이션 계획

- 원하는 결과: PHP 8.4에서 기존 사이트와 CMS 기능 유지, 확인된 호환성 오류·경고 정리 및 QA.
- 현재 문제: PHP 7.4 Docker, Intervention v2 nullable 경고, 구형 에디터 문법, 누락 include와 관리자 depth 빈 값, GD WebP 미지원.
- 기대 동작: 기존 HTTP/API 계약·디자인·언어·DB 스키마 그대로 유지하며 업로드와 소개서 흐름 정상화.
- 수정하지 않는 범위: 운영 서버·DB·고객 콘텐츠·기존 resource/dist 작업. 외부 SMTP 발송과 Git push 제외.
- 제약: 운영 PHP 전환 전 소스/vendor/업로드/DB 백업 필요. 로컬 검증은 격리된 복제 DB만 사용.
- 영향 분석: Backend `ImageHelper.php`, `func.php`, `base.class.php`, `site.info.php`, QA helper; Frontend `board.view.php` hidden 값만; Data 기존 schema 유지; i18n ko labels 유지; Test 기존 격리 HTTP/lint harness 확장; Docs 서버 명세 및 본 이력.

### 변경 계획
1. 수정 파일: dockerfile, composer.json/lock, ImageHelper, 공통 include/메타/삭제 helper, 관리자 depth 값, nullable 선언, legacy editor 문법·접근 차단.
2. 연관 파일: routes/web.php, BrochureRequestService, LoginProcessor, board.process.php, upload.php, backfill-webp.php, Apache 설정과 .gitignore.
3. 호출 흐름: 공개/console bootstrap → Composer autoload → 이미지 래퍼; multipart → imageUpload → WebP 생성; imageDelete → 파생본 삭제.
4. DB 영향: 신규 스키마 변경 없음. 기존 소개서 마이그레이션은 QA 복제본에만 적용.
5. 기존 기능 영향: 이미지 원본·비율·작은 이미지 크기 유지, WebP 지원 활성화. 현재 Summernote 보존. 구형 editor는 미사용 참조 확인 후 차단.
6. 리스크: 라이브러리 API와 WebP 결과·삭제 연동; FTP 배포 시 vendor 누락; 서버 세션/업로드 권한. 이전 vendor 복구본과 배포 안내 제공.
7. 테스트: PHP 전체 lint, Composer platform/audit, 이미지 단위 검사, 양 버전 공개/관리자/CRUD/업로드/소개서 성공·실패·권한 HTTP 회귀, 브라우저 UI 확인.
8. 문서: server-spec·architecture 관련 환경/래퍼 설명, 본 append-only 최종 검증 결과.

- 정책 제약: legacy 폴더 삭제 실행이 차단됨. 파일은 보존하고 하위 .htaccess로 웹 접근 차단, PHP 문법만 호환 수정. 사용자 기능 제거/DB 삭제 없음.

## 2026-10-15 — PHP 8.4 구현 및 서버 QA (화면 QA 미완료)

1. 날짜: 2026-10-15.
2. 작업명: PHP 8.4 마이그레이션 및 8.3 회귀 확인.
3. 목적: 확인된 nullable/구형 문법/include/NULL 경고와 이미지 라이브러리를 정리하고 기존 기능을 유지한다.
4. 변경 파일: composer.json/lock, dockerfile, .dockerignore, .htaccess, inc/lib/{base.class.php,func.php,site.info.php,helper/ImageHelper.php}, console/lib/QA/CrudQaInspector.php, console/pages/board/board.view.php, scripts/check-php-compat.php. Git ignored resource/vendor의 HTMLPurifier.standalone.php와 Common.php 문법도 보정하여 후보 패키지에 포함.
5. 연관 파일: config/setup.php, routes/web.php, src/Request/BrochureRequestService.php, console/lib/Auth/LoginProcessor.php, console/pages/board/ajax/{board.process.php,upload.php}, scripts/backfill-webp.php, apache.conf, .user.ini.
6. 주요 변경: Docker 기본 8.4 및 GD WebP/mbstring/exif 활성화, Intervention Image 3.11.10 + GIF 4.2.4, 기존 래퍼/원본/작은 이미지 크기 보존, 원본 삭제 시 WebP 동반 정리. 구형 에디터는 보존하되 비활성. 최종 차단 위치는 하위 .htaccess가 아닌 Git 추적 대상인 **루트 .htaccess**로 확정; 임시 하위 파일은 제거.
7. 영향 범위: Backend 호환성 및 이미지 처리; Frontend hidden 값 보정만; Data 운영 DB/스키마 불변; i18n 고객 문구 불변; Test CLI 추가/격리 HTTP 검증; Docs server-spec·architecture·상세 QA 기록. 기존 resource/dist 변경은 보존하고 이번 패키지에서 제외.
8. 리스크: 브라우저 초기화 오류(os error 3)로 UI/모바일 시각 검증 미완료. 운영 SAPI·확장·SMTP·Imagick 미검증. 새 vendor는 PHP 7.4에서 실행 불가, PHP와 소스/vendor 동시 전환 필요.
9. 테스트: PHP 8.3.35·8.4.26 각각 781파일 lint 오류/경고0, 단위22·HTTP41·추가HTTP7 통과. root 차단 규칙 + 로컬 production 모드 포함 GET24 통과. Composer platform 양쪽 성공, audit advisory0, validate 유효(기존 권고2). E_ALL 로그에 테스트된 PHP 오류/경고 없음. git diff --check 성공. 후보 runtime498파일 원본 SHA-256 일치 확인.
10. 후속: Codex 재시작 후 공식 브라우저로 화면 QA를 마친 뒤 실제 호스팅 조건 확인 및 배포 여부 결정. `php84-migration-candidate-20261015`는 검증 대기 후보이며 자동 배포하지 않는다. 신규 SQL/외부 메일 발송/Git push/운영 접근 없음. 상세 실행 보고는 `docs/04-history/php84-qa-20261015.md`, 전환·롤백은 server-spec 참조.

- 후속 검증: 브라우저 상태 조회를 다시 시도했으나 페이지 접근 전 같은 kernel assets/os error 3으로 실패하여 UI 미검증 상태 유지. 배포 ZIP 내부 runtime 498개를 압축 스트림에서 직접 SHA-256 검산하여 현재 소스와 전부 일치 확인(629,194 bytes, ZIP SHA-256 `7C585B00B651ACFF0A2E6B436EA5C214654C75BFCEA180DB526EFDA774F34B5F`). 패키지 경로 이탈·staging 대비 누락 없음. 기존 서버/QA 컨테이너를 새로 시작하지 않았으며 화면 QA는 앱 환경 복구 후 재개한다.

## 2026-10-15 — PHP 8.4 최종 UI QA 완료

- 사용자 재시작 후 공식 브라우저 복구. 격리 php84/db만 시작하여 UI 검사했고 기존 260915 프로젝트는 중지 상태를 유지했다.
- 9개 공개 페이지 × 4개 폭(320/390/768/1440) 총 36측정에서 제목·이미지 로딩 정상, 가로 넘침 0. PC/모바일 캡처 직접 확인, 모바일 메뉴와 고객사례 상세 추가 확인.
- 소개서 PC 신청/재시도·완료·재진입 초기화 및 모바일 첫 제출 완료 확인. PC 첫 연결 오류는 서버 접수 후 발생했으나 같은 요청 재시도 시 중복 저장 없이 1건 유지. 후속 모바일 정상, 원인 미확정 사실을 QA 보고서에 보존.
- UI 이후 PHP 오류/경고 기록 없음, 브라우저 error/warn 없음. 기존 PHP 8.3/8.4 전체 lint·단위·HTTP 결과는 런타임 변경이 없어 재사용했다.
- 변경 범위: 최종 QA 문서와 배포 설명만 갱신. 기존 프런트 dist 및 고객 문구/DB/소스 디자인은 불변. QA 전용 누락 이미지 fixture 3개만 복사.
- 최종 제공: php84-migration-final-20261015.zip, upload 내부 498개 런타임 파일. .env/uploads/DB/dist/QA 자료 제외. PHP 8.4와 vendor 동시 전환·전체 교체·백업 및 롤백 지침 포함. 실제 호스팅/SMTP/Imagick 미검증, 운영 배포와 Git push 미수행.
- 최종 수령 검증: ZIP 630,884 bytes, SHA-256 `CAAED0F494AD7A7125238EB7278750CCDB897A259AC3AECFB8C672488A4DCFC3`. 압축 내부 runtime 498파일을 현재 repo와 SHA-256 대조하여 불일치 0. `git diff --check` 공백 오류 없음(기존 CRLF 정규화 안내는 별도). 직접 만든 QA 브라우저 탭을 닫고 viewport를 초기화했으며, QA Compose 전체 중지 및 실행 컨테이너 0 확인. DB volume과 증거는 복구 가능하게 보존.

## 2026-10-15 — PHP 8.4 최종 GitHub 게시

### 요구·범위·계획

- 원하는 결과: 사용자가 승인한 최종 PHP 8.4 작업을 기존 GitHub 브랜치에 게시한다.
- 현재 상태: 로컬 구현/QA 완료, 원격에는 아직 미반영.
- 기대 동작: 검증된 소스·lock·CLI 검사·문서만 하나의 커밋으로 게시.
- 보존 범위: 기존 resource/dist 수정/삭제 6건, 원본/업로드/운영 DB는 건드리지 않는다.
- 권한 제약: 사용자 승인 대상은 Git push. main 병합·실서버 배포·외부 메일 발송은 제외.
- 저장 제약: .env/자격 증명/QA fixture/ZIP/root vendor/resource vendor는 커밋하지 않는다. vendor 보정본은 앞서 제공한 최종 서버 ZIP으로 전달한다.
- 영향 분석: Backend는 기존 PHP 호환 diff만 게시; Frontend는 board.view hidden 값 외 신규 변경 없음; Data/i18n 신규 변경 없음; Test는 기존 8.3/8.4 및 36 UI 측정 근거 재사용; Docs는 server-spec/architecture/QA 기록/본 이력을 게시.
- 계획1 수정: 이력만 추가하고 이미 검증된 16개 명시 경로를 stage한다.
- 계획2 연관: .gitignore, composer.json/lock, 원격 origin 및 현재 branch 확인.
- 계획3 흐름: fetch → 원격 차이 확인 → 명시 stage → staged 검사 → commit → 일반 push → 원격 SHA 대조.
- 계획4 DB: 변경/접속 없음.
- 계획5 기능: 추가 런타임 변경 없음, 기존 검증 결과 유지.
- 계획6 리스크: 프런트 산출물/민감정보 혼입과 vendor 누락. 명시 파일 목록 및 lock/별도 ZIP 안내로 분리.
- 계획7 검증: staged diff/check·민감 경로 제외·원격 HEAD 일치·비대상 로컬 변경 보존. 런타임이 같으므로 Docker/전체 QA 재시작 없음.
- 계획8 문서: 본 append-only Git 게시 이력과 최종 응답의 실제 commit/branch 결과.

### 실행 기록

1. 날짜: 2026-10-15.
2. 작업명: PHP 8.4 검증본 GitHub 게시.
3. 목적: 사용자 최종 업로드 요청 이행.
4. 파일: .dockerignore, .htaccess, composer.json/lock, dockerfile, 공통 PHP 4파일, console 2파일, CLI 검사 1파일, 문서 4파일.
5. 연관: .gitignore 및 최종 서버 ZIP(별도 전달), 기존 프런트 dist 보존.
6. 내용: 검증본만 명시 stage 및 일반 push; force push 없음.
7. 영향: Git 원격 소스/문서 갱신만. 배포/DB/고객 문구는 불변.
8. 리스크: Git에는 ignored vendor가 없으므로 실제 서버는 lock 기반 install 또는 최종 ZIP 필요.
9. 사전 검증: fetch 후 HEAD와 origin/codex/final-qa-20261007 차이 0/0, 시작 SHA 019f159b22f35fd63bfe4d7c407a17fe63b3d5a9. 기존 인덱스 비어 있음. 런타임 검사 근거는 바로 위 최종 QA 기록.
10. 후속 확인: push 성공과 원격 SHA 일치 여부를 최종 응답에 보고한다. 실제 호스팅 검증/배포는 별도 요청이 필요하다.
