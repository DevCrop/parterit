# 2026-11-05 정제 DB 설치본 검증

## 적용 규칙 및 범위

- #16 변경 7단계: patch-notes의 사전 요구·영향·계획에 따라 격리 복사본 준비 → 실제 복원/기능 검사 → 본 기록 및 인수인계 갱신.
- DEV/SERVER: 기존 마이그레이션과 CMS 데이터 관계를 유지. 원본/고객 서버 쓰기 없이 신규 DB 설치용 덤프 생성. 제품 라우트·UI·계정 비밀번호 변경 없음.
- 실제 확인: `config/setup.php` 운영 .env 파서/우선순위, `src/Database/DB.php` 연결, `database/migrations/261007-1600-brochure-request-fields.php` 최신 컬럼/유일키, `console/lib/Auth/LoginProcessor.php` 계정/역할/세션, `inc/lib/board.class.php` board/category/locale, `src/Request/BrochureRequestService.php` 저장/중복 방지/PDF, `views/components/partnerit-brochure-modal.php` 토큰, `routes/web.php` 실제 활성 경로.
- Backend: 필수 스키마·인증·소개서 연동. Frontend: 사례 본문·카테고리·이미지 및 현재 메타데이터 연결. Data: 원본 volume 읽기 전용 복사, 별도 staging만 정제. i18n: locale 13행 유지. Test: 새 MySQL/PHP 및 HTTP. Docs: 본 보고·patch-notes·requirement-changes·server-spec·handoff.

## 데이터 출처와 정제

최종 로컬 개발 DB(MySQL 8.0.46)를 복사했다. 최신 실서비스 백업이라는 의미가 아니다. 원본 DB volume은 읽기 전용 복사 대상으로만 사용했고 기존 개발 계정 비밀번호는 변경하지 않았다. 원본 DB 컨테이너의 포트가 타 프로젝트와 충돌하여 그 프로젝트를 종료하지 않고 전용 volume 사본을 사용했다.

17개 테이블, 고객사례 13건/locale 13건, 카테고리 7개, 사이트 설정 1개, 활성 경로 SEO 10개, 역할 3개, 기존 관리자 1개 및 마이그레이션 기록 3개. 사용자가 유지하도록 승인한 사례 시안을 보존했다. 사례 이미지 3개 모두 앞서 전달한 정제 www 파일과 연결된다. 관리자 BCrypt 해시는 원본과 동일하며 실제 기존 비밀번호로 로그인했다.

개발 테스트 소개서 신청 5건은 설치본에서 제외했다. 사용하지 않는 문의 백업 테이블도 제외했다. 미사용 배너/팝업/추적 설정·태그·데이터 스키마는 유지하되 행은 비웠다. 옛 로고 파일명과 미사용 사이트 API 설정을 비우고 현재 파트너잇 공개 회사/연락처·메타데이터만 사용했다. 내부 DKHOLD 데이터 연결 키는 유지했다.

## 실제 실행 및 결과

- `mysqldump --single-transaction --no-tablespaces --set-gtid-purged=OFF --skip-add-drop-table --skip-add-locks --skip-lock-tables --skip-triggers` → 설치 SQL 작성. 백업 테이블 제외, DB 생성/USE/사용자/GRANT/DROP/DEFINER/GTID/접속 비밀번호 0.
- `docker exec -i <fresh-mysql> mysql ... <SQL stdin>` → 별도 빈 MySQL **8.0.45**에서 오류 없이 복원. 정확한 COUNT 쿼리로 17개 테이블·13개 사례 등 기대 행수 일치, board/manage/category/locale 고아 행 0, 소개서 필수 컬럼/유일키 존재.
- `php /prep/validate-restoration.php` (PHP **8.4.27**, SELECT/INSERT/UPDATE/DELETE만 가진 로컬 fixture 계정) → 기존 관리자 상태·해시 형식·비밀번호, 사례 이미지, 브랜드 잔재 검사 및 사례 생성/조회/수정/삭제 후 rollback 통과. 기존 관리자 해시는 원본 사본과 별도 비교하여 일치.
- `python check-restored-http.py` → 정제 www 자체를 복원 DB와 연결, HTTP **22건** 통과. 공개 페이지/고객사례 상세/법적 페이지/관리자, .env403, 소개서 CSRF403/검증422/저장201/PDF200/중복키 단일행/다른 세션403, 기존 관리자 실제 로그인 및 board.add 접근 확인. CAPTCHA는 격리 PHP 세션에만 지정한 QA fixture이며 고객 환경의 챌린지를 자동 해결하지 않았다. 가상 신청 행은 검사 후 제거, 설치 SQL은 검사 전에 생성한 무오염 파일 그대로다.
- 기존 소개서 CLI 마이그레이션 재실행 → `Already applied`; 중복 변경 없음.
- 비공개 DB 설정을 격리 fixture에 넣고 `config/setup.php` 로드 → 운영 내부 호스트/포트/DB/앱 계정/charset 및 비밀번호 존재 확인. 실제 고객 DB 연결 시도 아님.
- SHA/구문/패키지 검사 → SQL 67,027 bytes, 17 CREATE TABLE, 비밀 연결 값 및 서버 전체/파괴문 없음. 현재 release/www 2,967개 파일은 기존 정제 ZIP과 내용 hash 전부 일치.

## 5블록 검증 및 한계

- 기능: 복원 후 사례 읽기/쓰기, 기존 관리자 로그인, 소개서 AJAX/PDF 실제 동작 통과.
- 코드 정합성: 제품 코드 변경 없음. 기존 migration·폼/서비스·운영 설정 계약으로 확인.
- 데이터 안정성: 원본 volume·기존 비밀번호·고객 서버 보존. 설치본 개인정보성 테스트 문의 제거. QA fixture는 SQL에 포함되지 않음.
- UI: 새 UI 변경 없음. 이번 검사는 HTTP/데이터 검증이며 새로운 PC/모바일 화면 검수라고 주장하지 않는다.
- 테스트: 신규 MySQL 8.0.45/PHP 8.4.27 격리 실행. 고객 서버 실제 PHP 패치/SAPI/권한·환경·파일 업로드·SQL import는 별도 적용 및 검수가 남았다.

검사 harness에서 Windows 기본 문자 인코딩, Docker stdin -i 누락, read-only mountpoint 생성 및 내부 네트워크의 loopback publish 제한을 각각 확인하고 전용 UTF-8/입력 전달/설정 사본/작업용 웹 네트워크로 해결했다. 제품 코드 오류와 구분했으며 다른 프로젝트 컨테이너·포트는 변경하지 않았다.

## 산출물

비공개 로컬 `release/partnerit-db-install-20261105/`에 설치 SQL·적용 안내·SHA256.txt·별도 server-private/.env를 준비했다. .env의 내용은 고객이 제공한 내부 DB/application 계정 설정으로, SQL이나 Git/www ZIP에 포함하지 않았다. 원본 백업·QA 스크립트/로그·fixture는 별도 준비 폴더에만 보관한다. **SQL은 /www에 올리지 않고 DB 클라이언트에서 실행**, 비공개 설정 파일만 `/www/.env`에 적용한다.

- SQL: `261105-1123-partnerit-landing-clean.sql`
- SHA-256: `D6D6953526B0EF46D9A1F69AC350D42DCF604DB35BD50B5B7162260BA9C772A9`
- 고객 서버 반영·Git push: 수행하지 않음.
