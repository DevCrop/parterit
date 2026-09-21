<?php

// bootstrap globals 안전 기본값 — 직접 호출/세션 만료 시 Undefined Warning 차단
$NO_IS_SUBDIR         = $NO_IS_SUBDIR         ?? '';
$NO_ADMIN_PAGES_BASE  = $NO_ADMIN_PAGES_BASE  ?? '';

// depthnum/pagenum/pageName 미설정 시 PageContext에서 URL·Role 기반 자동 감지
if (!isset($depthnum)) {
    require_once __DIR__ . '/../lib/PageContext.php';
    $ctx = PageContext::fromRequest($role);
    $depthnum = $ctx->depthnum;
    $pagenum = $ctx->pagenum;
    $pageName = $ctx->pageName;
}

$restrictedMenuKeys = $role->getRestrictedMenuKeys();
$BASE_URL = $NO_ADMIN_PAGES_BASE . '/';

$gnbActive = array_fill(1, 8, "");
$pageActive = [];
$pagenum = $pagenum ?? 0;

// 메뉴 정의 (단일 소스: config/menu.config.php)
$menus = require __DIR__ . '/../config/menu.config.php';

// 메뉴 활성화 설정 함수
function setActive(&$gnbActive, &$pageActive, $depth, $page, $gnbIndex, $key, $subCount = 0)
{
    $gnbActive[$gnbIndex] = "active";

    if ($subCount > 0) {
        $pageActive[$key] = array_fill(0, $subCount, "");
        if ($page > 0 && $page <= $subCount) {
            $pageActive[$key][$page - 1] = "active";
        }
    }
}

// 현재 페이지 정보로 활성화 설정 (외부인+사이트정보는 노출 서브 개수로 active 계산)
if (isset($menus[$depthnum])) {
    $menu = $menus[$depthnum];
    $subCount = isset($menu['subs']) && is_array($menu['subs']) ? count($menu['subs']) : 0;
    if ($menu['key'] === 'siteinfo' && $role->is('external')) {
        $subCount = 2; // 비밀번호 변경, 페이지별 SEO
    }
    setActive($gnbActive, $pageActive, $depthnum, $pagenum, $depthnum, $menu['key'], $subCount);
}
?>

<aside class="no-sidebar">
    <h1 class="no-sidebar-logo no-sidebar-logo--wireframe">
        <a href="<?= $NO_ADMIN_PAGES_BASE ?>/board/board.list.php">
            <span class="no-logo--default">파트너잇</span>
            <span class="no-logo--sm">P</span>
        </a>
        <div class="no-sidebar-toggle"><span><i class="bx bx-chevrons-left"></i></span></div>
    </h1>

    <div class="no-sidebar-menu">
        <nav class="no-sidebar-menu__inner">
            <ul class="no-menu-list">
                <?php foreach ($menus as $index => $menu): ?>
                <?php
                    $displayMenu = $menu;
                    // 외부인: 사이트정보관리 메뉴는 조회 가능 항목 + 비밀번호 변경만 노출 (사이트 정보 수정은 숨김)
                    if ($menu['key'] === 'siteinfo' && $role->is('external')) {
                        $displayMenu = array_merge($menu, ['subs' => [
                            ['title' => '비밀번호 변경', 'url' => 'setting/pwd.php'],
                            ['title' => '페이지별 SEO', 'url' => 'setting/seo.php'],
                        ]]);
                    }
                    if ($displayMenu['key'] === 'account' && !$role->canView()) continue; // 계정 및 권한 관리: canView()만
                    // 외부인은 siteinfo 중 '비밀번호 변경'만 보이므로 제한 목록에서 예외
                    if (in_array($displayMenu['key'], $restrictedMenuKeys) && !($displayMenu['key'] === 'siteinfo' && $role->is('external'))) continue;
                    if (isset($displayMenu['hidden']) && $displayMenu['hidden']) continue; // 가림처리된 메뉴 제외
                    if (!$role->canViewMenu($displayMenu['key'])) continue;
                    $hasSub = !empty($displayMenu['subs']);
                    ?>
                <li class="no-menu-item <?= $gnbActive[$index] ?? '' ?>">
                    <?php if ($hasSub): ?>
                    <span class="no-menu-link">
                        <span class="no-menu-icon"><i class="fa-solid <?= $displayMenu['icon'] ?>"></i></span>
                        <span class="no-menu-title"><?= $displayMenu['title'] ?></span>
                        <span class="no-menu-arrow"><i class="bx bx-chevron-down"></i></span>
                    </span>
                    <ul class="no-menu-sub">
                        <?php foreach ($displayMenu['subs'] as $i => $sub): ?>
                        <?php
                                    // sub 항목별 role 가드 (menu.config.php의 'role' 키)
                                    if (isset($sub['role']) && !$role->is($sub['role'])) continue;
                                    // 외부인: 문의 설정 등 레거시 서브메뉴 미노출(호환)
                                    $subUrl = $BASE_URL . $sub['url'];
                                    $isActive = $pageActive[$displayMenu['key']][$i] ?? '';
                                    ?>
                        <li class="no-menu-item">
                            <a href="<?= $NO_IS_SUBDIR . $subUrl ?>" class="no-menu-link <?= $isActive ?>">
                                <span class="no-menu-bullet"><span class="no-menu-bullet-dot"></span></span>
                                <span class="no-menu-title"><?= $sub['title'] ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <?php $menuUrl = isset($displayMenu['url']) ? $displayMenu['url'] : ''; ?>
                    <?php if ($menuUrl !== '' && strpos($menuUrl, '/') !== 0): ?>
                    <?php $menuUrl = $NO_ADMIN_PAGES_BASE . '/' . ltrim($menuUrl, '/'); ?>
                    <?php endif; ?>
                    <a href="<?= $menuUrl ?>" class="no-menu-link">
                        <span class="no-menu-icon"><i class="fa-solid <?= $displayMenu['icon'] ?>"></i></span>
                        <span class="no-menu-title"><?= $displayMenu['title'] ?></span>
                    </a>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>

    <div class="no-sidebar-guide">
        <button type="button" class="no-guide-trigger" id="guideOpen">
            <span class="no-menu-icon"><i class="fa-solid fa-circle-question"></i></span>
            <span class="no-menu-title">관리자 가이드</span>
        </button>
    </div>
</aside>

<?php
// 드로어에서 실제 노출되는 메뉴 키 목록 (가이드 항목 필터용)
$_visibleMenuKeys = [];
foreach ($menus as $_m) {
    if (isset($_m['hidden']) && $_m['hidden']) continue;
    if ($_m['key'] === 'account' && !$role->canView()) continue;
    if (in_array($_m['key'], $restrictedMenuKeys) && !($_m['key'] === 'siteinfo' && $role->is('external'))) continue;
    if (!$role->canViewMenu($_m['key'])) continue;
    $_visibleMenuKeys[] = $_m['key'];
}

// 개발사 지원 이메일 (admin.footer.php와 동일 소스)
$_guideSupportEmail = function_exists('project_config') ? trim((string) project_config('admin.support_email', '')) : '';

/**
 * 가이드 콘텐츠 스키마 (sub 단위):
 * - title      : 소제목 (raw HTML — 이모지 포함)
 * - url        : 바로가기 대상 (pages 기준 상대 경로) / link_label : 버튼 문구
 * - allowed    : 노출 역할 제한 배열 (omit 시 전체) — 차단 역할에겐 흐림 + 🔒 배지
 * - steps      : <ol> 단계 목록 (raw HTML)
 * - fields     : ['head' => [...], 'rows' => [[셀,...], ...]] 설명 표 (raw HTML, 첫 셀은 행 제목)
 * - callouts   : [['warn'|'tip'|'info', 'raw HTML'], ...] 강조 박스
 *
 * 'start' 키는 메뉴와 무관하게 항상 노출.
 */
$_guideMap = [
    'start' => [
        'icon' => 'fa-rocket',
        'chip' => '시작하기',
        'title' => '시작하기 — 처음이라면 여기부터',
        'desc' => '관리자 콘솔에 처음 접속했을 때 알아야 할 기본 사용법입니다.',
        'subs' => [
            [
                'title' => '🔑 로그인하기',
                'steps' => [
                    '브라우저 주소창에 <code>https://(도메인)/console</code> 입력',
                    '<strong>아이디</strong>·<strong>비밀번호</strong> 입력 후, 이미지에 보이는 <strong>보안코드(숫자)</strong>를 그대로 입력',
                    '<strong>[로그인]</strong> 클릭 → 첫 화면으로 <strong>게시글 관리</strong>가 열립니다',
                ],
                'callouts' => [
                    ['warn', '비밀번호를 <strong>5회</strong> 틀리면 <strong>15분간 로그인이 잠깁니다</strong>. 15분 뒤 다시 시도하세요.'],
                ],
            ],
            [
                'title' => '🧭 메뉴 한눈에 보기',
                'fields' => [
                    'head' => ['메뉴', '하는 일'],
                    'rows' => [
                        ['게시판', '홈페이지에 노출되는 게시글(클라이언트 로고 등) 등록·수정·삭제'],
                        ['디자인 관리', '홈 첫 화면 큰 배너와 알림 팝업 등록·수정·삭제'],
                        ['사이트정보관리', '회사 정보 · 검색엔진 노출(SEO) · 통계 스크립트 · 비밀번호'],
                        ['계정 및 권한 관리', '관리자 계정 추가와 권한 조정 (최고관리자 전용)'],
                    ],
                ],
                'callouts' => [
                    ['tip', '이 가이드는 왼쪽 사이드바 <strong>맨 아래 [관리자 가이드]</strong> 버튼으로 언제든 다시 열 수 있습니다. 각 항목의 파란 <strong>바로가기</strong> 버튼을 누르면 해당 화면으로 바로 이동합니다.'],
                ],
            ],
        ],
    ],
    'board' => [
        'icon' => 'fa-list',
        'chip' => '게시판',
        'title' => '게시판 관리',
        'desc' => '홈페이지에 노출되는 게시글을 등록·수정·삭제하고, 노출 순서를 조정합니다.',
        'subs' => [
            [
                'title' => '📝 새 글 올리기',
                'url' => 'board/board.list.php',
                'link_label' => '게시글 관리 바로가기',
                'steps' => [
                    '왼쪽 메뉴 → <strong>게시판 → 게시글 관리</strong> 클릭',
                    '목록 오른쪽 위 <strong>[글등록]</strong> 버튼 클릭',
                    '<strong>게시판 선택</strong> 후 아래 표를 참고해 내용 입력',
                    '맨 아래 <strong>[저장]</strong> 클릭 — 저장 즉시 반영됩니다',
                ],
                'fields' => [
                    'head' => ['입력 항목', '설명'],
                    'rows' => [
                        ['제목', '목록과 홈페이지에 표시되는 글 제목'],
                        ['내용', '에디터로 자유롭게 작성 — 굵게·색상·이미지 삽입 가능'],
                        ['작성자', '기본값은 내 이름 — 필요하면 수정'],
                        ['링크 URL', '글(로고)을 클릭했을 때 이동할 주소 (예: 고객사 홈페이지)'],
                        ['공지', '체크하면 목록 상단에 고정되고 <strong>공지</strong> 표시가 붙습니다'],
                        ['썸네일', '대표 이미지 1개 — jpg·png·gif·webp (동영상 mp4·mov·avi도 가능)'],
                        ['파일첨부', '최대 <strong>5개</strong> — 이미지·PDF·한글(hwp)·엑셀·압축(zip) 등'],
                    ],
                ],
            ],
            [
                'title' => '✏️ 글 수정',
                'steps' => [
                    '게시글 관리 목록에서 수정할 글의 <strong>제목</strong> 클릭',
                    '내용 수정 후 <strong>[저장]</strong> — 기존 첨부파일은 삭제 체크 후 저장하면 지워집니다',
                ],
            ],
            [
                'title' => '🗑 글 삭제',
                'steps' => [
                    '목록에서 삭제할 글의 <strong>체크박스</strong> 선택 (여러 개 가능)',
                    '<strong>[선택삭제]</strong> 클릭 — 한 개만 지울 때는 해당 행의 <strong>[삭제]</strong> 버튼도 가능',
                ],
                'callouts' => [
                    ['warn', '삭제된 글은 <strong>복구할 수 없습니다</strong>. 삭제 전 다시 한 번 확인하세요.'],
                ],
            ],
            [
                'title' => '↕️ 노출 순서 바꾸기',
                'steps' => [
                    '게시글 관리 목록의 <strong>순서변경</strong> 열에 있는 화살표 버튼 사용',
                    '<strong>▲/▼</strong> = 한 칸 위/아래 이동 · 맨위/맨아래 버튼 = 한 번에 끝으로 이동',
                    '바뀐 순서는 홈페이지 노출 순서에 그대로 반영됩니다',
                ],
            ],
            [
                'title' => '🔎 글 검색',
                'steps' => [
                    '목록 상단 검색 영역에서 <strong>게시판</strong>과 <strong>검색 일자(기간)</strong> 선택',
                    '<strong>제목/내용</strong> 중 검색 범위를 고르고 검색어 입력 → <strong>[검색]</strong>',
                ],
            ],
            [
                'title' => '🌐 글이 홈페이지 어디에 보이나요?',
                'fields' => [
                    'head' => ['게시판', '홈페이지 노출 위치'],
                    'rows' => [
                        ['클라이언트 게시판', '메인 페이지 로고 영역 + <strong>Partners</strong> 페이지'],
                    ],
                ],
                'callouts' => [
                    ['info', '위 표에 없는 게시판의 글은 아직 홈페이지 화면에 연결되어 있지 않습니다. 새 게시판을 홈페이지에 노출하려면 개발사에 요청하세요.'],
                ],
            ],
        ],
    ],
    'design' => [
        'icon' => 'fa-palette',
        'chip' => '디자인',
        'title' => '디자인 관리',
        'desc' => '홈페이지 첫 화면의 큰 이미지 배너와 메인 페이지에 띄우는 알림 팝업을 관리합니다.',
        'subs' => [
            [
                'title' => '✅ 디자인 관리에서 할 수 있는 일',
                'fields' => [
                    'head' => ['기능', '어디에 보이나요?', '언제 쓰나요?'],
                    'rows' => [
                        ['배너 관리', '홈페이지 메인 첫 화면의 큰 슬라이드 영역', '행사·서비스 소개·중요 메시지를 첫 화면에 크게 보여줄 때'],
                        ['팝업 관리', '홈페이지 메인 페이지 위에 뜨는 알림 창', '공지, 이벤트, 임시 안내처럼 방문자가 바로 봐야 하는 내용을 알릴 때'],
                    ],
                ],
                'callouts' => [
                    ['info', '헷갈리면 이렇게 구분하세요. <strong>배너</strong>는 페이지 안에 자연스럽게 보이는 큰 이미지이고, <strong>팝업</strong>은 페이지 위에 따로 떠서 방문자가 닫을 수 있는 알림입니다.'],
                ],
            ],
            [
                'title' => '🖼 메인 배너 등록·수정',
                'url' => 'design/banner.list.php',
                'link_label' => '배너 관리 바로가기',
                'allowed' => ['superadmin', 'manager'],
                'steps' => [
                    '왼쪽 메뉴 → <strong>디자인 관리 → 배너 관리</strong> 클릭',
                    '새로 만들 때는 목록 오른쪽 위 <strong>[등록]</strong> 클릭, 수정할 때는 기존 배너의 <strong>제목</strong> 클릭',
                    '<strong>배너 노출 위치</strong>는 메인 페이지 첫 화면으로 고정되어 있으므로 따로 선택하지 않습니다',
                    '<strong>제목</strong>과 <strong>설명</strong> 입력 — 이 문구가 메인 첫 화면 이미지 위에 표시됩니다',
                    '제목은 편집기에서 Enter로 줄바꿈을 넣을 수 있으며, 저장 후 메인 배너에는 줄바꿈으로 표시됩니다',
                    '클릭해서 다른 페이지로 보내고 싶으면 <strong>링크 사용</strong>을 켜고 URL 입력',
                    '<strong>배너 이미지 (데스크탑)</strong> 1개 업로드 — 권장 크기는 <strong>1920×1080px(16:9)</strong>이며, PC와 모바일에서 같은 이미지를 사용합니다',
                    '<strong>노출 여부</strong>를 노출로 두고, <strong>노출 날짜</strong>를 확인한 뒤 맨 아래 <strong>[저장]</strong> 클릭',
                    '저장 후 홈페이지 메인 화면을 새로고침해서 실제로 잘 보이는지 확인',
                ],
                'fields' => [
                    'head' => ['입력 항목', '설명'],
                    'rows' => [
                        ['제목', '배너에서 가장 크게 보이는 문구'],
                        ['설명', '제목 아래 보조 문구 — 비워 두면 설명 없이 제목만 보입니다'],
                        ['링크 사용', '방문자가 배너를 클릭했을 때 이동하게 할지 선택'],
                        ['링크 URL', '이동할 주소 — 예: <code>/business</code> 또는 <code>https://example.com</code>'],
                        ['새창 여부', '외부 사이트로 보낼 때는 새창을 권장, 사이트 안 페이지면 현재 창도 가능'],
                        ['배너 이미지', '메인 첫 화면에 표시되는 이미지 — <strong>1920×1080px(16:9)</strong> 권장, 데스크탑 이미지 1개만 사용'],
                        ['노출 여부', '<strong>노출</strong>일 때만 홈페이지에 표시됩니다. 임시 저장은 미노출로 두세요'],
                        ['노출 날짜', '시작일~종료일 사이에만 표시됩니다. 계속 보이게 하려면 무제한을 사용하세요'],
                    ],
                ],
                'callouts' => [
                    ['tip', '노출 가능한 배너가 1개 이상 있으면 관리자에 등록한 배너가 메인에 표시됩니다. 노출 가능한 배너가 하나도 없으면 기본 배너 3개가 자동으로 보입니다.'],
                    ['info', '배너 이미지는 화면 전체를 넓게 채우는 용도입니다. 중요한 로고·문구는 이미지 가장자리보다 중앙에 두고, 실제 문구는 가능하면 제목/설명 입력란에 작성하세요.'],
                    ['warn', 'PC용 배너 이미지가 없거나 이미지 파일이 서버에 없으면 해당 배너는 화면에 나오지 않습니다. 저장 후 반드시 실제 메인 화면을 확인하세요.'],
                ],
            ],
            [
                'title' => '📌 팝업 등록·수정',
                'url' => 'design/popup.list.php',
                'link_label' => '팝업 관리 바로가기',
                'allowed' => ['superadmin', 'manager'],
                'steps' => [
                    '왼쪽 메뉴 → <strong>디자인 관리 → 팝업 관리</strong> 클릭',
                    '새로 만들 때는 목록 오른쪽 위 <strong>[등록]</strong> 클릭, 수정할 때는 기존 팝업의 <strong>제목</strong> 클릭',
                    '<strong>팝업 노출 위치</strong>는 메인 페이지로 고정되어 있으므로 따로 선택하지 않습니다',
                    '<strong>제목</strong> 입력 — 관리자가 목록에서 구분하기 쉬운 이름으로 작성',
                    '팝업을 클릭했을 때 이동시킬 페이지가 있으면 <strong>링크 사용</strong>을 켜고 URL 입력',
                    '<strong>팝업 이미지</strong> 업로드 — 권장 크기는 <strong>600×800px(3:4 세로형)</strong>입니다. 더 선명하게 만들 때는 <strong>750×1000px</strong>처럼 같은 비율로 제작하세요',
                    '<strong>노출 여부</strong>를 노출로 두고, 노출 기간을 확인한 뒤 맨 아래 <strong>[저장]</strong> 클릭',
                    '저장 후 홈페이지 메인 페이지에 접속해서 팝업이 뜨는지 확인',
                ],
                'fields' => [
                    'head' => ['입력 항목', '설명'],
                    'rows' => [
                        ['팝업 노출 위치', '항상 <strong>메인 페이지</strong>입니다. 관리자가 별도로 경로를 입력하지 않습니다'],
                        ['제목', '관리자 목록에서 구분하는 이름 — 방문자 화면에는 이미지가 중심으로 보입니다'],
                        ['팝업 이미지', '방문자에게 실제로 표시되는 이미지 — 필수, <strong>600×800px(3:4 세로형)</strong> 권장'],
                        ['링크 사용', '이미지를 클릭했을 때 다른 페이지로 이동하게 할지 선택'],
                        ['링크 URL', '이동할 주소 — 비워 두면 이미지만 보여주고 이동하지 않습니다'],
                        ['노출 여부', '<strong>노출</strong>일 때만 표시됩니다. 준비 중이면 미노출로 저장하세요'],
                        ['노출 기간', '시작일~종료일 사이에만 표시됩니다. 계속 보이게 하려면 무제한을 사용하세요'],
                        ['순서', '같은 페이지에 팝업이 여러 개 있을 때 먼저 보이는 순서'],
                    ],
                ],
                'callouts' => [
                    ['tip', '같은 페이지에 팝업이 여러 개 있으면 팝업 창이 여러 개 뜨는 것이 아니라, 하나의 창 안에서 슬라이드로 넘겨 볼 수 있게 표시됩니다.'],
                    ['tip', '방문자가 <strong>오늘 하루 보지 않기</strong>를 체크하고 닫으면 그날은 같은 페이지의 팝업이 다시 뜨지 않습니다. 다음 날에는 다시 표시됩니다.'],
                    ['info', '팝업은 메인 페이지 전용입니다. 회사소개, 사업소개, 파트너스 같은 서브 페이지에는 표시되지 않습니다.'],
                    ['info', '팝업 이미지는 세로 배너형으로 통일됩니다. 권장 비율(3:4)보다 너무 가로로 길거나 세로로 긴 이미지를 올리면 여백이 생길 수 있으므로, 안내문과 이미지를 한 장 안에 정리해서 제작하세요.'],
                ],
            ],
            [
                'title' => '↕️ 순서 변경·숨김·삭제',
                'steps' => [
                    '목록의 순서 변경 버튼으로 배너/팝업이 보이는 순서를 조정',
                    '잠시 숨기고 싶은 항목은 삭제하지 말고 수정 화면에서 <strong>노출 여부</strong>를 <strong>미노출</strong>로 변경',
                    '완전히 필요 없는 항목만 <strong>[삭제]</strong> 클릭',
                    '삭제 확인창이 뜨면 내용을 다시 확인한 뒤 진행',
                ],
                'callouts' => [
                    ['warn', '삭제하면 복구가 어렵고 등록 이미지 파일도 함께 삭제될 수 있습니다. 기간이 끝난 배너나 팝업은 먼저 <strong>미노출</strong>로 바꾸는 것을 권장합니다.'],
                ],
            ],
            [
                'title' => '🧪 저장 후 확인 체크리스트',
                'steps' => [
                    '목록에서 방금 저장한 항목의 <strong>노출 여부</strong>가 노출인지 확인',
                    '노출 날짜가 오늘 날짜를 포함하는지 확인',
                    '이미지 미리보기가 정상인지 확인',
                    '배너는 홈페이지 메인 첫 화면에서 확인',
                    '팝업은 홈페이지 메인 페이지에서 확인',
                    '링크를 넣었다면 클릭해서 원하는 페이지로 이동하는지 확인',
                ],
                'callouts' => [
                    ['info', '저장했는데 화면에 안 보이면 대부분 <strong>미노출 상태</strong>, <strong>노출 날짜 범위 밖</strong>, <strong>이미지 누락</strong> 중 하나입니다.'],
                ],
            ],
        ],
    ],
    'siteinfo' => [
        'icon' => 'fa-gear',
        'chip' => '사이트정보',
        'title' => '사이트정보관리',
        'desc' => '회사 기본 정보, 검색엔진 노출(SEO), 통계 스크립트, 관리자 비밀번호를 관리합니다.',
        'subs' => [
            [
                'title' => '🏢 사이트 정보 (회사명·연락처·로고)',
                'url' => 'setting/index.php',
                'link_label' => '사이트 정보 바로가기',
                'allowed' => ['superadmin', 'manager'],
                'steps' => [
                    '왼쪽 메뉴 → <strong>사이트정보관리 → 사이트 정보</strong> 클릭',
                    '아래 표를 참고해 값 수정 → 맨 아래 <strong>[저장]</strong>',
                ],
                'fields' => [
                    'head' => ['입력 항목', '설명'],
                    'rows' => [
                        ['사이트 제목', '사이트를 대표하는 이름 — 브라우저에 노출됩니다'],
                        ['대표번호', '회사 대표 전화번호'],
                        ['하단 푸터 정보', '사이트명·주소·전화번호·이메일·대표자 — 아래 주의사항 참고'],
                    ],
                ],
                'callouts' => [
                    ['warn', '홈페이지 <strong>맨 아래(푸터)의 주소·이메일·전화번호</strong>는 이 화면이 아니라 사이트 코드에서 관리됩니다. 푸터 정보 변경이 필요하면 개발사에 요청하세요.'],
                ],
            ],
            [
                'title' => '🌐 SEO 관리 — 사이트 전역 검색 설정',
                'url' => 'setting/seo.config.php',
                'link_label' => 'SEO 관리 바로가기',
                'allowed' => ['superadmin', 'manager'],
                'steps' => [
                    '왼쪽 메뉴 → <strong>사이트정보관리 → SEO 관리</strong> 클릭',
                    '아래 표의 항목 입력 → 맨 아래 <strong>[저장]</strong>',
                ],
                'fields' => [
                    'head' => ['입력 항목', '설명'],
                    'rows' => [
                        ['사이트 타이틀 · 메타 타이틀', '구글·네이버 <strong>검색 결과에 표시되는 제목</strong>'],
                        ['메타 설명', '검색 결과 제목 아래 나오는 소개 문장 — 80~160자 권장'],
                        ['메타 키워드', '대표 키워드를 쉼표(,)로 구분해 입력'],
                        ['브라우저 파비콘', '브라우저 탭에 표시되는 작은 아이콘 — .ico/.png/.svg'],
                        ['OG 썸네일', '카카오톡·페이스북 <strong>공유 시 미리보기 이미지</strong> — <strong>1200×630px</strong> 권장'],
                        ['사이트맵 관리', '<strong>[라우터 스캔 → sitemap.xml 생성]</strong> 버튼으로 자동 갱신 — 직접 만든 XML 업로드도 가능 (5MB 이하)'],
                    ],
                ],
                'callouts' => [
                    ['tip', '저장 후 실제 검색 결과에 반영되기까지 <strong>며칠~몇 주</strong> 걸립니다 (검색엔진이 사이트를 다시 방문하는 주기).'],
                ],
            ],
            [
                'title' => '🔍 페이지별 SEO (특정 페이지만 따로 설정)',
                'url' => 'setting/seo.php',
                'link_label' => '페이지별 SEO 바로가기',
                'steps' => [
                    '왼쪽 메뉴 → <strong>사이트정보관리 → 페이지별 SEO</strong> 클릭',
                    '<strong>[생성]</strong> → <strong>경로</strong> 선택 (예: /about, /business — 등록된 페이지만 선택 가능)',
                    '<strong>페이지 제목</strong>과 <strong>메타 설명글</strong> 입력 → 저장',
                    '특정 페이지를 검색에서 숨기려면 <strong>검색엔진 노출 설정</strong>을 <code>noindex, follow</code>로 변경',
                ],
                'callouts' => [
                    ['tip', '비워 둔 항목은 <strong>SEO 관리(전역 설정)</strong> 값이 자동 적용됩니다. 다르게 쓸 페이지만 등록하면 됩니다.'],
                ],
            ],
            [
                'title' => '🗺 sitemap.xml — 자동 생성 (조작 불필요)',
                'steps' => [
                    'sitemap.xml은 홈페이지의 페이지 목록을 담은 파일로, 별도 관리 화면 없이 운영됩니다',
                    '주소: <code>https://(도메인)/sitemap.xml</code> — 페이지 구성이 바뀌면 <strong>SEO 관리</strong>의 생성 버튼으로 갱신',
                    '구글에 사이트 등록하기: <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console ↗</a> 에서 sitemap URL 제출',
                    '네이버: <a href="https://searchadvisor.naver.com" target="_blank" rel="noopener">네이버 서치어드바이저 ↗</a>',
                ],
            ],
            /* hidden — robots.txt 가이드 블록 숨김 (페이지 자체는 유지)
            [
                'title' => '🤖 robots.txt 관리 (검색엔진 차단 규칙)',
                'url' => 'setting/robots.php',
                'link_label' => 'robots.txt 관리 바로가기',
                'allowed' => ['superadmin'],
                'steps' => [
                    '왼쪽 메뉴 → <strong>사이트정보관리 → robots.txt 관리</strong> 클릭 (최고 관리자만 표시)',
                    '<strong>[기본 템플릿 불러오기]</strong>로 시작하는 것을 권장',
                    '<strong>관리자 경로 차단</strong>과 <strong>사이트맵 URL</strong>은 자동 추가됨 — 본문에서 지워도 다시 들어감',
                    '<strong>[저장]</strong> 후 우측 미리보기에서 실제 결과 확인',
                    '⚠️ <code>Disallow: /</code> 한 줄만 입력하면 사이트 전체가 검색에서 빠집니다 — 절대 주의',
                ],
            ],
            */
            [
                'title' => '📊 사이트 메타 태그 (Google Analytics·GTM 등)',
                'url' => 'setting/external.tag.php',
                'link_label' => '사이트 메타 태그 바로가기',
                'allowed' => ['superadmin'],
                'steps' => [
                    '왼쪽 메뉴 → <strong>사이트정보관리 → 사이트 메타 태그</strong> 클릭 (최고관리자에게만 표시)',
                    '<strong>[사이트 메타 태그 생성]</strong> 클릭 → <strong>제목</strong> 입력 (예: GA4 추적 코드)',
                    '아래 표를 참고해 <strong>위치</strong> 선택',
                    '발급받은 <strong>스크립트 태그</strong>를 그대로 붙여넣기 → <strong>[저장]</strong>',
                    '목록의 <strong>사용 여부</strong>로 각 태그를 켜고 끌 수 있습니다',
                ],
                'fields' => [
                    'head' => ['위치', '용도'],
                    'rows' => [
                        ['HEAD', 'Google Analytics(GA4) 스니펫 · 네이버/구글 사이트 소유 확인 코드'],
                        ['BODY', 'Google Tag Manager(noscript) 등 본문 시작 부분에 넣는 스크립트'],
                        ['FOOTER', '채팅 상담 위젯 등 페이지 맨 아래에서 실행되는 스크립트'],
                    ],
                ],
                'callouts' => [
                    ['warn', '잘못된 스크립트는 홈페이지 오류를 일으킬 수 있습니다. <strong>발급받은 코드를 수정 없이 그대로</strong> 붙여넣고, 문제가 생기면 해당 태그를 <strong>미사용</strong>으로 바꿔 즉시 끄세요.'],
                ],
            ],
            [
                'title' => '🔐 비밀번호 변경',
                'url' => 'setting/pwd.php',
                'link_label' => '비밀번호 변경 바로가기',
                'steps' => [
                    '왼쪽 메뉴 → <strong>사이트정보관리 → 비밀번호 변경</strong> 클릭',
                    '<strong>현재 비밀번호</strong> 입력 → <strong>신규 비밀번호</strong> 두 번 입력 → <strong>[확인]</strong>',
                ],
                'callouts' => [
                    ['info', '새 비밀번호 규칙: <strong>8자 이상</strong> + 영문 대문자·소문자·숫자·특수문자 중 <strong>3종류 이상</strong> 포함. 주기적으로 변경하는 것을 권장합니다.'],
                ],
            ],
        ],
    ],
    'account' => [
        'icon' => 'fa-user-shield',
        'chip' => '계정',
        'title' => '계정 및 권한 관리',
        'desc' => '관리자 계정을 추가하고 권한을 조정합니다. 최고관리자만 사용할 수 있습니다.',
        'subs' => [
            [
                'title' => '👤 새 계정 만들기',
                'url' => 'account/new.php',
                'link_label' => '계정 생성 바로가기',
                'steps' => [
                    '왼쪽 메뉴 → <strong>계정 및 권한 관리 → 계정 생성</strong> 클릭',
                    '<strong>권한</strong> 선택 (아래 안내 참고)',
                    '아래 표의 규칙에 맞게 정보 입력 → <strong>[등록]</strong>',
                ],
                'fields' => [
                    'head' => ['입력 항목', '규칙'],
                    'rows' => [
                        ['아이디', '4~20자 — 영문·숫자·밑줄(_)만 사용 가능'],
                        ['비밀번호', '8~30자'],
                        ['이름', '한글·영문 20자 이내'],
                        ['이메일', '필수 입력'],
                        ['연락처', '휴대폰 번호 — 숫자만 입력 (예: 01012345678)'],
                        ['상태', '<strong>비활성</strong>으로 두면 해당 계정은 로그인할 수 없습니다'],
                    ],
                ],
                'callouts' => [
                    ['tip', '권한 안내: <strong>최고관리자</strong> = 모든 기능 · <strong>중간관리자</strong> = 게시판·사이트정보·SEO 편집 (계정/메타 태그 불가) · <strong>외부인</strong> = 페이지별 SEO·비밀번호 변경만. 자세한 범위는 이 패널 맨 위 <strong>내 권한</strong> 표를 확인하세요.'],
                ],
            ],
            [
                'title' => '🛠 계정 수정·삭제',
                'url' => 'account/index.php',
                'link_label' => '계정 관리 바로가기',
                'steps' => [
                    '왼쪽 메뉴 → <strong>계정 및 권한 관리 → 계정 관리</strong> 클릭',
                    '<strong>[권한 및 수정]</strong>으로 이름·권한·상태 변경 · <strong>[삭제]</strong>로 계정 제거',
                    '수정 화면에서 <strong>비밀번호 칸을 비워 두면 기존 비밀번호가 유지</strong>됩니다',
                ],
                'callouts' => [
                    ['warn', '계정 삭제는 되돌릴 수 없습니다.'],
                ],
            ],
            [
                'title' => '🔓 로그인이 안 될 때',
                'steps' => [
                    '비밀번호 <strong>5회 오류</strong> → <strong>15분간 잠금</strong> — 15분 뒤 다시 시도',
                    '계정 관리에서 해당 계정의 <strong>상태</strong>가 <strong>비활성</strong>이 아닌지 확인',
                    '비밀번호를 잊었다면 최고관리자가 <strong>계정 관리 → [권한 및 수정]</strong>에서 새 비밀번호를 설정',
                ],
            ],
        ],
    ],
];

// 현재 페이지가 속한 섹션을 자동으로 펼침 (해당 없으면 '시작하기')
$_currentMenuKey = $menus[$depthnum]['key'] ?? '';
$_openGuideKey = in_array($_currentMenuKey, $_visibleMenuKeys, true) ? $_currentMenuKey : 'start';

// 본인 역할 코드 (권한 매트릭스 강조 / 잠금 배지용)
$_myRole = $role->getCode();
$_roleColMap = ['superadmin' => 1, 'manager' => 2, 'external' => 3];
$_myCol = $_roleColMap[$_myRole] ?? 0;

if (!function_exists('no_admin_guide_sub')) {
    /**
     * 가이드 소항목 1개 렌더링 (제목 → 바로가기 → 단계 → 설명 표 → 강조 박스)
     */
    function no_admin_guide_sub(array $sub, string $myRole, string $linkBase): void
    {
        $roleNames = ['superadmin' => '최고관리자', 'manager' => '중간관리자', 'external' => '외부인'];
        $allowed = $sub['allowed'] ?? null;
        $blocked = is_array($allowed) && !in_array($myRole, $allowed, true);
        ?>
        <div class="no-guide-sub<?= $blocked ? ' is-blocked' : '' ?>">
            <p class="no-guide-sub__title">
                <?= $sub['title'] ?>
                <?php if ($blocked):
                    $allowedNames = array_map(function ($code) use ($roleNames) {
                        return $roleNames[$code] ?? $code;
                    }, $allowed);
                ?>
                <span class="no-guide-lock">🔒 <?= htmlspecialchars(implode('·', $allowedNames), ENT_QUOTES, 'UTF-8') ?> 전용</span>
                <?php endif; ?>
            </p>
            <?php if (!empty($sub['url']) && !$blocked): ?>
            <a href="<?= $linkBase . $sub['url'] ?>" class="no-guide-link">
                <i class="bx bx-link-external"></i>
                <?= htmlspecialchars($sub['link_label'] ?? '바로가기', ENT_QUOTES, 'UTF-8') ?>
            </a>
            <?php endif; ?>
            <?php if (!empty($sub['steps'])): ?>
            <ol class="no-guide-steps">
                <?php foreach ($sub['steps'] as $step): ?>
                <li><?= $step ?></li>
                <?php endforeach; ?>
            </ol>
            <?php endif; ?>
            <?php if (!empty($sub['fields']['rows'])): ?>
            <table class="no-guide-field-table">
                <?php if (!empty($sub['fields']['head'])): ?>
                <thead>
                    <tr>
                        <?php foreach ($sub['fields']['head'] as $head): ?>
                        <th><?= htmlspecialchars($head, ENT_QUOTES, 'UTF-8') ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <?php endif; ?>
                <tbody>
                    <?php foreach ($sub['fields']['rows'] as $row): ?>
                    <tr>
                        <?php foreach ($row as $ci => $cell): ?>
                        <?php if ($ci === 0): ?>
                        <th scope="row"><?= $cell ?></th>
                        <?php else: ?>
                        <td><?= $cell ?></td>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            <?php foreach (($sub['callouts'] ?? []) as $callout): ?>
            <p class="no-guide-callout no-guide-callout--<?= htmlspecialchars($callout[0], ENT_QUOTES, 'UTF-8') ?>"><?= $callout[1] ?></p>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

if (!function_exists('no_admin_guide_section')) {
    /**
     * 가이드 섹션(details 아코디언) 1개 렌더링
     */
    function no_admin_guide_section(string $key, array $section, bool $open, string $myRole, string $linkBase): void
    {
        ?>
        <details class="no-guide-item" id="guide-sec-<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"<?= $open ? ' open' : '' ?>>
            <summary class="no-guide-item__head">
                <i class="fa-solid <?= $section['icon'] ?>"></i>
                <?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?>
            </summary>
            <div class="no-guide-item__body">
                <?php if (!empty($section['desc'])): ?>
                <p class="no-guide-desc"><?= htmlspecialchars($section['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <?php foreach ($section['subs'] as $sub): ?>
                <?php no_admin_guide_sub($sub, $myRole, $linkBase); ?>
                <?php endforeach; ?>
            </div>
        </details>
        <?php
    }
}

$_guideLinkBase = $NO_IS_SUBDIR . $BASE_URL;
?>

<div class="no-guide-overlay" id="guideOverlay"></div>
<div class="no-guide-panel" id="guidePanel" role="dialog" aria-modal="true" aria-label="관리자 가이드">
    <div class="no-guide-panel__head">
        <h2>관리자 가이드</h2>
        <button type="button" class="no-guide-panel__close" id="guideClose" aria-label="닫기">
            <i class="bx bx-x"></i>
        </button>
    </div>
    <nav class="no-guide-panel__nav" aria-label="가이드 빠른 이동">
        <button type="button" class="no-guide-nav__chip" data-guide-target="guide-sec-start">시작하기</button>
        <button type="button" class="no-guide-nav__chip" data-guide-target="guide-sec-role">내 권한</button>
        <?php foreach ($_visibleMenuKeys as $_key): ?>
        <?php if (!isset($_guideMap[$_key])) continue; ?>
        <button type="button" class="no-guide-nav__chip" data-guide-target="guide-sec-<?= htmlspecialchars($_key, ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($_guideMap[$_key]['chip'] ?? $_guideMap[$_key]['title'], ENT_QUOTES, 'UTF-8') ?>
        </button>
        <?php endforeach; ?>
    </nav>
    <div class="no-guide-panel__body">
        <?php no_admin_guide_section('start', $_guideMap['start'], $_openGuideKey === 'start', $_myRole, $_guideLinkBase); ?>

        <?php
        // 권한 매트릭스 정의 (라벨 → [superadmin, manager, external])
        $_roleMatrix = [
            ['label' => '게시판 작성·수정·삭제',                    'cells' => ['✅ 전체', '✅ 전체', '❌']],
            ['label' => '디자인 관리 (메인 배너·팝업)',             'cells' => ['✅', '✅', '❌']],
            ['label' => '사이트 정보(연락처·로고) 편집',            'cells' => ['✅', '✅', '❌']],
            ['label' => 'SEO 관리 (사이트 전역 메타·OG·파비콘)',    'cells' => ['✅', '✅', '❌']],
            ['label' => '페이지별 SEO 편집',                        'cells' => ['✅', '✅', '✅']],
            ['label' => 'sitemap.xml 자동 생성 확인',               'cells' => ['✅', '✅', '✅ 보기']],
            // ['label' => 'robots.txt 관리',                           'cells' => ['✅', '❌', '❌']], // hidden
            ['label' => '사이트 메타 태그 (GA·GTM 등)',             'cells' => ['✅', '❌', '❌']],
            ['label' => '계정 및 권한 관리',                        'cells' => ['✅', '❌', '❌']],
            ['label' => '비밀번호 변경',                            'cells' => ['✅', '✅', '✅']],
        ];
        ?>
        <details class="no-guide-item" id="guide-sec-role">
            <summary class="no-guide-item__head">
                <i class="fa-solid fa-user-check"></i>
                내 권한 — <?= htmlspecialchars($role->getName(), ENT_QUOTES, 'UTF-8') ?>
            </summary>
            <div class="no-guide-item__body">
                <p class="no-guide-desc">역할별로 사용할 수 있는 기능입니다. 아래 표에서 내 권한 컬럼이 강조 표시됩니다.</p>
                <table class="no-guide-role-table">
                    <thead>
                        <tr>
                            <th>기능</th>
                            <th<?= $_myCol === 1 ? ' class="is-me"' : '' ?>>최고관리자</th>
                            <th<?= $_myCol === 2 ? ' class="is-me"' : '' ?>>중간관리자</th>
                            <th<?= $_myCol === 3 ? ' class="is-me"' : '' ?>>외부인</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($_roleMatrix as $_row): ?>
                        <tr>
                            <td><?= htmlspecialchars($_row['label'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td<?= $_myCol === 1 ? ' class="is-me"' : '' ?>><?= htmlspecialchars($_row['cells'][0], ENT_QUOTES, 'UTF-8') ?></td>
                            <td<?= $_myCol === 2 ? ' class="is-me"' : '' ?>><?= htmlspecialchars($_row['cells'][1], ENT_QUOTES, 'UTF-8') ?></td>
                            <td<?= $_myCol === 3 ? ' class="is-me"' : '' ?>><?= htmlspecialchars($_row['cells'][2], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </details>

        <?php foreach ($_visibleMenuKeys as $_key): ?>
        <?php
            if (!isset($_guideMap[$_key])) continue;
            no_admin_guide_section($_key, $_guideMap[$_key], $_openGuideKey === $_key, $_myRole, $_guideLinkBase);
        ?>
        <?php endforeach; ?>

        <?php if ($_guideSupportEmail !== ''): ?>
        <p class="no-guide-support">
            해결되지 않는 문제는
            <a href="mailto:<?= htmlspecialchars($_guideSupportEmail, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($_guideSupportEmail, ENT_QUOTES, 'UTF-8') ?></a>
            로 문의하세요.
        </p>
        <?php endif; ?>
    </div>
</div>

<script defer>
const activatedMenu = document.querySelector('.no-menu-item.active .no-menu-arrow');
if (activatedMenu) activatedMenu.classList.add('open');

(function() {
    const panel = document.getElementById('guidePanel');
    const overlay = document.getElementById('guideOverlay');
    const openBtn = document.getElementById('guideOpen');
    const closeBtn = document.getElementById('guideClose');
    if (!panel || !overlay || !openBtn) return;

    function openGuide() {
        panel.classList.add('is-open');
        overlay.classList.add('is-open');
    }

    function closeGuide() {
        panel.classList.remove('is-open');
        overlay.classList.remove('is-open');
    }

    openBtn.addEventListener('click', openGuide);
    if (closeBtn) closeBtn.addEventListener('click', closeGuide);
    overlay.addEventListener('click', closeGuide);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeGuide();
    });

    // 빠른 이동 칩: 대상 섹션 펼침 + 스크롤 + 현재 섹션 칩 강조
    const chips = panel.querySelectorAll('.no-guide-nav__chip');

    function setActiveChip(id) {
        chips.forEach(function(chip) {
            chip.classList.toggle('is-active', chip.getAttribute('data-guide-target') === id);
        });
    }

    chips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            const target = document.getElementById(chip.getAttribute('data-guide-target'));
            if (!target) return;
            if (target.tagName === 'DETAILS') target.open = true;
            setActiveChip(target.id);
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // 섹션을 직접 펼쳤을 때도 해당 칩 강조
    panel.querySelectorAll('details.no-guide-item').forEach(function(item) {
        item.addEventListener('toggle', function() {
            if (item.open) setActiveChip(item.id);
        });
    });

    const initiallyOpen = panel.querySelector('details.no-guide-item[open]');
    if (initiallyOpen) setActiveChip(initiallyOpen.id);
})();
</script>
