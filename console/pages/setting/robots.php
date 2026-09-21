<?php
include_once "../../bootstrap/admin.php";

// superadmin 전용
$role->redirectIfReadOnly();
if (!$role->is('superadmin')) {
    $role->redirectWithError('robots.txt 관리는 최고 관리자만 사용할 수 있습니다.');
    exit;
}

try {
    $db = DB::getInstance();
    $stmt = $db->prepare('SELECT robots_content FROM nb_siteinfo WHERE sitekey = :sitekey LIMIT 1');
    $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY]);
    $robotsContent = (string) ($stmt->fetchColumn() ?: '');
} catch (Exception $e) {
    $robotsContent = '';
}

// 미리보기는 RobotsGenerator로 실제 응답값과 동일하게 생성
require_once dirname(__DIR__, 3) . '/src/Seo/RobotsGenerator.php';
$preview = (new \Seo\RobotsGenerator())->build();
$defaultTemplate = (new \Seo\RobotsGenerator())->defaultTemplate();

include_once "../../inc/admin.title.php";
include_once "../../inc/admin.css.php";
include_once "../../inc/admin.js.php";
?>
</head>

<body data-page="robots">
    <div class="no-wrap">
        <?php include_once "../../inc/admin.header.php"; ?>

        <main class="no-app no-container">
            <?php include_once "../../inc/admin.drawer.php"; ?>

            <form id="frm" name="frm" method="post" autocomplete="off">
                <input type="hidden" name="mode" value="robots.update">

                <section class="no-content">
                    <div class="no-toolbar">
                        <div class="no-toolbar-container no-flex-stack">
                            <div class="no-page-indicator">
                                <h1 class="no-page-title">robots.txt 관리</h1>
                                <div class="no-breadcrumb-container">
                                    <ul class="no-breadcrumb-list">
                                        <li class="no-breadcrumb-item"><span>사이트정보관리</span></li>
                                        <li class="no-breadcrumb-item"><span>robots.txt 관리</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="no-toolbar-container">
                        <div class="no-card">
                            <div class="no-card-header no-card-header--detail">
                                <h2 class="no-card-title">robots.txt 본문 편집</h2>
                            </div>

                            <div class="no-card-body no-admin-column no-admin-column--detail">

                                <div class="no-admin-block wide">
                                    <span class="no-admin-info" style="display:block;margin-bottom:10px;">
                                        <i class="bx bxs-info-circle"></i>
                                        검색엔진(구글·네이버 등)에게 사이트의 어느 부분을 크롤링해도 되는지 안내하는 파일입니다.
                                        <strong>관리자 경로 차단</strong>과 <strong>사이트맵 URL</strong>은 자동으로 추가되니
                                        본문에서 지워도 다시 들어갑니다.
                                    </span>
                                </div>

                                <div class="no-admin-block wide">
                                    <h3 class="no-admin-title">
                                        <label for="robots_content">본문</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <textarea name="robots_content" id="robots_content" rows="14"
                                            class="no-textarea--detail"
                                            style="font-family:Menlo,Consolas,monospace;font-size:13px;"
                                            placeholder="비워두면 기본 템플릿이 사용됩니다."><?= htmlspecialchars($robotsContent, ENT_QUOTES, 'UTF-8') ?></textarea>
                                        <div class="no-admin-desc" style="margin-top:6px;color:#888;">
                                            <button type="button" id="loadDefaultBtn" class="no-btn no-btn--small no-btn--normal" style="margin-right:6px;">기본 템플릿 불러오기</button>
                                            <span>변경 후 저장 → 우측 미리보기에서 실제 응답을 확인하세요.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="no-admin-block wide">
                                    <h3 class="no-admin-title">
                                        <label>현재 응답 미리보기 <small style="color:#888;">(자동 주입 포함)</small></label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <pre id="robotsPreview" style="background:#f7f7f9;padding:14px;border-radius:6px;font-family:Menlo,Consolas,monospace;font-size:13px;white-space:pre-wrap;word-break:break-all;margin:0;"><?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?></pre>
                                        <div class="no-admin-desc" style="margin-top:6px;color:#888;">
                                            저장 후 새로고침하면 갱신됩니다. 또는
                                            <a href="<?= htmlspecialchars((isset($NO_IS_SUBDIR) ? $NO_IS_SUBDIR : '') . '/robots.txt', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">실제 /robots.txt 열어보기 ↗</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="no-admin-block wide">
                                    <span class="no-admin-info" style="display:block;color:#b94a48;">
                                        <i class="bx bxs-error-circle"></i>
                                        <strong>주의:</strong> <code>Disallow: /</code> 한 줄만 입력하면 사이트 전체가 검색에서 빠집니다.
                                        의도치 않게 입력하지 않도록 주의하세요.
                                    </span>
                                </div>

                                <div class="no-items-center center">
                                    <button type="submit" class="no-btn no-btn--big no-btn--main" id="submitBtn">저장</button>
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
    (function() {
        var defaultTpl = <?= json_encode($defaultTemplate, JSON_UNESCAPED_UNICODE) ?>;
        var loadBtn = document.getElementById('loadDefaultBtn');
        var ta = document.getElementById('robots_content');
        if (loadBtn && ta) {
            loadBtn.addEventListener('click', function() {
                if (!ta.value || confirm('현재 입력 내용을 기본 템플릿으로 덮어쓸까요?')) {
                    ta.value = defaultTpl;
                }
            });
        }

        var form = document.getElementById('frm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var fd = new FormData(form);
                fetch('./ajax/setting.process.php', {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin'
                })
                .then(function(r) { return r.json(); })
                .then(function(json) {
                    alert(json.msg || (json.result === 'success' ? '저장되었습니다.' : '실패'));
                    if (json.result === 'success') {
                        location.reload();
                    }
                })
                .catch(function() { alert('저장 중 오류가 발생했습니다.'); });
            });
        }
    })();
    </script>

</body>
</html>
