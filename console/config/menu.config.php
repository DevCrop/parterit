<?php
/**
 * 관리자 메뉴 설정
 * - admin.drawer.php: 메뉴 렌더링
 * - PageContext: URL 기반 depthnum/pagenum/pageName 계산
 *
 * 새 메뉴를 추가할 때:
 * 1. depthnum에 맞는 subs 항목 추가
 * 2. 상세/추가 페이지는 PageContext::getRouteOverrides()에 경로 등록
 */
return [
    1 => [
        'key' => 'board',
        'title' => '게시판',
        'icon' => 'fa-list',
        'subs' => [
            ['title' => '게시글 관리', 'url' => 'board/board.list.php'],
        ],
    ],
    2 => [
        'key' => 'design',
        'title' => '디자인',
        'icon' => 'fa-paint-roller',
        'subs' => [
            ['title' => '팝업 관리', 'url' => 'design/popup.list.php'],
            ['title' => '배너 관리', 'url' => 'design/banner.list.php'],
        ],
    ],
    3 => [
        'key' => 'request',
        'title' => '신청 관리',
        'icon' => 'fa-inbox',
        'hidden' => true,
        'subs' => [
            ['title' => '자산진단 신청', 'url' => 'request/request.list.php'],
        ],
    ],
    5 => [
        'key' => 'siteinfo',
        'title' => '사이트정보관리',
        'icon' => 'fa-gear',
        // 'role' 키가 있는 sub는 admin.drawer.php에서 해당 role만 노출.
        // 키가 없으면 권한 게이트 통과 (기존 동작 유지).
        'subs' => [
            ['title' => '사이트 정보', 'url' => 'setting/index.php'],
            ['title' => 'SEO 관리', 'url' => 'setting/seo.config.php'],
            ['title' => '페이지별 SEO', 'url' => 'setting/seo.php'],
            // ['title' => 'robots.txt 관리', 'url' => 'setting/robots.php', 'role' => 'superadmin'], // hidden — page kept, drawer entry hidden
            ['title' => '사이트 메타 태그', 'url' => 'setting/external.tag.php', 'role' => 'superadmin'],
            ['title' => '비밀번호 변경', 'url' => 'setting/pwd.php'],
        ],
    ],
    6 => [
        'key' => 'account',
        'title' => '계정 및 권한 관리',
        'icon' => 'fa-user-shield',
        'subs' => [
            ['title' => '계정 관리', 'url' => 'account/index.php'],
            ['title' => '계정 생성', 'url' => 'account/new.php'],
        ],
    ],
];
