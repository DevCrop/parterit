---
title: 배포 환경 / 서버 명세
categories: [ops]
desc: 이 프로젝트의 배포 환경, 인프라, 도메인 정보
related: [overview.goals]
updatedAt: 2026-10-15
---

# 배포 환경 / 서버 명세

## 확인된 환경과 범위

- 기존 검수 주소: `http://partnerit.nineonelabs.co.kr/`, 관리자 `/console/`. 2026-10-15 작업에서는 운영 서버에 접속하거나 배포하지 않았다.
- 로컬 기본 구성: Apache + PHP 8.4 (`dockerfile`), MySQL 8.0, Node 22.15 (프런트 빌드 전용). 사이트 포트는 8888.
- 격리 QA: PHP 8.3.35 / 8.4.26, 별도 MySQL·uploads. 운영 호스팅 OS·SAPI·확장·권한은 별도 확인 필요.
- Composer 최소 PHP는 8.3, 의존성 해석 기준은 8.4.0. 신규 소스/vendor를 PHP 7.4에서 실행하지 않는다.

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
2. 검수 환경에 PHP 8.4와 확장을 준비하고 `composer check-platform-reqs --no-dev`를 실행한다.
3. 변경 PHP와 `composer.json`, `composer.lock`, **새 vendor 전체**를 같은 릴리스로 적용한다. vendor는 덮어 섞지 않고 백업 후 전체 교체한다.
4. 서버에서 설치한다면 `composer install --no-dev --prefer-dist --optimize-autoloader` 사용. 운영에서 `composer update` 실행 금지.
5. `.htaccess` 변경도 적용한다. 이 전환 자체의 신규 SQL은 없다. 기존 소개서 기능의 마이그레이션 적용 상태는 유지한다.
6. 유지보수 시간에 PHP와 릴리스를 전환하고 OPcache를 호스팅 정상 절차로 갱신한다. 메인·로그인·게시물 CRUD·이미지·소개서 다운로드를 확인한다.
7. 실패하면 소스/vendor/PHP 설정을 함께 이전 백업으로 복원한다. 이번 작업은 DB 스키마를 바꾸지 않는다.

이번 PHP 전환은 JS/CSS 빌드가 필요 없다. `.env`, uploads, QA 데이터, 로컬 백업은 배포에서 제외한다. 격리 서버 기능 검사와 공개 9페이지·4개 폭의 브라우저 검증을 완료했다. 상세 결과와 실제 호스팅의 잔여 확인 사항은 `docs/04-history/php84-qa-20261015.md`의 최종 판정을 따른다.
