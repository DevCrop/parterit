# 2026-10-07 공통 간격·초기화 정리

## 요구사항 재정의
- 원하는 기능: 고객 피드백 디자인 적용 확인, 리소스 재빌드, fluid 크기 및 INIT 중복 정리.
- 현재 문제: 신규 공통 spacing 토큰은 고정 rem, RESET과 INIT에 목록 초기화 중복, backdrop 토큰이 뒤에서 덮어써짐.
- 기대 동작: 기존 desktop 디자인을 유지하며 모바일 간격을 연속 보간하고 실제 적용 CSS를 갱신.
- 수정 제외: 고객 문구·섹션 순서·목업 배치·DB·AJAX·권한·기존 사용자 변경.
- 제약: DB/API/다국어 변경 없음. 기존 공통 typography/container/section fluid 재사용. 실행 CSS만 배포 묶음에 갱신.
- 가정: 리소스 업데이트는 기존 고객 피드백 변경분과 이번 CSS의 배포 파일 갱신이며 실서버 직접 업로드는 아님.

## 영향 분석
- Backend: 이번 CSS 정리 해당 없음.
- Frontend: base/index.scss의 reset/root/init 순서, _reset.scss, _root.scss, _init.scss, _partnerit-brochure.scss, _partnerit-partners.scss; main layout의 dist CSS 로딩.
- Data: 해당 없음. 테스트도 POST하지 않아 문의·고객사례 데이터를 생성하지 않음.
- i18n: 해당 없음. 문구·키 변경 없음.
- Test: 기존 qa-261007.cjs의 QA_READ_ONLY 모드와 추가 공통 레이아웃 검사.
- Docs: 본 기록, patch-notes.md, requirement-changes.md, 배포 목록/QA 문서.

## 변경 계획
1. 수정 파일: 위 5개 SCSS, 생성 dist CSS/map 및 관련 기록.
2. 연관 파일: abstract/_functions.scss, base/index.scss, components의 spacing 소비 지점, views/layouts/main.php, build.js.
3. 호출 흐름: SCSS → esbuild Sass → resource/dist/style.css → 공통 layout → 공개 페이지/소개서 dialog. AJAX 경로 변경 없음.
4. DB 영향: 컬럼/FK/인덱스/데이터 변경 없음. SQL 재적용 불필요.
5. 기존 기능: 전역 목록 리셋은 ol/ul/li를 RESET 한 곳에서 유지; 모달 닫기 크기는 최소 40px 보존; hidden 전역 선언 유지.
6. 리스크: 신규 간격 감소로 모바일/중간 폭 배치 변화. 최소 간격과 모달 버튼 크기를 유지하고 브라우저로 확인.
7. 테스트: build 및 diff check; 320/390/768/1440px 공개 페이지 overflow/JS 오류/이미지 로딩/목록 리셋; dialog 열기/닫기/hidden/실측. API·권한은 수정하지 않아 이전 실행 증거 재사용.
8. 문서: 결과를 본 기록에 추가하고 배포 CSS와 목록을 갱신. 결과·한계는 실제 실행값으로 보고.

## 구현·검증 결과
- 고객 피드백 디자인의 현재 구현: 히어로는 Figma 원본 노트북 + HTML 실텍스트 카드/대시보드, 로고는 원본 14개 7/4/2열 grid, 소개서 다운로드는 기존 브랜드 컬러/타이포를 재사용한 native dialog와 AJAX 성공 후 다운로드. 이번 턴에서 재캡처하고 히어로1440/로고1440/모달390 이미지를 직접 확인.
- 변경 파일: base/_root.scss: 공통 spacing 5개를 fluid(min,max)로 전환; base/_reset.scss: ol/ul/li 초기화 단일화; base/_init.scss: 중복 list-style 제거; _partnerit-brochure.scss: 중복 hidden/mobile padding 제거, 닫기 버튼 max(4rem,spacing) 유지; _partnerit-partners.scss: 중복 list-style 제거.
- root/_root.scss의 backdrop 선언은 기존 최종 적용값 10을 유지해 단일화. *의 margin/padding은 form control까지 적용하므로 보존. 다른 컴포넌트의 명시적 목록 선언은 이번에 일괄 제거하지 않음.
- units: 브라우저 실측 root 10px. fluid(24,32)는 clamp(2.4rem, vw + rem, 3.2rem), 모달 width는 min(68rem,calc(...)), grid는 minmax(0,1fr), 닫기 버튼은 max(4rem,...). 1px border/미디어 쿼리 px/목업 cqw는 역할에 맞게 유지.
- `npm run build` → 종료0, 오류0/경고0, 생성물4개.
- `node qa-fluid-261007.cjs` → 7개 공개 route × 320/390/768/1440px = 28건 HTTP200; 가로 overflow0, 목록 reset 불일치0, 로딩 완료 이미지 실패0, pageerror0. dialog 열기/Escape 닫기/hidden 성공영역/내부 overflow 정상, 닫기40px 유지.
- modal padding 실측: 320=24px, 390=24.0777px, 768=26.035px, 1440=29.5146px. 1920px에서 토큰 최대32px. 기존 desktop 최대값은 유지.
- `QA_READ_ONLY=1 node qa-261007.cjs` → 1440/390/320 재캡처, 각14개 로고 loaded+contain, dialog 열기/포커스/닫기 정상, overflow false, JS 오류0. 이번 실행은 POST 없음.
- `git diff --check` → 종료0. 수정된 소스와 생성 스타일의 리셋/spacing 선언 확인. backend/API/권한/DB는 이번 턴 변경 없음; 지난 고객피드백 검증과 구분.
- 배포: 기존 release/20261007-customer-feedback/upload/의 style.css와 app.js를 최종 빌드본으로 갱신. 총34개 파일 SHA-256 대조. 이번 정리만 추가 반영 시 resource/dist/style.css만 덮어쓰면 됨. SQL 재실행 불필요.

## 적용한 규칙·파일 확인
- change-process §1~7: 변경 전 요구/6분류 영향/8항목 계획 기록, 구현 후 테스트·문서·히스토리 기록.
- UI 공통 토큰 재사용: 개별 페이지 임의 clamp 대신 _root.scss의 spacing 보간, 기존 font/container/section 생성 유지.
- 실제 확인: base/index.scss:1(import 순서), base/_reset.scss:120(목록), base/_init.scss:11(root), base/_root.scss:6(tokens), abstract/_functions.scss:5(fluid 계산), _partnerit-brochure.scss:2(dialog 폭), _partnerit-partners.scss:6(grid), build.js:3(entry), views/layouts/main.php(dist CSS 연결).

## 남은 리스크·후속 작업
- 로컬 Chromium 검증이며 이번 추가 변경을 실서버에 업로드하지 않음. 실제 기기·운영 캐시 환경은 배포 후 확인 필요.
- 기존 개인정보 수집 관련 HTTPS/동의문/보유기간 확정 필요사항은 그대로 유지. 이번 SCSS 정리가 해당 운영 문제를 해결하지 않음.
- px/rem을 전부 기계 변환하거나 기존 모든 컴포넌트 reset을 삭제하지 않음. 일부 고정 크기는 hairline·터치 크기·이미지 비율 유지 목적.

## 최종 검수·GitHub 게시 계획
- 원하는 것: 마지막 필요한 보완 후 GitHub 게시. 현재 main과 origin/main 차이0이며 공개 DevCrop/parterit 원격 확인.
- 문제/기대: 작업 변경분 미커밋 상태; 기능별 커밋·검수 브랜치/PR로 보존하고 기존 main을 강제 변경하지 않음.
- 제외: .env/DB dump/QA 데이터/개인자료/vendor/node_modules/dist 빌드 생성물. 기존 사용자 변경 삭제·reset 금지.
- 제약: UI/API 기존 동작 유지, 운영 DB/실서버 변경 없음, 개인정보 수집 운영 조건은 기존 기록 유지.
- 영향6분류: Backend=BrochureRequestService/routes/request admin; Frontend=변경 views/JS/SCSS; Data=추가 migration만 확인하고 새 실행 없음; i18n=고객의 공개 legal 문구 변경 보존; Test=마지막 build/lint/read-only 28건/비밀정보 검사; Docs=본 기록/기존 patch history.
- 계획8항목: (1) 최종 필요한 작은 결함과 기록만 수정 (2) JS·뷰·빌드 의존 동시 확인 (3) 기존 공개 route→공통 layout 및 brochure API 유지 (4) DB 변경 없음 (5) 기능별 명시 경로 staging (6) 공개 repo 비밀정보와 generated 제외 (7) PHP lint/build/diff/읽기전용 QA (8) 검수결과·게시 경로 기록.

### 최종 실행 결과
- 사용자 최종 게시 승인 후 origin fetch → HEAD/origin/main 차이0. codex/final-qa-20261007 브랜치 사용, main 직접 수정/강제 push 없음.
- 변경 PHP 28개 `docker compose exec -T web php -l <파일>` → 실패0. `npm run build` → 0 errors / 0 warnings / 4 outputs. `git diff --check` → 종료0(기존 CRLF→LF 안내만 있음).
- 현재 변경 텍스트 파일에서 알려진 테스트 비밀번호·GitHub token·개인키 헤더 패턴 검사 → 0건. .env/계정/덤프는 제외. 이미 공개된 과거 Git 히스토리에 대한 보안 감사는 이번 검사 범위가 아님.
- 앞선 28건 레이아웃 QA와 실 POST/중복/관리자/권한 QA 증거를 재사용. 최종 inspection에서 별도 기능 보완 필요 결함은 발견하지 못해 추가 디자인·데이터 변경을 하지 않음.
- GitHub 게시 대상은 기존 공개 페이지 변경분+고객 피드백 기능+공통 reset/fluid 개선+문서/에셋/추가 migration. 프로젝트 conventions의 generated 제외 규칙에 따라 dist CSS/JS/maps/assets 삭제는 커밋하지 않고 로컬 최종 배포 빌드본으로 보존.
- 서버 업로드용 upload/은 별도 최신 빌드본을 유지. 서버 업로드/운영 DB 변경/PR merge는 수행하지 않음. GitHub 브랜치 사용 시 기존 추적 dist보다 소스가 최신이므로 `npm ci && npm run build` 후 배포해야 함.
