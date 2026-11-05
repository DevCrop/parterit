# 2026-11-05 최종 CMS·도입 문의 검수

## ① 요구사항
- 원하는 것: 도입 문의 모달, 관리자 팝업·배너, 계정 및 사이트 정보의 실제 동작 확인과 누적 수정 업로드본 제공.
- 현재 문제: 푸터 회사 정보가 하드코딩되어 관리자 변경과 연결되지 않음. 사이트 정보 저장 시 제출하지 않은 항목도 빈 값으로 갱신됨.
- 기대 동작: 제출한 설정만 저장하고 현재 설정을 푸터에 안전하게 표시. 이미지 교체 실패 시 원본 보존.
- 제외: 실서버 계정·비밀번호 변경, 고객 데이터 삭제, 임의 실서버 배포, 새 기능·디자인 체계 도입.
- 제약: PHP 8.4.27 FPM 실제 서버에 mbstring/GD/DOM/ZIP 없음. 기존 DB 구조와 API 응답 형식 유지.
- UI 검수: 사용자 허용 Chrome 사용. 도구 초기화 실패로 승인된 desktop fallback 사용 중. 화면 검증과 코드·HTTP 검증을 구분.

## ② 영향 분석
- Backend: Popup/Banner Controller → Model → PDO, setting.process.php → nb_siteinfo, 기존 AccountController/Validator.
- Frontend: footer.php, setting/index.php, account/edit.php 및 기존 BrochureFormHandler·모달 컴포넌트. 새 SCSS/JS 체계 없음.
- Data: 스키마 변경 없음. 기존 tmaster 해시 유지. 설정 부분 갱신과 기존 이미지 보존이 회귀 검증 대상.
- i18n: 한국어만. 비밀번호 안내·사업자등록번호 라벨 정합성.
- Test: 격리 DB/FPM의 HTTP CRUD 및 실패 케이스, 실제 공개 경로·캡차 점검, Chrome 모달 확인.
- Docs: 이 문서에 결과, patch-notes·server-spec·handoff 관련 사항 추가.

## ③ 변경 계획
### 1. 수정 파일
- views/components/footer.php; console/pages/setting/ajax/setting.process.php; console/pages/setting/index.php; console/pages/account/edit.php.
- console/Controller/PopupController.php, BannerController.php: 앞선 독립 검토 작업의 이미지 교체 수정 통합·검증.
### 2. 연관 파일
- inc/lib/site.info.php, config/helpers.php, console/Model/{Popup,Banner}Model.php, console/core/Validator.php, brochure 관련 기존 PHP/JS/SCSS.
### 3. 호출 흐름
- 관리자 설정 POST → 권한 검사 → 허용 필드 부분 UPDATE → site.info.php → footer.php.
- 이미지 수정 → 필수값 검증 → 새 파일 저장 → DB 트랜잭션 → 성공 후 이전 파일 정리.
### 4. DB 영향
- 스키마·인덱스 변경 없음. 로컬 복원 DB에만 테스트 데이터 작성 후 정리.
### 5. 기존 기능 영향
- 기존 URL·응답 형태·타이포·클래스 유지. 공란 비밀번호의 기존 해시 유지 동작 확인.
### 6. 리스크
- 화면 포커스가 사용자/다른 작업에 의해 변경될 수 있음. 파트너잇 대상 탭만 사용하고 결과 미확인 항목은 명시.
- DOM 없는 환경의 고급 HTML 편집 기능은 별도 서버 의존성으로 구분하며 근거 없는 정상 판정 금지.
### 7. 테스트 계획
- 정상: 설정 부분 저장·푸터 반영·이미지 교체·문의 접수/다운로드·계정 공란 비밀번호 유지.
- 실패: 빈 제목/파일 오류 시 원본 보존, 잘못된 설정 배열 입력 거부, 미인증 저장 차단.
- 회귀: 대표 사례 상세, 캡차 PNG, 홈페이지·주요 서브 경로.
### 8. 문서 대상
- 본 문서 결과 절, docs/04-history/patch-notes.md, handoff/README.md, 업로드 manifest.

## 추가 범위 확정 — 코드 중심 전체 관리자 검수
- 사용자 지시로 Chrome 검수를 중단. 배너는 미사용이므로 이후 수정·최종 추가 업로드 대상에서 제외.
- 게시판 등록의 LOCK TABLES 의존과 수정 전 파일 삭제, 계정 자기 삭제·자기 비활성화 방지, 알 수 없는 역할의 쓰기 차단을 추가 점검한다.
- 수정 후보: console/pages/board/ajax/board.process.php, console/Controller/AccountController.php, console/lib/Auth/Role.php 및 확인된 프론트 연결 의존 파일.
- DB 구조·기존 계정·대표 사례 데이터 변경 없이 로컬 복원 DB에만 CRUD/권한 fixture를 만들고 제거한다.
- 정상 CRUD, 실패 시 원본·정렬 보존, 권한 1/2/3/알 수 없는 역할별 차단 및 공개 고객사례·팝업 연결을 검증한다.
- 추가 확정 결함: popup.php는 기간설정 저장값 2 대신 0만 검사하여 정상 기간 팝업도 미노출. request.list.php는 신청 행이 있으면 mb_strlen/mb_substr를 호출하여 무mbstring 서버에서 500.
- 최소 수정: popup.php는 무기한 1과 기간설정 0/2를 호환하고, request.list.php는 기존 TextHelper의 UTF8 메서드를 재사용. 기간/활성/노출 및 실제 접수 후 관리자 목록·상세를 로컬 HTTP로 검증한다.
- 계정 최소 수정 계획: AccountController의 본인 단건/일괄 삭제·본인 비활성화를 거부하고 선택 이메일/전화의 빈 값은 중복 검사하지 않는다. 입력 문자열/아이디/활성값을 서버에서 확인한다.
- 권한 갱신 계획: admin bootstrap은 로그인 세션의 계정 존재·활성·사이트·최신 역할을 요청마다 확인한다. 삭제/비활성/알 수 없는 역할은 세션 로그인값 제거, 역할 변경은 다음 요청부터 반영. Role은 알려지지 않은 역할의 쓰기를 허용하지 않는다. 공개 bootstrap과 기존 비밀번호는 변경하지 않는다.
- 프론트 연결 추가 점검: 대표 5개 초과분의 갤러리 누락, 100건 목록 캐시에 의존하는 상세 조회를 확인하고 해당 데이터 조립 헬퍼만 수정한다.
- 게시판 구현 상세: board.process 단일 소유 수정. mode/JSON/locale/extras 유지, 게시판·제목·업로드 사전검증, nb_board_manage row FOR UPDATE로 직렬화한 CRUD/locale 트랜잭션, sitekey+board_no 범위 고정, commit 후 구파일 삭제/rollback 후 신파일 정리. 복제의 DOM 의존을 없애고 첨부는 독립 복제·본문 이미지 참조는 기존 공유 보존. 실패·정렬·복제·프론트 연결을 로컬 검증.
- 프론트 AJAX 공용 fetcher는 기존 result=fail/msg 응답도 실패로 처리하도록 호환. 팝업 종료일 날짜 문자열은 당일 종료까지 포함하고 시간까지 지정한 값은 기존 시각 경계를 유지한다.
- 팝업 나머지 CRUD도 같은 보존 원칙 적용: 빈 제목 등록 전 업로드 금지, 등록/정렬/삭제 트랜잭션, DB 실패 시 기존 파일 보존·신규 파일 정리. 기존 종료일 날짜/시각 경계와 파일 예외를 로컬로 추가 검증.
- 계정 신규/수정 UI의 선택 이메일·연락처 패턴·비밀번호 안내를 서버 정책에 맞추고 AccountController.js는 브라우저 reportValidity를 호출해 HTML 검증을 실제 실행한다.
- 기존 게시판 선택 UI의 게시판 간 이동은 보존한다. 두 게시판의 관리행을 같은 순서로 잠그고 이전/새 게시판 정렬을 별도 조정한다. 임의 기능 제거 없이 같은 사이트의 유효 게시판만 허용한다.
- 사이트 정보 입력값은 HTML 속성 출력 시 escape 처리하고, 팝업 비연속 선택 삭제의 정렬은 실제 삭제 순서에 따라 보정한다.
- 실제 로컬 복원 덤프에서 nb_board/nb_board_manage/nb_board_comment는 MyISAM, nb_board_locale는 InnoDB임을 확인했다. 트랜잭션 보장을 위해 네 테이블의 InnoDB 전환을 별도 CLI 마이그레이션으로 공급한다. 테이블별 DB 내부 백업·적용 이력·재실행 검사, 변경 전후 행 데이터 보존을 검증한다. 실서버 DB에는 적용하지 않는다.
- 팝업 JS는 브라우저 저장소 사용이 차단돼도 닫기 동작을 유지하고, 하루 숨김 키를 현재 프로젝트 이름으로 구분한다. 새 UI·스타일은 만들지 않고 JS 재빌드 결과만 배포한다.
- 게시판 복제의 공유 본문 이미지가 에디터 삭제 API로 제거되지 않도록 upload.php의 삭제 경로 경계와 기존 게시물/locale 참조를 확인한다. 참조 중 파일은 유지하고 응답 형식 success는 보존한다.
- 기존 고객사례 목록의 임의 100건 상한도 제거한다. 사이트·게시판별 실제 건수에 맞춰 기존 조립/6건 페이지네이션을 유지하고, 100건 초과 목록 마지막 페이지와 단건 조회를 검증한다. 대표 슬라이더는 계속 최대 5개다.

## ④ 구현 결과
- 게시판: LOCK TABLES 의존 제거. 게시물·locale·정렬은 같은 트랜잭션에서 저장하며 파일 정리는 commit 뒤 수행한다. 실패 시 신규 파일만 정리한다. 복제·게시판 이동·extra1~30·대표 여부와 회사명은 유지한다.
- 프론트: 대표 슬라이더 5개 초과분은 갤러리에 남긴다. 100건 초과 목록과 개별 상세도 조회한다. 본문 공유 이미지의 에디터 삭제를 방지한다.
- 팝업: 기간설정 2/기존 0 호환, 날짜 종료일 당일 포함, 시간 지정은 해당 시각까지. 등록·수정·삭제·정렬 DB 실패 시 이미지·정렬을 보존한다. 비연속 선택 삭제 정렬 보정. 저장소 차단 브라우저에서도 닫기 가능.
- 문의: 신청 행이 존재하는 관리자 목록에서 mbstring을 요구하지 않도록 수정. 소개서 신청·관리자 목록/상세·PDF 연결 유지.
- 정보: 제출한 필드만 저장. 화면에 없는 필드 보존. 저장한 푸터 정보 연결 및 출력 escape, 명시적 공란 반영.
- 계정·권한: 본인 삭제/일괄 삭제/비활성화/강등 거절, 선택 연락처 공란 중복검사 제외, 서버·폼 검증 일치. 기존 로그인 세션에도 최신 역할/활성 상태 반영. 기존 tmaster 비밀번호 변경 없음.
- 배너: 사용하지 않는다는 최신 지시에 따라 최종 검수·추가 업로드에서 제외. 앞선 독립 작업의 BannerController 변경은 작업 트리에 보존했으나 최종 ZIP에는 넣지 않음.

## ⑤ 실제 수행한 검증
모두 별도 복원 QA DB / PHP 8.4.27 FPM. 실제 고객 서버 DB·소스 수정 없음. 공식 PHP 이미지의 mbstring은 내장되어 있으므로 확장 제거라고 주장하지 않으며, 65개 mb 함수 비활성화로 미사용 경로를 검증했다. GD는 실제 미설치다.

| 명령/검증 스크립트 | 실제 결과 |
|---|---|
| check-board-crud.py / check-board-move.py / check-board-guard-inline.py | 15+3+3 통과. CRUD·locale 실패 rollback·이미지 보존·복제·이동·엔진 guard·공유 참조. 원래 사례 13/정렬/파일 보존 |
| check-permissions.py | 20 통과. 권한 1/2/3, 자기 삭제·중지·강등 거부, 세션 중 역할 변경·중지·알 수 없는 역할 반영 |
| check-settings-account.py | HTTP 19 통과. 부분 저장·공란·escape·미인증 저장 거부·공란 비밀번호 해시 보존 |
| check-brochure-public.py | HTTP 38 통과. 13개 실제 제목/전체 본문 대조, Unicode/잘못된 입력, 접수/중복/PDF/다른 세션 다운로드 거부 |
| check-popup-request-boundary.py | 11 통과. 노출 기간·활성·메인 한정, 실제 접수 후 관리자 목록·상세·PDF |
| check-popup-final-safety.py | 7 통과. 빈 제목 고아 파일 방지, insert/delete/bulk DB 실패 원자성, 비연속 정렬, 날짜/시각 종료 경계 |
| node check-ui-logic.mjs | 8 통과. 구/신 AJAX 실패 응답과 JSON 오류 판정, 저장소 차단/허용 시 팝업 닫기 |
| check-front-boundaries.py | 6 통과. 대표 최대5/6번째 갤러리, 100건 이후 상세/목록 마지막 페이지, 참조 이미지/경로 경계 |
| check-admin-extra.py | 11 통과. 분류 CRUD/입력폼 조회, 틀린 현재 비밀번호/약한 새 비밀번호/불일치 거부, QA 계정 비밀번호 변경·로그아웃·재로그인, robots/sitemap |
| check-migration.py | 읽기 점검, 3 MyISAM→InnoDB, 4테이블 원본 행 논리 해시·백업 일치, 두 번째 실행 무변경·이력 1개 확인 |
| PHP 8.4.27 php -l (최종 PHP 25개) / git diff --check (배포 대상) | 통과 |
| node build.js (npm run build) | 오류 0 / 경고 0 / 출력 4개 |

검증 JSON은 최종 ZIP의 verification에 제공한다. 테스트 도구·fixture·계정정보는 www에 넣지 않는다. 기능/코드/데이터/마크업·반응형 규칙/테스트의 5블록을 위 범위에서 확인했다. QA 게시물·문의·계정·분류·이미지·트리거·마이그레이션 임시 DB를 정리했다.

## 적용 규칙과 실제 확인 파일
- #16: 이 문서의 요구·영향·계획 뒤 구현, 실패 케이스 검증·기록.
- #17/#19: 기존 footer/팝업/문의/사례 컴포넌트·타이포 유틸 재사용. 신규 페이지 BEM/폰트/간격 변경 없음.
- #8/#14: 기존 legacy POST mode/API 응답과 기존 확인 모달 연동 유지. 이번 작업에서 REST 라우트 전면 개편은 하지 않음.
- Backend: console/pages/board/ajax/board.process.php·upload.php, Popup/AccountController.php, Popup/BoardLocale/AccountModel, Role와 admin bootstrap, setting.process.php, BrochureRequestService.
- Frontend: partnerit-stories-data/gallery/featured/story-card, footer, popup/image-popup, brochure-modal/form-field, Popup/Account JS와 fetcher, brochure SCSS.
- Data/i18n: nb_board/locale/manage/comment 엔진과 정렬·extra 필드, nb_request 동의·중복키, nb_admin 활성/역할/해시, nb_siteinfo 부분 저장 및 한국어 라벨.
- Test/Docs: 위 실제 QA 스크립트 결과·최종 manifest, server-spec/handoff/patch-notes 갱신.

## 배포와 남은 한계
- 기존 덤프는 게시판 3테이블이 MyISAM이었다. 실서버 엔진을 이번 작업에서 직접 조회하거나 변경하지 않았으므로 서버 담당자가 네 테이블을 점검해야 한다.
- database/migrations/261105-1504-board-transaction-engines.php: CLI 기본 실행은 점검만. 관리 쓰기를 멈추고 DB 백업 후 import 권한 계정으로 --apply 실행한다. .env의 런타임 계정은 바꾸지 않는다. 이미 적용되면 재실행해도 중복 적용하지 않는다. 미적용 MyISAM에서는 새 코드가 저장을 거절해 파일 손상을 방지한다.
- PHP 호환 수정은 요청한 공개 기능과 관리자 경로를 대상으로 검증했다. 모든 미사용 레거시 기능의 GD/DOM/ZIP/mbstring 의존 제거를 뜻하지 않는다. 팝업 description은 현 이미지 팝업 UI에 표시하지 않으며 무DOM fallback의 고급 서식은 지원하지 않는다.
- 사용자 지시로 Chrome/실제 렌더 검수를 중단했다. 모바일 반응형 규칙과 컴포넌트 연결을 코드로 확인했고, 실제 화면 실측이나 시각 QA 완료로 보고하지 않는다.
- 고객 서버 업로드·DB migration·Git push는 이번 작업에서 실행하지 않았다. 추가 수정본에는 SQL 덤프/.env/개인키/테스트 계정/배너 파일을 포함하지 않는다.

## ⑥ 문서 업데이트 / ⑦ 작업 이력
1. 날짜: 2026-11-05.
2. 요청: 사용 중 관리자·프론트 연결을 코드 중심으로 최종 점검/수정, 배너 제외.
3. 분류: UI/DEV/RISK/SERVER.
4. 발견: 기간값·mb 함수·DB 엔진·파일 삭제 순서·권한 세션·사례 누락·설정 저장 문제.
5. 변경 파일: 최종 ZIP manifest의 www 상대 경로 32개. source PopupController.js도 수정하고 dist로 빌드.
6. 의존 흐름: admin bootstrap→Role→Controller→Model/DB, 공개 컴포넌트→DB 정규화/프론트 JS.
7. 검증: 위 실제 테스트 결과와 PHP lint/빌드/diff 검사.
8. 데이터 영향: 로컬 복원 DB만 테스트. 고객 DB/기존 비밀번호·원본 개발 DB·사용자 사례 파일 변경 없음.
9. 운영 한계: InnoDB 선행 점검/전환, 실제 시각 검수 제외, 미사용 모듈 범위 밖.
10. 전달: 누적 코드 수정 ZIP + CLI 마이그레이션·설명·manifest·검증 JSON. 되돌릴 때는 업로드 전 코드 백업 복원; InnoDB는 데이터 보존 전환이므로 자동 MyISAM 복귀는 제공하지 않음.


### 최종 전달물 검증
- 파일: partnerit-admin-final-20261105.zip
- www 상대 경로 32개 / PHP 25개 / 검증 JSON 14개. CRC와 각 파일 SHA256 대조 통과. .env·계정정보·개인키·배너·SQL 덤프 제외.
- ZIP SHA256: 9af9ec44334f636dfdc02b06b194035bbb153334bea49f8ed1b8cd46b82f8aec
- 원격 업로드/DB 반영/Git push 미수행. 작업 전용 Docker 컨테이너·네트워크만 소유 라벨 확인 후 정리하며 다른 프로젝트 환경은 보존한다.


### GitHub 게시 정리 시 포맷 정합성
- uploads/request/.htaccess 끝 빈 줄만 제거해 git diff --check를 맞췄다. rewrite 규칙 두 줄은 동일하다. 기능 재검증이 필요한 코드 변경은 없다.
- 동일 전달 ZIP의 해당 파일·manifest만 동기화하고 CRC/32파일 해시 재검증. 최신 ZIP SHA256: 0b86c64ff0884069268ae7ab2adfb96e8730ed303441c75fcfac74721814de8d
