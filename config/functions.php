<?php

require_once dirname(__DIR__) . '/inc/lib/Helper.php';

if (!function_exists('isDevelopment')) {
    function isDevelopment()
    {
        return ENV === ENV_DEVELOPMENT;
    }
}

if (!function_exists('isProduction')) {
    function isProduction()
    {
        return ENV === ENV_PRODUCTION;
    }
}

if (!function_exists('set_locale')) {
    function set_locale(string $locale): void
    {
        $locale = in_array($locale, project_supported_locales(), true) ? $locale : project_default_locale();
        $app = app();
        if (method_exists($app, 'setLocale')) {
            $app->setLocale($locale);
        } else {
            $GLOBALS['app_locale'] = $locale;
        }
    }
}

if (!function_exists('locale')) {
    function locale(): string
    {
        if (!project_localization_enabled()) {
            return project_default_locale();
        }

        $app = app();
        if (method_exists($app, 'getLocale')) {
            return $app->getLocale();
        }

        return $GLOBALS['app_locale'] ?? project_default_locale();
    }
}

/**
 * `/lang/{locale}.php` 배열에서 `a.b.c` 형식 키를 조회한다.
 *
 * @param mixed $default
 * @return string|array
 */
if (!function_exists('__')) {
    function __(string $key, array $replace = [], $default = null)
    {
        static $cache = [];

        $lookup = static function (string $locale) use (&$cache, $key) {
            if (!isset($cache[$locale])) {
                $file = rtrim(ROOT_PATH, '/') . '/lang/' . $locale . '.php';
                $cache[$locale] = is_file($file) ? (include $file) : [];
            }

            return array_reduce(explode('.', $key), function ($carry, $segment) {
                if (is_array($carry) && array_key_exists($segment, $carry)) {
                    return $carry[$segment];
                }

                return null;
            }, $cache[$locale]);
        };

        $loc = locale();
        $val = $lookup($loc);

        if ($val === null && $loc !== 'en') {
            $val = $lookup('en');
        }

        if ($val === null) {
            $val = $default !== null ? $default : $key;
        }

        if (is_array($val)) {
            return $val;
        }

        $str = (string) $val;
        if ($replace) {
            foreach ($replace as $k => $v) {
                $str = str_replace(':' . $k, (string) $v, $str);
            }
        }

        return $str;
    }
}

if (!function_exists('dump')) {
    /**
     * 디버그 출력을 보기 좋게 렌더링한다.
     *
     * @param mixed ...$vars
     * @return void
     */
    function dump(...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre style="background:#222;color:#eee;padding:10px;border-radius:6px;white-space:pre-wrap;font-size:13px;line-height:1.4">';
            echo htmlspecialchars(print_r($var, true), ENT_QUOTES, 'UTF-8');
            echo '</pre>';
        }
    }
}

if (!function_exists('dd')) {
    /**
     * 디버그 출력 후 즉시 종료한다.
     *
     * @param mixed ...$vars
     * @return never
     */
    function dd(...$vars): void
    {
        dump(...$vars);
        exit(1);
    }
}

if (!function_exists('e')) {
    function e(string $string)
    {
        return TextHelper::escape($string);
    }
}

if (!function_exists('e_nl2br')) {
    /**
     * 개행을 유지하면서 안전하게 escape 처리한 뒤 `<br>`로 변환한다.
     */
    function e_nl2br($value): string
    {
        $text = (string) $value;
        $hasBr = preg_match('/<br\s*\/?>/i', $text) === 1;
        $token = '__NB_BR__';

        $text = preg_replace('/\s*<br\s*\/?>\s*/i', $token, $text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        if ($hasBr) {
            // 기존 <br>가 포함된 경우 줄바꿈 중복을 피하기 위해 공백으로 정리한다.
            $text = str_replace("\n", ' ', $text);
            $text = preg_replace('/\s+/', ' ', $text);
        } else {
            $text = preg_replace("/\n{2,}/", "\n", $text);
        }

        $text = str_replace($token, "\n", $text);
        $text = trim($text);

        return TextHelper::nl2brEscaped($value);
    }
}

if (!function_exists('e_nl2br_html')) {
    /**
     * `e_nl2br()`와 같지만 `<strong>`, `<b>`, `<br>` 태그는 보존한다.
     */
    function e_nl2br_html($value): string
    {
        $text = (string) $value;
        $tags = [];
        $text = preg_replace_callback(
            '#</?(?:strong|b)\b[^>]*>|<br\s*/?>#i',
            static function (array $m) use (&$tags): string {
                $raw = $m[0];
                if (preg_match('#^<br\s*/?>#i', $raw)) {
                    $tags[] = '<br>';
                } elseif (preg_match('#^</(strong|b)>#i', $raw, $mm)) {
                    $tags[] = '</' . strtolower($mm[1]) . '>';
                } elseif (preg_match('#^<(strong|b)\b[^>]*>#i', $raw, $mm)) {
                    $tags[] = '<' . strtolower($mm[1]) . '>';
                } else {
                    $tags[] = '';
                }

                return '__EC_H_' . (count($tags) - 1) . '__';
            },
            $text
        );
        $out = e_nl2br($text);
        foreach ($tags as $i => $tag) {
            $out = str_replace('__EC_H_' . $i . '__', $tag !== '' ? $tag : '', $out);
        }

        return TextHelper::nl2brEscapedHtml($value);
    }
}

if (!function_exists('ed')) {
    function ed(string $string)
    {
        return TextHelper::decode($string);
    }
}

if (!function_exists('locales')) {
    function locales(): array
    {
        if (!project_localization_enabled()) {
            return [project_default_locale()];
        }

        return project_supported_locales();
    }
}

if (!function_exists('project_localization_enabled')) {
    function project_localization_enabled(): bool
    {
        return ProjectHelper::localizationEnabled();
    }
}

if (!function_exists('project_default_locale')) {
    function project_default_locale(): string
    {
        return ProjectHelper::defaultLocale();
    }
}

if (!function_exists('project_supported_locales')) {
    function project_supported_locales(): array
    {
        return ProjectHelper::supportedLocales();
    }
}

if (!function_exists('project_feature_enabled')) {
    function project_feature_enabled(string $key, bool $default = false): bool
    {
        return ProjectHelper::featureEnabled($key, $default);
    }
}

if (!function_exists('project_profile')) {
    function project_profile(): array
    {
        return ProjectHelper::profile();
    }
}

if (!function_exists('project_config')) {
    function project_config(string $key, $default = null)
    {
        return ProjectHelper::config($key, $default);
    }
}

/** 요청 경로에서 앱의 BASE_PATH 접두를 제거한다. */
if (!function_exists('strip_app_base_from_path')) {
    function strip_app_base_from_path(string $path): string
    {
        return PathHelper::stripAppBaseFromPath($path);
    }
}

/**
 * 경로에서 locale prefix 존재 여부와 제거된 경로를 함께 반환한다.
 *
 * @return array{0:bool,1:?string,2:string} had_prefix, url_locale, stripped_path
 */
if (!function_exists('path_locale_segments')) {
    function path_locale_segments(string $path): array
    {
        return PathHelper::localeSegments($path);
    }
}

/** locale prefix가 붙은 URL을 SEO 비교용 경로로 정규화한다. */
if (!function_exists('strip_locale_prefix_from_path')) {
    function strip_locale_prefix_from_path(string $path): string
    {
        return PathHelper::stripLocalePrefix($path);
    }
}

/**
 * 경로와 쿼리를 받아 현재 프로젝트 규칙에 맞는 로컬라이즈드 URL을 만든다.
 */
if (!function_exists('build_localized_url')) {
    function build_localized_url(string $pathAndQuery, ?string $locale = null): string
    {
        return PathHelper::buildLocalizedUrl($pathAndQuery, $locale);
    }
}

/**
 * 경로만 받아 현재 locale 기준 URL로 변환한다.
 */
if (!function_exists('localized_path')) {
    function localized_path(string $path): string
    {
        return PathHelper::localizedPath($path);
    }
}

/** locale prefix 예외가 필요한 정적 공개 경로인지 확인한다. */
if (!function_exists('is_locale_prefix_exempt_path')) {
    function is_locale_prefix_exempt_path(string $path): bool
    {
        return PathHelper::isLocalePrefixExemptPath($path);
    }
}

/** 정적 공개 디스크 경로인지 확인한다. */
if (!function_exists('is_public_static_disk_path')) {
    function is_public_static_disk_path(string $path): bool
    {
        return PathHelper::isPublicStaticDiskPath($path);
    }
}

if (!function_exists('path')) {
    function path(string $path, bool $cache = false): string
    {
        return PathHelper::diskPath($path, $cache);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path, bool $cache = false): string
    {
        return PathHelper::basePath($path, $cache);
    }
}

if (!function_exists('project_brand_name')) {
    function project_brand_name(string $default = 'Site'): string
    {
        return ProjectHelper::brandName($default);
    }
}

if (!function_exists('project_primary_cta_path')) {
    function project_primary_cta_path(): string
    {
        return ProjectHelper::primaryCtaPath();
    }
}

if (!function_exists('project_primary_cta_url')) {
    function project_primary_cta_url(): string
    {
        return localized_path(project_primary_cta_path());
    }
}

if (!function_exists('project_asset_url')) {
    function project_asset_url(string $key, string $default = '', bool $cache = false): string
    {
        return AssetHelper::projectAssetUrl($key, $default, $cache);
    }
}

if (!function_exists('aos')) {
    /**
     * Scroll-reveal 속성 빌더 (AOS). 페이지 마크업에서 모션을 "PHP 변수"로 제어.
     *   <h2 <?= aos() ?>>                          // 기본 fade-up
     *   <li <?= aos('up', ['delay' => $i * 90]) ?>> // 리스트 순차 등장(stagger)
     *   <div <?= aos('zoom', ['duration' => 1200]) ?>>
     * AOS.init() 은 resource/js/app.js 에서 1회 실행됨 — 여기선 data-aos 속성만 생성한다.
     * (메인비주얼처럼 상단 고정 영역은 AOS 대신 GSAP active 연출로 처리)
     *
     * @param string $preset up|down|left|right|zoom|fade
     * @param array  $opts    delay·duration·offset(px) · once(bool) · easing · anchor · anchor_placement
     */
    function aos(string $preset = 'up', array $opts = []): string
    {
        static $presets = [
            'up'    => 'fade-up',
            'down'  => 'fade-down',
            'left'  => 'fade-left',
            'right' => 'fade-right',
            'zoom'  => 'zoom-in',
            'fade'  => 'fade',
        ];

        $attrs = [
            'data-aos'          => $presets[$preset] ?? $presets['up'],
            'data-aos-duration' => (string) (int) ($opts['duration'] ?? 900),
        ];

        if (isset($opts['delay'])) {
            $attrs['data-aos-delay'] = (string) (int) $opts['delay'];
        }
        if (isset($opts['offset'])) {
            $attrs['data-aos-offset'] = (string) (int) $opts['offset'];
        }
        if (!empty($opts['easing'])) {
            $attrs['data-aos-easing'] = (string) $opts['easing'];
        }
        if (array_key_exists('once', $opts)) {
            $attrs['data-aos-once'] = $opts['once'] ? 'true' : 'false';
        }
        if (!empty($opts['anchor'])) {
            $attrs['data-aos-anchor'] = (string) $opts['anchor'];
        }
        if (!empty($opts['anchor_placement'])) {
            $attrs['data-aos-anchor-placement'] = (string) $opts['anchor_placement'];
        }

        $out = [];
        foreach ($attrs as $key => $value) {
            $out[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        }

        return implode(' ', $out);
    }
}

if (!function_exists('seo_manager')) {
    function seo_manager(): \Seo\SeoManager
    {
        if (app()->has('seo_manager')) {
            return app()->get('seo_manager');
        }

        $manager = new \Seo\SeoManager();
        app()->share('seo_manager', $manager);

        return $manager;
    }
}

if (!function_exists('sitemap_generator')) {
    function sitemap_generator(): \Seo\SitemapGenerator
    {
        if (app()->has('sitemap_generator')) {
            return app()->get('sitemap_generator');
        }

        $generator = new \Seo\SitemapGenerator(app()->router());
        app()->share('sitemap_generator', $generator);

        return $generator;
    }
}

if (!function_exists('robots_generator')) {
    function robots_generator(): \Seo\RobotsGenerator
    {
        if (app()->has('robots_generator')) {
            return app()->get('robots_generator');
        }

        $generator = new \Seo\RobotsGenerator();
        app()->share('robots_generator', $generator);

        return $generator;
    }
}

if (!function_exists('img_path')) {
    function img_path(string $path, bool $cache = false): string
    {
        return AssetHelper::imagePath($path, $cache);
    }
}

if (!function_exists('image_path')) {
    function image_path(string $path, bool $cache = false): string
    {
        return img_path($path, $cache);
    }
}

if (!function_exists('image_picture')) {
    /**
     * <picture> 태그 출력 — webp 짝꿍이 있으면 type="image/webp" source를 끼우고, 없으면 <img>만 반환.
     * 입력은 image_path() 와 동일 형태 (상대 / 절대 / 외부 URL 자동 인식).
     *
     * @param array<string,string|bool|int> $attrs alt, class, loading, decoding, fetchpriority …
     */
    function image_picture(string $input, array $attrs = []): string
    {
        return ImageHelper::picture($input, $attrs);
    }
}

if (!function_exists('picture_from_url')) {
    /**
     * 이미 결정된 src · src_webp 두 URL로 <picture>를 직접 출력한다.
     * DB·동적 변수에서 src가 오는 케이스용. $srcWebp가 빈 문자열이면 plain <img>.
     *
     * @param array<string,string|bool|int> $attrs
     */
    function picture_from_url(string $srcUrl, string $srcWebp, array $attrs = []): string
    {
        return ImageHelper::pictureFromUrl($srcUrl, $srcWebp, $attrs);
    }
}

if (!function_exists('image_webp_url')) {
    /**
     * 입력 경로에 짝꿍 .webp 가 디스크에 있으면 캐시버스터 포함 public URL, 없으면 ''.
     */
    function image_webp_url(string $input): string
    {
        return ImageHelper::webpUrlFor($input);
    }
}

if (!function_exists('video_source_set')) {
    /**
     * 동영상 base 경로(확장자 제외)를 받아 av1 → webm → h264 mp4 순으로
     * 디스크에 존재하는 변형만 [['src' => url, 'type' => mime], ...] 으로 반환.
     * 브라우저는 위에서부터 첫 매치만 다운로드한다.
     *
     * 파일명 규칙: <base>.av1.mp4 / <base>.webm / <base>.h264.mp4
     */
    function video_source_set(string $base): array
    {
        $base = trim($base);
        if ($base === '') return [];

        $candidates = [
            ['suffix' => '.av1.mp4',  'type' => 'video/mp4; codecs="av01.0.05M.08"'],
            ['suffix' => '.webm',     'type' => 'video/webm'],
            ['suffix' => '.h264.mp4', 'type' => 'video/mp4'],
        ];

        $rootPath = defined('ROOT_PATH') ? (string) ROOT_PATH : '';
        $sources  = [];

        foreach ($candidates as $c) {
            $relUrl = $base . $c['suffix'];
            if ($rootPath !== '') {
                $diskPath = rtrim($rootPath, '/\\') . '/' . ltrim($relUrl, '/');
                if (!is_file($diskPath)) {
                    continue;
                }
            }
            $sources[] = [
                'src'  => base_path($relUrl, true),
                'type' => $c['type'],
            ];
        }

        return $sources;
    }
}

if (!function_exists('lord_icon')) {
    function lord_icon(
        string $key,
        ?string $primary = null,
        string $secondary = '#08a88a',
        bool $loop = false
    ): string {
        // Lord Icon 스크립트는 app.js에서 로드하며, 여기서는 색상과 옵션만 맞춘다.
        // 기본 primary 색상은 프로젝트 기본 톤을 따른다.
        if ($primary === null) {
            $primary = '#3617ce'; // primary default
        }

        if ($loop) {
            $option = '
                trigger="loop" 
                delay="2000"
            ';
        } else {
            $option = 'trigger="loop"';
        }

        return AssetHelper::lordIcon($key, $primary, $secondary, $loop);
    }
}

if (!function_exists('biz_icon')) {
    /**
     * Business 사업영역 카드용 커스텀 2톤 라인 아이콘 (lord-icon 대체).
     * - 주 구조  = .ic-p  → currentColor (= --clr-primary-def, navy)
     * - 보조 디테일 = .ic-s / .ic-fs → --clr-primary-400 (#3f6296)
     * 색·굵기·캡은 SCSS(.ico--svg 자식)에서 토큰으로 부여한다. 마크업엔 path + class 만.
     *
     * @return string 알 수 없는 key 면 '' (호출부 .ico:empty placeholder 노출)
     */
    function biz_icon(string $key): string
    {
        static $icons = [
            // 1. Real-time PCR — DNA 이중나선(주, navy) + 사다리 rung·증폭곡선 그래프(보조)
            'pcr' => '<g class="ic-s"><path d="M13 12 H19 M13 20 H19 M13 28 H19 M13 36 H19"/><path d="M29 10 V20 H42"/><path d="M29 19 C33 19 33 12 37 12 C39.5 12 40 11 42 11"/></g>'
                   . '<g class="ic-p"><path d="M16 8 C20 10 20 14 16 16 C12 18 12 22 16 24 C20 26 20 30 16 32 C12 34 12 38 16 40"/><path d="M16 8 C12 10 12 14 16 16 C20 18 20 22 16 24 C12 26 12 30 16 32 C20 34 20 38 16 40"/></g>',

            // 2. Syndromic — 돋보기(주) + 렌즈 안 서로 다른 병원체 3종: 스파이크 바이러스·막대균·세포(보조)
            'syndromic' => '<g class="ic-s"><path d="M17.5 14.6 V13.2 M17.5 21.4 V22.8 M13.9 18 H12.5 M21.1 18 H22.5 M15 15.5 L14.2 14.7 M20 15.5 L20.8 14.7 M15 20.5 L14.2 21.3 M20 20.5 L20.8 21.3"/><path d="M23.6 16.4 L26.6 18.6"/></g>'
                         . '<g class="ic-fs"><circle cx="17.5" cy="18" r="2.2"/><circle cx="21" cy="24.5" r="1.6"/></g>'
                         . '<g class="ic-p"><circle cx="20.5" cy="20.5" r="11"/><path d="M28.4 28.4 L38 38"/></g>',

            // 3. Respiratory — 폐+기관지(주) + 우상단 바이러스 액센트(보조)
            'respiratory' => '<g class="ic-s"><path d="M24 14 V20 M24 20 L20 24 M24 20 L28 24 M20 24 L18.5 27.5 M20 24 L21.5 27.5 M28 24 L26.5 27.5 M28 24 L29.5 27.5"/><path d="M38 7.6 V8.8 M38 14.4 V13.2 M34.4 11 H35.6 M41.6 11 H40.4"/></g>'
                           . '<g class="ic-fs"><circle cx="38" cy="11" r="1"/></g>'
                           . '<g class="ic-p"><path d="M24 8 V14"/><path d="M22 14.5 C16.5 16 11 21 10 28.5 C9.4 33.5 12 38 17 37 C20.5 36 21.5 31 21.5 26.5 C21.5 22 21.5 17 22 14.5 Z"/><path d="M26 14.5 C31.5 16 37 21 38 28.5 C38.6 33.5 36 38 31 37 C27.5 36 26.5 31 26.5 26.5 C26.5 22 26.5 17 26 14.5 Z"/></g>',

            // 4. Molecular Workflow — 마이크로플레이트(주, 웰그리드 내부) + 피펫 디스펜싱·드롭(보조)
            'workflow' => '<g class="ic-p"><path d="M22.5 8 H25.5 V16 L24 19 L22.5 16 Z"/><path d="M10 25 H38 a2 2 0 0 1 2 2 V37 a2 2 0 0 1 -2 2 H10 a2 2 0 0 1 -2 -2 V27 a2 2 0 0 1 2 -2 Z"/></g>'
                        . '<g class="ic-fs"><circle cx="24" cy="22" r="1.1"/><circle cx="12.5" cy="29.5" r="1.3"/><circle cx="19.5" cy="29.5" r="1.3"/><circle cx="26.5" cy="29.5" r="1.3"/><circle cx="33.5" cy="29.5" r="1.3"/><circle cx="12.5" cy="34.5" r="1.3"/><circle cx="19.5" cy="34.5" r="1.3"/><circle cx="26.5" cy="34.5" r="1.3"/><circle cx="33.5" cy="34.5" r="1.3"/></g>',

            // 5. Blood Typing — 혈액방울(주) + 판정 트레이 양성/음성 웰(보조)
            'typing' => '<g class="ic-p"><path d="M24 8 C24 8 17.5 16.5 17.5 22.5 a6.5 6.5 0 1 0 13 0 C30.5 16.5 24 8 24 8 Z"/><path d="M12.5 33 H35.5 V38.5 H12.5 Z"/></g>'
                      . '<g class="ic-fs"><circle cx="19" cy="35.7" r="2"/></g>'
                      . '<g class="ic-s"><circle cx="29" cy="35.7" r="2"/></g>',

            // 6. Transfusion — IV 혈액백(주) + 혈액 채움·눈금·드립방울(보조)
            'transfusion' => '<g class="ic-fs"><path d="M16 19 H32 V25 a3 3 0 0 1 -3 3 H19 a3 3 0 0 1 -3 -3 Z"/><circle cx="24" cy="37.5" r="1.8"/></g>'
                           . '<g class="ic-s"><path d="M18 14 H20.5 M18 16.5 H20.5"/></g>'
                           . '<g class="ic-p"><path d="M22 9 a2 2 0 0 1 4 0"/><path d="M16 11 H32 V25 a3 3 0 0 1 -3 3 H19 a3 3 0 0 1 -3 -3 Z"/><path d="M24 28 V34"/></g>',

            // 7. Immunohematology — 적혈구 2개(주) + 적혈구1·항체 Y 응집(보조)
            'immuno' => '<g class="ic-s"><circle cx="31" cy="16" r="4"/><ellipse cx="31" cy="16" rx="1.7" ry="1.1"/><path d="M24 25 V28 M24 25 L22 22.5 M24 25 L26 22.5"/></g>'
                      . '<g class="ic-p"><circle cx="18" cy="21" r="6.5"/><ellipse cx="18" cy="21" rx="2.8" ry="1.7"/><circle cx="30" cy="30" r="5"/><ellipse cx="30" cy="30" rx="2.2" ry="1.4"/></g>',

            // 8. Automated BloodBank — 분석기 본체+화면(주) + 화면 차트 그래프·바이알 랙(보조)
            'bloodbank' => '<g class="ic-s"><path d="M14.5 19.5 L17.5 16 L20.5 18 L24 14"/><path d="M15 28 V35 M20 28 V35 M25 28 V35 M30 28 V35 M34 28 V35"/></g>'
                         . '<g class="ic-fs"><circle cx="31" cy="17.5" r="1.2"/></g>'
                         . '<g class="ic-p"><path d="M12 8 H36 a3 3 0 0 1 3 3 V37 a3 3 0 0 1 -3 3 H12 a3 3 0 0 1 -3 -3 V11 a3 3 0 0 1 3 -3 Z"/><path d="M13 13 H25 a1 1 0 0 1 1 1 V21 a1 1 0 0 1 -1 1 H13 a1 1 0 0 1 -1 -1 V14 a1 1 0 0 1 1 -1 Z"/><path d="M11 27.5 H37"/></g>',
        ];

        $inner = $icons[$key] ?? '';
        if ($inner === '') {
            return '';
        }

        // 루트에 기본 표현속성 — CSS(.ic-*) 미적용/캐시 상황에도 검은 덩어리 대신
        // 네이비 라인으로 안전하게 렌더. CSS 가 오면 .ic-p/.ic-s/.ic-fs 가 2톤으로 덮어쓴다.
        return '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.7"'
             . ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
             . $inner . '</svg>';
    }
}
