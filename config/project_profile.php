<?php

return [
    'branding' => [
        'display_name' => '파트너잇',    // 화면에 표시되는 사이트명
        'footer_name'  => '파트너잇',    // 법적 표기용 이름
        // CMS 데이터 조인용 내부 키입니다. 공개 브랜드명으로 사용하지 않습니다.
        'sitekey'      => 'DKHOLD',
    ],
    'admin' => [
        'dir'           => 'console',
        'support_email' => '',
        'support_phone' => '',
        'vendor_name'   => '',
        'vendor_url'    => '',
        'login_caption' => '관리 시스템',
    ],
    'assets' => [
        'logo_header_path' => '',
        'logo_footer_path' => '',
    ],
    // 메인 영상: video_base 는 확장자 제외 base 경로. 같은 폴더에
    //   <base>.av1.mp4 / <base>.webm / <base>.h264.mp4 를 두면
    //   브라우저가 av1 → webm → mp4 순으로 첫 매치만 다운로드한다.
    'home_hero' => [
        'video_base' => '',
        'logo_src'   => '',
        'poster_src' => '',
    ],
    'home_business' => [
        'video_base' => '',
        'poster_src' => '',
    ],
    'routes' => [
        'home_path'          => '/',
        'primary_cta_path'   => '/contact',
        'contact_index_path' => '/contact',
    ],
    // 다국어 로직은 코드에 그대로 유지하되, 새 콘텐츠 확정 전 한국어 와이어프레임으로 둔다.
    // enabled=false → URL 에 locale prefix 가 붙지 않는 루트 라우트(/about, /business …)로 서빙된다.
    'localization' => [
        'enabled'           => false,
        'default_locale'    => 'ko',
        'supported_locales' => ['ko'],
    ],
    'features' => [
        'ui'      => ['language_switcher'  => false],
        'content' => ['site_tags'          => false],
        'seo'     => ['alternate_hreflang' => false],
    ],
    'links' => [
        'instagram'     => '',
        'kakao_map'     => '',
        'naver_map'     => '',
        'google_map'    => '',
        'youtube'       => '',
        'blog'          => '',
        'kakao_channel' => '',
        'twitter'       => '',
        'linkedin'      => '',
    ],
    'offices' => [
        'usa' => [
            'label'   => 'USA Office',
            'address' => '',
        ],
        'korea' => [
            'label'   => 'Korea Office',
            'address' => '',
        ],
        'japan' => [
            'label'   => 'Japan Office',
            'address' => '',
        ],
    ],
    'contact' => [
        'phone'   => '010-9235-4407',
        'email'   => 'partnerit917@gmail.com',
        'address' => '부산광역시 해운대구 센텀중앙로 90, 큐비e센텀 917호',
        'fax'     => '',
    ],
    'inquiries' => [
        'business' => [
            'label' => 'Business Inquiries',
            'email' => '',
            'desc'  => 'Partnerships and business development',
        ],
        'pr' => [
            'label' => 'PR & Marketing',
            'email' => '',
            'desc'  => 'Media and marketing inquiries',
        ],
    ],
    'careers' => [
        'email_ko' => '',
        'email_jp' => '',
    ],
    'mail' => [
        'use_gmail'          => false,
        'gmail_from'         => '',
        'gmail_from_name'    => '',
        'gmail_app_password' => '',
        'notify_to'          => '',
    ],
    'form' => [
        'request_type'   => 'contact_request',
        'notify_subject' => '문의 신청 접수',
        'notify_title'   => '문의 신청이 접수되었습니다',
    ],
];
