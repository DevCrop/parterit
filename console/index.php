<?php
include_once __DIR__ . "/bootstrap/base.php";
include_once  dirname(__DIR__) . "/inc/lib/site.info.php";

$loginError = trim((string) ($_SESSION['admin_login_error'] ?? ''));
$loginUid = trim((string) ($_SESSION['admin_login_uid'] ?? ''));
unset($_SESSION['admin_login_error'], $_SESSION['admin_login_uid']);
?>

<!DOCTYPE html>
<html lang="ko">

<head>
  <title>사이트 관리 시스템</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <link rel="stylesheet" href="/resource/vendor/boxicons/css/boxicons.min.css" />
  <link rel="stylesheet" href="/resource/vendor/fontawsome/css/all.css" />
  <link rel="stylesheet" href="./resource/css/style.css" />
  <link rel="stylesheet" href="./resource/css/wireframe.css?v=1" />

  <!-- admin only -->
  <script type="text/javascript" src="./resource/js/login.js" defer></script>
</head>

<body class="no-sub-body">
  <div class="no-sub-wrap">
    <header class="no-login-header">
      <h1 class="no-login-logo">
        <span class="no-login-wordmark">파트너잇</span>
      </h1>
    </header>

    <main id="no-main">
      <form method="POST" action="./lib/login/login.process.php" id="login_form" name="login_form"
        autocomplete="off">
        <div class="no-login-wrap">
          <div class="no-login-content">
            <div class="no-login-top">
              <h2 class="no-login-title">사이트 관리 시스템</h2>
              <p class="no-login-desc">
                본 페이지는 인증이 필요한 관리자 페이지 입니다.
              </p>
            </div>

            <div class="no-login-control">
              <div class="no-login-error<?= $loginError !== '' ? ' show' : '' ?>" role="alert" aria-live="polite">
                <i class="bx bx-info-circle"></i>
                <span><?= e($loginError) ?></span>
              </div>

              <div class="no-form-control no-form-control--login">
                <label for="uid">아이디</label>
                <input type="text" name="uid" id="uid" placeholder="아이디" value="<?= e($loginUid) ?>" />
                <span class="no-form-icon">
                  <i class="bx bxs-user"></i>
                </span>
                <p class="no-invalid">
                  <i class="bx bxs-info-circle"></i>
                  <span>아이디를 입력하세요.</span>
                </p>
              </div>
              <!-- form-control -->
              <div class="no-form-control no-form-control--login">
                <div class="no-form-pwd">
                  <label for="upwd">비밀번호</label>

                  <div class="no-pwd-btn">
                    <i class="fa-regular fa-eye no-pwd-icon"></i>
                    <span class="no-pwd-text">Show</span>
                  </div>
                </div>
                <input type="password" name="upwd" id="upwd" placeholder="비밀번호" />
                <div class="no-form-icon">
                  <i class="bx bxs-lock"></i>
                </div>
                <p class="no-invalid">
                  <i class="bx bxs-info-circle"></i>
                  <span>비밀번호를 입력하세요.</span>
                </p>
              </div>
              <!-- form-control -->
              <div class="no-form-control no-form-control--login">
                <label for="r_captcha">보안코드</label>

                <div class="no-form-flex">
                  <div class="no-captcha-image">
                    <img src="/captcha/admin.php" alt="captcha" style="height:32px;" />
                  </div>
                  <input type="text" name="r_captcha" id="r_captcha" placeholder="보안코드"
                    autocomplete="off" maxlength="5" class="no-captcha-input" />
                  <p class="no-invalid">
                    <i class="bx bxs-info-circle"></i>
                    <span>보안코드를 입력하세요.</span>
                  </p>
                </div>
              </div>
              <!-- form-control -->
            </div>

            <div class="no-login-bot">
              <div class="no-form-btn">
                <button type="submit" class="no-btn--submit">로그인</button>
              </div>

              <small>
                <span>비밀번호 분실시</span>
                <span>
                  <b>고객센터</b>
                  <?php $adminSupportPhone = trim((string) project_config('admin.support_phone', '')); ?>
                  <?php if ($adminSupportPhone !== ''): ?>
                  <a href="tel:<?= e($adminSupportPhone) ?>"><?= e($adminSupportPhone) ?></a>
                  <?php endif; ?>
                </span>
              </small>
            </div>
          </div>
          <!-- login content -->
        </div>
        <!-- login wrap -->
      </form>
    </main>

    <footer class="no-login-footer">
      <?php $adminVendorUrl = trim((string) project_config('admin.vendor_url', '')); ?>
      <?php $adminVendorName = trim((string) project_config('admin.vendor_name', '')); ?>
      <span><?= e(project_brand_name()) ?> · <?= e((string) project_config('admin.login_caption', '')) ?> · 제작
        <?php if ($adminVendorUrl !== '' && $adminVendorName !== ''): ?>
        <a href="<?= e($adminVendorUrl) ?>" target="_blank" rel="noopener noreferrer"><?= e($adminVendorName) ?></a>
        <?php elseif ($adminVendorName !== ''): ?>
        <?= e($adminVendorName) ?>
        <?php endif; ?>
      </span>
    </footer>
  </div>

  <style>
    .no-form-control--login {
      display: block;
      background: none;
      border: 0;
      height: auto;
    }

    .no-form-control--login .no-form-icon {
      left: 2.2rem;
    }
  </style>
</body>

</html>
