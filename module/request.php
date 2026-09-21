<?php

declare(strict_types=1);

use Http\Request;
use Http\Response;
use Mail\GmailMailer;
use Mail\NullMailer;
use Request\AssetRequestService;

if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__) . '/inc/lib/base.class.php';
}

function _asset_request_service(): AssetRequestService
{
    global $request_apply_gender_options, $request_apply_residence_options, $request_apply_interest_options;

    $useGmail = (bool) ProjectHelper::config('mail.use_gmail', false);

    if ($useGmail) {
        $mailer = new GmailMailer(
            (string) ProjectHelper::config('mail.gmail_from', ''),
            (string) ProjectHelper::config('mail.gmail_from_name', ''),
            (string) ProjectHelper::config('mail.gmail_app_password', '')
        );
    } else {
        $mailer = new NullMailer();
    }

    return new AssetRequestService(
        \Database\DB::getInstance(),
        $mailer,
        (string) ProjectHelper::config('mail.notify_to', ''),
        [
            'gender'    => is_array($request_apply_gender_options    ?? null) ? $request_apply_gender_options    : [],
            'residence' => is_array($request_apply_residence_options ?? null) ? $request_apply_residence_options : [],
            'interests' => is_array($request_apply_interest_options  ?? null) ? $request_apply_interest_options  : [],
        ],
        (string) ProjectHelper::config('form.notify_title', '문의 신청이 접수되었습니다'),
        (string) ProjectHelper::config('form.request_type', 'contact_request')
    );
}

if (!function_exists('asset_request_option_sets')) {
    function asset_request_option_sets(): array
    {
        global $request_apply_gender_options, $request_apply_residence_options, $request_apply_interest_options;

        return [
            'gender'    => is_array($request_apply_gender_options    ?? null) ? $request_apply_gender_options    : [],
            'residence' => is_array($request_apply_residence_options ?? null) ? $request_apply_residence_options : [],
            'interests' => is_array($request_apply_interest_options  ?? null) ? $request_apply_interest_options  : [],
        ];
    }
}

if (!function_exists('asset_request_response')) {
    function asset_request_response(Request $request): Response
    {
        return _asset_request_service()->response($request);
    }
}

$isDirectRequest = realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === __FILE__;

if ($isDirectRequest) {
    $response = asset_request_response(app()->request());
    $response->send();
    exit;
}
