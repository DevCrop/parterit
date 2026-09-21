---
title: 배포 환경 / 서버 명세
categories: [ops]
desc: 이 프로젝트의 배포 환경, 인프라, 도메인 정보
related: [overview.goals]
updatedAt: [REPLACE: YYYY-MM-DD]
---

# 배포 환경 / 서버 명세

## 도메인

- 공개 사이트: `[REPLACE: https://example.com]`
- 관리자: `[REPLACE: https://example.com/console]`
- 상태: `[REPLACE: 미확정 / 확정]`

## 서버

- OS: `[REPLACE: Ubuntu 22.04 / CentOS 등]`
- 웹서버: `[REPLACE: Apache / Nginx]`
- PHP: `[REPLACE: 8.x]`
- DB: `[REPLACE: MySQL 8.x / MariaDB 10.x]`
- Node: `[REPLACE: 18.x (빌드 전용)]`

## 배포

- 방식: `[REPLACE: git pull / CI/CD / FTP 등]`
- 브랜치 전략: `[REPLACE: main → production]`
- 빌드 명령: `[REPLACE: npm run build]`

## 환경 변수 (.env)

```
APP_ENV=[REPLACE: production]
APP_URL=[REPLACE: https://example.com]
DB_HOST=[REPLACE]
DB_NAME=[REPLACE]
DB_USER=[REPLACE]
DB_PASS=[REPLACE]
```

## 접속 정보

- SSH: `[REPLACE: user@host -p port]`
- 담당자: `[REPLACE: 이름·연락처]`

## 주의사항

- `[REPLACE: 예) 파일 업로드 경로, 권한 설정 등]`
