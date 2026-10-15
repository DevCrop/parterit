# 2026-10-07 고객 피드백 반영 및 배포

## 변경과 범위

- 메인 히어로: Figma 350:8962의 고해상도 노트북 원본을 사용. 중복/깨짐이 있는 래스터 카드와 대시보드는 실제 HTML/CSS 텍스트로 재구성. 기존 position 배치와 모바일 구성, 무료체험 외부 새창 링크는 유지.
- 파트너 로고: Figma 350:10554의 image 104–117 원본 PNG 14종을 다시 내려받음. 검은 캡처 테두리가 있는 figma-logo-*는 이 영역에서 사용하지 않음. 브랜드 중복 및 무한 marquee 제거, 공통 container 안에 7/4/2열 그리드. 큰 원본 캔버스의 빈 공간만 CSS로 조정하며 브랜드 전체 표시.
- 모든 소개서 다운로드 버튼: 공통 native dialog → 필수 회사명/이름/연락처/이메일, 선택 문의 → 필수 개인정보 동의 → AJAX 저장 성공 → PDF 다운로드. 직접 파일 링크는 JS 미지원 시 기존 다운로드 폴백이며 PDF 자체를 비공개로 보호하는 기능은 아님.
- POST /brochure-requests: CSRF, 필드 검증, honeypot, session-bound unique submission key, 세션 10초 간격 및 IP당 5건/시간 제한. 오류시 다운로드 금지, 입력값 유지, 20초 fetch timeout, 중복 재시도는 동일 1건.
- GET /brochure/download: 성공 세션의 30분 토큰을 검증. PDF attachment/private no-store.
- 기존 nb_request에 7개 컬럼과 unique index를 추가. CLI migration은 먼저 DB 내부에 기존 테이블을 백업하고 재실행해도 중복 변경하지 않음. 관리자 ‘도입 문의 → 소개서·도입 문의’에서 조회/검색. 최고·중간 관리자만 접근, 외부인 403. 기존 자산진단 데이터는 유지.
- 기존 design tokens, CTA, ScrollManager, 공개 layout과 console 구조 재사용. 가이드의 admin/form 파일은 실제 저장소에 없어 공개용 partnerit-form-field primitive를 추가함. 메일/마케팅 수신/새 contact 페이지는 추가하지 않음.

## 실제 검증

- Docker localhost:8888, Chromium: 메인 1440/390/320px 렌더와 캡처. 로고 전부 로드, 가로 넘침 없음, 모달 자동 focus/Escape 닫기와 opener focus 복귀.
- /about/company, /matching, /ai-report, /consortium, /pricing, /partners: 모바일 390px HTTP 200, JS 오류 0, 가로 넘침 없음, 각 소개서 버튼의 공통 모달 연결.
- WebKit 모바일 390px: 모달 열림 확인. 실제 iPhone/Safari 단말 검증은 아님.
- 실제 AJAX POST 201 → DB 저장/동의 일시·버전 → PDF 다운로드(%PDF-). 동일 key 재요청 201이며 추가 삽입 없음. CSRF 오류 403, 잘못된 필드/봇 422, 빠른 신규 재요청 429, 임의 다운로드 토큰 403.
- UI 503 응답 시 다운로드 없음, 입력 유지, 제출 버튼 재활성화.
- 기존 tmaster 계정으로 실제 콘솔 로그인 후 목록/상세에서 회사명·이메일·문의·동의문 버전 확인. script 테스트 문자열은 escape되어 텍스트로 표시. 비로그인 302 로그인 이동, 외부인 목록·상세 403.
- QA용 신규 문의 3건(no 8–10)만 제거. 기존 문의/고객사례/관리자 비밀번호 변경 없음. 로컬 문의 원래 데이터는 0건, DB 백업 0건.
- PHP 변경 파일 16개 lint 통과, npm run build 오류 0/경고 0, diff whitespace 검사 통과. 빌드 결과 resource/dist에 반영.
- 캡처·실행 결과: C:/Users/USER/Documents/ChatGPT/파트너잇/qa-261007/ (개인정보 없는 합성 QA 데이터).

## 서버 반영 순서

1. 현 서버 파일/DB를 백업. 기존 .env와 DB 계정은 유지. .env가 가리키는 운영 DB 선택.
2. DB 변경을 먼저 적용: CLI 가능하면 `php database/migrations/261007-1600-brochure-request-fields.php`. FTP/phpMyAdmin 환경이면 `database/migrations/261007-brochure-request-fields.sql`을 기존 스키마에 **한 번만** 실행. 두 방식 중 하나만 사용. SQL은 이미 컬럼이 추가된 DB에 다시 실행하지 말 것.
3. 배포 파일 목록대로 같은 상대 경로에 덮어쓰기. resource/dist/app.js·style.css, 새 hero/로고 PNG, PDF 누락 금지. SQL/PHP migration과 docs는 웹 공개 업로드 대상이 아님. CLI migration이 필요하면 서버 비공개 작업 경로에서 실행.
4. 서버 PHP OPcache가 있으면 갱신. 페이지의 base_path 캐시버스터는 파일 mtime 기반이므로 변경파일 mtime을 갱신하고 CDN 캐시도 제거.
5. 실서버에서 모달 → 합성 테스트 제출 → 관리자 조회 → PDF 다운로드 재확인. 현 작업은 로컬만 반영했고 실서버 배포/운영 DB 변경은 수행하지 않음.

## 운영 확인사항

- 개인정보 수집을 운영에 열기 전 HTTPS를 적용할 것. 기존 HTTP 환경의 통신 암호화 문제를 CSRF나 AJAX가 해결하지는 않음.
- config/project_profile.php의 brochure.retention/consent_version과 동의문은 고객의 실제 보유·파기 운영 기준에 맞춰 확정해야 함. 자동 파기 배치 기능은 이번 범위에 없음.
- 접수는 관리자 DB에서 확인하며 메일은 발송하지 않음. 메일 알림이 필요하면 수신자·SMTP 및 운영 정책을 별도 설정.
- content.brochure_request=false이면 모달/접수 API를 비활성화하고 기존 직접 PDF 다운로드로 돌아감. DB 추가 컬럼은 그대로 유지.
