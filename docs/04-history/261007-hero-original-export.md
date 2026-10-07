# 2026-10-07 히어로 원본 재추출 정정

## ① 요구사항
- 원하는 것: 기존 Figma 히어로 이미지 그대로 고해상도 추출.
- 현재 문제: 이전 HTML 재구성으로 맥북 내부와 카드 디자인이 원본과 달라짐.
- 기대 동작: 원래 화면·보라색 메일·리포트·매칭 카드 유지, 텍스트 넘침 제거.
- 제외: 로고 그리드, 다운로드 폼, AJAX, DB, 문구 변경.
- 제약: Figma 원본 폰트 일부 서버에서 로드 불가. 텍스트 수정/대체 없이 복제·클리핑 및 렌더 사용.
- 원본 보존: 350:8962는 유지, 내보내기 복제본 490:1081의 목록 490:1132에만 clipsContent 적용.

## ② 영향 분석
- Backend: PHP 컴포넌트의 이미지 출력만 변경, 라우트/API 유지.
- Frontend: hero-preview.php, 전용 SCSS import, 기존 _hero.scss 배치 재사용.
- Data: DB 변경 없음. Figma 4개 노드를 3배 PNG로 추가.
- i18n: 문구 변경 없음, 기존 시각 영역 aria-label 유지.
- Test: PHP 문법, 빌드, 1440/390/320 화면·이미지 해상도·넘침 검증.
- Docs: 본 기록 및 배포 파일 목록에 추출 근거/검증 결과 기록.

## ③ 변경 계획
1. 수정 파일: views/components/partnerit-hero-preview.php, resource/scss/components/index.scss, 불필요한 _partnerit-hero-preview.scss.
2. 연관 파일: hero.php 호출 유지, _hero.scss 기존 반응형 absolute 배치 유지.
3. 호출 흐름: 메인 → hero.php → hero-preview.php → 원본 이미지 4개.
4. DB 영향: 없음.
5. 기능 영향: CTA·다운로드·모달·로고 변경 없음.
6. 리스크: 원본 자체의 숨겨진 메타데이터가 목록 밖으로 넘침. 복제본 목록 클리핑으로 해결. 작은 모바일 글씨는 원본 비율 유지.
7. 테스트 계획: 빌드/PHP lint/4개 이미지 로드/중복 DOM 없음/가로 넘침 및 화면 캡처.
8. 문서 업데이트: 이 파일에 실제 결과 추가, 수정 파일 배포 목록 작성.

## ④–⑦ 실제 구현·검증·기록
1. 요청: 원본 재추출이며 UI 재구성이 아니라는 사용자 정정 반영.
2. 원인: 이전 구현이 원본 대신 HTML 화면을 만들었음. Figma 목록의 350:9030/9048/9065 메타데이터는 요약과 중첩되고 목록 밖으로 넘쳤음.
3. 처리: 복제본 490:1081에서만 해당 세 레이어 숨김 및 목록 클리핑. 원본 화면·문구·색상·배치 유지.
4. 에셋: Figma exportAsync PNG SCALE 3, contentsOnly true 직접 내보내기로 투명도 보존. download_assets 렌더는 캔버스 배경이 들어가 최종본으로 쓰지 않음.
5. 규칙: #1 기존 히어로 컴포넌트 재사용, #5 인라인 스타일 없음, #10 사용하지 않는 임의 UI SCSS/import 제거, #19 페이지는 기존 컴포넌트 호출 유지.
6. 실제 파일: views/components/partnerit-hero-preview.php 이미지 4개, resource/scss/components/_hero.scss 기존 배치, index.scss 불필요 import 제거.
7. 기능·UI 검사: Playwright localhost 1440/390/320에서 이미지 4개 로드, 모든 이미지 visual 내부, document 가로 넘침 없음, 커스텀 dashboard/card DOM 0개. 1440/390 캡처 육안 검사.
8. 코드·데이터 검사: npm run build 0 errors/0 warnings, docker exec PHP lint 통과, git diff --check 통과. DB/API 변경 없음.
9. 에셋 해상도: 맥북 2400×1541, 메일 753×807, 리포트 774×690, 매칭 969×774. 기존 원본 및 다른 작업 파일 삭제하지 않음. 제거한 SCSS는 Git으로 복구 가능.
10. 한계: 맥북 하드웨어는 원본 래스터의 디테일 한계가 있음. UI는 원본 Figma 렌더로 고해상도 재추출했으며 새 내용을 만들지 않음. 실서버 업로드는 수행하지 않음.
