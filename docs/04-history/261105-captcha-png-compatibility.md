# 2026-11-05 GD 미로드 캡차 PNG 호환 수정

## 요구·범위와 영향

사용자가실서버관리자캡차이미지이상보고. 직접GET한captcha/admin.php/default.php는각500/HTML/0byte였고원본코드첫imagecreatetruecolor가GD를요구한다. 앞선실제FPM진단에서GD미로드를확인했다. #16 사전계획/백업후최소코드수정·검증·문서화, DEV/SERVER 범위. RISK번들검토: 인증검증의fail-closed조건만보완하며삭제/로그아웃/백업복원같은위험액션/새UI추가는없어확인모달대상아님. 계정/비밀번호/역할/잠금정책·DB보존.

Backend: 5자리랜덤challenge·세션·형식/상수시간비교, Frontend: 기존PNG/MIME/크기및cache금지, Data: 실제고객DB쓰기없음/격리QA SELECT로그인, i18n숫자형식동일, TestGD/무GD/무zlib·PNGdecode/로컬Auth, Docs본보고·명세·이력·handoff.

## 실제 변경4파일

- inc/lib/helper/CaptchaHelper.php 신규: 서버random_int로5자리생성, PARTNERITSESSID/captcha_secure사용. GD없는경로의100x30/120x30 RGB raster PNG를bitmap숫자로생성. PHP gzcompress가있으면사용, 없으면64KiB미만stored DEFLATE와core hash Adler32/CRC로유효PNG생성. 자의적치수/색/코드거부. PNG는IHDR/IDAT/IEND만있으며SVG/text메타데이터/클라이언트정답출력없음.
- captcha/admin.php/default.php: 공용helper와no-store/nosniff, GD함수없을때만대체PNG. GD있으면기존imagechar렌더그대로사용. 어드민100x30/기본120x30배경유지.
- console/lib/Auth/LoginProcessor.php: helper명시require와세션/입력모두ASCII숫자5자리+hash_equals일치검사. 기존빈세션/빈입력둘다같다는이유로검증을통과할수있던조건을수정. SQL·uid/password_verify·실패잠금·역할·redirect는변경하지않음.

## 실제 수행 검증

1. php:8.4.27-fpm에서GD/imagecreatetruecolor없음확인. 알려진단위fixture01234/56789 ×100/120폭 ×gzcompress유무로8개PNG생성. 독립Pythonzlib/Pillow로PNG서명·chunk CRC·크기/픽셀색·image.load 확인. stored DEFLATE형식도정상. 텍스트메타데이터chunk0. 단위fixture이미지를직접시각확인해잘림없는읽을수있는5자리bitmap확인. 실제서버CAPTCHA를해독/제출하지않음.
2. helper검증11조건: 정상/leadingzero승인, 빈/누락/배열/정수/길이오류/전각/개행/오답거부. 렌더인자4가지부적합조건거부.
3. 실제Apache→PHP8.4.27-FPM(GD없음, 기존65mb함수도비활성)과별도nativeGD PHP8.4.27이미지서버를실행. 두분기의admin/default이미지200/image/png, 정확한치수, no-store/nosniff, Cookie ID/기존세션marker보존·5자리코드저장확인. 이미지cache때문에세션코드가어긋나지않도록확인.
4. 로컬의명시적knownsession fixture만사용해로그인검증: 세션코드없음+빈입력→captcha에러redirect, 오답→동일거부, 알려진5자리fixture+기존로컬tmaster비밀번호→기존board.list redirect/로그인후목록200. 총HTTP25통과. 실제고객서버로그인/실제CAPTCHA자동풀이/계정변경없음.
5. PHP4개lint0, 작업전snapshot대비affected.patch검토. 현재소스/QAstage/전달4파일byte/SHA일치, 실제접속비밀값0. ZIP4파일CRC/testzip/내용SHA통과.

## 데이터·보안·UI 한계

실제서버는읽기GET으로문제응답만확인했다. 서버파일직접변경/임시probe업로드/DB쓰기/비밀번호변경없음. QA는기존소유권있는로컬MySQL8.0.45 fixture와PHP세션만사용했고다른프로젝트는건드리지않음. 로그인화면HTML/CSS변경없고이미지크기유지. 이번에는로컬단위이미지시각확인/HTTP검사이며새Chrome·모바일시각검수아님.

CAPTCHA는기존과같은5자리엔트로피/세션연동을유지하며그림을SVG text로바꾸거나정답을JSON으로클라이언트에반환하지않았다. 알려진로컬fixture는QA폴더에만있고ZIP/실서버에없다. 비밀번호/역할검증은유지, 빈captcha는명시적으로거부한다. 이미지처리일반/에디터의GD/DOM/ZIP요구를없앤것은아니다. 실제업로드뒤이미지·로그인후속확인이필요하고OPcache/서버정책이변경반영을막으면서버담당검수가남는다.

## 산출물·정리

- partnerit-captcha-fix-20261105.zip **5,340 bytes**, SHA256 **AF468439F8EF035D5B99C4774D96DDA48424C90A31DE9BAF5D11DF35035C05F2**.
- 압축www안4파일만기존/www에동일경로덮어쓰기. 새helper와LoginProcessor를같이올려야한다. SQL/.env/DB/계정변경없음.
- 로컬PNG/JSON/fixture/affected.patch는qa/captcha-fallback-20261105에보관. ZIP에테스트/키/설정/PDF/SQL미포함. 작업용4컨테이너·2네트워크만제거하고volume/타프로젝트보존. 원격배포/Gitpush미수행.

## 10항목 히스토리

1. 날짜2026-11-05.
2. 작업: 관리자GD없는캡차500소스호환.
3. 목적: 이미지정상출력과5자리세션검증유지.
4. 변경: helper/admin/default/LoginProcessor4PHP.
5. 연관: cookie/captcha_secure·기존Auth쿼리/redirect.
6. 내용: 순수PHP raster PNG fallback/no-cache/빈captcha거부.
7. 영향: 고객계정/DB/스타일/기존GD이미지보존.
8. 한계: 실서버적용전·일반이미지기능의필수확장은별도.
9. 검사: 8PNG독립decode/11검증/4인자거부/HTTP25/4lint/ZIP·diff.
10. 전달: 4파일ZIP만덮어쓰기, DB·.env재실행없음.
