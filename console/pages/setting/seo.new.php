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
        // 라우트 등록을 위해 파일 include (라우터는 이미 초기화되어 있음)
        include_once $routesFile;
    }

    // 이름이 있는 라우트 목록 가져오기
    $namedRoutes = $router->getNamedRoutes();
    $routeOptions = [];

    // SEO 등록 대상 라우트 화이트리스트
    // 현재 공개 라우트 중 SEO 등록 대상
    $allowedRouteNames = [
        'home',
        'company',
        'business',
        'business.rx',
        'business.poc',
        'business.partnership',
        'insight.blog',
        'insight.blog.show',
        'insight.research',
        'news',
        'news.show',
        'careers',
        'contact',
    ];

    foreach ($namedRoutes as $name => $route) {
        $uri = $route->getUri();
        $method = $route->getMethod();

        // GET 메서드만 표시 (POST 등은 제외)
        // 인증(auth) 관련 라우트는 제외
        // 화이트리스트에 등록된 라우트만 노출 (insight-dark / legal / sitemap / robots 제외)
        if ($method === 'GET'
            && strpos($name, 'auth.') !== 0
            && in_array($name, $allowedRouteNames, true)) {
            // 한국어 표시명 생성
            $displayName = $buildRouteDisplayName($name, $uri);

            // 그룹명 추출 (첫 번째 점 이전)
            $parts = explode('.', $name);
            $groupName = $parts[0] ?? '';
            $groupLabel = $buildRouteGroupLabel($groupName);

            // 라우트 이름과 URI를 함께 저장
            $routeOptions[] = [
                'name' => $name,
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
    echo "데이터베이스 연결 오류: " . $e->getMessage();
    exit;
}


include_once "../../inc/admin.title.php";
include_once "../../inc/admin.css.php";
include_once "../../inc/admin.js.php";
?>

<script>
    // PHP에서 준비한 라우트 목록을 JavaScript에 전달
    window.routeOptions = <?= json_encode($routeOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

</head>

<body data-page="seo">
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

                                <!-- 경로 입력 -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="path">경로</label></h3>
                                    <div class="no-admin-content">
                                        <select name="path" id="path" required>
                                            <option value="">페이지 경로 선택</option>
                                            <?php
                                            $currentGroup = '';
                                            foreach ($routeOptions as $route):
                                                // 그룹이 변경되면 optgroup 시작/종료
                                                if ($currentGroup !== $route['groupLabel']):
                                                    if ($currentGroup !== ''):
                                                        echo '</optgroup>';
                                                    endif;
                                                    $currentGroup = $route['groupLabel'];
                                                    echo '<optgroup label="' . htmlspecialchars($route['groupLabel'], ENT_QUOTES, 'UTF-8', false) . '">';
                                                endif;
                                            ?>
                                                <option value="<?= htmlspecialchars($route['uri'], ENT_QUOTES, 'UTF-8', false) ?>">
                                                    <?= htmlspecialchars($route['display'], ENT_QUOTES, 'UTF-8', false) ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <?php if ($currentGroup !== ''): ?>
                                                </optgroup>
                                            <?php endif; ?>
                                        </select>
                                        <div class="no-admin-desc" style="margin-top: 5px; color: #888;">
                                            실제 페이지 경로를 입력하세요 (지점 폴더 하위 기준).
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $LOCALES = function_exists('project_supported_locales') ? project_supported_locales() : ['en', 'ko', 'jp'];
                                $DEFAULT_LOCALE = function_exists('project_default_locale') ? project_default_locale() : 'en';
                                $LOCALE_LABEL = ['en' => 'EN', 'ko' => 'KO', 'jp' => 'JP'];
                                $SHOW_LOCALE_TABS = count($LOCALES) > 1; // 단일 언어 운영 시 로케일 탭/안내 숨김 (config 로 복원 가능)
                                ?>

                                <!-- locale 탭 (다국어 운영 시에만 노출) -->
                                <?php if ($SHOW_LOCALE_TABS): ?>
                                <div class="no-admin-block wide">
                                    <div class="no-locale-tabs" role="tablist" aria-label="언어 선택">
                                        <?php foreach ($LOCALES as $i => $lc): ?>
                                            <button type="button"
                                                class="no-locale-tab <?= $i === 0 ? 'is-active' : '' ?>"
                                                role="tab"
                                                id="locale-tab-<?= $lc ?>"
                                                aria-controls="locale-panel-<?= $lc ?>"
                                                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                                                data-locale="<?= $lc ?>">
                                                <?= $LOCALE_LABEL[$lc] ?? strtoupper($lc) ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                    <span class="no-admin-info" style="display:block;margin-top:6px;">
                                        <i class="bx bxs-info-circle"></i>
                                        기본 언어(<?= strtoupper($DEFAULT_LOCALE) ?>)는 필수, 나머지는 미입력 시 기본 언어 값으로 자동 대체됩니다.
                                    </span>
                                </div>
                                <?php endif; ?>

                                <?php foreach ($LOCALES as $i => $lc): ?>
                                    <fieldset class="no-locale-panel <?= $i === 0 ? 'is-active' : '' ?>"
                                        id="locale-panel-<?= $lc ?>"
                                        <?php if ($SHOW_LOCALE_TABS): ?>role="tabpanel" aria-labelledby="locale-tab-<?= $lc ?>" <?= $i === 0 ? '' : 'hidden' ?><?php endif; ?>>
                                        <?php if ($SHOW_LOCALE_TABS): ?><legend class="sr-only"><?= $LOCALE_LABEL[$lc] ?? strtoupper($lc) ?> 메타 정보</legend><?php endif; ?>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="page_title_<?= $lc ?>">페이지 제목</label></h3>
                                            <div class="no-admin-content">
                                                <input type="text" id="page_title_<?= $lc ?>" name="page_title_<?= $lc ?>"
                                                    placeholder="페이지 제목 입력"
                                                    <?= $lc === $DEFAULT_LOCALE ? 'required' : '' ?>>
                                            </div>
                                        </div>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="meta_title_<?= $lc ?>">메타 타이틀</label></h3>
                                            <div class="no-admin-content">
                                                <input type="text" id="meta_title_<?= $lc ?>" name="meta_title_<?= $lc ?>"
                                                    placeholder="검색결과 제목">
                                            </div>
                                        </div>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="meta_description_<?= $lc ?>">메타 설명글</label></h3>
                                            <div class="no-admin-content">
                                                <textarea name="meta_description_<?= $lc ?>" id="meta_description_<?= $lc ?>"
                                                    class="no-textarea--detail" rows="4" placeholder="검색결과 요약 설명 입력"></textarea>
                                            </div>
                                        </div>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="meta_keywords_<?= $lc ?>">메타 키워드</label></h3>
                                            <div class="no-admin-content">
                                                <input type="text" id="meta_keywords_<?= $lc ?>" name="meta_keywords_<?= $lc ?>"
                                                    placeholder="쉼표(,)로 구분">
                                            </div>
                                        </div>
                                    </fieldset>
                                <?php endforeach; ?>

                                <!-- 검색 노출 설정 (페이지별 robots 메타) -->
                                <div class="no-admin-block wide">
                                    <h3 class="no-admin-title"><label for="robots">검색엔진 노출 설정</label></h3>
                                    <div class="no-admin-content">
                                        <select name="robots" id="robots">
                                            <option value="">자동 (사이트 기본 설정 사용)</option>
                                            <option value="index, follow">노출 + 링크 따라가기 (index, follow)</option>
                                            <option value="noindex, follow">노출 안 함 + 링크 따라가기 (noindex, follow)</option>
                                            <option value="index, nofollow">노출 + 링크 무시 (index, nofollow)</option>
                                            <option value="noindex, nofollow">완전 비공개 (noindex, nofollow)</option>
                                        </select>
                                        <div class="no-admin-desc" style="margin-top:5px;color:#888;">
                                            특정 페이지를 검색 결과에서 빼고 싶을 때 사용합니다. 비워두면 사이트 전역 설정을 따릅니다.
                                        </div>
                                    </div>
                                </div>

                                <!-- OG 이미지 -->
                                <input type="hidden" name="og_image_existing" value="">
                                <div class="no-admin-block wide">
                                    <h3 class="no-admin-title"><label for="og_image_file">OG 이미지</label></h3>
                                    <div class="no-admin-content">
                                        <div class="no-file-wrap">
                                            <div class="no-file-control">
                                                <input type="text" class="no-fake-file" id="fake_og_image_file"
                                                    placeholder="파일을 선택해주세요." readonly disabled />
                                                <div class="no-file-box">
                                                    <input type="file" id="og_image_file" name="og_image_file"
                                                        accept="image/*"
                                                        onchange="document.getElementById('fake_og_image_file').value = this.value" />
                                                    <button type="button" class="no-btn no-btn--main">파일찾기</button>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            SNS 공유 시 표시될 이미지 (1200×630 권장). 비워두면 사이트 전역 OG 이미지를 사용합니다.
                                        </span>
                                    </div>
                                </div>

                                <!-- OG 타입 -->
                                <div class="no-admin-block wide">
                                    <h3 class="no-admin-title"><label for="og_type">OG 타입</label></h3>
                                    <div class="no-admin-content">
                                        <select name="og_type" id="og_type">
                                            <option value="">자동 (전역 설정 사용)</option>
                                            <option value="website">website</option>
                                            <option value="article">article (블로그·뉴스 상세 페이지)</option>
                                            <option value="product">product</option>
                                            <option value="video.other">video.other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Twitter 카드 -->
                                <div class="no-admin-block wide">
                                    <h3 class="no-admin-title"><label for="twitter_card">Twitter 카드</label></h3>
                                    <div class="no-admin-content">
                                        <select name="twitter_card" id="twitter_card">
                                            <option value="">자동 (전역 설정 사용)</option>
                                            <option value="summary_large_image">summary_large_image (대형 이미지)</option>
                                            <option value="summary">summary (소형 이미지)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- 버튼 -->
                                <div class="no-items-center center">
                                    <a href="./seo.php" class="no-btn no-btn--big no-btn--normal">목록</a>
                                    <button type="submit" class="no-btn no-btn--big no-btn--main"
                                        id="submitBtn">저장</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </form>

        </main>
        <?php if ($SHOW_LOCALE_TABS): ?>
        <script type="text/javascript" src="./js/locale-tabs.js"></script>
        <?php endif; ?>
        <?php include_once "../../inc/admin.footer.php"; ?>
    </div>


</body>

</html>
