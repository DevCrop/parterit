# 서비스 상세 콘텐츠 보강 — 2026-09-16

## 요구 / 기준
- 목적: 기능 나열 중심 설명을 이용 과정과 결과 해설로 구체화.
- 공고 매칭은 탐색, AI 보고서는 판단, 컨소시엄은 연결·협업 역할.
- 기존 실제 목업과 V3 아이콘 재사용. 생성 이미지·가짜 지표 추가 없음.
- 기존 타이포 클래스, 컬러 토큰, 공통 컨테이너와 CTA 컴포넌트 사용.
- 가격·플랜별 기능은 최신 확정 전까지 변경하지 않음.
- 실제 서비스 기능 구현, API·DB·인증·결제 변경은 범위 밖.

## 적용 파일 / 호출 흐름
- views/pages/business/index.php → 기존 서비스 컴포넌트 호출 유지.
- partnerit-announcement-profile: 기업정보 입력 → 맞춤 공고 확인 → 보고서 검토 3단계.
- partnerit-ai-report-context: 기존 조건 비교 + 보고서 예시·세 가지 읽기 기준·주의사항.
- partnerit-ai-report-capability: 컨소시엄 페이지 연결 CTA.
- partnerit-consortium-matching-system: 모집/참여 관점별 추천 검토 과정. 실제 승인 절차는 명시하지 않음.
- partnerit-pricing: compact 옵션은 서비스 3페이지에만 적용. 전체 요금은 /pricing 연결.
- partnerit-final-cta: 서비스 경로별 제목·설명. 외부 무료 체험 동작 유지.
- 각 기존 SCSS 컴포넌트에서 BEM 확장. 새 의존성·인라인 스타일 없음.

## 영향 분석
- Backend: routes/web.php의 기존 경로만 소비. 수정 없음.
- Frontend: 위 PHP 7개와 해당 SCSS 4개. 기존 애니메이션·헤더/드로어 보존.
- Data: 고정 설명 배열만 수정. DB·고객 데이터 무관.
- i18n: 기존 한국어 공개 마케팅 컴포넌트 패턴 유지. 번역 서비스 변경 없음.
- Test: PHP lint, watch, Playwright 반응형·이미지·링크·가격표 회귀.
- Docs: 본 문서, components.md, patch-notes.md.

## 검수
- 3페이지 × 320/390/768/1440: document 가로 넘침0, 로드 완료 이미지 실패0, JS 예외0.
- 390/1440 섹션 캡처 직접 확인. 캡처는 고정 헤더를 일시 숨겨 본문 확인.
- PHP 수정7개 lint 통과.
- compact 플랜 안내는 3서비스에만 존재, /pricing 전체 플랜3개 유지.
- 캡처: handoff/verification-v3/content-{announcement-matching,ai-report,consortium-matching}-{390,1440}.png (디자인 워크스페이스).

## 잔여 확인
- 공식 사이트와 현재 시안의 가격/기능 차이: 고객 최종 승인 필요.
- 신뢰도 검증 방식·경쟁률 산식은 새로 단정하지 않음.
- 서비스 소개서 다운로드 링크는 기존 /business 연결 유지. 실제 다운로드 연결은 별도 확정 필요.
- 랜딩 목업은 정적인 예시이며 실제 추천·분석 실행 기능 아님.
