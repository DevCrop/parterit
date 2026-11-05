# 2026-11-05 서버 500 호환 보완 배포 파일

## 적용 규칙·요구·영향

- 사용자 요청: 앞선 원인 재현 결과를 바탕으로 최종 서버 업로드 파일만 제공. #16 계획을 patch-notes에 기록한 뒤 구현·검증·문서화했다. 직접 원격 배포 요청으로 확대하지 않았다.
- SERVER/DEV: 기존 문구·화면·기능·계정·DB·HTTP 정책을 유지하고 배포 설정 호환성만 보완. 비밀은 기존 비공개 .env 전달본에만 보관한다.
- Backend: config/setup.php 환경 우선순위, .htaccess 및 FPM 한도. Frontend: 정적 자산/기존 라우팅만 영향. Data: 고객 DB 변경/SQL 없음, 재사용 격리 QA DB에서만 가상 신청 검사. i18n: 변경 없음. Test: 환경 계약·Apache보호·실제FPM설정·전체런타임HTTP. Docs: 본 보고·server-spec·handoff·requirements·patch-notes.

## 실제 변경 파일과 흐름

1. `.htaccess:1`: .env 보호를 mod_rewrite의 F/END 규칙으로 유지하여 AuthConfig override 의존 제거. Options +FollowSymLinks 선언 제거. server Directory의 기본 rewrite 옵션은 여전히 필요하다. RewriteEngine을 조건문 밖에 두어 rewrite모듈이 없으면 안전하게500으로 실패하고 .env는 노출하지 않는다.
2. `config/setup.php:68`: 서버 APP_ENV > 비공개 파일 APP_ENV > Host fallback. 서버의 explicit development는 파일을 읽지 않고 DB는 서버 값만 사용. production은 기존처럼 서버 DB값 > 파일값. 잘못된 ini는 값 대신 일반 오류만 반환.
3. `.user.ini:6`: max_input_vars=5000을 추가하여 mod_php와 FPM 설정 일치.
4. `uploads/request/.htaccess:1`: 해당 폴더 전체 차단을 rewrite F/END로 유지하여 FileInfo-only 환경에서도403. 이 파일은 기존 uploads ignore 대상이며 Git이 아닌 배포 ZIP에 포함한다.
5. 비공개 운영 `.env`: APP_ENV=production 한 줄만 추가. 고객이 제공한 내부 DB/application 계정 정보는 기존과 동일, 코드·Git·문서 본문에는 값 미기재.

HTTP→Apache 보호/rewrite→PHP setup의 explicit 환경→운영 DB 설정→기존 앱 흐름이다. SQL/마이그레이션/계정 변경 없음. 프런트 코드 변경이 없어 npm build를 반복하지 않았다. 앞선 정제 www 2,967파일을 그대로 복사한 격리 stage에 네 코드 파일과 가상 QA .env만 overlay하여 기존 사이트 동작을 검사했다.

## 실제 검사

- `python check-config-contract.py` →12조건 통과: localhost/loopback 파일production, 서버development 우선·오류파일 미로드, serverproduction 우선, 서버DB 우선, filedevelopment의 DB값 무시, 기존 Host fallback, 필수값 실패, 잘못된 ini의 일반 오류, 파일 없음. 실제 자격 증명·DB 연결 사용 안 함.
- `python check-apache-policy.py` →6정책×8경로=48요청 통과. All/mod_php, All/모듈없음, FileInfo, FileInfo AuthConfig, scopedOptions 각각SVG200. .env/대문자/백업형/하위폴더/PATH_INFO/인코딩형 및 uploads/request는403. rewrite모듈 없음은500이며 가상 private marker 노출0. 새 규칙으로 이전 FileInfo/AuthConfig500 두 조건이 해소됐다.
- `python check-fpm-environment.py` →공식 PHP8.4.27-fpm + Apacheproxy_fcgi, 실제 fpm-fcgi6요청 통과. 외부Host/localhost/127.0.0.1 모두production·설정로드200, max_input_vars5000. SVG200/.env403. Composer platform check통과. 이 FPM검사는 설정/보호/ini이며 전체 앱DB흐름 검사가 아님.
- `php -l config/setup.php` →PHP8.4.27 문법 오류0.
- 격리 MySQL8.0.45 retained QA volume 소유권 확인 후 새 작업용 Apache/PHP8.4.27 실행, 서버 APP_ENV/DB값을 주입하지 않고 **stage .env의 APP_ENVproduction/가상 QA DB값만으로** 실행. `python check-restored-http.py` →HTTP22통과: 메인·회사·서비스·요금·사례/상세·법적페이지·관리자, .env403, CSRF403/검증422/소개서저장201/중복단일행/PDF200/다른세션403, 기존관리자 실제 로그인·board.add200. 관리자 hash를 비공개 원본 snapshot과 비교하여 동일. CAPTCHA는 해당 격리 세션만의 fixture. 가상 문의는 검사 후 삭제, 최종0건.
- 실제 전달용 비공개 .env를 별도 네트워크없는 컨테이너에서 Hostlocalhost로 로드 →production/기존 고객 DB 설정 확인, 실제 DB 연결 없음.
- source/QA stage/전달 네 코드파일 byte 일치. private .env는 원본과 APP_ENV 한 줄을 제외한 설정 동일. ZIP은 www/ 아래5파일만, testzip통과, 내용SHA일치. 코드 네파일에 실제 접속 비밀0. 비공개 값은 전달 .env만 포함.
- `git diff --check` 및 작업 직전 snapshot 대비 affected diff검토. 기존 정제 작업 변경은 되돌리지 않음.

## 5블록과 남은 한계

- 기능: 기존 사이트/관리자/소개서22요청 및 config/FPM/보호규칙 검증.
- 코드정합: 기존 라이브러리/라우트/모듈 신규 도입 없고 파일 환경 우선순위만 명시적 확장.
- 데이터안정: 원본/고객 DB·계정·업로드 유지, 실제 고객 서버 쓰기0. 기존 비밀번호 보존.
- UI: 마크업/JS/SCSS/디자인 변경 없음, 이번 새 브라우저 화면/모바일 QA라고 주장하지 않음.
- 테스트:48+6+22 HTTP 및12 환경계약/PHP lint/패키지검사 통과. 수정본은 실서버에 직접 배포하지 않았다.

실제 서버 원인은 Apacheerrorlog를 못 읽어 아직 확정 전이다. 이 변경은 실제 재현된 코드 측 취약 조건을 보완한 것이며 실서버500 해결 완료를 보장하지 않는다. 고객이 안내한 mod_rewrite 및 Directory의 FollowSymLinks/SymLinksIfOwnerMatch가 필요하고, FileInfo조차 허용하지 않거나 다른상위지시어가 실패하면 서버 담당 조정이 남는다. FPM ini캐시/OPcache 반영 및 실제 startup보안 ini는 서버 검수가 필요하다. 공개 오류 표시/비밀 출력·원격 파일 임시교체·고객 DB초기화는 하지 않았다.

테스트 harness의 잘못된 malformed ini 예제(PHP파서가 허용함)는 실제 오류 섹션 예제로 고쳤다. Docker inspect의 동일 이름 volume과 container 구분은 --type container로 보완했다. 최종 패키지 검사에서 Windows 줄바꿈 차이는 설정 line단위 비교+실제 parser확인으로 검증, 파일/ZIP 자체 SHA는 byte기준으로 대조했다. 제품 오류와 도구 검증 문제를 구분했다.

## 산출물·정리

- 비공개 `release/partnerit-server-fix-20261105.zip`: **4,753 bytes**, SHA256 **9BFF63495B0263FDF1598E8954A514BE639136184A1299BDD76DFF59DCF7319E**.
- www/.htaccess, www/.user.ini, www/config/setup.php, www/uploads/request/.htaccess, www/.env만 포함. 안내·검사자료·SQL·백업·manifest는 ZIP밖이다. 비밀번호 포함 운영.env가 있으므로 공개/Git배포용 ZIP이 아니다.
- 압축의 www 안 내용만 기존 원격 /www에 경로대로 덮어쓰기. SQL재실행/DB초기화/전체자산교체 없음. 이전 전체정제 ZIP 위에 이5파일을 적용하는 후속 묶음이다.
- 검사 fixture/JSON/이전파일 snapshot은 비공개 release/server-fix-20261105-qa에 보관. 작업용 컨테이너/네트워크만 제거하고 retained QA volume 및 원본·타 프로젝트는 보존한다. Git push/실제 서버 배포는 하지 않았다.
