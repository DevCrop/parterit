<?php
include_once "../../bootstrap/admin.php";
$role->redirectIfRestrictedMenu('board');

// PDO 인스턴스 가져오기
$pdo = DB::getInstance();

$no = $_REQUEST['no'] ?? null;
/* param */
$board_no = $_REQUEST['board_no'] ?? null;
$page = $_REQUEST['page'] ?? null;
$sdate = $_REQUEST['sdate'] ?? null;
$edate = $_REQUEST['edate'] ?? null;

$searchParam = "board_no=" . $board_no . "&page=" . $page . "&sdate=" . $sdate . "&edate=" . $edate;

// 게시글 정보 가져오기
$query = "SELECT a.* FROM nb_board a WHERE a.no = :no";
$stmt = $pdo->prepare($query);
$stmt->execute([':no' => $no]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("정보를 찾을 수 없습니다");
}

$boardManage_info = getBoardManageInfoByNo($data['board_no']);

// 다국어 본문 (nb_board_locale) 로딩
require_once dirname(__DIR__, 2) . '/Model/BoardLocaleModel.php';
$localeRows = BoardLocaleModel::findAllByBoardId((int)$data['no']);
$LOCALES = function_exists('project_supported_locales') ? project_supported_locales() : ['en', 'ko', 'jp'];
$DEFAULT_LOCALE = function_exists('project_default_locale') ? project_default_locale() : 'en';
$LOCALE_LABEL = ['en' => 'EN', 'ko' => 'KO', 'jp' => 'JP'];
$SHOW_LOCALE_TABS = count($LOCALES) > 1; // 단일 언어 운영 시 로케일 탭/라벨 숨김 (config 로 복원 가능)

/** locale 행이 없거나 비었으면 nb_board legacy 컬럼으로 fallback */
$localeVal = static function (array $localeRows, string $lc, string $field, array $legacy) {
    $v = $localeRows[$lc][$field] ?? null;
    if ($v !== null && $v !== '') return (string)$v;
    return (string)($legacy[$field] ?? '');
};

include_once "../../inc/admin.title.php";
include_once "../../inc/admin.css.php";
include_once "../../inc/admin.js.php";
?>

</head>

<body>
    <div class="no-wrap">
        <!-- Header -->
        <?php include_once "../../inc/admin.header.php"; ?>

        <!-- Main -->
        <main class="no-app no-container">
            <!-- Drawer -->
            <?php include_once "../../inc/admin.drawer.php"; ?>

            <!-- Contents -->
            <?php
            $query = "SELECT no, title, skin, sort_no FROM nb_board_manage ORDER BY no ASC";
            $stmt_2nd = $pdo->query($query);
            $arrBoardList = $stmt_2nd->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <form id="frm" name="frm" method="post" enctype="multipart/form-data">
                <input type="hidden" id="mode" name="mode" value="edit">
                <input type="hidden" id="no" name="no" value="<?= htmlspecialchars($data['no'], ENT_QUOTES, 'UTF-8', false) ?>">
                <input type="hidden" id="default_locale" name="default_locale" value="<?= htmlspecialchars($DEFAULT_LOCALE, ENT_QUOTES, 'UTF-8', false) ?>">

                <input type="hidden" id="depth1" value="<?= htmlspecialchars($data['depth1'], ENT_QUOTES, 'UTF-8', false) ?>">
                <input type="hidden" id="depth2" value="<?= htmlspecialchars($data['depth2'], ENT_QUOTES, 'UTF-8', false) ?>">
                <input type="hidden" id="depth3" value="<?= htmlspecialchars($data['depth3'], ENT_QUOTES, 'UTF-8', false) ?>">
                <input type="hidden" id="depth4" value="<?= htmlspecialchars($data['depth4'], ENT_QUOTES, 'UTF-8', false) ?>">

                <section class="no-content">
                    <!-- Page Title -->
                    <div class="no-toolbar">
                        <div class="no-toolbar-container no-flex-stack">
                            <div class="no-page-indicator">
                                <h1 class="no-page-title">게시글 관리</h1>
                                <div class="no-breadcrumb-container">
                                    <ul class="no-breadcrumb-list">
                                        <li class="no-breadcrumb-item"><span>게시판</span></li>
                                        <li class="no-breadcrumb-item"><span>게시글 관리</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- card-title -->
                    <div class="no-toolbar-container">
                        <div class="no-card">
                            <div class="no-card-header no-card-header--detail">
                                <h2 class="no-card-title">게시글 등록</h2>
                            </div>
                            <div class="no-card-body no-admin-column no-admin-column--detail">
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="board_no">게시판 선택</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <select name="board_no" id="board_no">
                                            <option value="">게시판 선택</option>
                                            <?php foreach ($arrBoardList as $v) { ?>
                                            <option value="<?= htmlspecialchars($v['no'], ENT_QUOTES, 'UTF-8', false) ?>"
                                                data-board-kind="<?= $v['title'] === '고객사례' ? 'customer-case' : '' ?>"
                                                <?php if ($data['board_no'] == $v['no']) echo "selected"; ?>>
                                                <?= htmlspecialchars($v['title'], ENT_QUOTES, 'UTF-8', false) ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            글을 등록하시려는 게시판을 선택하세요
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->

                                <?php
                                $category_yn = $boardManage_info[0]['category_yn'] ?? null;
                                $viewCategory = ($category_yn == "Y") ? "display:flex;" : "display:none;";
                                ?>
                                <div class="no-admin-block no_table_category " style="<?= $viewCategory ?>">
                                    <h3 class="no-admin-title">
                                        <label for="category_no">게시판 카테고리</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <select name="category_no" id="category_no">
                                            <option value="">카테고리 선택</option>
                                            <?php
                                            if ($category_yn == "Y") {
                                                $query = "SELECT no, name FROM nb_board_category WHERE sitekey = :sitekey AND board_no = :board_no ORDER BY sort_no ASC";
                                                $stmt_in = $pdo->prepare($query);
                                                $stmt_in->execute([':sitekey' => $NO_SITE_UNIQUE_KEY, ':board_no' => $data['board_no']]);
                                                while ($row = $stmt_in->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value="<?= htmlspecialchars($row['no'], ENT_QUOTES, 'UTF-8', false) ?>"
                                                <?php if ($data['category_no'] == $row['no']) echo "selected"; ?>>
                                                <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8', false) ?>
                                            </option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            원하는 카테고리를 선택하세요
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->

                                <?php
                                for ($i = 1; $i <= 30; $i++) {
                                    $extra_field_key = 'extra_match_field' . $i;
                                    $extra_field = $boardManage_info[0][$extra_field_key] ?? null;
                                    $extra_value = $data['extra' . $i] ?? null;

                                    if (!empty($extra_field)) { ?>
                                <div class="no-admin-block extra_fields">
                                    <h3 class="no-admin-title">
                                        <label for="extra<?= $i ?>"><?= htmlspecialchars($extra_field, ENT_QUOTES, 'UTF-8', false) ?></label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <?php if (($data['board_no'] == 28 && ($i == 1 || $i == 2)) ||
                                                    ($data['board_no'] == 29 && $i >= 1 && $i <= 5)
                                                ): ?>
                                        <!-- 포트폴리오 게시판의 extra1, extra2 또는 멤버 소개 게시판의 extra1~5는 textarea로 렌더링 -->
                                        <textarea name="extra<?= $i ?>" id="extra<?= $i ?>" class="no-textarea--detail"
                                            rows="5"
                                            placeholder="<?= htmlspecialchars($extra_field, ENT_QUOTES, 'UTF-8', false) ?>"><?= htmlspecialchars($extra_value, ENT_QUOTES, 'UTF-8', false) ?></textarea>
                                        <?php else: ?>
                                        <!-- 다른 필드는 input으로 렌더링 -->
                                        <input type="text" name="extra<?= $i ?>" id="extra<?= $i ?>"
                                            value="<?= htmlspecialchars($extra_value, ENT_QUOTES, 'UTF-8', false) ?>" class="no-input--detail"
                                            placeholder="<?= htmlspecialchars($extra_field, ENT_QUOTES, 'UTF-8', false) ?>" />
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php }
                                } ?>



                                <!-- locale 탭: 제목 / 내용 (다국어 운영 시에만 노출) -->
                                <?php if ($SHOW_LOCALE_TABS): ?>
                                <div class="no-admin-block wide">
                                    <div class="no-locale-tabs" role="tablist" aria-label="언어 선택">
                                        <?php foreach ($LOCALES as $i => $lc): ?>
                                            <button type="button"
                                                class="no-locale-tab <?= $i === 0 ? 'is-active' : '' ?>"
                                                role="tab"
                                                id="board-locale-tab-<?= $lc ?>"
                                                aria-controls="board-locale-panel-<?= $lc ?>"
                                                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                                                data-locale="<?= $lc ?>">
                                                <?= $LOCALE_LABEL[$lc] ?? strtoupper($lc) ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                    <span class="no-admin-info" style="display:block;margin-top:6px;">
                                        <i class="bx bxs-info-circle"></i>
                                        제목 / 내용 을 언어별로 입력하세요. 미입력 언어는 기본 언어(<?= strtoupper($DEFAULT_LOCALE) ?>)로 fallback 됩니다.
                                    </span>
                                </div>
                                <?php endif; ?>

                                <?php foreach ($LOCALES as $i => $lc): ?>
                                    <?php
                                    $tVal = $localeVal($localeRows, $lc, 'title', $data);
                                    $cRaw = $localeVal($localeRows, $lc, 'contents', $data);
                                    $cVal = htmlspecialchars_decode($cRaw);
                                    ?>
                                    <fieldset class="no-locale-panel <?= $i === 0 ? 'is-active' : '' ?>"
                                        id="board-locale-panel-<?= $lc ?>"
                                        <?php if ($SHOW_LOCALE_TABS): ?>role="tabpanel" aria-labelledby="board-locale-tab-<?= $lc ?>" <?= $i === 0 ? '' : 'hidden' ?><?php endif; ?>>
                                        <?php if ($SHOW_LOCALE_TABS): ?><legend class="sr-only"><?= $LOCALE_LABEL[$lc] ?? strtoupper($lc) ?> 본문</legend><?php endif; ?>

                                        <div class="no-admin-block no-admin-pos">
                                            <h3 class="no-admin-title"><label for="title_<?= $lc ?>">제목<?= $SHOW_LOCALE_TABS ? ' (' . strtoupper($lc) . ')' : '' ?></label></h3>
                                            <div class="no-admin-content">
                                                <input type="text" name="title_<?= $lc ?>" id="title_<?= $lc ?>"
                                                    value="<?= htmlspecialchars($tVal, ENT_QUOTES, 'UTF-8', false) ?>"
                                                    class="no-input--detail"
                                                    placeholder="제목을 입력해주세요." />
                                            </div>
                                        </div>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="contents_<?= $lc ?>">내용<?= $SHOW_LOCALE_TABS ? ' (' . strtoupper($lc) . ')' : '' ?></label></h3>
                                            <div class="no-admin-content">
                                                <div class="no-admin-check">
                                                    <textarea name="contents_<?= $lc ?>" id="contents_<?= $lc ?>" class="board-summernote" data-locale="<?= $lc ?>"><?= $cVal ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                <?php endforeach; ?>

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="write_name">작성자</label></h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="write_name" id="write_name"
                                            value="<?= htmlspecialchars($NO_ADM_NAME ?? '', ENT_QUOTES, 'UTF-8', false) ?>" placeholder="사이트관리자"
                                            class="no-input--detail" />
                                    </div>
                                </div>

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="direct_url">링크 URL</label></h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="direct_url" id="direct_url"
                                            value="<?= $data['direct_url'] ?>" placeholder="링크 URL"
                                            class="no-input--detail" />
                                    </div>
                                </div>

                                <div class="no-admin-block no-admin-pos">
                                    <h3 class="no-admin-title">
                                        <label for="regdate">작성일자</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="regdate" id="regdate"
                                            value="<?= htmlspecialchars($data['regdate'], ENT_QUOTES, 'UTF-8', false) ?>" class="no-input--detail"
                                            placeholder="작성일자를 입력해주세요." />
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            작성일자를 변경할 수 있습니다. 반드시 기존 형식에 맞추어 수정해야 합니다.
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><span>공지</span></h3>
                                    <div class="no-admin-content">
                                        <label for="is_notice" class="no-items-center">
                                            <div class="no-checkbox-form">
                                                <input type="checkbox" name="is_notice" id="is_notice"
                                                    class="no-input--detail" value="Y"
                                                    <?= ($data['is_notice'] === 'Y') ? 'checked' : '' ?> />

                                                <span><i class="bx bxs-check-square"></i></span>
                                            </div>
                                            <span class="no-admin-info no-mt">공지사항으로 등록하면 게시판 상단에 고정됩니다.</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="title">썸네일 파일</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <div class="no-file-control">
                                            <input type="text" class="no-fake-file" id="fakeThumbFileTxt"
                                                placeholder="파일을 선택해주세요." readonly disabled />
                                            <div class="no-file-box">
                                                <input type="file" name="thumb_image" id="thumb_image"
                                                    accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.avi"
                                                    onchange="javascript:document.getElementById('fakeThumbFileTxt').value = this.value" />
                                                <button type="button" class="no-btn no-btn--main">
                                                    파일찾기
                                                </button>
                                            </div>
                                        </div>
                                        <!-- file control -->
                                        <?php if ($data['thumb_image']) { ?>
                                        <div class="no-board-thumb">
                                            <a href="/inc/lib/board.file.download.php?no=<?= $data['no'] ?>&fld=thumb">
                                                <?= $data['thumb_image'] ?>
                                            </a>
                                            <label class="no-thumb-check">
                                                <div class="no-checkbox-form">
                                                    <input type="checkbox" name="attach_file_del[]" value="0">
                                                    <span>
                                                        <i class="bx bxs-check-square"></i>
                                                    </span>
                                                </div>
                                                <span>삭제</span>
                                            </label>
                                        </div>
                                        <?php } ?>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            갤러리 게시판은 썸네일 파일을 필수 등록해야 합니다.
                                        </span>
                                    </div>
                                </div>


                                <!-- 파일 첨부 -->
                                <div class="no-admin-block no-admin-field">
                                    <h3 class="no-admin-title">
                                        <label for="title">파일첨부</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <div class="no-file-wrap">
                                            <?php for ($i = 1; $i <= 5; $i++) { ?>
                                            <div class="no-file-control">
                                                <input type="text" class="no-fake-file" id="fakeFileTxt<?= $i ?>"
                                                    placeholder="파일을 선택해주세요." readonly disabled />
                                                <div class="no-file-box">
                                                    <input type="file" name="addFile<?= $i ?>"
                                                        accept=".jpg,.jpeg,.png,.gif,.webp,.zip,.xls,.xlsx,.ppt,.pptx,.doc,.docx,.pdf,.hwp,.mp4,.mov,.avi,.txt"
                                                        onchange="document.getElementById('fakeFileTxt<?= $i ?>').value = this.value" />
                                                    <button type="button" class="no-btn no-btn--main">파일찾기</button>
                                                </div>
                                                <?php if (!empty($data['file_attach_' . $i])) { ?>
                                                <div class="no-board-thumb">
                                                    <a
                                                        href="/inc/lib/board.file.download.php?no=<?= htmlspecialchars($data['no'], ENT_QUOTES, 'UTF-8', false) ?>&fld=attach<?= $i ?>">
                                                        <?= htmlspecialchars($data['file_attach_origin_' . $i], ENT_QUOTES, 'UTF-8', false) ?>
                                                    </a>
                                                    <label class="no-thumb-check">
                                                        <div class="no-checkbox-form">
                                                            <input type="checkbox" name="attach_file_del[]"
                                                                value="<?= $i ?>">
                                                            <span><i class="bx bxs-check-square"></i></span>
                                                        </div>
                                                        <span>삭제</span>
                                                    </label>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <span class="no-admin-info">
                                            zip,xls,xlsx,pdf,ppt,pptx,doc,docx,hwp 파일만 등록 가능합니다.
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->


                                <div class="no-items-center center">
                                    <?php if (!$role->isReadOnly()): ?>
                                    <a href="javascript:void(0);" class="no-btn no-btn--big no-btn--delete-outline"
                                        onClick="doDelete(<?= htmlspecialchars($data['no'], ENT_QUOTES, 'UTF-8', false) ?>);">삭제</a>
                                    <?php endif; ?>
                                    <a href="./board.list.php?<?= $searchParam ?>"
                                        class="no-btn no-btn--big no-btn--normal">목록</a>
                                    <?php if (!$role->isReadOnly()): ?>
                                    <a href="javascript:void(0);" class="no-btn no-btn--big no-btn--main"
                                        onClick="doEditSave();">수정</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- card-body -->
                        </div>
                    </div>
                </section>
            </form>
        </main>

        <!-- Footer -->
        <?php if ($SHOW_LOCALE_TABS): ?>
        <script type="text/javascript" src="../setting/js/locale-tabs.js?v=<?= date('YmdHis') ?>"></script>
        <?php endif; ?>
        <script type="text/javascript" src="./js/board.process.js?v=<?= date('YmdHis') ?>"></script>
        <script type="text/javascript" src="./js/board.customer-case.js?v=<?= date('YmdHis') ?>"></script>
        <?php include_once "../../inc/admin.footer.php"; ?>

    </div>
</body>

</html>
