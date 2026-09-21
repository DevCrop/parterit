<?php
include_once "../../bootstrap/admin.php";
$role->redirectIfReadOnly();

try {
    $db = DB::getInstance(); // PDO 인스턴스
} catch (Exception $e) {
    echo "데이터베이스 연결 오류: " . $e->getMessage();
    exit;
}



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
            try {
                $query = "SELECT no, title, skin, sort_no FROM nb_board_manage ORDER BY no ASC";
                $stmt = $db->query($query); // PDO 인스턴스 사용
                $arrBoardList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                echo "데이터베이스 쿼리 오류: " . $e->getMessage();
                exit;
            }
            ?>

            <form id="frm" name="frm" method="post" enctype="multipart/form-data">
                <input type="hidden" id="mode" name="mode" value="">
                <input type="hidden" id="default_locale" name="default_locale" value="<?= htmlspecialchars(function_exists('project_default_locale') ? project_default_locale() : 'en', ENT_QUOTES, 'UTF-8', false) ?>">

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
                                        <label for="board_no"> 게시판 선택 </label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <select name="board_no" id="board_no">
                                            <option value="">게시판 선택</option>
                                            <?php foreach ($arrBoardList as $board): ?>
                                            <option value="<?= htmlspecialchars($board['no'], ENT_QUOTES, 'UTF-8', false) ?>"
                                                data-board-kind="<?= $board['title'] === '고객사례' ? 'customer-case' : '' ?>"
                                                <?= isset($board_no) && $board_no == $board['no'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($board['title'], ENT_QUOTES, 'UTF-8', false) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i> 글을 등록하시려는 게시판을 선택하세요
                                        </span>
                                    </div>
                                </div>

                                <div class="no-admin-block no_table_category" style="display:none;">
                                    <h3 class="no-admin-title">
                                        <label for="category_no">
                                            게시판 카테고리
                                        </label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <select name="category_no" id="category_no">
                                            <option value="">
                                                카테고리 선택
                                            </option>
                                        </select>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            원하는 카테고리를 선택하세요
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->

                                <div class="no-admin-block no_table_category_depth no-admin-field"
                                    style="display:none;">
                                    <h3 class="no-admin-title">
                                        <label for="category_no">
                                            게시판 카테고리 분류
                                        </label>
                                    </h3>
                                    <div class="no-admin-content">


                                        <div class="no-catg-wrapper">
                                            <select name="depth1" id="category_big" class="no-form-control"
                                                onChange="doChangeCategory(this.value, 'mid');">
                                                <option value="">
                                                    대분류 선택
                                                </option>
                                            </select>

                                            <select name="depth2" id="category_mid" class="no-form-control"
                                                onChange="doChangeCategory(this.value, 'sml');">
                                                <option value="">
                                                    중분류 선택
                                                </option>
                                            </select>

                                            <select name="depth3" id="category_sml" class="no-form-control"
                                                onChange="doChangeCategory(this.value, 'itm');">
                                                <option value="">
                                                    소분류 선택
                                                </option>
                                            </select>

                                            <select name="depth4" id="category_itm" class="no-form-control">
                                                <option value="">
                                                    상세분류 선택
                                                </option>
                                            </select>
                                        </div>



                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            원하는 카테고리를 선택하세요
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->

                                <?php
                                $LOCALES = function_exists('project_supported_locales') ? project_supported_locales() : ['en', 'ko', 'jp'];
                                $DEFAULT_LOCALE = function_exists('project_default_locale') ? project_default_locale() : 'en';
                                $LOCALE_LABEL = ['en' => 'EN', 'ko' => 'KO', 'jp' => 'JP'];
                                $SHOW_LOCALE_TABS = count($LOCALES) > 1; // 단일 언어 운영 시 로케일 탭/라벨 숨김 (config 로 복원 가능)
                                ?>

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
                                        제목 / 내용 은 언어별로 입력하세요. 미입력 언어는 기본 언어(<?= strtoupper($DEFAULT_LOCALE) ?>)로 fallback 됩니다.
                                    </span>
                                </div>
                                <?php endif; ?>

                                <?php foreach ($LOCALES as $i => $lc): ?>
                                    <fieldset class="no-locale-panel <?= $i === 0 ? 'is-active' : '' ?>"
                                        id="board-locale-panel-<?= $lc ?>"
                                        <?php if ($SHOW_LOCALE_TABS): ?>role="tabpanel" aria-labelledby="board-locale-tab-<?= $lc ?>" <?= $i === 0 ? '' : 'hidden' ?><?php endif; ?>>
                                        <?php if ($SHOW_LOCALE_TABS): ?><legend class="sr-only"><?= $LOCALE_LABEL[$lc] ?? strtoupper($lc) ?> 본문</legend><?php endif; ?>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="title_<?= $lc ?>">제목<?= $SHOW_LOCALE_TABS ? ' (' . strtoupper($lc) . ')' : '' ?></label></h3>
                                            <div class="no-admin-content">
                                                <input type="text" name="title_<?= $lc ?>" id="title_<?= $lc ?>" class="no-input--detail"
                                                    placeholder="제목을 입력해주세요." />
                                            </div>
                                        </div>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="contents_<?= $lc ?>">내용<?= $SHOW_LOCALE_TABS ? ' (' . strtoupper($lc) . ')' : '' ?></label></h3>
                                            <div class="no-admin-content">
                                                <textarea name="contents_<?= $lc ?>" id="contents_<?= $lc ?>" class="board-summernote" data-locale="<?= $lc ?>"></textarea>
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
                                            value="<?= isset($data['direct_url']) ? htmlspecialchars($data['direct_url'], ENT_QUOTES, 'UTF-8', false) : '' ?>"
                                            placeholder="링크 URL" class="no-input--detail" />

                                    </div>
                                </div>

                                <div class="no-admin-block no-admin-pos">
                                    <h3 class="no-admin-title"><span>공지</span></h3>
                                    <div class="no-admin-content">
                                        <label for="is_notice" class="no-items-center">
                                            <div class="no-checkbox-form">
                                                <input type="checkbox" name="is_notice" id="is_notice"
                                                    class="no-input--detail" value="Y" />
                                                <span><i class="bx bxs-check-square"></i></span>
                                            </div>
                                            <span class="no-admin-info no-mt">공지사항으로 등록하면 게시판 상단에 고정됩니다.</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="title">썸네일 파일</label></h3>
                                    <div class="no-admin-content">
                                        <div class="no-file-control">
                                            <input type="text" class="no-fake-file" id="fakeThumbFileTxt"
                                                placeholder="파일을 선택해주세요." readonly disabled />
                                            <div class="no-file-box">
                                                <input type="file" name="thumb_image" id="thumb_image"
                                                    accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.avi"
                                                    onchange="document.getElementById('fakeThumbFileTxt').value = this.value" />
                                                <button type="button" class="no-btn no-btn--main">파일찾기</button>
                                            </div>
                                        </div>
                                        <span class="no-admin-info"><i class="bx bxs-info-circle"></i> 갤러리 게시판은 썸네일 파일을
                                            필수 등록해야 합니다.</span>
                                    </div>
                                </div>

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="title">파일첨부</label></h3>
                                    <div class="no-admin-content">
                                        <div class="no-file-wrap">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <div class="no-file-control">
                                                <input type="text" class="no-fake-file" id="fakeFileTxt<?= $i ?>"
                                                    placeholder="파일을 선택해주세요." readonly disabled />
                                                <div class="no-file-box">
                                                    <input type="file" name="addFile<?= $i ?>"
                                                        accept=".jpg,.jpeg,.png,.gif,.webp,.zip,.xls,.xlsx,.ppt,.pptx,.doc,.docx,.pdf,.hwp,.mp4,.mov,.avi,.txt"
                                                        onchange="document.getElementById('fakeFileTxt<?= $i ?>').value = this.value" />
                                                    <button type="button" class="no-btn no-btn--main">파일찾기</button>
                                                </div>
                                            </div>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="no-admin-info"><i class="bx bxs-info-circle"></i> zip, xls, xlsx,
                                            pdf, ppt, pptx, doc, docx, hwp 파일만 등록 가능합니다.</span>
                                    </div>
                                </div>

                                <div class="no-items-center center">
                                    <a href="./board.list.php" class="no-btn no-btn--big no-btn--normal">목록</a>
                                    <a href="javascript:void(0);" class="no-btn no-btn--big no-btn--main"
                                        onClick="doRegSave();">저장</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </main>

        <!-- Footer -->
        <?php if ($SHOW_LOCALE_TABS): ?>
        <script type="text/javascript"
            src="../setting/js/locale-tabs.js?c=<?= htmlspecialchars($STATIC_ADMIN_JS_MODIFY_DATE, ENT_QUOTES, 'UTF-8', false) ?>"></script>
        <?php endif; ?>
        <script type="text/javascript"
            src="./js/board.process.js?c=<?= htmlspecialchars($STATIC_ADMIN_JS_MODIFY_DATE, ENT_QUOTES, 'UTF-8', false) ?>"></script>
        <script type="text/javascript"
            src="./js/board.customer-case.js?c=<?= htmlspecialchars($STATIC_ADMIN_JS_MODIFY_DATE, ENT_QUOTES, 'UTF-8', false) ?>"></script>
        <script type="text/javascript"
            src="./js/board.category.depth.js?c=<?= htmlspecialchars($STATIC_ADMIN_JS_MODIFY_DATE, ENT_QUOTES, 'UTF-8', false) ?>"></script>
        <?php include_once "../../inc/admin.footer.php"; ?>

    </div>
</body>

</html>
