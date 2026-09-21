<?php

include_once "../../bootstrap/admin.php";

$NO_STATIC_TITLE = '자산진단 신청 상세';

$no = isset($_REQUEST['no']) ? (int) $_REQUEST['no'] : 0;
$backParams = $_GET;
unset($backParams['no']);
$searchParam = http_build_query($backParams);

$pdo = DB::getInstance();
$stmt = $pdo->prepare(
    'SELECT * FROM nb_request WHERE no = :no AND sitekey = :sitekey LIMIT 1'
);
$stmt->execute([
    ':no' => $no,
    ':sitekey' => $NO_SITE_UNIQUE_KEY,
]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('정보를 찾을 수 없습니다');
}

$request_type_labels = [
    'free_asset_diagnosis' => '무료 자산진단',
];
$typeKey = (string) ($data['request_type'] ?? '');
$typeLabel = $request_type_labels[$typeKey] ?? $typeKey;

$gLabel = $request_apply_gender_options[(int) ($data['gender'] ?? 0)] ?? '-';
$res = (int) ($data['residence'] ?? 0);
$resLabel = $request_apply_residence_options[$res] ?? '-';
if ($res === 8 && !empty($data['residence_other'])) {
    $resLabel .= ' (' . $data['residence_other'] . ')';
}

$interestLines = [];
$codes = json_decode((string) ($data['interest_codes'] ?? '[]'), true);
if (is_array($codes)) {
    foreach ($codes as $c) {
        $ci = (int) $c;
        if (isset($request_apply_interest_options[$ci])) {
            $interestLines[] = $request_apply_interest_options[$ci];
        }
    }
}

?>

<?php include_once "../../inc/admin.head.php"; ?>

<body data-page="request">
    <div class="no-wrap">
        <?php include_once "../../inc/admin.header.php"; ?>
        <main class="no-app no-container">
            <?php include_once "../../inc/admin.drawer.php"; ?>

            <section class="no-content">
                <div class="no-toolbar">
                    <div class="no-toolbar-container no-flex-stack">
                        <div class="no-page-indicator">
                            <h1 class="no-page-title">자산진단 신청 상세</h1>
                            <div class="no-breadcrumb-container">
                                <ul class="no-breadcrumb-list">
                                    <li class="no-breadcrumb-item"><span>신청 관리</span></li>
                                    <li class="no-breadcrumb-item"><span>자산진단 신청</span></li>
                                    <li class="no-breadcrumb-item"><span>상세</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="no-items-center">
                            <a href="./request.list.php?<?= htmlspecialchars($searchParam, ENT_QUOTES, 'UTF-8') ?>"
                                class="no-btn no-btn--normal no-btn--big">목록</a>
                        </div>
                    </div>
                </div>

                <div class="no-toolbar-container">
                    <div class="no-card">
                        <div class="no-card-header no-card-header--detail">
                            <h2 class="no-card-title">신청 정보</h2>
                        </div>
                        <div class="no-card-body no-admin-column no-admin-column--detail">
                            <div class="no-admin-block">
                                <h3 class="no-admin-title">접수 번호</h3>
                                <div class="no-admin-content">
                                    <p><?= (int) $data['no'] ?></p>
                                </div>
                            </div>
                            <div class="no-admin-block">
                                <h3 class="no-admin-title">요청 유형</h3>
                                <div class="no-admin-content">
                                    <p><?= htmlspecialchars($typeLabel, ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                            <div class="no-admin-block">
                                <h3 class="no-admin-title">이름</h3>
                                <div class="no-admin-content">
                                    <p><?= htmlspecialchars((string) $data['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                            <div class="no-admin-block">
                                <h3 class="no-admin-title">연락처</h3>
                                <div class="no-admin-content">
                                    <p><?= htmlspecialchars((string) $data['phone'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                            <div class="no-admin-block">
                                <h3 class="no-admin-title">성별</h3>
                                <div class="no-admin-content">
                                    <p><?= htmlspecialchars($gLabel, ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                            <div class="no-admin-block">
                                <h3 class="no-admin-title">거주지</h3>
                                <div class="no-admin-content">
                                    <p><?= htmlspecialchars($resLabel, ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                            <div class="no-admin-block">
                                <h3 class="no-admin-title">관심분야</h3>
                                <div class="no-admin-content">
                                    <?php if ($interestLines !== []) { ?>
                                    <ul style="margin:0;padding-left:1.25rem">
                                        <?php foreach ($interestLines as $line) { ?>
                                        <li><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8') ?></li>
                                        <?php } ?>
                                    </ul>
                                    <?php } else { ?>
                                    <p>-</p>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="no-admin-block">
                                <h3 class="no-admin-title">등록일</h3>
                                <div class="no-admin-content">
                                    <p><?= htmlspecialchars((string) $data['regdate'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <?php include_once "../../inc/admin.footer.php"; ?>
    </div>
</body>

</html>
