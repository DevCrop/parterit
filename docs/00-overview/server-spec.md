---
title: 배포 환경 / 서버 명세
categories: [ops]
desc: 이 프로젝트의 배포 환경, 인프라, 도메인 정보
related: [overview.goals]
updatedAt: 2026-11-05
---

# 배포 환경 / 서버 명세

## 확인된 환경과 범위

- 기존 검수 주소: `http://partnerit.nineonelabs.co.kr/`, 관리자 `/console/`. 10/26까지 격리 로컬 QA와 파일 전달을 수행했으며, 11/3 신규 서버 SFTP·SSH 터널·DB 읽기 접속을 확인했다. 신규 서버 소스 업로드·DB 반영은 하지 않았다.
- 로컬 기본 구성: Apache + PHP 8.4.27 (`dockerfile`), MySQL 8.0, Node 22.15 (프런트 빌드 전용). 기본 사이트 포트는 8888.
- 최신 격리 QA: PHP 8.4.27, 별도 MySQL·uploads, 개발 18884/운영 모드 18885. 이전 8.3.35/8.4.26 결과는 10/15 기록으로 보존한다. 운영 호스팅 OS·SAPI·확장·권한은 별도 확인 필요.
- Composer 요구 PHP는 `~8.4.27`(8.4.27 이상 8.5 미만), 의존성 해석 기준은 8.4.27. 새 vendor의 platform check도 같은 최소 버전을 요구한다.

## 확장·웹서버·권한

- `pdo_mysql`, `mysqli`, `mbstring`, `fileinfo`, `gd`, `zip`, `exif`; GD의 JPEG·PNG·FreeType·WebP 지원 확인. 의존성의 ctype/filter/hash도 필요하다.
- 검증한 이미지 드라이버는 GD. 기존 Imagick 분기는 유지했지만 이번 QA 환경에는 설치하지 않았다.
- Apache `mod_rewrite`, `mod_headers`, `.htaccess` 허용 필요. Nginx에서는 rewrite·보호 경로·WebP 규칙을 별도로 구성해야 한다.
- 루트 `.htaccess`는 미사용 SmartEditor2 및 TinyMCE jbimages 경로를 403으로 차단한다. 현재 CMS는 Summernote를 사용한다.
- 업로드와 PHP 세션 저장소에 웹서버 사용자 쓰기 권한 필요. `777` 일괄 적용은 하지 않는다.
- 기존 업로드 한도 유지: mod_php `.htaccess`는 파일 32M / POST 64M, FPM `.user.ini`는 각 110M, Apache body 한도는 64MiB. 실제 SAPI의 유효 한도는 운영에서 확인한다.

## 운영 환경 변수

`config/setup.php`는 운영에서 서버 환경 변수 > 루트 `.env` 순으로 DB 설정을 읽는다. `.env`는 `parse_ini_file` 형식이므로 특수문자 포함 값은 따옴표로 감싼다. 실제 자격 증명을 Git·배포 ZIP에 넣지 않는다.

```ini
DB_HOST="호스팅에서 받은 DB 호스트"
DB_PORT=3306
DB_NAME="운영 DB 이름"
DB_USER="운영 DB 사용자"
DB_PASS="운영 DB 비밀번호"
DB_CHARSET="utf8mb4"
```

- `APP_ENV=production`은 Apache `SetEnv` 또는 PHP-FPM 환경 변수로 지정한다. 현재 구현은 `.env`의 APP_ENV로 환경을 선택하지 않는다. 미지정이면 요청 호스트로 판별한다.
- 기존 `.env` 및 SMTP/호스팅 설정은 유지한다. 이번 전환에서 HTTP/HTTPS 정책을 변경하지 않았다.

## PHP 8.4 전환 순서

1. 운영 소스, 기존 vendor, uploads, DB 및 PHP 설정을 백업한다. 롤백 파일은 공개 웹 루트 밖에 둔다.
2. 검수 환경에 PHP 8.4.27 이상 8.4.x와 확장을 준비하고 `composer check-platform-reqs --no-dev`를 실행한다.
3. 변경 PHP와 `composer.json`, `composer.lock`, **새 vendor 전체**를 같은 릴리스로 적용한다. vendor는 덮어 섞지 않고 백업 후 전체 교체한다.
4. 서버에서 설치한다면 `composer install --no-dev --prefer-dist --optimize-autoloader` 사용. 운영에서 `composer update` 실행 금지.
5. `.htaccess` 변경도 적용한다. 이 전환 자체의 신규 SQL은 없다. 기존 소개서 기능의 마이그레이션 적용 상태는 유지한다.
6. 유지보수 시간에 PHP와 릴리스를 전환하고 OPcache를 호스팅 정상 절차로 갱신한다. 메인·로그인·게시물 CRUD·이미지·소개서 다운로드를 확인한다.
7. 실패하면 소스/vendor/PHP 설정을 함께 이전 백업으로 복원한다. 이번 작업은 DB 스키마를 바꾸지 않는다.

이번 PHP 전환 자체는 JS/CSS 빌드가 필요 없다. 실제 `.env`, QA 데이터, 로컬 백업은 소스 배포에서 제외한다. 기존 운영 uploads는 별도 콘텐츠 이관 대상으로 백업·전달한다. 격리 서버 기능 검사와 공개 9페이지·4개 폭의 브라우저 검증을 완료했다. 최신 PHP 8.4.27 결과는 `docs/04-history/261026-php8427-qa.md`, 이전 8.3/8.4 비교는 `docs/04-history/php84-qa-20261015.md`를 따른다.

## 2026-10-26 보안 패치 적용

- PHP 8.4.27은 10/21 공지의 CVE-2026-109618·CVE-2026-109619 수정 버전이다. 10/23 추가 공지에 따라 `session.upload_progress.enabled=0`도 서버 초기 설정에 적용한다. 공식 근거: https://www.php.net/archive/2026.php.
- Docker는 `config/php-security.ini`를 `/usr/local/etc/php/conf.d/zz-partnerit-security.ini`로 복사한다. 일반 호스팅은 전달 ZIP의 `server-settings/php-security.ini` 내용을 **실제 웹 SAPI가 읽는 php.ini/conf.d 또는 FPM 풀 설정**에 반영한다. 웹 루트 업로드나 FPM `.user.ini`만으로 적용했다고 판단하지 않는다.
- 설치 후 PHP-FPM 또는 Apache 등 실제 PHP 워커 전체를 재시작한다. CLI와 웹 요청 양쪽에서 PHP 버전 및 해당 ini값을 확인한다. 로컬 개발/운영 모드 웹 요청은 모두 8.4.27/apache2handler/값 0을 검증했다.
- 새 ZIP은 이전 PHP 전환 파일과 vendor를 누적 포함한다. 신규 SQL 없음. 실제 호스팅의 엔진 업데이트는 SFTP 소스 업로드로 대체되지 않는다.
- 재검수 상세: `docs/04-history/261026-php8427-qa.md`. GD 기준 검증, 외부 SMTP·Imagick·실제 호스팅 FPM 환경은 미검증.

## 신규 서버 이관 상태 — 2026-11-05 문서 정리

아래 서버 안내값은 고객 담당자가 제공했으며, 직접 접속 검사는 **2026-11-03**에 수행했다. 11/5 신규 서버 재접속·배포를 수행했다는 의미가 아니다. 비밀번호와 개인키는 Git에 저장하지 않는다.

| 항목 | 값 / 근거 |
|---|---|
| 테스트 주소·계정 | 고객의 비공개 접속 안내 및 별도 인수인계 파일 참조. 공개 저장소에 실제 값을 넣지 않는다 |
| 웹 루트 | SFTP `/www`, 진입 `index.php` — 고객 안내 |
| SFTP | 포트 `22`, 비밀번호 인증, 원격 경로 `/www` |
| 웹서버 | Apache 2.4 + Nginx reverse proxy, mod_rewrite/mod_headers/.htaccess 활성화 — 고객 안내, 사이트 배포 후 검증 필요 |
| PHP | 고객 안내는 8.4이며 실제 웹 SAPI 패치 버전·확장·초기 ini는 미확인. 코드 요구는 `~8.4.27` |
| 접속 허용 IP | 고객에게 전달한 비공개 설정 요청값. 작업 PC 변경 시 담당자에게 재확인 |
| DB 터널 | SSH 포트 `22`, 등록 공개키에 대응하는 기존 개인키 사용 |
| DB 서버 | 사설망 MySQL 포트 `3306`; 직접 조회한 버전 `8.0.45`. 실제 호스트·DB명은 비공개 안내 참조 |
| DB 계정 역할 | 덤프/DDL용 및 홈페이지 연결용을 분리 — 역할은 고객 안내, 양 계정 로그인·조회 성공 |

11/3 실제 결과: SFTP 로그인·`/www` 디렉터리 목록 조회 성공(항목 0개). Windows OpenSSH 기존 개인키 인증 및 로컬 `127.0.0.1:13306`에서 사설망 MySQL로 포워딩 성공. 그 터널을 통해 양 DB 계정의 `SELECT 1, VERSION(), DATABASE()` 및 테이블 수 조회 성공(각 0개). 파일 업로드·DB 쓰기/DDL 권한은 테스트하지 않았다. 검사 후 작업용 터널 종료.

최초 터널 인증 실패는 서버 담당자가 공개키 파일 접근 권한 문제로 확인·수정했다고 회신했다. 이후 같은 개인키로 성공했으며 로컬 키를 재발급하거나 원격 권한을 직접 변경하지 않았다.

서버 PHP에서는 고객이 안내한 내부 DB 주소와 홈페이지 연결용 계정을 사용한다. 개발 PC 전용 주소 `127.0.0.1:13306`을 서버 `.env`에 쓰지 않는다. 신규 서버는 마지막 검사 당시 소스·테이블이 비어 있었으므로 전체 사이트·최신 운영 DB·업로드 이관이 필요하다. PHP 업데이트 ZIP은 전체 신규 설치본이 아니다.

진행 순서와 비밀정보 없는 재접속 명령은 [최종 인수인계](../../handoff/README.md)를 따른다. HTTP QA에서는 가상 데이터만 사용하고 정식 오픈 정책·실제 웹 런타임·동의문·개인정보 처리 설정은 운영 담당자와 최종 확인한다.
