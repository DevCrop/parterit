# 2026-11-05 실제 서버 고객사례 상세500·필수확장 점검

## 결과

고객이 업로드한 수정본 이후 메인/소개/서비스/요금/사례목록/법적페이지는 정상200으로 확인됐으나 기존 고객사례 상세13건은500이다. 실제 PHP-FPM에서 인증된 일회성 읽기 진단을 수행해 **PHP8.4.27/fpm-fcgi, mbstring 미로드와 Call to undefined function mb_substr() 예외를 직접 확인**했다. 사례11 데이터와 본문 컴포넌트는 존재하며 관계파일6개가 소스와SHA일치한다.

소개서의 토큰이 맞는 빈필수값 입력 검증도500이다. 해당 서비스는 storage 이전에 mb_strlen을 사용하고 실제 mbstring이 없으므로 같은 확장 누락의 영향을 받는다. 실제 제출/DB쓰기 검사는 하지 않았다. gd/zip/dom도 웹FPM에서 미로드 확인. 고객 서버 관리자의 확장 설치·활성화와FPM재시작이 필요하며 SFTP 소스 교체로 해결되는 항목이 아니다.

## 적용 규칙·실제 파일·영향

#16 계획 기록 후 제한 진단/공개 경로·자산 검수/자료 대조/문서화. SERVER·DEV 범위로 제품 기능/DB/고객 계정은 변경하지 않았다. Backend는 show.php의mb_substr, BrochureRequestService.php의mb_strlen 및 Composer필수확장을 확인. Frontend는 detail/data/summernote컴포넌트·실제자산, Data는기존사례SELECT만, i18n변경없음, Test는서버실행진단/GET·읽기전용확장검사/빈폼검증, Docs는본보고·명세·인수인계·히스토리.

실제 확인 경로:
- views/pages/partners/show.php:8 — 사례 SEO description mb_substr 호출.
- src/Request/BrochureRequestService.php:42 — validation mb_strlen, storage 전 실패.
- views/components/partnerit-story-detail.php — summernote-content 포함.
- views/components/summernote-content.php — 실제원격존재/소스일치.
- views/components/partnerit-stories-data.php — 실제사례11존재 확인.
- composer.json — ext-fileinfo/ext-gd/ext-mbstring/ext-pdo_mysql 요구.
- inc/lib/SummerNote.php:17 — DOMDocument를 사용하는 관리자 본문 처리, dom/XML 필요.

## 실제 웹 FPM 상태

|항목|결과|
|---|---|
|PHP|8.4.27|
|SAPI|fpm-fcgi|
|pdo_mysql|로드됨|
|fileinfo|로드됨|
|exif|로드됨|
|mbstring|미로드|
|gd|미로드|
|zip|미로드|
|dom|미로드|

이번확인은CLI가아닌실제웹요청으로확정했다. startup보안ini나모든PHP옵션값까지확인했다고주장하지않는다. mbstring은현재상세500의확정원인, gd/zip/dom은추가필수환경누락으로관리자이미지/본문등의실패가능성이있다. 해당관리자쓰기는실행하지않았으므로실패를직접재현했다고표시하지않는다.

## 실제 검증

- 메인/회사/공고/AI/컨소시엄/요금/고객사례목록/개인정보/약관/이메일수집거부10페이지HTTP200, title존재, 오류문자열0. 별도로console로그인페이지200, favicon200, .env403.
- 페이지동일출처자산124개 중 독립읽기전용에이전트가우선100개검사200/Content-Type정상. 부모가남은24개를검사하여모두200확인. 썸네일업로드PNG3개도정상. 새브라우저/모바일시각검사는수행하지않았다.
- 기존고객사례13개ID6,7,8,11~20 상세500. 존재하지않는사례는notfound문구200으로돌아와실제사례렌더경로와구분했다.
- token이맞고필수값이빈소개서POST는storage전500. 외부/실제개인정보사용없음. 문의행INSERT/수정/삭제없음.
- 이전 /about /business 및서비스긴경로는현재주소로정상리디렉트. /contact는기존외부플랫폼로그인으로302.
- 인증진단파일에서해당detail View를직접렌더해 story_found=true, detail_rendered=false, exception Error/Call to undefined function mb_substr() 확인. 렌더HTML/자격증명/원문예외trace는반환하지않았다.
- remote show/detail/summernote/data/setup/BrochureService6개파일존재+SHA가현재소스와일치. 초기로컬release/www의컴포넌트부재는사용자가해당폴더를최신5파일업데이트용으로바꾼데따른것이며원본전체ZIP누락이아니다. 원본ZIP에서해당컴포넌트포함확인. 잘못된중간판단을사용자에게즉시정정했다.

## 임시 진단 안전·정리

사용자요청의제한진단으로기존파일과겹치지않는고유PHP파일을SFTP로잠시올렸다. 파일존재없음확인후업로드, 임의256bit토큰의hash인증, 필수확장bool/파일hash/비밀없는예외종류만반환. phpinfo/DB접속정보/개인정보를반환하지않고공개오류설정을켜지않았다. SQL은기존렌더의SELECT뿐이다.

진단직후정확한원격파일한개만SFTP삭제, ls not found확인, SFTP종료. 같은URL은서버의알수없는주소처리에따라메인HTML200을반환했으므로404를삭제조건으로오인하지않고진단JSON이없음을검증했다. 실제probe는남아있지않다. 고객사이트기존소스/권한/환경/DB계정수정없음. 로컬진단·토큰자료는qa/server-detail-20261105에만있고Git/배포파일에는없다.

## 조치와 남은 검증

서버담당자에게 **실제PHP8.4-FPM의mbstring,GD,ZIP,DOM(XML)설치/활성화및FPM재시작**을요청한다. CLI만활성화한결과로대체하지않는다. 이후실제사례제목/본문정상, 빈폼validation422, 소개서가상신청/다운로드, 관리자이미지/본문동작을최종재검수해야한다. 현재상세/소개서500은해결완료가아니며우리SFTP계정으로서버모듈을설치할수없다. 기능을바이트절단/우회하여필수확장누락을숨기는패치는하지않았다.

## 10항목 이력

1. 날짜2026-11-05.
2. 작업: 실제서버 partners/11 상세500 및다른경로검수.
3. 목적: 오류확정및실제배포환경누락분리.
4. 변경: 제품코드없음, 임시인증진단파일업로드/즉시삭제, 문서갱신.
5. 연관: detail/seo/입력validation/필수PHP확장/공개자산.
6. 결과: mb_substrundefined로상세500확정, 웹mbstring/GD/ZIP/DOM누락.
7. 영향: 고객DB쓰기없음·기존파일/계정보존, UI변경없음.
8. 한계: 서버확장활성화대기, 실제관리자쓰기/모바일시각검사미수행.
9. 검증: 주요10페이지/자산124정상, 상세13오류, 파일6SHA일치, 임시파일삭제검증.
10. 전달: 서버담당자용필수확장요청및나머지정상범위보고. 신규배포ZIP/SQL/코드push없음.
