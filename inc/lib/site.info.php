<?php

$db = DB::getInstance(); // DB 인스턴스 가져오기

$query = "SELECT no, title, phone, hp, fax, email, customercenter_able_time, company_able_time, google_map_key, logo_top, logo_footer, 
          footer_name, footer_address, footer_phone, footer_hp, footer_fax, footer_email, footer_owner, footer_ssn, footer_policy_charger, 
          meta_keywords, meta_description, meta_thumb, meta_favicon_ico 
          FROM nb_siteinfo WHERE sitekey = :sitekey";

$stmt = $db->prepare($query);
$stmt->bindValue(":sitekey", $NO_SITE_UNIQUE_KEY, PDO::PARAM_STR);
$stmt->execute();
$data_siteinfo = $stmt->fetch(PDO::FETCH_ASSOC);

// 데이터가 있는 경우 각 변수에 할당
if ($data_siteinfo) {
    $SITEINFO_TITLE = $data_siteinfo['title'] ?? '';
    $SITEINFO_PHONE = $data_siteinfo['phone'] ?? '';
    $SITEINFO_HP = $data_siteinfo['hp'] ?? '';
    $SITEINFO_FAX = $data_siteinfo['fax'] ?? '';
    $SITEINFO_EMAIL = $data_siteinfo['email'] ?? '';
    $SITEINFO_CUSTOMER_CENTER_ABLE_TIME = $data_siteinfo['customercenter_able_time'] ?? '';
    $SITEINFO_COMPANY_ABLE_TIME = $data_siteinfo['company_able_time'] ?? '';
    $SITEINFO_GOOGLE_MAP_KEY = $data_siteinfo['google_map_key'] ?? '';
    $SITEINFO_LOGO_TOP = $data_siteinfo['logo_top'] ?? '';
    $SITEINFO_LOGO_FOOTER = $data_siteinfo['logo_footer'] ?? '';
    $SITEINFO_FOOTER_NAME = $data_siteinfo['footer_name'] ?? '';
    $SITEINFO_FOOTER_ADDRESS = $data_siteinfo['footer_address'] ?? '';
    $SITEINFO_FOOTER_PHONE = $data_siteinfo['footer_phone'] ?? '';
    $SITEINFO_FOOTER_HP = $data_siteinfo['footer_hp'] ?? '';
    $SITEINFO_FOOTER_FAX = $data_siteinfo['footer_fax'] ?? '';
    $SITEINFO_FOOTER_EMAIL = $data_siteinfo['footer_email'] ?? '';
    $SITEINFO_FOOTER_OWNER = $data_siteinfo['footer_owner'] ?? '';
    $SITEINFO_FOOTER_SSN = $data_siteinfo['footer_ssn'] ?? '';
    $SITEINFO_FOOTER_CHARGER = $data_siteinfo['footer_policy_charger'] ?? '';
    $SITEINFO_META_KEYWORDS = $data_siteinfo['meta_keywords'] ?? '';
    $SITEINFO_META_DESCRIPTION = $data_siteinfo['meta_description'] ?? '';
    $SITEINFO_META_THUMB = $data_siteinfo['meta_thumb'] ?? '';
    $SITEINFO_META_FAVICON_ICO = $data_siteinfo['meta_favicon_ico'] ?? '';
}

// 메타 태그 설정
$protocol = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ||
    (($_SERVER['SERVER_PORT'] ?? '') == 443)
) ? 'https://' : 'http://';

$NO_STATIC_TITLE = $SITEINFO_TITLE;
$NO_META_KEYWORDS = $SITEINFO_META_KEYWORDS;
$NO_META_DESCRIPTION = $SITEINFO_META_DESCRIPTION;
$NO_META_URL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$NO_META_TWITTER_CARD = "Summary";
$NO_META_TWITTER_URL = $NO_META_URL;
$NO_META_TWITTER_TITLE = $NO_STATIC_TITLE;
$NO_META_TWITTER_DESCRIPTION = $NO_META_DESCRIPTION;
$NO_META_TWITTER_IMAGE = $UPLOAD_META_WDIR . "/" . $SITEINFO_META_THUMB;
$NO_META_OG_URL = $protocol . $_SERVER['HTTP_HOST'];
$NO_META_OG_TYPE = "website";
$NO_META_OG_IMAGE = $NO_META_OG_URL . $UPLOAD_META_WDIR . "/" . $SITEINFO_META_THUMB;
$NO_META_OG_SITE_NAME = $NO_STATIC_TITLE;
$NO_META_OG_LOCALE = "ko";
$NO_META_OG_TITLE = $NO_STATIC_TITLE;
$NO_META_OG_DESCRIPTION = $NO_META_DESCRIPTION;
$NO_META_ITEMPROP_NAME = $NO_STATIC_TITLE;
$NO_META_ITEMPROP_IMAGE = "";
$NO_META_ITEMPROP_URL = $NO_META_URL;
$NO_META_ITEMPROP_DESCRIPTION = $NO_META_DESCRIPTION;
$NO_META_ITEMPROP_KEYWORD = $NO_META_KEYWORDS;
$NO_META_SHORTCUT_ICON = $UPLOAD_META_WDIR . "/" . $SITEINFO_META_FAVICON_ICO;
$NO_META_APPLE_TOUCH_ICON = "";
