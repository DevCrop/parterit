<?php include_once "../../bootstrap/admin.php";

$role->redirectIfCannotView();

$perpage = 10;
$listCurPage = isset($_POST['page']) ? (int)$_POST['page'] : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
$listCurPage = max(1, $listCurPage);
$pageBlock = 2;
$count = ($listCurPage - 1) * $perpage;

$db = DB::getInstance();

// 총 개수 구하기
$totalStmt = $db->query("SELECT COUNT(*) FROM nb_admin");
$totalCount = (int)$totalStmt->fetchColumn();
$Page = ceil($totalCount / $perpage);

// 실제 데이터 조회
$sql = "SELECT a.no, a.uid, a.uname, a.email, a.phone, a.active_status, a.role_id, r.role_name, a.created_at 
        FROM nb_admin a
        LEFT JOIN nb_roles r ON a.role_id = r.role_id
        ORDER BY a.no DESC
        LIMIT {$count}, {$perpage}";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!--=====================HEAD========================= -->
<?php include_once "../../inc/admin.head.php"; ?>

<body data-page="account">
    <div class="no-wrap">

        <!--=====================HEADER========================= -->
        <?php include_once "../../inc/admin.header.php"; ?>

        <!--=====================MAIN========================= -->
        <main class="no-app no-container">

            <!--=====================DRAWER========================= -->
            <?php include_once "../../inc/admin.drawer.php"; ?>

            <!--=====================CONTENTS========================= -->
            <form method="POST" name="frm" id="frm" autocomplete="off">
                <input type="hidden" name="mode" id="mode" value="list">
                <section class="no-content">
                    <div class="no-toolbar">
                        <div class="no-toolbar-container no-flex-stack">
                            <div class="no-page-indicator">
                                <h1 class="no-page-title"><?=$pageName?> 관리</h1>
                                <div class="no-breadcrumb-container">
                                    <ul class="no-breadcrumb-list">
                                        <li class="no-breadcrumb-item"><span>게시판</span></li>
                                        <li class="no-breadcrumb-item"><span><?=$pageName?> 관리</span></li>
                                    </ul>
                                </div>
                            </div>
                            <?php if (!$role->isReadOnly()): ?>
                            <div class="no-items-center">
                                <a href="./new.php" class="no-btn no-btn--main no-btn--big"> <?=$pageName?> 생성 </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Contents -->
                    <div class="no-content-container">
                        <div class="no-card">
                            <div class="no-card-header">
                                <h2 class="no-card-title"><?=$pageName?> 관리</h2>
                            </div>
                            <div class="no-card-body">
                                <div class="no-table-option">
                                    <ul class="no-table-check-control">
                                        <li><a href="#" class="no-btn no-btn--sm no-btn--check active "
                                                data-action="selectAll">전체선택</a></li>
                                        <li><a href="#" class="no-btn no-btn--sm no-btn--check"
                                                data-action="deselectAll">선택해제</a></li>
                                        <li><a href="#" class="no-btn no-btn--sm no-btn--check"
                                                data-action="deleteSelected">선택삭제</a></li>
                                    </ul>

                                </div>
                                <div class="no-table-responsive">
                                    <table class="no-table">
                                        <thead>
                                            <tr>
                                                <th class="no-width-25 no-check">
                                                    <div class="no-checkbox-form">
                                                        <label>
                                                            <input type="checkbox" id="selectAllCheckbox" />
                                                            <span><i class="bx bxs-check-square"></i></span>
                                                        </label>
                                                    </div>
                                                </th>
                                                <th>번호</th>
                                                <th>아이디</th>
                                                <th>이름</th>
                                                <th>이메일</th>
                                                <th>권한</th>
                                                <th>등록일</th>
                                                <th>상태</th>
                                                <th>관리</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($rows)): ?>
                                            <?php foreach ($rows as $row): ?>
                                            <tr>
                                                <td class="no-check">
                                                    <div class="no-checkbox-form">
                                                        <label>
                                                            <input type="checkbox" class="no-chk"
                                                                value="<?= $row['no'] ?>" />
                                                            <span><i class="bx bxs-check-square"></i></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td><?= $row['no'] ?></td>
                                                <td><?= htmlspecialchars($row['uid'], ENT_QUOTES, 'UTF-8', false) ?></td>
                                                <td><?= htmlspecialchars($row['uname'], ENT_QUOTES, 'UTF-8', false) ?></td>
                                                <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8', false) ?></td>
                                                <td><?= htmlspecialchars($admin_roles[$row['role_id']]['name'] ?? '알 수 없음', ENT_QUOTES, 'UTF-8', false) ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8', false) ?></td>
                                                <td>
                                                    <span
                                                        class="no-btn <?= $row['active_status'] === 'Y' ? 'no-btn--notice' : 'no-btn--normal' ?>">
                                                        <?= $row['active_status'] === 'Y' ? '활성' : '비활성' ?>
                                                    </span>

                                                </td>
                                                <td>
                                                    <div class="no-table-role">
                                                        <span class="no-role-btn"><i
                                                                class="bx bx-dots-vertical-rounded"></i></span>
                                                        <div class="no-table-action">
                                                            <a href="edit.php?no=<?= $row['no'] ?>"
                                                                class="no-btn no-btn--sm no-btn--normal">권한 및 수정 </a>
                                                            <button type="button"
                                                                class="no-btn no-btn--sm no-btn--delete-outline delete-btn"
                                                                data-id="<?= $row['no'] ?>">
                                                                삭제
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <tr>
                                                <td colspan="9">등록된 계정이 없습니다.</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        </div>
                    </div>
                    <?php include_once "../../lib/admin.pagination.php"; ?>
                </section>
            </form>
        </main>
    </div>
    <?php include_once "../../inc/admin.footer.php"; ?>
