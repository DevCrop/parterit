<?php

function getBoardExtraFieldsSelect(): string
{
    return "a.extra1, a.extra2, a.extra3, a.extra4, a.extra5, a.extra6, a.extra7, a.extra8, a.extra9, a.extra10, a.extra11, a.extra12, a.extra13, a.extra14, a.extra15";
}

/**
 * 다국어 fallback: 현재 locale 행 → 기본 locale 행 → nb_board legacy 컬럼 순.
 * SELECT 절에 들어갈 COALESCE 표현식 + JOIN 절 + 바인딩할 파라미터 반환.
 *
 * 사용 예:
 *   ['select' => $sel, 'join' => $join, 'params' => $bind] = getBoardLocaleSelect('a');
 *   $query = "SELECT a.no, {$sel}, ... FROM nb_board a {$join} ...";
 *   $stmt->execute(array_merge([':sitekey'=>...], $bind));
 *
 * @return array{select:string, join:string, params:array<string,string>}
 */
function getBoardLocaleSelect(string $alias = 'a'): array
{
    $cur = function_exists('locale') ? locale() : (function_exists('project_default_locale') ? project_default_locale() : 'en');
    $def = function_exists('project_default_locale') ? project_default_locale() : 'en';

    $join = " LEFT JOIN nb_board_locale lc ON lc.board_id = {$alias}.no AND lc.locale = :loc_cur "
          . " LEFT JOIN nb_board_locale ld ON ld.board_id = {$alias}.no AND ld.locale = :loc_def ";

    $fields = ['title', 'contents', 'write_name'];
    for ($i = 1; $i <= 30; $i++) {
        $fields[] = "extra{$i}";
    }

    $parts = [];
    foreach ($fields as $f) {
        $parts[] = "COALESCE(NULLIF(lc.`{$f}`, ''), NULLIF(ld.`{$f}`, ''), {$alias}.`{$f}`) AS `{$f}`";
    }

    return [
        'select' => implode(",\n              ", $parts),
        'join'   => $join,
        'params' => [':loc_cur' => $cur, ':loc_def' => $def],
    ];
}

function getBoardExtraMatchFieldsSelect(): string
{
    return "a.extra_match_field1, a.extra_match_field2, a.extra_match_field3, a.extra_match_field4, a.extra_match_field5, a.extra_match_field6, a.extra_match_field7, a.extra_match_field8, a.extra_match_field9, a.extra_match_field10, a.extra_match_field11, a.extra_match_field12, a.extra_match_field13, a.extra_match_field14, a.extra_match_field15";
}

function getBoardInfoByName($nm) {
    global $NO_SITE_UNIQUE_KEY;
    $connect = DB::getInstance(); // PDO 인스턴스 가져오기

    $query = "SELECT a.no, a.sitekey, a.title, a.skin, a.top_banner_image, a.contents, a.view_yn, a.secret_yn, 
                     a.sort_no, a.list_size, a.fileattach_yn, a.fileattach_cnt, a.comment_yn, a.depth1, 
                     a.depth2, a.depth3, a.lnb_path, a.view_skin 
              FROM nb_board_manage a 
              WHERE a.sitekey = :sitekey AND a.title = :title";
    
    $stmt = $connect->prepare($query);
    $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY, ':title' => $nm]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBoardInfoByNo($board_no) {
    global $NO_SITE_UNIQUE_KEY;
    $connect = DB::getInstance();

    $query = "SELECT a.no, a.sitekey, a.title, a.skin, a.top_banner_image, a.contents, a.view_yn, 
                     a.secret_yn, a.sort_no, a.list_size, a.fileattach_yn, a.fileattach_cnt, a.comment_yn, 
                     a.depth1, a.depth2, a.depth3, a.lnb_path, a.isOpen, a.view_skin 
              FROM nb_board_manage a 
              WHERE a.sitekey = :sitekey AND a.no = :board_no";
    
    $stmt = $connect->prepare($query);
    $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY, ':board_no' => $board_no]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBoardManageInfoByNo($board_no) {
    global $NO_SITE_UNIQUE_KEY;
    $connect = DB::getInstance();
    $extraMatchFields = getBoardExtraMatchFieldsSelect();

    $query = "SELECT a.sitekey, a.title, a.skin, a.regdate, a.top_banner_image, a.contents, a.view_yn, 
                     a.secret_yn, a.list_size, a.fileattach_yn, a.fileattach_cnt, a.comment_yn, a.depth1, 
                     a.depth2, a.depth3, a.lnb_path, a.category_yn, a.view_skin, $extraMatchFields 
              FROM nb_board_manage a 
              WHERE a.sitekey = :sitekey AND a.no = :board_no";

    $stmt = $connect->prepare($query);
    $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY, ':board_no' => $board_no]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 게시판 업로드 썸네일 파일명 → 웹 경로 (/uploads/board/…)
 */
function board_thumb_public_url(?string $filename): string
{
    if ($filename === null || $filename === '') {
        return '';
    }

    return PathHelper::basePath('/uploads/board/' . ltrim($filename, '/'), true);
}

/**
 * 동일 basename의 .webp 파생본이 디스크에 존재하면 그 웹 경로를 반환한다.
 * 없으면 빈 문자열 → <picture> 컴포넌트가 자동으로 <img> 폴백을 사용.
 *
 * News(5)·Blog(4) 게시판 업로드 시 board.process.php가 ImageHelper로 .webp를 생성한다.
 */
function board_thumb_webp_url(?string $filename): string
{
    if ($filename === null || $filename === '') {
        return '';
    }

    $ext = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext === 'webp' || !in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return '';
    }

    $base = pathinfo($filename, PATHINFO_FILENAME);
    $dir  = dirname($filename);
    $relWebp = ($dir === '.' || $dir === '') ? $base . '.webp' : $dir . '/' . $base . '.webp';

    $diskRoot = $GLOBALS['UPLOAD_DIR_BOARD'] ?? '';
    if ($diskRoot === '' || !is_file(rtrim($diskRoot, '/\\') . '/' . ltrim($relWebp, '/'))) {
        return '';
    }

    return PathHelper::basePath('/uploads/board/' . ltrim($relWebp, '/'), true);
}

/**
 * board_no 목록 (프론트: sort_no → no 순)
 *
 * @return list<array<string,mixed>>
 */
function getBoardForFront(int $board_no, int $limit = 100): array
{
    return getBoardLimit($board_no, $limit, 'a.sort_no ASC, a.no ASC');
}

/**
 * 단건 글 (sitekey·board_no 일치 시에만)
 *
 * @return array<string,mixed>|null
 */
function getBoardPostByNo(int $no, int $board_no): ?array
{
    global $NO_SITE_UNIQUE_KEY;
    $connect = DB::getInstance();
    $loc = getBoardLocaleSelect('a');

    $query = "SELECT a.no, a.board_no, a.user_no, a.category_no, a.comment_cnt,
                     a.regdate, a.read_cnt, a.thumb_image, a.is_admin_writed, a.is_notice,
                     a.is_secret, a.secret_pwd, a.isFile,
                     a.file_attach_1, a.file_attach_origin_1,
                     a.file_attach_2, a.file_attach_origin_2,
                     a.file_attach_3, a.file_attach_origin_3,
                     a.file_attach_4, a.file_attach_origin_4,
                     a.file_attach_5, a.file_attach_origin_5,
                     a.is_admin_comment_yn, a.direct_url, a.sort_no,
                     {$loc['select']},
                     c.name AS category_name
              FROM nb_board a
              {$loc['join']}
              LEFT JOIN nb_board_category c ON a.category_no = c.no
              WHERE a.sitekey = :sitekey AND a.no = :no AND a.board_no = :board_no";

    $stmt = $connect->prepare($query);
    $params = array_merge($loc['params'], [
        ':sitekey' => $NO_SITE_UNIQUE_KEY,
        ':no' => $no,
        ':board_no' => $board_no,
    ]);
    $stmt->execute($params);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

/**
 * 동일 게시판에서 sort_no ASC, no ASC 기준 이전·다음 글
 *
 * @return array{prev: ?array<string,mixed>, next: ?array<string,mixed>}
 */
function getBoardPrevNext(int $board_no, int $sort_no, int $no): array
{
    global $NO_SITE_UNIQUE_KEY;
    $connect = DB::getInstance();
    $loc = getBoardLocaleSelect('a');

    // title 만 locale fallback 으로 취득. 나머지는 nb_board 직접.
    $titleExpr = "COALESCE(NULLIF(lc.title, ''), NULLIF(ld.title, ''), a.title) AS title";

    $baseSelect = "SELECT a.no, a.board_no, {$titleExpr}, a.sort_no, a.thumb_image, c.name AS category_name
              FROM nb_board a
              {$loc['join']}
              LEFT JOIN nb_board_category c ON a.category_no = c.no ";

    // PDO 네이티브 준비: 동일 이름 플레이스홀더(:sort_no 등) 재사용 시 HY093 발생 → 이름 분리
    $prevSql = $baseSelect . 'WHERE a.sitekey = :sitekey AND a.board_no = :board_no
              AND ((a.sort_no < :sort_no_lt) OR (a.sort_no = :sort_no_eq AND a.no < :no))
              ORDER BY a.sort_no DESC, a.no DESC LIMIT 1';
    $pst = $connect->prepare($prevSql);
    $pst->execute(array_merge($loc['params'], [
        ':sitekey' => $NO_SITE_UNIQUE_KEY,
        ':board_no' => $board_no,
        ':sort_no_lt' => $sort_no,
        ':sort_no_eq' => $sort_no,
        ':no' => $no,
    ]));
    $prev = $pst->fetch(PDO::FETCH_ASSOC) ?: null;

    $nextSql = $baseSelect . 'WHERE a.sitekey = :sitekey AND a.board_no = :board_no
              AND ((a.sort_no > :sort_no_gt) OR (a.sort_no = :sort_no_eq2 AND a.no > :no2))
              ORDER BY a.sort_no ASC, a.no ASC LIMIT 1';
    $nst = $connect->prepare($nextSql);
    $nst->execute(array_merge($loc['params'], [
        ':sitekey' => $NO_SITE_UNIQUE_KEY,
        ':board_no' => $board_no,
        ':sort_no_gt' => $sort_no,
        ':sort_no_eq2' => $sort_no,
        ':no2' => $no,
    ]));
    $next = $nst->fetch(PDO::FETCH_ASSOC) ?: null;

    return ['prev' => $prev, 'next' => $next];
}

/**
 * board_no=2 등 미디어 스트립용 items (direct_url 외부 링크)
 *
 * @param list<array<string,mixed>> $rows
 * @return list<array<string,mixed>>
 */
function board_rows_to_media_items(array $rows): array
{
    $items = [];
    foreach ($rows as $row) {
        $href = function_exists('safe_url')
            ? safe_url(trim((string) ($row['direct_url'] ?? '')), ['http', 'https'], true)
            : trim((string) ($row['direct_url'] ?? ''));
        if ($href === '') {
            continue;
        }
        $img = board_thumb_public_url(isset($row['thumb_image']) ? (string) $row['thumb_image'] : null);
        if ($img === '') {
            $img = 'sub/thumb_1.png';
        }
        $items[] = [
            'title' => (string) ($row['title'] ?? ''),
            'href'  => $href,
            'image' => $img,
        ];
    }

    return $items;
}

function getBoardLimit($board_no, $limit = 5, $orderby = null) {
    global $NO_SITE_UNIQUE_KEY;
    $connect = DB::getInstance();
    $loc = getBoardLocaleSelect('a');

    $mainqry = "WHERE a.sitekey = :sitekey AND a.board_no = :board_no";
    $orderByqry = $orderby ? $orderby : "a.is_notice='Y' DESC, a.regdate DESC";

    $query = "SELECT a.no, a.board_no, a.user_no, a.category_no, a.comment_cnt,
                     a.regdate, a.read_cnt, a.direct_url, a.thumb_image, a.sort_no, a.is_admin_writed, a.is_notice,
                     a.is_secret, a.secret_pwd, a.isFile, a.file_attach_1, a.file_attach_origin_1,
                     a.file_attach_2, a.file_attach_origin_2, a.file_attach_3, a.file_attach_origin_3,
                     a.file_attach_4, a.file_attach_origin_4, a.file_attach_5, a.file_attach_origin_5,
                     {$loc['select']},
                     b.title as board_name, c.name as category_name
              FROM nb_board a
              {$loc['join']}
              LEFT JOIN nb_board_manage b ON a.board_no = b.no
              LEFT JOIN nb_board_category c ON a.category_no = c.no
              {$mainqry} ORDER BY {$orderByqry} LIMIT " . (int)$limit;

    $stmt = $connect->prepare($query);
    $stmt->execute(array_merge($loc['params'], [':sitekey' => $NO_SITE_UNIQUE_KEY, ':board_no' => $board_no]));

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBoardRole($board_no, $user_lev) {
    global $NO_SITE_UNIQUE_KEY;
    $connect = DB::getInstance();

    $query = "SELECT no, lev_no, role_write, role_edit, role_view, role_list, role_delete, role_comment 
              FROM nb_board_lev_manage 
              WHERE sitekey = :sitekey AND lev_no = :user_lev AND board_no = :board_no";
    
    $stmt = $connect->prepare($query);
    $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY, ':user_lev' => $user_lev, ':board_no' => $board_no]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBoardCategory($board_no) {
    global $NO_SITE_UNIQUE_KEY;
    $connect = DB::getInstance();

    $query = "SELECT no, name 
              FROM nb_board_category 
              WHERE sitekey = :sitekey AND board_no = :board_no 
              ORDER BY sort_no ASC";
    
    $stmt = $connect->prepare($query);
    $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY, ':board_no' => $board_no]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBoardCnt($board_no) {
    $connect = DB::getInstance();

    $query = "SELECT COUNT(no) as cnt 
              FROM nb_board 
              WHERE board_no = :board_no";
    
    $stmt = $connect->prepare($query);
    $stmt->execute([':board_no' => $board_no]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
}

function getBoardCategoryCnt($category_no) {
    $connect = DB::getInstance();

    $query = "SELECT COUNT(no) as cnt
              FROM nb_board
              WHERE category_no = :category_no";

    $stmt = $connect->prepare($query);
    $stmt->execute([':category_no' => $category_no]);

    return $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
}

/**
 * board_no → 프론트 라벨 (4=BLOG / 5=NEWS / 6=RESEARCH)
 */
function board_no_to_label(int $boardNo): string
{
    return [4 => 'BLOG', 5 => 'NEWS', 6 => 'RESEARCH'][$boardNo] ?? '';
}

/**
 * board_no → 상세 라우트명
 */
function board_no_to_route(int $boardNo): string
{
    return [
        4 => 'insight.blog.show',
        5 => 'news.show',
        6 => 'insight.research.show',
    ][$boardNo] ?? '';
}

/**
 * 프론트 카드 리스트용으로 nb_board 행을 정규화한다.
 * 정렬: sort_no ASC → no ASC (admin에서 부여한 노출 순서 그대로)
 *
 * @return list<array<string,mixed>>
 */
function list_board_posts(int $boardNo, int $limit = 100): array
{
    $rows      = getBoardForFront($boardNo, $limit);
    $routeName = board_no_to_route($boardNo);
    $label     = board_no_to_label($boardNo);

    // RESEARCH(board_no=6)는 direct_url로 외부 링크. 그 외는 내부 상세 라우트.
    $isExternal = $boardNo === 6;

    $items = [];
    foreach ($rows as $row) {
        $no = (int) ($row['no'] ?? 0);
        if ($isExternal) {
            $rawUrl = trim((string) ($row['direct_url'] ?? ''));
            $href   = function_exists('safe_url') ? safe_url($rawUrl, ['http', 'https'], true) : $rawUrl;
        } else {
            $href = $routeName !== '' ? route($routeName, ['no' => $no]) : '#';
        }
        $ts = !empty($row['regdate']) ? strtotime((string) $row['regdate']) : false;

        // RESEARCH(6): extra1=conference 라벨(앵커로 노출), contents 본문은 카드 요약(plain text)으로 사용
        // BLOG/NEWS: extra1을 일반 'authors' 슬롯으로 (현 단계 호환)
        $extra1 = isset($row['extra1']) ? (string) $row['extra1'] : '';
        $excerpt = '';
        if ($isExternal) {
            $rawContents = (string) ($row['contents'] ?? '');
            $decoded     = html_entity_decode($rawContents, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $plain       = trim((string) preg_replace('/\s+/u', ' ', strip_tags($decoded)));
            $excerpt     = mb_strlen($plain) > 240 ? mb_substr($plain, 0, 240) . '…' : $plain;
        }

        $thumbName = isset($row['thumb_image']) ? (string) $row['thumb_image'] : '';
        $thumbExt  = strtolower((string) pathinfo($thumbName, PATHINFO_EXTENSION));
        $isVideo   = in_array($thumbExt, ['mp4', 'mov', 'avi', 'webm'], true);

        $items[] = [
            'href'       => $href !== '' ? $href : '#',
            'external'   => $isExternal,
            'media_type' => $isVideo ? 'video' : 'image',
            'src'        => board_thumb_public_url($thumbName !== '' ? $thumbName : null),
            'src_webp'   => $isVideo ? '' : board_thumb_webp_url($thumbName !== '' ? $thumbName : null),
            'alt'        => (string) ($row['title'] ?? ''),
            'category'   => $label,
            'title'      => (string) ($row['title'] ?? ''),
            'date'       => $ts ? date('M j, Y', $ts) : '',
            'date_iso'   => $ts ? date('Y-m-d', $ts) : '',
            'conference' => $isExternal ? $extra1 : '',
            'excerpt'    => $excerpt,
            'authors'    => $isExternal ? '' : $extra1,
            'venue'      => isset($row['extra2']) ? (string) $row['extra2'] : '',
        ];
    }

    return $items;
}

/**
 * 단건 상세를 post-detail 컴포넌트 인자 형태로 정규화한다.
 *
 * @param array<string,mixed> $row  nb_board 행
 * @return array<string,mixed>
 */
function format_board_post_for_view(array $row): array
{
    $boardNo  = (int) ($row['board_no'] ?? 0);
    $ts       = !empty($row['regdate']) ? strtotime((string) $row['regdate']) : false;
    $thumbRaw = isset($row['thumb_image']) ? (string) $row['thumb_image'] : null;
    $thumb    = board_thumb_public_url($thumbRaw);
    $thumbWebp = board_thumb_webp_url($thumbRaw);
    $title    = (string) ($row['title'] ?? '');

    // Summernote contents are stored inconsistently — some rows are raw HTML, others
    // are HTML-entity encoded by the admin save path. Decode unconditionally so the
    // detail view always renders parsed markup. Raw HTML passes through unchanged.
    $rawContents = (string) ($row['contents'] ?? '');
    $contentHtml = html_entity_decode($rawContents, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    return [
        'category'     => board_no_to_label($boardNo),
        'title'        => $title,
        'date'         => $ts ? date('M j, Y', $ts) : '',
        'date_iso'     => $ts ? date('Y-m-d', $ts) : '',
        'media'        => [
            'type'     => 'image',
            'src'      => $thumb,
            'src_webp' => $thumbWebp,
            'alt'      => $title,
        ],
        'content_html' => $contentHtml,
        'authors'      => isset($row['extra1']) ? (string) $row['extra1'] : '',
        'venue'        => isset($row['extra2']) ? (string) $row['extra2'] : '',
        'no'           => (int) ($row['no'] ?? 0),
        'sort_no'      => (int) ($row['sort_no'] ?? 0),
        'board_no'     => $boardNo,
    ];
}
