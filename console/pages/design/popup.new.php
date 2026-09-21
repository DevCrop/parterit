<?php
include_once "../../bootstrap/admin.php";
$role->redirectIfReadOnly();

try {
    $db = DB::getInstance();

    $router = app()->router();
    $buildRouteDisplayName = static function (string $routeName, string $uri): string {
        return $routeName !== '' ? $routeName . ' (' . $uri . ')' : $uri;
    };
    $buildRouteGroupLabel = static function (string $groupName): string {
        return $groupName !== '' ? strtoupper($groupName) : 'ROUTE';
    };

    // routes/web.php 파일을 include하여 라우트 등록
    $routesFile = dirname(__DIR__, 3) . '/routes/web.php';
    if (file_exists($routesFile)) {
        include_once $routesFile;
    }

    // 이름이 있는 라우트 목록 가져오기
    $namedRoutes = $router->getNamedRoutes();
    $routeOptions = [];

    $allowedRoutes = [
        'home', 'about-us',
        'service', 'service.personal', 'service.corporate',
        'service.insurance', 'service.investment', 'service.premium',
        'professionals', 'case-studies',
        'contact', 'contact.faq', 'contact.location',
    ];

    $routeLabels = [
        'home'                      => '메인',
        'about-us'                  => '회사소개',
        'service'                   => '서비스',
        'service.personal'          => '개인 자산관리',
        'service.corporate'         => '법인 자산관리',
        'service.insurance'         => '보험 컨설팅',
        'service.investment'        => '투자 컨설팅',
        'service.premium'           => '프리미엄 PB',
        'professionals'             => '전문가 소개',
        'case-studies'              => '고객 성공사례',
        'contact'                   => '문의 / 무료 자산진단',
        'contact.faq'               => '자주 묻는 질문',
        'contact.location'          => '오시는 길',
    ];

    foreach ($allowedRoutes as $routeName) {
        if (!isset($namedRoutes[$routeName])) {
            continue;
        }

        $route = $namedRoutes[$routeName];
        $uri = $route->getUri();
        $method = $route->getMethod();

        if ($method === 'GET') {
            $label = $routeLabels[$routeName] ?? $routeName;
            $displayName = $label . ' (' . $uri . ')';

            $parts = explode('.', $routeName);
            $groupName = $parts[0] ?? '';
            $groupLabel = $buildRouteGroupLabel($groupName);

            $routeOptions[] = [
                'name' => $routeName,
                'uri' => $uri,
                'display' => $displayName,
                'group' => $groupName,
                'groupLabel' => $groupLabel
            ];
        }
    }

    // 그룹별로 정렬 (그룹명 -> 표시명)
    usort($routeOptions, function ($a, $b) {
        $groupCompare = strcmp($a['groupLabel'], $b['groupLabel']);
        if ($groupCompare !== 0) {
            return $groupCompare;
        }
        return strcmp($a['display'], $b['display']);
    });
} catch (Exception $e) {
    echo "DB 오류: " . $e->getMessage();
    exit;
}

include_once "../../inc/admin.title.php";
include_once "../../inc/admin.css.php";
include_once "../../inc/admin.js.php";
?>

</head>

<body data-page="popup">
    <div class="no-wrap">
        <?php include_once "../../inc/admin.header.php"; ?>

        <main class="no-app no-container">
            <?php include_once "../../inc/admin.drawer.php"; ?>

            <form id="frm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="mode" value="insert">

                <section class="no-content">

                    <div class="no-toolbar">
                        <div class="no-toolbar-container no-flex-stack">
                            <div class="no-page-indicator">
                                <h1 class="no-page-title"><?= $pageName ?> 등록</h1>
                                <div class="no-breadcrumb-container">
                                    <ul class="no-breadcrumb-list">
                                        <li class="no-breadcrumb-item"><span><?= $pageName ?></span></li>
                                        <li class="no-breadcrumb-item"><span><?= $pageName ?> 등록</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="no-toolbar-container">
                        <div class="no-card">
                            <div class="no-card-header no-card-header--detail">
                                <h2 class="no-card-title"><?= $pageName ?> 등록</h2>
                            </div>
                            <div class="no-card-body no-admin-column no-admin-column--detail">

                                <input type="hidden" name="popup_type" value="1">
                                <input type="hidden" name="popup_path" value="/">

                                <!-- 팝업 노출 위치 -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">팝업 노출 위치</h3>
                                    <div class="no-admin-content">
                                        <span class="no-admin-info"><i class="bx bxs-info-circle"></i>팝업은 메인 페이지에서만 노출됩니다.</span>
                                    </div>
                                </div>

                                <!-- 무기한 여부 -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">노출 설정</h3>
                                    <div class="no-admin-content">
                                        <div class="no-radio-form no-list">
                                            <?php foreach ($is_unlimited as $value => $label):
                                                $id = "unlimited_$value";
                                                $checked = ($value == 1) ? 'checked' : '';
                                            ?>
                                            <label for="<?= $id ?>">
                                                <div class="no-radio-box">
                                                    <input type="radio" name="is_unlimited" id="<?= $id ?>"
                                                        value="<?= $value ?>" <?= $checked ?>>
                                                    <span><i class="bx bx-radio-circle-marked"></i></span>
                                                </div>
                                                <span class="no-radio-text"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8', false) ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>


                                <!-- 노출 기간 -->
                                <div class="no-admin-block" id="display_period">
                                    <h3 class="no-admin-title">노출 기간</h3>
                                    <div class="no-admin-content no-admin-date">
                                        <input type="text" name="start_at" id="start_at"
                                            value="<?php echo isset($start_at) ? htmlspecialchars($start_at, ENT_QUOTES, 'UTF-8', false) : ''; ?>" />
                                        <span></span>
                                        <input type="text" name="end_at" id="end_at"
                                            value="<?php echo isset($end_at) ? htmlspecialchars($end_at, ENT_QUOTES, 'UTF-8', false) : ''; ?>" />
                                    </div>
                                </div>


                                <!-- 제목 -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="title">제목</label></h3>
                                    <div class="no-admin-content">
                                        <input type="text" id="title" name="title" required>
                                    </div>
                                </div>

                                <!-- 설명글 -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="description">설명</label></h3>
                                    <div class="no-admin-content">
                                        <textarea name="description" id="description"></textarea>
                                    </div>
                                </div>

                                <!-- 링크 여부 -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">링크 여부</h3>
                                    <div class="no-admin-content">
                                        <div class="no-radio-form no-list">
                                            <?php foreach ($has_link as $value => $label):
                                                $id = "link_$value";
                                                $checked = ($value == 2) ? 'checked' : '';
                                            ?>
                                            <label for="<?= $id ?>">
                                                <div class="no-radio-box">
                                                    <input type="radio" name="has_link" id="<?= $id ?>"
                                                        value="<?= $value ?>" <?= $checked ?>>
                                                    <span><i class="bx bx-radio-circle-marked"></i></span>
                                                </div>
                                                <span class="no-radio-text"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8', false) ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- 새창 여부 -->
                                <div class="no-admin-block" id="link_target_block">
                                    <!-- <-- 여기에 id 추가 -->
                                    <h3 class="no-admin-title">새창 여부</h3>
                                    <div class="no-admin-content">
                                        <div class="no-radio-form no-list">
                                            <?php foreach ($link_targets as $value => $info):
                                                $current_target = isset($popup['is_target']) ? (int)$popup['is_target'] : 1;
                                                $id = "target_$value";
                                                $checked = ($current_target === $value) ? 'checked' : '';
                                            ?>
                                            <label for="<?= $id ?>">
                                                <div class="no-radio-box">
                                                    <input type="radio" name="is_target" id="<?= $id ?>"
                                                        value="<?= $value ?>" <?= $checked ?>>
                                                    <span><i class="bx bx-radio-circle-marked"></i></span>
                                                </div>
                                                <span
                                                    class="no-radio-text"><?= htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8', false) ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>


                                <!-- 링크 URL -->
                                <div class="no-admin-block" id="link_url_block">
                                    <h3 class="no-admin-title"><label for="link_url">링크 URL</label></h3>
                                    <div class="no-admin-content">
                                        <input type="url" id="link_url" name="link_url"
                                            placeholder="http:// 또는 https://">
                                    </div>
                                </div>

                                <!-- 팝업 이미지 -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="popup_image">팝업 이미지</label></h3>
                                    <div class="no-admin-content">
                                        <div class="no-file-control">
                                            <input type="text" class="no-fake-file" id="fakePopupFileTxt"
                                                placeholder="파일을 선택해주세요." readonly disabled />
                                            <div class="no-file-box">
                                                <input type="file" name="popup_image" id="popup_image"
                                                    onchange="document.getElementById('fakePopupFileTxt').value = this.value"
                                                    accept="image/*" />
                                                <button type="button" class="no-btn no-btn--main">파일찾기</button>
                                            </div>
                                        </div>
                                        <span class="no-admin-info"><i class="bx bxs-info-circle"></i>팝업에 사용되는
                                            이미지입니다.</span>
                                    </div>
                                </div>


                                <!-- 정렬 순서 -->
                                <!--
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="sort_no">정렬 순서</label></h3>
                                    <div class="no-admin-content">
                                        <input type="number" id="sort_no" name="sort_no" value="0" min="0">
                                    </div>
                                </div>-->


                                <!-- 버튼 -->
                                <div class="no-items-center center">
                                    <a href="./popup.list.php" class="no-btn no-btn--big no-btn--normal">목록</a>
                                    <button type="submit" class="no-btn no-btn--big no-btn--main"
                                        id="submitBtn">저장</button>
                                </div>

                            </div>

                        </div>
                    </div>

                </section>
            </form>

        </main>
        <?php include_once "../../inc/admin.footer.php"; ?>
    </div>

    <script>
    $(document).ready(function() {
        // Summernote 초기화
        $('#description').summernote({
            height: 300,
            lang: 'ko-KR',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onInit: function() {
                    var self = this;
                    setTimeout(function() {
                        var $editable = $(self).next('.note-editor').find('.note-editable');
                        if ($editable.length) convertEmptyPToDiv($editable[0]);
                    }, 100);
                }
            }
        });

        // 폼 제출
        $('#frm').on('submit', function(e) {
            e.preventDefault();

            // Summernote 내용을 textarea에 동기화
            $('#description').val($('#description').summernote('code'));

            var formData = new FormData(this);

            $.ajax({
                url: '../../Controller/PopupController.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.href = window.noAdminResolveReturnUrl ?
                            window.noAdminResolveReturnUrl('./popup.list.php', document.getElementById('frm')) :
                            './popup.list.php';
                    } else {
                        alert(response.message || '등록에 실패했습니다.');
                    }
                },
                error: function() {
                    alert('처리 중 오류가 발생했습니다.');
                }
            });
        });
    });
    </script>

</body>

</html>
