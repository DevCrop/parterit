<?php

include_once "../../bootstrap/admin.php";

$NO_STATIC_TITLE = '자산진단 신청';

$page = isset($_REQUEST['page']) ? max(1, (int) $_REQUEST['page']) : 1;
$perpage = isset($_REQUEST['perpage']) ? max(1, min(200, (int) $_REQUEST['perpage'])) : 20;
$listCurPage = $page;
$listRowCnt = $perpage;
$pageBlock = $pageBlock ?? 5;
$count = ($listCurPage - 1) * $listRowCnt;

$sdate = trim((string) ($_REQUEST['sdate'] ?? ''));
$edate = trim((string) ($_REQUEST['edate'] ?? ''));
$request_type = trim((string) ($_REQUEST['request_type'] ?? ''));
$gender = isset($_REQUEST['gender']) ? (int) $_REQUEST['gender'] : 0;
$residence = isset($_REQUEST['residence']) ? (int) $_REQUEST['residence'] : 0;
$interest = isset($_REQUEST['interest']) ? (int) $_REQUEST['interest'] : 0;
$searchKeyword = trim((string) ($_REQUEST['searchKeyword'] ?? ''));

$connect = DB::getInstance();
$mainqry = ' WHERE r.sitekey = :sitekey ';
$params = [':sitekey' => $NO_SITE_UNIQUE_KEY];

if ($request_type !== '') {
    $mainqry .= ' AND r.request_type = :request_type ';
    $params[':request_type'] = $request_type;
}

if ($gender > 0) {
    $mainqry .= ' AND r.gender = :gender ';
    $params[':gender'] = $gender;
}

if ($residence > 0) {
    $mainqry .= ' AND r.residence = :residence ';
    $params[':residence'] = $residence;
}

if ($interest > 0) {
    $mainqry .= ' AND JSON_CONTAINS(r.interest_codes, :interest_json) ';
    $params[':interest_json'] = json_encode($interest);
}

if ($searchKeyword !== '') {
    $mainqry .= " AND (r.name LIKE :kw OR REPLACE(r.phone, ' ', '') LIKE :kw2) ";
    $params[':kw'] = '%' . $searchKeyword . '%';
    $kwDigits = preg_replace('/\D+/', '', $searchKeyword) ?? '';
    $params[':kw2'] = '%' . $kwDigits . '%';
}

if ($sdate !== '' && $edate !== '') {
    $mainqry .= " AND (DATE(r.regdate) BETWEEN :sdate AND :edate) ";
    $params[':sdate'] = $sdate;
    $params[':edate'] = $edate;
}

$request_type_labels = [
    'free_asset_diagnosis' => '무료 자산진단',
];

$arrResultSet = [];
$totalCnt = 0;
$Page = 0;
$rnumber = 0;

try {
    $query = "SELECT COUNT(*) AS cnt FROM nb_request r $mainqry";
    $stmt = $connect->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCnt = (int) ($data['cnt'] ?? 0);
    $Page = (int) ceil($totalCnt / $listRowCnt);

    $query = "
        SELECT
            r.no,
            r.request_type,
            r.name,
            r.phone,
            r.gender,
            r.residence,
            r.residence_other,
            r.interest_codes,
            r.regdate
        FROM nb_request r
        $mainqry
        ORDER BY r.regdate DESC, r.no DESC
        LIMIT :count, :listRowCnt
    ";
    $stmt = $connect->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':count', $count, PDO::PARAM_INT);
    $stmt->bindValue(':listRowCnt', $listRowCnt, PDO::PARAM_INT);
    $stmt->execute();
    $arrResultSet = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $typeStmt = $connect->prepare(
        'SELECT DISTINCT request_type FROM nb_request WHERE sitekey = :sitekey ORDER BY request_type ASC'
    );
    $typeStmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY]);
    $distinctTypes = $typeStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    echo '데이터베이스 오류가 발생하였습니다: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}

$searchParam = http_build_query([
    'page' => $page,
    'perpage' => $perpage,
    'sdate' => $sdate,
    'edate' => $edate,
    'request_type' => $request_type,
    'gender' => $gender,
    'residence' => $residence,
    'interest' => $interest,
    'searchKeyword' => $searchKeyword,
]);

?>

<?php include_once "../../inc/admin.head.php"; ?>

<body data-page="request">
    <div class="no-wrap">
        <?php include_once "../../inc/admin.header.php"; ?>
        <main class="no-app no-container">
            <?php include_once "../../inc/admin.drawer.php"; ?>

            <form method="get" name="frm" id="frm" autocomplete="off">
                <section class="no-content">
                    <div class="no-toolbar">
                        <div class="no-toolbar-container no-flex-stack">
                            <div class="no-page-indicator">
                                <h1 class="no-page-title"><?= htmlspecialchars($pageName ?: '자산진단 신청', ENT_QUOTES, 'UTF-8') ?></h1>
                                <div class="no-breadcrumb-container">
                                    <ul class="no-breadcrumb-list">
                                        <li class="no-breadcrumb-item"><span>신청 관리</span></li>
                                        <li class="no-breadcrumb-item"><span>자산진단 신청</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="no-search no-toolbar-container">
                        <div class="no-card">
                            <div class="no-card-header">
                                <h2 class="no-card-title">신청 검색</h2>
                            </div>
                            <div class="no-card-body no-admin-column">
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">요청 유형</h3>
                                    <div class="no-admin-content">
                                        <select name="request_type" id="request_filter_request_type">
                                            <option value="">전체</option>
                                            <?php
                                            $typesToShow = $distinctTypes ?: array_keys($request_type_labels);
                                            foreach ($typesToShow as $t) {
                                                if ($t === '' || $t === null) {
                                                    continue;
                                                }
                                                $t = (string) $t;
                                                $label = $request_type_labels[$t] ?? $t;
                                                ?>
                                            <option value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>"
                                                <?= $request_type === $t ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">성별</h3>
                                    <div class="no-admin-content">
                                        <select name="gender" id="request_filter_gender">
                                            <option value="0">전체</option>
                                            <?php foreach ($request_apply_gender_options as $code => $label) { ?>
                                            <option value="<?= (int) $code ?>"
                                                <?= $gender === (int) $code ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">거주지</h3>
                                    <div class="no-admin-content">
                                        <select name="residence" id="request_filter_residence">
                                            <option value="0">전체</option>
                                            <?php foreach ($request_apply_residence_options as $code => $label) { ?>
                                            <option value="<?= (int) $code ?>"
                                                <?= $residence === (int) $code ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">관심분야 (포함)</h3>
                                    <div class="no-admin-content">
                                        <select name="interest" id="request_filter_interest">
                                            <option value="0">전체</option>
                                            <?php foreach ($request_apply_interest_options as $code => $label) { ?>
                                            <option value="<?= (int) $code ?>"
                                                <?= $interest === (int) $code ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">등록일</h3>
                                    <div class="no-admin-content no-admin-date">
                                        <input type="text" name="sdate" id="sdate"
                                            value="<?= htmlspecialchars($sdate, ENT_QUOTES, 'UTF-8') ?>" />
                                        <span></span>
                                        <input type="text" name="edate" id="edate"
                                            value="<?= htmlspecialchars($edate, ENT_QUOTES, 'UTF-8') ?>" />
                                    </div>
                                </div>
                                <div class="no-admin-block wide">
                                    <h3 class="no-admin-title">이름·연락처</h3>
                                    <div class="no-search-wrap">
                                        <div class="no-search-input">
                                            <i class="bx bx-search-alt-2"></i>
                                            <input name="searchKeyword" id="searchKeyword" type="text"
                                                title="검색어 입력" placeholder="이름 또는 연락처"
                                                value="<?= htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8') ?>" />
                                        </div>
                                        <div class="no-search-btn">
                                            <button type="button" title="검색"
                                                class="no-btn no-btn--main no-btn--search"
                                                onclick="doSearchList();">
                                                검색
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="no-content-container">
                        <div class="no-card">
                            <div class="no-card-header">
                                <h2 class="no-card-title">신청 목록</h2>
                            </div>
                            <div class="no-card-body">
                                <div class="no-table-option">
                                    <span>총 <?= (int) $totalCnt ?>건</span>
                                </div>
                                <div class="no-table-responsive">
                                    <table class="no-table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="no-width-80">번호</th>
                                                <th scope="col">요청 유형</th>
                                                <th scope="col">이름</th>
                                                <th scope="col">연락처</th>
                                                <th scope="col">성별</th>
                                                <th scope="col">거주지</th>
                                                <th scope="col">관심분야</th>
                                                <th scope="col">등록일</th>
                                                <th scope="col" class="no-min-width-role">관리</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($arrResultSet as $v) {
                                                $typeKey = (string) ($v['request_type'] ?? '');
                                                $typeLabel = $request_type_labels[$typeKey] ?? $typeKey;
                                                $gLabel = $request_apply_gender_options[(int) $v['gender']] ?? '-';
                                                $res = (int) $v['residence'];
                                                $resLabel = $request_apply_residence_options[$res] ?? '-';
                                                if ($res === 8 && !empty($v['residence_other'])) {
                                                    $resLabel .= ' (' . $v['residence_other'] . ')';
                                                }
                                                $interestLabels = [];
                                                $codes = json_decode((string) ($v['interest_codes'] ?? '[]'), true);
                                                if (is_array($codes)) {
                                                    foreach ($codes as $c) {
                                                        $ci = (int) $c;
                                                        if (isset($request_apply_interest_options[$ci])) {
                                                            $interestLabels[] = $request_apply_interest_options[$ci];
                                                        }
                                                    }
                                                }
                                                $interestSummary = $interestLabels !== [] ? implode(', ', $interestLabels) : '-';
                                                if (mb_strlen($interestSummary) > 60) {
                                                    $interestSummary = mb_substr($interestSummary, 0, 57) . '…';
                                                }
                                                ?>
                                            <tr>
                                                <td><?= (int) $v['no'] ?></td>
                                                <td><?= htmlspecialchars($typeLabel, ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <a href="./request.view.php?no=<?= (int) $v['no'] ?>&amp;<?= htmlspecialchars($searchParam, ENT_QUOTES, 'UTF-8') ?>">
                                                        <?= htmlspecialchars((string) $v['name'], ENT_QUOTES, 'UTF-8') ?>
                                                    </a>
                                                </td>
                                                <td><?= htmlspecialchars((string) $v['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($gLabel, ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($resLabel, ENT_QUOTES, 'UTF-8') ?></td>
                                                <td style="max-width: 24rem"><?= htmlspecialchars($interestSummary, ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars((string) $v['regdate'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <div class="no-table-role">
                                                        <span class="no-role-btn">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </span>
                                                        <div class="no-table-action">
                                                            <a href="./request.view.php?no=<?= (int) $v['no'] ?>&amp;<?= htmlspecialchars($searchParam, ENT_QUOTES, 'UTF-8') ?>"
                                                                class="no-btn no-btn--sm no-btn--normal">보기</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php if (!$arrResultSet) { ?>
                                    <p>등록된 내용이 없습니다.</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include_once "../../lib/admin.pagination.php"; ?>
                </section>
            </form>
        </main>
        <?php include_once "../../inc/admin.footer.php"; ?>
    </div>
</body>

</html>
