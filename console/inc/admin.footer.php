<?php if (isset($role)) $role->echoFlashErrorAlert(); ?>
<?php
$adminSupportEmail = trim((string) project_config('admin.support_email', ''));
$adminVendorName = trim((string) project_config('admin.vendor_name', ''));
$adminVendorUrl = trim((string) project_config('admin.vendor_url', ''));
?>

<!-- Footer -->
<footer class="no-footer no-content-container">
    <p class="no-footer-para">
        <?php if ($adminVendorName !== ''): ?>
        <span>제작사: <?= e($adminVendorName) ?></span>
        <?php endif; ?>
        <?php if ($adminSupportEmail !== ''): ?>
        <span>문의 :
            <a href="mailto:<?= e($adminSupportEmail) ?>"><?= e($adminSupportEmail) ?></a>
        </span>
        <?php endif; ?>
        <span>Copyright &copy;
            <?php if ($adminVendorUrl !== ''): ?>
            <a href="<?= e($adminVendorUrl) ?>"><?= e($adminVendorName !== '' ? $adminVendorName : $adminVendorUrl) ?></a>
            <?php else: ?>
            <?= e($adminVendorName !== '' ? $adminVendorName : project_brand_name()) ?>
            <?php endif; ?>
            All rights reserved.
        </span>
    </p>
</footer>

<div class="no-drawer-overlay"></div>
</body>

</html>
