# 2026-11-05 Apache 500 코드·FPM 재점검

## 결론과 범위

사용자 요청은 고객 서버에서 발생한 500을 코드 측에서 다시 분석하는 것이다. 제품 소스·고객 서버·DB를 수정하지 않은 읽기 전용 검토이며, 진단 fixture만 로컬에 생성했다. 현재 서버 원인은 오류 로그가 없어 확정하지 못했다. 다만 현재 소스로 두 가지 500 조건을 실제 재현했고 FPM ini 불일치 한 가지를 확인했다.

## 실제 확인 파일과 영향

- `.htaccess:2-8`: .env 보호의 Require/AuthConfig 의존과 Options/FollowSymLinks override 요구.
- `config/setup.php:76-83,106-129`: APP_ENV/HTTP_HOST로 환경 판별 후 production에서만 .env 파싱. 필수 DB 설정 누락 시 예외.
- `.user.ini:1-5`: FPM 파일/POST/시간/메모리 한도. max_input_vars는 미설정.
- `src/autoload.php:5`, `inc/lib/base.class.php:214`: setup/Composer가 운영 예외 처리기 등록보다 먼저 실행되므로 초기 오류는 PHP/FPM 로그가 필요할 수 있음.
- `composer.json:3`, `vendor/composer/platform_check.php:7`: PHP 최소8.4.27. DB 드라이버는 mod_php 전용 의존 없음.
- `uploads/request/.htaccess`: 문의 업로드 폴더 접근 차단. 루트/SVG500의 직접 설명은 아님.
- Backend: 부트스트랩·설정·FPM 및 Apache지시어. Frontend: 정적 자산/라우팅/환경. Data: DB 접속·쓰기·DDL 없음. i18n: 변경 없음. Test: HTTP·SFTP 읽기 비교·Apache 정책5조건·실제FPM6요청. Docs: 본 보고·server-spec·handoff·patch-notes.

## 원격에서 실제 확인

SFTP에서 원격 파일을 비공개 로컬 진단 폴더로 읽기만 했다. index.php, config/setup.php, src/autoload.php, vendor/composer/platform_check.php, .user.ini, inc/lib/base.class.php, composer.json **7개 모두 전달본과 byte/hash 일치**. 앞선 읽기 검사에서 루트 .htaccess와 .env도 전달본과 일치했고 SVG 파일이 실제 존재함을 확인했다.

검토 종료 시 HTTP 재요청에서도 메인/console/존재하는 favicon SVG는 각500. 이것만으로 Apache 설정인지 전역 rewrite/PHP 처리인지 모든 가능성을 단정하지 않는다. 원격 파일 변경·오류 표시 활성화·phpinfo/임시 probe 업로드·권한 수정·DB 쓰기·서버 서비스 재시작 없음. SFTP 연결 종료.

고객은 웹 PHP8.4.27/PHP-FPM/Nginx·Apache 연동 완료를 회신했다. 이는 고객 안내 근거이며 이번 직접 웹 SAPI 버전·확장/ini 확인 결과로 취급하지 않는다.

## 로컬 재현1: Apache override 정책

현재 .htaccess를 수정하지 않고 Apache2.4.69 컨테이너에서 같은 정적 SVG와 비밀 없는 가상 .env로 검사했다. 실제 고객은2.4.58 안내이므로 버전 동일 재현이라고 표시하지 않는다.

|AllowOverride|mod_php|SVG|.env|실제 로그|
|---|---|---|---|---|
|All|활성|200|403|오류없음|
|All|비활성|200|403|오류없음|
|FileInfo|비활성|500|500|Require not allowed in <FilesMatch> context|
|FileInfo AuthConfig|비활성|500|500|Options not allowed here|
|FileInfo AuthConfig Options=FollowSymLinks|비활성|200|403|오류없음|

현재 소스는 단순 rewrite 허용 이상의 권한이 필요하다. FPM 환경에서 mod_php가 없다는 이유만으로 .htaccess가 깨지는 것은 아니며, php_value 블록은 조건문으로 건너뛰었다. Require나 Options를 무작정 삭제하면 보호/라우팅 전제가 변하므로 실제 서버 설정과 오류 로그를 먼저 대조한다. Apache 공식 근거: https://httpd.apache.org/docs/2.4/mod/core.html#allowoverride . LimitRequestBody의 Override:All은 특정 AllowOverride All만 가능하다는 뜻으로 해석하지 않는다.

## 로컬 재현2: 실제 PHP-FPM8.4.27

공식 php:8.4.27-fpm(digest sha256:3a6527017daca7917709afa08a37388ec108db093857f31d642cfff2e6f0449e)과 Apache mod_proxy_fcgi를 연결했다. mod_php는 비활성, 별도 내부 Docker 네트워크, 외부 포트 공개 없음. 현재 config/Composer platform check/.user.ini/.htaccess를 복사하고 가상 DB값만 사용했다. DB 연결이나 전체 페이지/CRUD 검사는 수행하지 않았다.

- 외부 Host partnerit.qa + APP_ENV미지정 → fpm-fcgi/PHP8.4.27/production, .env 값 로드 및 Composer검사 통과,200.
- Host localhost + APP_ENV미지정 → development, .env를 읽지 않아 Database credentials are not configured,500.
- Host localhost + Apache SetEnv APP_ENV production → FPM에 전달됨, production/.env 로드 및 Composer검사 통과,200.
- Host127.0.0.1 + APP_ENV미지정 → development, 동일 DB설정 누락500.
- 같은 구성의 정적 SVG200, .env403. PHP security startup ini를 별도로 마운트했을 때 session.upload_progress.enabled=0.

따라서 프록시가 Host를 localhost/127.0.0.1로 바꾸고 APP_ENV를 전달하지 않는 환경에서는 실제로 실패한다. **고객 서버의 backend Host와 APP_ENV 값은 아직 미확인**이므로 현재 서버의 원인으로 확정하지 않는다. 기본 해결은 원래 Host 보존 및 명시적 APP_ENV=production 전달이며, 코드 보완안은 .env에 명시한 APP_ENV도 환경 판정에서 읽도록 계약을 확장하는 것이다. 단순히 지금 .env에 APP_ENV를 추가하는 것만으로는 현재 코드가 이를 사용하지 않는다.

## 별도 FPM 일관성 항목

실제 FPM 요청에서 .user.ini의 upload_max_filesize/post_max_size110M 적용을 확인했으나 max_input_vars는 기본1000이었다. mod_php 경로의5000과 다르므로 .user.ini에 max_input_vars=5000을 추가하는 것이 보완안이다. 전체500의 원인은 아니며 큰 폼과 설정 일관성에 관한 항목이다. 앱 소스 개선안과 실제500 확정을 혼동하지 않는다.

## 최소 조치와 남은 확인

1. 서버 error log에서 Require/Options 거부 여부를 확인하고 해당 DocumentRoot의 AllowOverride/AllowOverrideList와 대조. 필요한 범위는 로컬에서 FileInfo AuthConfig Options=FollowSymLinks로 검증했다. Apache Options FollowSymLinks 전제와 기존 보안 정책은 서버 관리자가 확인한다.
2. PHP-FPM 요청에 APP_ENV=production을 명시하고 Nginx가 원래 Host를 보존하는지 확인. 또는 명시적 파일 기반 APP_ENV를 지원하도록 코드 계약을 확장하고 환경 회귀검사를 한 뒤 배포한다.
3. .user.ini의 max_input_vars5000 보완. startup 보안 ini의 실제 고객 FPM 적용은 서버 로그/설정으로 별도 확인.
4. 초기 부트스트랩 오류는 PHP/FPM error log에서 확인. 진단 편의 때문에 공개 화면에 비밀값/오류 trace를 출력하지 않는다.

이번 요청은 점검이므로 제품 코드·전달 ZIP·비공개 운영 .env·원격 파일을 바꾸지 않았다. 빌드가 필요한 프런트 변경 없음. 서버 원인을 확정하거나 복구 완료했다고 보고하지 않는다. 5개 Apache 사례+6개 FPM 요청은 해당 설정 경로만 검증한 것이며 신규 서버 전체 UI/모바일/DB·CRUD QA의 대체물이 아니다.

## 실행 근거·정리

비공개 release/server-qa-20261105의 check-apache-policy.py 및 check-fpm-environment.py를 실행, apache-policy-validation.json/fpm-environment-validation.json/remote-source-comparison.json/http-after-local-review.json에 결과 저장. fixture는 고객 자격 증명 대신 가상 값 사용. 실제 .env 읽기 사본은 비공개 로컬에만 보관하며 Git·www에 넣지 않음. 작업 전용 컨테이너·네트워크는 각 검사 finally에서 제거, 다른 프로젝트 서비스는 보존. 읽기 전용 독립 PHP/FPM 감사 에이전트1개 결과를 파일 근거 및 부모의 실제 재현으로 통합했다.
