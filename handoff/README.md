# 파트너잇 개발·배포 인수인계

정리일: **2026-11-05 / Asia/Seoul**. 소스 저장소는 `DevCrop/parterit`, 현재 작업 브랜치는 `codex/final-qa-20261007`이다. 이 문서는 현재 코드와 과거 실제 검사 결과를 구분해 다음 작업을 안내한다. 저장소 반영은 신규 서버 배포 완료를 의미하지 않는다.

**11/5 후속 정제:** 현재 로컬에는 타 프로젝트 잔재 제거 변경이 추가되어 있으며 아직 Git에 게시하지 않았다. 파일 배포는 새 `partnerit-www-clean-20261105.zip`을 사용한다. 이전 `partnerit-www-php8427-20261105.zip`은 대체 대상이다. `www/`만 업로드하고 DB·.env는 다음 단계에서 별도로 적용한다. 소스/파일 해시는 패키지 밖 manifest에 기록한다.

## 먼저 확인할 상태

| 구분 | 확인된 내용 |
|---|---|
| 홈페이지 | 한국어 PC·모바일 반응형 소개 사이트와 PC 관리자 CMS |
| 런타임 | PHP `~8.4.27` / MySQL 8.0 / utf8mb4; Docker 기준 PHP 8.4.27 Apache |
| 최근 기능 QA | 10/26 격리 환경의 PHP·HTTP·PC/모바일 검수 완료. 상세 근거는 아래 기록 |
| 신규 서버 접속 | 11/3 SFTP 및 기존 개인키 SSH 터널, 터널 경유 DB 두 계정 로그인·조회 성공 |
| 신규 서버 콘텐츠 | 마지막 확인 당시 `/www` 항목 0개, 대상 DB 테이블 0개 |
| 아직 하지 않은 일 | 신규 서버 업로드·DB import·쓰기 권한 검사·웹 SAPI 확인·도메인 전환 |
| 이번 Git 정리 | 기존 PHP 8.4.27 패치·검수 기록, 현재 소스와 일치하는 프런트 dist 및 본 인수인계 게시 |

## 사이트 범위와 확정된 결정

- 공개 경로: `/`, `/about/company`, `/matching`, `/ai-report`, `/consortium`, `/pricing`, `/partners`, `/partners/{no}`. 예전 `/business/...` 경로는 짧은 서비스 경로로 리디렉트한다. 경로 정본은 [routes/web.php](../routes/web.php).
- 무료체험은 기존 외부 플랫폼 `https://app.partnerit.kr/account/signin`으로 연결한다. 랜딩 내부 회원가입·로그인·결제 기능을 추가하지 않는다. `/contact`는 비활성 상태에서 외부 플랫폼으로 리디렉트한다.
- 회사소개·공고 매칭·AI 보고서·컨소시엄 매칭·요금정책은 소개 페이지다. 실제 공고 분석·회의 연결·매칭 기능은 랜딩 목업과 구분한다.
- 고객사례는 CMS 연동 목록·상세·대표 슬라이더를 사용한다. 공용 후기 캐러셀은 대표 사례를 최대 5개 사용하며, 데이터가 없을 때 시안 후기로 대체하는 기존 로직이 있다. 더미 사례는 사용자의 보존 요청에 따라 임의 삭제하지 않는다.
- 서비스 소개서 버튼은 신청 모달 → AJAX 저장 → 토큰 기반 PDF 다운로드 흐름이다. 수집·이용 내용은 펼쳐 보여주며, 완료 후 닫고 다시 열면 새 신청 폼을 보여준다. 일반 문의·마케팅 동의·메일 발송 기능과 구분한다.
- 전역 페이지 전환을 제거하고 AOS와 히어로 GSAP 연출을 사용한다. 파트너 로고 마퀴는 고객 요청으로 유지한다.
- 메인 히어로는 기존 Figma 원본의 3x PNG 4개를 사용한다. 임의 서비스 화면을 새로 만들지 않는다. 원본 디자인·텍스트 중복 제거·해상도 처리 근거는 [원본 에셋 기록](../docs/04-history/261007-hero-original-export.md).
- 프로젝트 설정 정본은 [config/project_profile.php](../config/project_profile.php). 내부 `sitekey=DKHOLD`는 기존 CMS 데이터 연결 키이므로 브랜드명처럼 임의 치환하지 않는다.

## 코드와 디자인 확인 위치

| 대상 | 위치 |
|---|---|
| 요청 진입·부트스트랩 | `index.php`, `bootstrap/`, `inc/lib/base.class.php` |
| 라우팅·렌더 | `routes/web.php`, `views/pages/`, `views/components/` |
| PHP 서비스·DB | `src/`, `config/setup.php`, `database/migrations/` |
| 관리자 | `console/` |
| 프런트 소스·배포 산출물 | `resource/js/`, `resource/scss/`, `resource/dist/`, `build.js` |
| 이미지·폰트·파일 | `resource/images/`, `resource/fonts/`, `resource/files/` |
| 소개서 신청 | `src/Request/BrochureRequestService.php`, `/brochure-requests`, `/brochure/download` |
| 환경·QA 정본 | [서버 명세](../docs/00-overview/server-spec.md), [PHP 검사 스크립트](../scripts/check-php-compat.php) |

Figma: [파트너잇 260916](https://www.figma.com/design/0QPPy4tbPlPCgHOixVlWhN?node-id=350-8931). 규칙판은 같은 파일의 `374:676`. 디자인 수정 시 과거 캡처보다 현재 노드를 우선한다. 이번 Git 정리에서는 Figma를 다시 검사하거나 새 디자인 변경을 하지 않았다.

## 로컬 실행·빌드

Node는 기존 Docker `node:22.15`, 프런트 빌드는 esbuild + Sass다. 새 체크아웃에서는 필요한 비공개 런타임 자산도 먼저 복원한다.

```sh
npm ci
npm run build
# 작업 중 자동 빌드가 필요할 때만
npm run watch

# PHP 8.4.27 이상 8.4.x 및 필요한 확장이 있는 환경
composer install --no-dev --prefer-dist --optimize-autoloader
composer check-platform-reqs --no-dev

# 로컬 개발 환경: 기본 사이트 http://localhost:8888
docker compose up -d --build web
docker compose exec web php scripts/check-php-compat.php --lint
docker compose exec web php scripts/check-php-compat.php
```

개발 DB는 Compose의 `nineonelabs`를 사용하지만 빈 DB에 사이트 전체 스키마가 자동 복원되지는 않는다. 운영 DB를 QA fixture로 바꾸지 않는다. 종료는 해당 프로젝트의 `docker compose stop`을 사용한다. 기존 Makefile의 `stop/reset`은 전체 컨테이너 또는 DB volume에 영향을 줄 수 있으므로 일반 종료 명령으로 사용하지 않는다.

이 개발 DB 이름·계정은 Compose에서 주입하며 서버 업로드 묶음에 넣지 않는다. PHP 런타임은 개발/운영 모두 명시된 DB명·사용자·비밀번호를 요구하고, DB 드라이버도 자체 기본 계정으로 추측 접속하지 않는다. 실제 운영 DB 준비는 이번 파일 정제와 별도다.

로컬 Windows 전역 PHP는 11/5 확인 당시 **8.4.24**여서 현재 최소 요구 버전에 못 미친다. 이 PHP로 검사 통과를 주장하지 말고 검증된 8.4.27 Docker 또는 맞는 런타임을 사용한다.

## 신규 서버 접속 정보와 증거

이 저장소는 공개 상태다. 실제 호스트·계정·내부 DB 주소·접속 허용 IP는 Git 제외된 `handoff/server-access-private.md` 및 고객의 비공개 안내를 이용한다. 비밀번호·개인키·키 암호는 그 파일에도 넣지 않는다.

| 용도 | 안내값 |
|---|---|
| 테스트 웹 | 고객이 안내한 HTTP QA 주소 |
| SFTP | SSH 포트 `22`, 비밀번호 인증, 원격 경로 `/www` |
| 웹 루트 | `/www` 자체이며 `/www/index.php` 기준 업로드. 실제 OS 절대 경로는 비공개 안내 참조 |
| SSH 터널 | DB 전용 SSH 계정, 등록된 기존 개인키로 인증 |
| MySQL | 사설망 호스트의 포트 `3306` |
| DB 이관/구조 변경 계정 | 고객이 지정한 import 전용 계정 |
| PHP 실행용 DB 계정 | 고객이 지정한 application 전용 계정 |
| 접속 허용 IP | 고객에게 전달한 작업 PC의 허용 IP. 변경 시 재확인 |

개발 PC PowerShell에서 터널을 여는 명령(키 파일은 해당 PC에 별도 보관):

```powershell
ssh -i "<개인키 절대 경로>" -o IdentitiesOnly=yes -o ExitOnForwardFailure=yes -N -T -L "127.0.0.1:13306:<내부 DB 호스트>:3306" "<SSH 터널 계정>@<SSH 호스트>"
```

터널을 열어둔 동안 PC의 MySQL 클라이언트는 `127.0.0.1:13306`으로 접속한다. 이미 다른 터널이 사용 중이면 해당 프로세스를 임의 종료하지 않는다. 키 암호 요청은 로컬 비공개 키 설정에 따른다.

11/3 실제 확인: Windows OpenSSH 공개키 인증·포워딩 성공 → **해당 로컬 포트를 경유**해 import/application 계정 각각 로그인 → `SELECT 1, VERSION(), DATABASE()` 및 `information_schema.tables` 수 조회 성공. MySQL **8.0.45**, 조회 DB명은 고객 안내값과 일치, 각 계정에서 테이블 **0개**. SFTP 로그인·`/www` 목록 조회도 성공(파일 0개). 테스트 후 작업용 터널은 종료했다. 업로드·DB 쓰기/DDL은 수행하지 않았다.

초기 인증 실패에 대해 서버 담당자가 공개키 파일 접근 권한 문제를 확인·수정했다고 회신했고, 같은 개인키로 재검사에 성공했다. 새 키 발급은 필요하지 않았다.

## 새 서버 적용 순서와 남은 확인

1. 서버 담당자에게 **실제 웹 PHP 패치 버전**과 확장, PHP SAPI, 쓰기 경로 권한을 확인받는다. 고객 안내의 PHP “8.4”만으로 코드 요구 `~8.4.27` 충족을 판단하지 않는다. Apache 2.4 + Nginx reverse proxy, mod_rewrite/mod_headers/.htaccess 활성화는 고객 안내값이며 배포 후 실제 동작 검사가 남았다.
2. 기존 검수/운영 사이트의 **최신 DB와 uploads**를 확보·백업한다. 9/17 SQL이나 QA DB는 최신 운영 데이터의 대체물이 아니다. 현재 새 DB는 비어 있으므로 PHP 패치 파일만 올려서는 사이트가 실행되지 않는다.
3. 웹 루트 `/www`에 전체 런타임과 별도 런타임 자산을 배치하고 의존성을 lock 기준으로 설치한다. 기존 서버가 있다면 소스·vendor·DB·uploads·PHP 설정의 복구용 백업을 웹 루트 밖에 둔다. 실제 전송은 이 인수인계 작업에 포함하지 않았다.
4. 서버 환경 변수 또는 비공개 `/www/.env`에 DB 설정을 넣는다. PHP는 **내부 호스트와 application 전용 계정**을 사용한다. `APP_ENV=production`은 서버/웹 SAPI 환경 변수로 설정한다. 이 코드에서는 `.env`의 APP_ENV가 환경 선택에 쓰이지 않는다.

   ```ini
   DB_HOST="<내부 DB 호스트>"
   DB_PORT=3306
   DB_NAME="<운영 DB명>"
   DB_USER="<홈페이지 DB 계정>"
   DB_PASS="<비공개로 전달받은 값>"
   DB_CHARSET="utf8mb4"
   ```

5. 최신 DB 복원·스키마 상태를 점검한 뒤 누락된 마이그레이션만 기존 CLI 절차로 적용한다. [소개서 마이그레이션](../database/migrations/261007-1600-brochure-request-fields.php)은 기존 `nb_request` 테이블을 전제로 하므로 빈 DB 전체 설치를 대신하지 않는다. 이번 PHP 패치는 신규 SQL을 요구하지 않는다.
6. [config/php-security.ini](../config/php-security.ini)의 `session.upload_progress.enabled=0`을 **실제 PHP 시작 설정**에 적용하고 워커를 재시작한다. CLI·웹 버전과 ini 적용을 함께 확인한다. 단순 웹 루트 업로드·`.user.ini`만으로 완료라고 판단하지 않는다. 근거는 10/26 QA 기록에 있다.
7. 메인/서비스/고객사례/관리자/소개서 다운로드를 PC·모바일에서 확인한다. 업로드·이미지/WebP·권한·세션/captcha·로그와 쓰기/DDL 권한도 실제 서버에서 점검한다. HTTP QA는 가상 데이터로만 수행한다.
8. 검수 승인 후 도메인 연결·오픈을 진행한다. 현재 소스는 `config/setup.php`에서 HTTP 정책을 강제한다. HTTPS 종료가 추가되면 해당 설정·프록시 인식·리디렉트·세션 정책을 함께 검토하며 임의 전환하지 않는다.

## Git에 포함되지 않는 자료와 기존 전달물

- `.env`, 계정 비밀번호, 개인키, DB 덤프, 고객 원본, QA 접수 데이터·로그·캡처는 Git 제외다. 비공개 자료는 별도 안전한 경로로 전달한다.
- root `vendor/`는 lock 기준 Composer 설치 대상이다. **`resource/vendor/`도 현재 vendor ignore 규칙에 걸리므로**, 브라우저/CMS용 제3자 런타임 자산은 기존 검증된 배포본에서 별도 확보한다. Node 설치만으로 이 폴더가 모두 복원된다고 가정하지 않는다.
- `uploads/`와 ignore된 MP4는 별도 콘텐츠다. Git에 추적 중인 webm/mov 등은 별개다. 필요한 영상·이미지·PDF 파일의 실제 로딩을 배포 후 확인한다.
- 로컬 `handoff/`의 디자인 원본·가공 에셋은 일괄 게시하지 않는다. 이 README만 예외 허용해 비공개 자료와 대용량 자료의 혼입을 막았다.
- 기존 전달물 `partnerit-php8427-final-20261026.zip`: 625,624 bytes, SHA-256 `B605FFE04B9407D784EC2D7571765257F534BB14E605942BC54D621DDBDEB3CE`. **PHP 전환 누적 업데이트용**이며 전체 사이트 신규 설치본이 아니다. Git에 ZIP 자체는 없다.
- 위 ZIP은 보정된 레거시 `resource/vendor` 2파일도 포함한다. 원격 Git의 lock 설치만으로 해당 파일들이 새로 생성되지 않는다. 미사용 에디터는 현재 `.htaccess`에서 차단하며 Summernote CMS를 유지한다.
- 새 전체 정제본은 미사용 에디터 파일을 싣지 않으며, 현재 사이트/관리자의 브라우저 vendor 7종과 Bootstrap 3.4.1 Glyphicons 폰트 5개·라이선스를 포함한다. 이 추가 폰트도 기존 vendor ignore 대상이므로 파일 전달본 또는 공식 같은 버전 자산으로 별도 복원한다.

## 검증 근거와 한계

- [10/26 PHP 8.4.27 QA](../docs/04-history/261026-php8427-qa.md): 781 PHP lint issues 0, 단위 23, HTTP 41+7, 개발/production GET 16 통과. 9페이지 × 4폭(320/390/768/1440) 36측정에서 가로 넘침·깨진 이미지 0. 실제 모달·메뉴·고객사례 상세 확인. 이는 당시 격리 환경 결과이며 신규 고객 서버 검수가 아니다.
- [10/15 이전 버전 비교](../docs/04-history/php84-qa-20261015.md), [작업 이력](../docs/04-history/patch-notes.md), [요구 변경 이력](../docs/04-history/requirement-changes.md).
- 11/5 `build.js`를 `write:false`로 재현해 오류/경고 0, 출력 JS/CSS/map 4개 모두 기존 로컬 산출물과 바이트 일치를 확인했다. map의 프로젝트 소스도 현재 소스와 일치한다. 재게시만 수행하며 새 UI 디자인은 추가하지 않는다.
- 원격 PHP 패치·확장·웹 SAPI·시작 설정·SMTP·Imagick·쓰기 권한·신규 서버의 화면 및 CRUD는 아직 검증하지 않았다. 접속 성공과 배포 성공을 혼동하지 않는다.
- 후속 정제본 검증: 패키지 PHP698 lint, 기존 호환23, DB 설정 우선순위·누락 시 안전 실패6, 격리 HTTP20·자산135 통과. 정적 의존성 누락0. 브라우저 연결 도구의 초기화 오류로 이번 새 화면 검수는 미실행이며 10/26 화면 검증 결과와 구분한다. 실제 고객 DB·서버 쓰기는 없음.

## 11/5 정제 DB 전달본 (후속)

신규 빈 DB용 `261105-1123-partnerit-landing-clean.sql`을 비공개 로컬 release/partnerit-db-install-20261105에 준비했다. 원본을 보존한 최종 로컬 개발 DB 사본이며 최신 운영 백업을 확보했다는 뜻은 아니다. 사례13/분류7/locale13 및 기존 관리자 ID·비밀번호를 유지했고 개발 테스트 문의·불필요한 백업/추적 설정은 제외했다.

SQL은 SFTP /www에 올리지 않는다. SSH 터널을 연 DB 클라이언트에서 대상 DB가 비어 있는지 확인하고 import 전용 계정으로 실행한다. 별도 server-private/.env 파일만 /www/.env에 업로드하여 내부 호스트와 application 계정을 적용한다. 서버 환경변수의 기존 DB 값은 파일보다 우선한다. 비공개 파일은 Git·공개 자료에 넣지 않는다. 기존 www ZIP과 DB 설치본은 별도다.

MySQL8.0.45/PHP8.4.27 빈 DB 실제 복원, 기존 로그인/사례CRUD/소개서AJAX·PDF 및 HTTP22를 검사했다. [상세 보고](../docs/04-history/261105-db-installation-qa.md). 고객 서버에 직접 업로드·SQL import·Git push는 하지 않았다. 실제 적용 후 PHP/ini/쓰기 권한·세션·파일/DB 동작과 도메인 연결을 최종 확인해야 한다.

## 11/5 실제 업로드 후500 재점검

사용자 업로드 뒤 원격 주요 PHP/설정7파일과 기존 .env/.htaccess를 읽어 전달본 일치를 확인했으나 메인·console·정적SVG는500이다. 실제 서버가 정상 운영된다고 보고하지 않는다. 코드·원격 파일·DB 변경 없이 [Apache/PHP-FPM 검증](../docs/04-history/261105-apache500-review.md)을 수행했다.

서버 담당 확인 항목: 해당 DocumentRoot에서 Require/AuthConfig, rewrite·header/FileInfo, Options FollowSymLinks 허용과 오류 로그를 대조한다. 로컬에서는 필요한 override 범위에서 정적200/.env403 통과했다. FPM에 APP_ENV=production을 명시하고 프록시가 원래 Host를 보존하는지 확인한다. localhost Host+APP_ENV미지정은 실제 FPM8.4.27에서 .env 미로드500을 재현했다. FPM max_input_vars5000은 별도 코드 보완 후보이며 전체500과 구분한다. 서버 원인 확정 전 임의 보호 해제나 무차별 권한 변경을 하지 않는다.

## 11/5 후속 서버 호환 수정본 적용

최신 비공개 전달물은 partnerit-server-fix-20261105.zip이다. **압축의 www안5파일을 기존 원격/www에 덮어쓴다.** .htaccess/.user.ini/config/setup.php/uploads/request/.htaccess/.env만포함. 기존운영DB/파일보존, SQL재실행없음. 실제원격업로드는사용자가진행하며 이기록에서직접배포하지않았다.

source환경선택은 serverAPP_ENV > fileAPP_ENV > Host로바뀌었다. 새.env의production표시로 프록시localhost에서도운영설정로드. 기존서버의explicitdevelopment는여전히우선하므로운영서버에잘못설정된서버환경변수는수정해야한다. 보호규칙은FileInfo용rewrite로유지, 기본mod_rewrite/Directoryrewrite옵션필요.

[구현검증](../docs/04-history/261105-server-compatibility-fix.md): 환경12, Apache보호48, 실제FPM설정6, 전체앱HTTP22통과. .user.ini/OPcache반영과실제서버500은업로드후검수. 이ZIP은private.env를포함하므로Git/공개저장소에올리지않는다. uploads/request보호파일은ignored runtime자산이므로Git만으로공급되지않는점에유의한다.

## 11/5 실제 서버 추가 검수 — 확장 활성화 대기

서버는실제PHP8.4.27/FPM이나mbstring/GD/ZIP/DOM(XML)이미로드다. mb_substrundefined를직접확인하여실제고객사례13상세500의원인을확정했고, 소개서빈입력validation도500이다. 소스파일/데이터누락이아니다. 주요10페이지/자산124와관리자로그인페이지는정상.

서버담당자에게웹FPM의네확장설치·활성화/재시작을요청하고사례본문·소개서422/가상접수/PDF·관리자이미지/본문을재검수해야한다. 현재해결완료아님. 진단용파일은보호된읽기probe한개만일시업로드후삭제/SFTPnotfound·진단응답없음검증완료. 고객DB쓰기/기존사이트코드교체없음. [검수기록](../docs/04-history/261105-live-detail-extension-qa.md).

## 11/5 고객사례·소개서 코드 호환 수정본

추가수정본partnerit-detail-brochure-fix-20261105.zip의www안PHP3파일을기존/www에동일경로덮어쓴다. TextHelper/show/BrochureRequestService만포함하고SQL/.env/스타일재적용은없다. 직접원격배포하지않았다.

공개기능의문자처리를native mb함수또는PCRE UTF8으로처리한다. 기본/fallback각1119검사, 실제FPM에서mb함수미사용·본문13/HTTP38검증완료. GD/DOM/ZIP과다른기존mb함수사용처는서버확장필요범위로남는다. 업로드뒤실제partners/11과소개서validation422/신청·PDF를확인한다. [상세검증](../docs/04-history/261105-public-text-compatibility-fix.md).

## 11/5 캡차 이미지 수정본

partnerit-captcha-fix-20261105.zip의www안4파일만기존/www에동일경로덮어쓴다. captcha/admin.php/default.php, inc/lib/helper/CaptchaHelper.php, console/lib/Auth/LoginProcessor.php. 새helper와검증파일을함께올린다. 계정/비밀번호/SQL/.env변경없음. GD없는환경도5자리PNG를생성하며빈세션/빈입력은거부한다.

로컬무GD FPM·nativeGD의이미지/세션·정상로그인/게시판목록HTTP25와PNG독립decode8개통과. 실제서버적용은사용자업로드후확인하며이번작업은직접배포하지않았다. 일반이미지/본문의나머지필수모듈은별도. [검증](../docs/04-history/261105-captcha-png-compatibility.md).


## 11/5 관리자 최종 추가 수정본

`partnerit-admin-final-20261105.zip`의 `www/` **안의 내용**을 기존 원격 `/www/`에 같은 상대 경로로 덮어쓴다. 배너·SQL 덤프·.env·개인키·QA fixture는 제외하며 기존 .env/업로드/계정을 유지한다. 누적 서버 호환/본문/캡차 수정도 포함한다. 검증자료와 설명서는 www 밖에 있으며 서버에 올리지 않는다.

게시판 저장은 네 테이블의 InnoDB가 필요하다. 기존 덤프의 nb_board/nb_board_manage/nb_board_comment는 MyISAM이었다. 실제 고객 서버 엔진은 미조회이므로 서버 담당자가 `database/migrations/261105-1504-board-transaction-engines.php`를 DB import 권한으로 CLI 점검한 뒤 필요한 경우 --apply 해야 한다. 런타임 DB 계정은 그대로 둔다. migration은 DB 내부 백업/이력/재실행 검사를 제공한다. 미전환 상태는 새 코드가 업로드 전에 저장을 거부한다.

게시판21, 권한20, 설정HTTP19, 공개/소개서HTTP38, 팝업/문의경계11, 팝업실패7, JS8, 프론트경계6, 분류·비밀번호·SEO11 및 migration 행 보존을 확인했다. 배너는 미사용 제외. 실제 시각 검수 없이 코드의 마크업/반응형/연결만 확인했다. [전체 검수기록](../docs/04-history/261105-final-cms-qa.md).


## GitHub 인수인계 범위 (2026-11-05)

게시 저장소는 DevCrop/parterit, 대상은 기존 codex/final-qa-20261007 및 기본 main이다. PHP8.4.27 호환·정제 소스·관리자 최종 수정·빌드·마이그레이션·검수 기록을 관리한다. 실제 서버 .env/SSH 키/SQL 데이터 덤프/테스트 fixture/배포 ZIP은 저장소에 올리지 않는다. Docker Compose의 user/password 값은 격리 개발 예시이며 실제 서버 계정이 아니다. 이미지/문서의 원본 및 비공개 업로드는 별도 전달물로 관리한다. 미사용 배너의 이전 로컬 수정은 이번 게시에서 제외한다. GitHub 게시와 서버 배포는 별개이며, 실제 배포에는 위 InnoDB 점검/마이그레이션이 선행된다.
