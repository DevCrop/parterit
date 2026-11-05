# 2026-11-05 공개 고객사례·소개서 UTF8 호환 수정

## 요구·적용 규칙·영향

사용자가 서버확장활성화대신코드수정가능여부를요청하여현재상세/소개서500의명시적mb함수호출을공용UTF8헬퍼로대체했다. #16 계획/원본snapshot 후수정·검증·문서화, DEV/SERVER 범위. 새라이브러리/전역mb함수polyfill/환경·DB정책변경없음.

Backend: BrochureRequestService의문자수검증/user-agent255자저장. Frontend: 사례메타설명150자처리만변경, 실제본문/레이아웃동일. Data: 마이그레이션·실제고객DB쓰기없음, 격리QA DB에서가상신청만검사/삭제. i18n:UTF8한글/영문/이모지/codepoint보존. Test: 기본및모든mb함수비활성경로의동일결과, 실제FPM/본문·경계validation·PDF. Docs: 본기록/명세/인수인계/요구변경/patch-notes.

## 실제 변경3파일

- `inc/lib/helper/TextHelper.php`: utf8Length는유효UTF8에대해문자수(int), malformed는null. mb_strlen이있으면UTF8명시native, 없으면PCRE /./us 카운트. utf8Prefix는mb_substr이있으면native, 없으면분할한도를가진PCRE분할로첫N codepoint결합. 바이트substr로한글을자르지않음. 잘못된UTF8prefix는빈문자열, 음수한도는명시예외, 큰한도overflow방지.
- `views/pages/partners/show.php`: SEO설명150자에공용헬퍼사용.
- `src/Request/BrochureRequestService.php`: 필드문자수·User-Agent255자에공용헬퍼사용. malformed입력은422검증오류, 나머지필수/CSRF/동의/한도/중복방지/SQL/PDF정책유지.

기존TextHelper autoload를재사용했고mbstring확장요구를프로젝트전체에서제거하지않았다. 코드포인트단위는기존mb_substr/strlen UTF8의의미를유지하며grapheme단위자르기를새로도입한것은아니다. GD/DOM/ZIP의이미지/본문등관리자처리는여전히서버확장필요하고,이번3파일밖의기존mb호출기능도남는다.

## 실제 수행 검증

1. 한국어/영문/이모지/skin-tone/ZWJ/variation selector/결합문자/개행/tab/NUL/빈값과결정적120개혼합문자열, 양/음/0/아주큰한도, 잘못된UTF8검사. Python Unicode codepoint 기준의기대길이/prefix와비교하여 **기본1,119 + fallback1,119** 통과.
2. Docker공식PHP이미지는mbstring이정적으로컴파일돼 php -n만으로모듈이사라지지않는것을확인했다. 따라서fallback검사는65개모든mb_*함수를PHP시작설정에서비활성화하여 function_exists가false인것을명시확인했다. 확장등록flag가false인환경을재현했다고표시하지않으며현재코드는함수존재로분기하므로같은실패조건을검사한다.
3. 공식PHP8.4.27-fpm에pdo_mysql만추가컴파일한전용이미지와Apacheproxy_fcgi/FileInfo-only·MySQL8.0.45의소유권있는QA volume을연결했다. **실제fpm-fcgi에서mb_strlen/mb_substr없는상태** 확인 후동작검사. 고객DB/서버는접속·수정하지않음.
4. 정제전체ZIP SHA256을검증해안전한새폴더에복원하고현소스/기존서버호환수정+가상QA.env만overlay. 사용자가release/www를업데이트파일용으로바꾼것을정본전체소스로오인하지않았다.
5. **고객사례13개 ID6,7,8,11~20의HTTP200뿐아니라h1제목과전체본문을QA DB의실제내용과비교하여일치**, no-prose본문존재/100자초과/notfound문구없음 확인. 이전의상태코드만통과하는검증을대체했다.
6. 메인·회사·서비스·요금·고객사례목록·법적페이지·관리자로그인화면정상. .env403/소개서무토큰403.
7. 소개서CSRF403, 빈값422, 회사명한글101자422, 메시지emoji2001자422, 배열입력422, 동의없음422, 잘못된UTF8필드422. storage전에검증실패하며신청행0 확인.
8. 회사명한글100자/이름100자/메시지emoji2000자정상신청201, UTF8 emoji User-Agent255자보관을MySQL CHAR_LENGTH로확인. 동일키재요청201/신청1행유지, PDF서명/ContentType200, 다른세션403. 가상신청만삭제후최종0행. 총 **HTTP38요청통과**.
9. 변경PHP3개문법오류0, 실제소스/QA/전달파일byte일치, 실제접속비밀코드0. affected.patch를작업전snapshot과대조하고기존변경보존. SQL/.env/JS/CSS변경없고불필요프런트build안함.
10. ZIP www/아래3파일만, CRC/testzip/각파일SHA일치. 소스나원격에진단endpoint를추가하지않았으며QA진단은로컬stage만포함하고ZIP에서제외했다.

## 실제 검증 자료와 5블록

비공개 qa/mbstring-fallback-20261105에unicode-cases.json/test-unicode.php/unicode-validation.json, no-mb-functions.ini, Dockerfile.fpm, check-http.py/http-validation.json, before snapshot/affected.patch 및runtime metadata보관. 원본자료/Git에는운영비밀미포함. 기능은상세13/소개서정상경로, 코드정합은공용헬퍼/기존계약, 데이터안정은실제고객변경0/격리신청정리, UI는마크업·스타일변경없음/새시각검사미실시, 테스트는UTF8 2,238검사+HTTP38+lint/ZIP완료.

## 배포·한계

- `partnerit-detail-brochure-fix-20261105.zip`: **4,488 bytes**, SHA256 **86624316027BFD08633B85215B4508BBA0A27FCFA64F5990276831F9F00A5D85**.
- www/inc/lib/helper/TextHelper.php, www/views/pages/partners/show.php, www/src/Request/BrochureRequestService.php만포함. www안내용만기존원격/www에동일경로덮어쓰기.
- 기존서버호환수정본위에적용하는추가수정. DB/.env/SQL재실행없음. 직접서버배포/Gitpush안함. 고객업로드뒤실제13상세/validation422/PDF후속확인이남아있다.
- 이번공개기능호환과GD/DOM/ZIP·다른기존mb함수경로의필수서버확장을구분. 관리자이미지/본문작성을모두해결했다고표시하지않는다.
- 작업용DB/FPM/Apache컨테이너와네트워크만삭제, 소유권있는retained QA volume과타프로젝트보존.

## 작업 이력

1. 날짜2026-11-05.
2. 작업: 현재공개기능mbstring예외코드호환수정.
3. 목적: 모듈없는고객환경의사례/소개서500완화.
4. 변경: TextHelper/show/BrochureService3파일.
5. 연관: 기존autoload/Unicode검증/SEO·AJAX·PDF.
6. 결과: codepoint안전prefix/길이·malformed422/나머지기존정책보존.
7. 영향: 실제고객서버/DB변경0, 문구·레이아웃·계정보존.
8. 한계: 다른필수확장/기존mb사용처는남아있고원격적용전.
9. 검사: native/fallback각1119, 실제FPM HTTP38·본문13·PHP3lint/패키지검사.
10. 전달: 별도3파일ZIP, DB재실행없음. 실제배포후최종확인필요.
