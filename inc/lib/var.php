<?php

/**
 * 관리자·레거시 inc 공통 옵션 맵
 * (base.class.php 등에서 로드, 기존 변수명은 템플릿·process와 호환 유지)
 */

// ── 계정 권한 (role_id → code/name) ─────────────────────────────
$admin_roles = [
    1 => ['code' => 'superadmin', 'name' => '최고 관리자'],
    2 => ['code' => 'manager',    'name' => '중간 관리자'],
    3 => ['code' => 'external',   'name' => '외부인'],
];

// ── 노출 영역: 배너·팝업 동일 코드 (banner_type / popup_type) ──
$display_page_slots = [
    1 => '메인 페이지',
    2 => '상단 배너',
    3 => '하단 배너',
];
$banner_types = $display_page_slots;
$popup_types  = $display_page_slots;

// ── 배너·팝업 폼: 링크 / 기간 / 창 타깃 ─────────────────────────
$has_link = [
    1 => '링크',
    2 => '비링크',
];

$is_unlimited = [
    1 => '무기한 노출',
    2 => '노출 기간 설정',
];

// is_target DB값: 0=_self, 1=_blank
$link_targets = [
    0 => ['label' => '현재창', 'target' => '_self'],
    1 => ['label' => '새창',   'target' => '_blank'],
];

// ── 활성 플래그 공통 (is_active 등, DB 1/0) ───────────────────
$is_active = [
    1 => '활성화',
    0 => '비활성화',
];

// ── 게시판 스킨/타입 코드 ──────────────────────────────────────
$board_type = [
    'bbs' => '게시판',
    'gal' => '갤러리',
    'new' => '뉴스',
    'not' => '공지사항',
];

// ── 첨부 허용 확장자 ────────────────────────────────────────────
$board_file_allow = [
    'jpg', 'jpeg', 'png', 'gif', 'zip', 'xls', 'xlsx', 'ppt', 'pptx',
    'doc', 'docx', 'pdf', 'hwp', 'mp4', 'mov', 'avi', 'txt', 'webp',
];

// ── 사이트 삽입 태그 위치 ───────────────────────────────────────
$tag_locations = [
    1 => 'HEAD',
    2 => 'BODY',
    3 => 'FOOTER',
];