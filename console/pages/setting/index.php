<?php
	include_once "../../bootstrap/admin.php";
	$role->redirectIfReadOnly();

	include_once "../../inc/admin.title.php";
	include_once "../../inc/admin.css.php";
	include_once "../../inc/admin.js.php";
?>
</head>



<body>
    <div class="no-wrap">
        <!-- Header -->
        <?php 
        include_once "../../inc/admin.header.php";
        ?>

        <!-- Main -->
        <main class="no-app no-container">
            <!-- Drawer -->
            <?php
                include_once "../../inc/admin.drawer.php";
            ?>

            <!-- Contents -->
            <form id="frm" name="frm" method="post" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" id="mode" name="mode" value="">
                <section class="no-content">
                    <!-- Page Title -->
                    <div class="no-toolbar">
                        <div class="no-toolbar-container no-flex-stack">
                            <div class="no-page-indicator">
                                <h1 class="no-page-title">사이트 정보관리</h1>
                                <div class="no-breadcrumb-container">
                                    <ul class="no-breadcrumb-list">
                                        <li class="no-breadcrumb-item">
                                            <span>설정</span>
                                        </li>
                                        <li class="no-breadcrumb-item">
                                            <span>사이트 정보관리</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- page indicator -->
                        </div>
                    </div>

                    <!-- card-title -->
                    <div class="no-toolbar-container">
                        <div class="no-card">
                            <div class="no-card-header no-card-header--detail">
                                <h2 class="no-card-title">사이트 정보 설정</h2>
                            </div>
                            <div class="no-card-body no-admin-column no-admin-column--detail">
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="title">사이트 제목</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="title" id="title" class="no-input--detail"
                                            placeholder="사이트 제목 입력" value="<?=$SITEINFO_TITLE?>" />
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            브라우저에 노출됩니다.
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="phone">대표번호</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="phone" id="phone" class="no-input--detail"
                                            placeholder="대표번호" value="<?=$SITEINFO_PHONE?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->

                                <?php if (false) { /* hidden — 블로그·카카오톡 채널·인스타 주소 미사용 (2026-07-03) */ ?>
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="hp">블로그 주소</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="hp" id="hp" class="no-input--detail"
                                            placeholder="블로그 주소" value="<?=$SITEINFO_HP?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="email">카카오톡 채널 주소</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="email" id="email" class="no-input--detail"
                                            placeholder="카카오톡 채널 주소" value="<?=$SITEINFO_EMAIL?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="customercenter_able_time">
                                            인스타 주소
                                        </label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="customercenter_able_time" id="customercenter_able_time"
                                            class="no-input--detail" placeholder="인스타 주소"
                                            value="<?=$SITEINFO_CUSTOMER_CENTER_ABLE_TIME?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <?php } ?>

                                <!--<div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="company_able_time"
                                            >네이버 블로그 주소</label
                                        >
                                    </h3>
                                    <div class="no-admin-content">
                                        <input
                                            type="text"
                                            name="company_able_time"
                                            id="company_able_time"
                                            class="no-input--detail"
                                            placeholder="운영시간"
                                            value="<?=$SITEINFO_COMPANY_ABLE_TIME?>" 
                                        />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <!-- <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="google_map_key"
                                            >구글 지도 API KEY</label
                                        >
                                    </h3>
                                    <div class="no-admin-content">
                                        <input
                                            type="text"
                                            name="google_map_key"
                                            id="google_map_key"
                                            class="no-input--detail"
                                            placeholder="구글 지도 API KEY"
                                            value="<?=$SITEINFO_GOOGLE_MAP_KEY?>"
                                        />
                                    </div>
                                </div> -->
                                <!-- admin-block -->

                                <?php if (false) { /* hidden — 상단·하단 푸터 로고 미사용 (2026-07-03), JS 필수 검증은 요소 부재 시 자동 통과 */ ?>
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="title">상단 로고</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <?php if($SITEINFO_LOGO_TOP){?>
                                        <input type="hidden" id="logo_top_filename" value="<?=$SITEINFO_LOGO_TOP?>">
                                        <div class="no-banner-image logo">
                                            <img src="<?=$UPLOAD_SITEINFO_WDIR_LOGO?>/<?=$SITEINFO_LOGO_TOP?>"
                                                alt="<?=$SITEINFO_TITLE?>" style="width: 200px" />
                                        </div>
                                        <?php } ?>

                                        <div class="no-file-control">
                                            <input type="text" class="no-fake-file" id="fake_logo_top"
                                                placeholder="파일을 선택해주세요." readonly disabled />
                                            <div class="no-file-box">
                                                <input type="file" name="logo_top" id="logo_top"
                                                    onchange="document.getElementById('fake_logo_top').value = this.value" />
                                                <button type="button" class="no-btn no-btn--main">
                                                    파일찾기
                                                </button>
                                            </div>
                                        </div>
                                        <!-- file control -->
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            로고의 최대 크기는 가로 300px, 세로
                                            50px 입니다.
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->

                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="title">하단 푸터 로고</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <?php if($SITEINFO_LOGO_FOOTER){?>
                                        <input type="hidden" id="logo_footer_filename"
                                            value="<?=$SITEINFO_LOGO_FOOTER?>">
                                        <div class="no-banner-image logo">
                                            <img src="<?=$UPLOAD_SITEINFO_WDIR_LOGO?>/<?=$SITEINFO_LOGO_FOOTER?>"
                                                alt="<?=$SITEINFO_TITLE?>" style="width: 200px" />
                                        </div>
                                        <?php } ?>
                                        <div class="no-file-control">
                                            <input type="text" class="no-fake-file" id="fake_logo_footer"
                                                placeholder="파일을 선택해주세요." readonly disabled />
                                            <div class="no-file-box">
                                                <input type="file" name="logo_footer" id="logo_footer"
                                                    onchange="document.getElementById('fake_logo_footer').value = this.value" />
                                                <button type="button" class="no-btn no-btn--main">
                                                    파일찾기
                                                </button>
                                            </div>
                                        </div>
                                        <!-- file control -->
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            로고의 최대 크기는 가로 300px, 세로
                                            50px 입니다.
                                        </span>
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <?php } ?>
                            </div>
                            <!-- card-body -->
                        </div>
                        <!-- card -->

                        <div class="no-card">
                            <div class="no-admin-mid-block">
                                <div class="no-card-header no-card-header--detail">
                                    <h2 class="no-card-title">
                                        하단 푸터 정보
                                    </h2>
                                    <div class="no-admin-header">
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            사이트 하단에 노출되는 정보를
                                            관리합니다.
                                        </span>
                                        <span class="no-admin-info">
                                            <i class="bx bxs-info-circle"></i>
                                            입력한 정보만 노출됩니다.
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="no-card-body no-admin-column no-admin-column--detail">
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_name">하단 푸터 사이트명</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="footer_name" id="footer_name" class="no-input--detail"
                                            placeholder="하단 푸터 사이트명" value="<?=$SITEINFO_FOOTER_NAME?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_address">주소</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="footer_address" id="footer_address"
                                            class="no-input--detail" placeholder="주소"
                                            value="<?=$SITEINFO_FOOTER_ADDRESS?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_phone">전화번호</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="footer_phone" id="footer_phone"
                                            class="no-input--detail" placeholder="전화번호"
                                            value="<?=$SITEINFO_FOOTER_PHONE?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_email">이메일</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="footer_email" id="footer_email"
                                            class="no-input--detail" placeholder="이메일"
                                            value="<?=$SITEINFO_FOOTER_EMAIL?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <!--<div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_fax"
                                            >팩스번호</label
                                        >
                                    </h3>
                                    <div class="no-admin-content">
                                        <input
                                            type="text"
                                            name="footer_fax"
                                            id="footer_fax"
                                            class="no-input--detail"
                                            placeholder="팩스번호"
                                            value="<?=$SITEINFO_FOOTER_FAX?>"
                                        />
                                    </div>
                                </div> -->
                                <!-- admin-block -->
                                <!--<div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_hp"
                                            >입원문의</label
                                        >
                                    </h3>
                                    <div class="no-admin-content">
                                        <input
                                            type="text"
                                            name="footer_hp"
                                            id="footer_hp"
                                            class="no-input--detail"
                                            placeholder="입원문의"
                                            value="<?=$SITEINFO_FOOTER_HP?>"
                                        />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_ssn">SSN</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="footer_ssn" id="footer_ssn" class="no-input--detail"
                                            placeholder="SSN" value="<?=$SITEINFO_FOOTER_SSN?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_owner">대표자</label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input type="text" name="footer_owner" id="footer_owner"
                                            class="no-input--detail" placeholder="대표자"
                                            value="<?=$SITEINFO_FOOTER_OWNER?>" />
                                    </div>
                                </div>
                                <!-- admin-block -->
                                <!--                                 <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_ssn"
                                            >사업자번호</label
                                        >
                                    </h3>
                                    <div class="no-admin-content">
                                        <input
                                            type="text"
                                            name="footer_ssn"
                                            id="footer_ssn"
                                            class="no-input--detail"
                                            placeholder="사업자번호"
                                            value="<?=$SITEINFO_FOOTER_SSN?>"
                                        />
                                    </div>
                                </div> -->
                                <!-- admin-block -->
                                <!--                                 <div class="no-admin-block">
                                    <h3 class="no-admin-title">
                                        <label for="footer_policy_charger">
                                            하단 푸터 개인정보관리책임자
                                        </label>
                                    </h3>
                                    <div class="no-admin-content">
                                        <input
                                            type="text"
                                            name="footer_policy_charger"
                                            id="footer_policy_charger"
                                            class="no-input--detail"
                                            placeholder="하단 푸터 개인정보관리책임자"
                                            value="<?=$SITEINFO_FOOTER_CHARGER?>"
                                        />
                                    </div>
                                </div> -->
                                <!-- admin-block -->
                            </div>
                        </div>



                        <div class="no-items-center center">
                            <a href="javascript:void(0);" class="no-btn no-btn--big no-btn--main"
                                onClick="doSettingSave();">
                                저장
                            </a>
                        </div>
                        <!-- admin-block -->
                    </div>
                </section>
            </form>
        </main>



        <!-- Footer -->
        <script type="text/javascript" src="./js/setting.process.js?c=<?=$STATIC_ADMIN_JS_MODIFY_DATE?>"></script>
        <?php
            include_once "../../inc/admin.footer.php";
        ?>

    </div>
</body>

</html>
