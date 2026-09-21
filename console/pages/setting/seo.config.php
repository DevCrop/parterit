<?php
include_once "../../bootstrap/admin.php";
$role->redirectIfReadOnly();

$db = DB::getInstance();

$LOCALES = function_exists('project_supported_locales') ? project_supported_locales() : ['en', 'ko', 'jp'];
$DEFAULT_LOCALE = function_exists('project_default_locale') ? project_default_locale() : 'en';

$LOCALE_LABEL = ['en' => 'EN', 'ko' => 'KO', 'jp' => 'JP'];
$SHOW_LOCALE_TABS = count($LOCALES) > 1; // 단일 언어 운영 시 로케일 탭/안내 숨김 (config 로 복원 가능)

// 사이트 메타 + 다국어 컬럼 + 파일
$stmt = $db->prepare("SELECT * FROM nb_siteinfo WHERE sitekey = :sitekey LIMIT 1");
$stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY]);
$siteinfo = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$val = static function (array $row, string $key) {
    return isset($row[$key]) ? (string)$row[$key] : '';
};

// 사이트맵 파일 정보 (project root 기준 sitemap.xml)
$sitemapPath = ROOT_PATH . '/sitemap.xml';
$sitemapExists = is_file($sitemapPath);
$sitemapMtime = $sitemapExists ? date('Y-m-d H:i:s', (int)filemtime($sitemapPath)) : '';
$sitemapSize = $sitemapExists ? (int)filesize($sitemapPath) : 0;
?>

<?php include_once "../../inc/admin.head.php"; ?>

<body data-page="seo-config">
    <div class="no-wrap">
        <?php include_once "../../inc/admin.header.php"; ?>

        <main class="no-app no-container">
            <?php include_once "../../inc/admin.drawer.php"; ?>

            <form id="frm" name="frm" method="post" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" id="mode" name="mode" value="">
                <input type="hidden" name="meta_favicon_ico_filename" value="<?= htmlspecialchars($val($siteinfo, 'meta_favicon_ico'), ENT_QUOTES, 'UTF-8', false) ?>">
                <input type="hidden" name="meta_thumb_filename" value="<?= htmlspecialchars($val($siteinfo, 'meta_thumb'), ENT_QUOTES, 'UTF-8', false) ?>">

                <section class="no-content">
                    <div class="no-toolbar">
                        <div class="no-toolbar-container no-flex-stack">
                            <div class="no-page-indicator">
                                <h1 class="no-page-title">SEO 관리</h1>
                                <div class="no-breadcrumb-container">
                                    <ul class="no-breadcrumb-list">
                                        <li class="no-breadcrumb-item"><span>사이트정보관리</span></li>
                                        <li class="no-breadcrumb-item"><span>SEO 관리</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 카드 1: 다국어 사이트 메타 -->
                    <div class="no-toolbar-container">
                        <div class="no-card">
                            <div class="no-card-header no-card-header--detail">
                                <h2 class="no-card-title"><?= $SHOW_LOCALE_TABS ? '다국어 사이트 메타' : '사이트 메타' ?></h2>
                                <?php if ($SHOW_LOCALE_TABS): ?>
                                <div class="no-admin-header">
                                    <span class="no-admin-info">
                                        <i class="bx bxs-info-circle"></i>
                                        지원 언어 (<?= implode(', ', array_map('strtoupper', $LOCALES)) ?>) 별로 사이트 타이틀 / 메타 정보를 입력합니다.
                                    </span>
                                    <span class="no-admin-info">
                                        <i class="bx bxs-info-circle"></i>
                                        해당 언어가 비어 있으면 기본 언어(<?= strtoupper($DEFAULT_LOCALE) ?>) 값으로 자동 대체됩니다.
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="no-card-body no-admin-column no-admin-column--detail">
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
                                </div>
                                <?php endif; ?>

                                <?php foreach ($LOCALES as $i => $lc): ?>
                                    <fieldset class="no-locale-panel <?= $i === 0 ? 'is-active' : '' ?>"
                                        id="locale-panel-<?= $lc ?>"
                                        <?php if ($SHOW_LOCALE_TABS): ?>role="tabpanel" aria-labelledby="locale-tab-<?= $lc ?>" <?= $i === 0 ? '' : 'hidden' ?><?php endif; ?>>
                                        <?php if ($SHOW_LOCALE_TABS): ?><legend class="sr-only"><?= $LOCALE_LABEL[$lc] ?? strtoupper($lc) ?> 메타 정보</legend><?php endif; ?>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="title_<?= $lc ?>">사이트 타이틀</label></h3>
                                            <div class="no-admin-content">
                                                <input type="text" id="title_<?= $lc ?>" name="title_<?= $lc ?>"
                                                    class="no-input--detail"
                                                    placeholder="브라우저 탭에 표시되는 사이트명"
                                                    value="<?= htmlspecialchars($val($siteinfo, 'title_' . $lc), ENT_QUOTES, 'UTF-8', false) ?>" />
                                            </div>
                                        </div>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="meta_title_<?= $lc ?>">메타 타이틀</label></h3>
                                            <div class="no-admin-content">
                                                <input type="text" id="meta_title_<?= $lc ?>" name="meta_title_<?= $lc ?>"
                                                    class="no-input--detail"
                                                    placeholder="검색 결과 제목"
                                                    value="<?= htmlspecialchars($val($siteinfo, 'meta_title_' . $lc), ENT_QUOTES, 'UTF-8', false) ?>" />
                                            </div>
                                        </div>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="meta_description_<?= $lc ?>">메타 설명</label></h3>
                                            <div class="no-admin-content">
                                                <textarea id="meta_description_<?= $lc ?>" name="meta_description_<?= $lc ?>"
                                                    class="no-textarea--detail" rows="4"
                                                    placeholder="검색 결과에 노출되는 요약 설명"><?= htmlspecialchars($val($siteinfo, 'meta_description_' . $lc), ENT_QUOTES, 'UTF-8', false) ?></textarea>
                                            </div>
                                        </div>

                                        <div class="no-admin-block">
                                            <h3 class="no-admin-title"><label for="meta_keywords_<?= $lc ?>">메타 키워드</label></h3>
                                            <div class="no-admin-content">
                                                <input type="text" id="meta_keywords_<?= $lc ?>" name="meta_keywords_<?= $lc ?>"
                                                    class="no-input--detail"
                                                    placeholder="쉼표(,)로 구분"
                                                    value="<?= htmlspecialchars($val($siteinfo, 'meta_keywords_' . $lc), ENT_QUOTES, 'UTF-8', false) ?>" />
                                            </div>
                                        </div>
                                    </fieldset>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 카드 2: 파비콘 / OG 썸네일 -->
                        <div class="no-card">
                            <div class="no-admin-mid-block">
                                <div class="no-card-header no-card-header--detail">
                                    <h2 class="no-card-title">파비콘 / OG 썸네일</h2>
                                    <div class="no-admin-header">
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            모든 언어에 공통으로 적용됩니다.
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="no-card-body no-admin-column no-admin-column--detail">
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="meta_favicon_ico">브라우저 파비콘</label></h3>
                                    <div class="no-admin-content">
                                        <?php if (!empty($siteinfo['meta_favicon_ico'])): ?>
                                            <div class="no-banner-image favicon">
                                                <img src="<?= $UPLOAD_META_WDIR ?>/<?= htmlspecialchars($siteinfo['meta_favicon_ico'], ENT_QUOTES, 'UTF-8', false) ?>" alt="favicon" />
                                            </div>
                                        <?php endif; ?>
                                        <div class="no-file-control">
                                            <input type="text" class="no-fake-file" id="fake_meta_favicon_ico"
                                                placeholder="파일을 선택해주세요." readonly disabled />
                                            <div class="no-file-box">
                                                <input type="file" id="meta_favicon_ico" name="meta_favicon_ico"
                                                    accept=".ico,.png,.svg"
                                                    onchange="document.getElementById('fake_meta_favicon_ico').value = this.value" />
                                                <button type="button" class="no-btn no-btn--main">파일찾기</button>
                                            </div>
                                        </div>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            ico / png / svg 형식 권장.
                                        </span>
                                    </div>
                                </div>

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="meta_thumb">OG 썸네일</label></h3>
                                    <div class="no-admin-content">
                                        <div class="no-file-wrap">
                                            <?php if (!empty($siteinfo['meta_thumb'])): ?>
                                                <div class="no-banner-image">
                                                    <img src="<?= $UPLOAD_META_WDIR ?>/<?= htmlspecialchars($siteinfo['meta_thumb'], ENT_QUOTES, 'UTF-8', false) ?>" alt="og thumb" style="width: 200px" />
                                                </div>
                                            <?php endif; ?>
                                            <div class="no-file-control">
                                                <input type="text" class="no-fake-file" id="fake_meta_thumb"
                                                    placeholder="파일을 선택해주세요." readonly disabled />
                                                <div class="no-file-box">
                                                    <input type="file" id="meta_thumb" name="meta_thumb"
                                                        accept="image/*"
                                                        onchange="document.getElementById('fake_meta_thumb').value = this.value" />
                                                    <button type="button" class="no-btn no-btn--main">파일찾기</button>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            소셜 공유 시 노출되는 대표 이미지 (1200x630 권장).
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 카드 3: 사이트맵 관리 -->
                        <div class="no-card">
                            <div class="no-admin-mid-block">
                                <div class="no-card-header no-card-header--detail">
                                    <h2 class="no-card-title">사이트맵 관리</h2>
                                    <div class="no-admin-header">
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            라우터 자동 생성 또는 수동 업로드 중 한 가지 방식을 사용하세요. (<code>/sitemap.xml</code>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="no-card-body no-admin-column no-admin-column--detail">
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">현재 사이트맵</h3>
                                    <div class="no-admin-content">
                                        <?php if ($sitemapExists): ?>
                                            <p class="no-admin-desc">
                                                마지막 갱신: <strong><?= htmlspecialchars($sitemapMtime, ENT_QUOTES, 'UTF-8', false) ?></strong>
                                                (<?= number_format($sitemapSize) ?> bytes)
                                                &middot; <a href="/sitemap.xml" target="_blank" rel="noopener">/sitemap.xml 확인</a>
                                            </p>
                                        <?php else: ?>
                                            <p class="no-admin-desc">
                                                정적 파일 없음. 라우터 자동 생성 라우트로만 응답 중. 아래 버튼으로 정적 파일을 생성할 수 있습니다.
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">라우터 자동 생성</h3>
                                    <div class="no-admin-content">
                                        <button type="button" id="btnSitemapGenerate" class="no-btn no-btn--main">
                                            <i class="bx bx-refresh"></i> 라우터 스캔 → sitemap.xml 생성
                                        </button>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            <code>routes/web.php</code> 의 named GET 라우트를 스캔하여 사이트맵을 생성합니다.
                                        </span>
                                    </div>
                                </div>

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title"><label for="sitemap_xml">수동 업로드</label></h3>
                                    <div class="no-admin-content">
                                        <div class="no-file-control">
                                            <input type="text" class="no-fake-file" id="fake_sitemap_xml"
                                                placeholder="sitemap.xml 파일을 선택해주세요." readonly disabled />
                                            <div class="no-file-box">
                                                <input type="file" id="sitemap_xml" name="sitemap_xml"
                                                    accept=".xml,application/xml,text/xml"
                                                    onchange="document.getElementById('fake_sitemap_xml').value = this.value" />
                                                <button type="button" class="no-btn no-btn--main">파일찾기</button>
                                            </div>
                                        </div>
                                        <button type="button" id="btnSitemapUpload" class="no-btn no-btn--normal" style="margin-top:8px;">
                                            <i class="bx bx-upload"></i> 업로드
                                        </button>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            직접 작성한 sitemap.xml 을 업로드합니다. (5MB 이하, well-formed XML)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="no-items-center center">
                            <a href="javascript:void(0);" class="no-btn no-btn--big no-btn--main"
                                onClick="doSeoConfigSave();">
                                저장
                            </a>
                        </div>
                    </div>
                </section>
            </form>
        </main>

        <?php if ($SHOW_LOCALE_TABS): ?>
        <script type="text/javascript" src="./js/locale-tabs.js?c=<?= $STATIC_ADMIN_JS_MODIFY_DATE ?>"></script>
        <?php endif; ?>
        <script type="text/javascript" src="./js/seo.config.process.js?c=<?= $STATIC_ADMIN_JS_MODIFY_DATE ?>"></script>
        <?php include_once "../../inc/admin.footer.php"; ?>
    </div>
</body>

</html>
