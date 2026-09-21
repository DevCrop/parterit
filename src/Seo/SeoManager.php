<?php

namespace Seo;

use Database\DB;
use PDO;

class SeoManager
{
    protected ?array $siteInfo = null;

    public function buildForCurrentRequest(?SeoContext $context = null): SeoMeta
    {
        $context = $context ?: SeoContext::fromGlobals();
        $siteInfo = $this->getSiteInfo();
        $pathSeo = $this->findPathSeo($context->innerPath());

        $locale = $context->locale();
        $defaultLocale = \function_exists('project_default_locale') ? \project_default_locale() : 'en';

        // Locale fallback chain (high → low):
        // 1. nb_branch_seos.<field>_<locale>
        // 2. nb_branch_seos.<field>_<defaultLocale>
        // 3. nb_branch_seos.<field>          (legacy)
        // 4. nb_siteinfo.<field>_<locale>
        // 5. nb_siteinfo.<field>_<defaultLocale>
        // 6. nb_siteinfo.<field>             (legacy)
        $resolveLocaleField = function (string $field, array $siteFallback = []) use ($locale, $defaultLocale, $pathSeo, $siteInfo) {
            $candidates = [];
            if (is_array($pathSeo)) {
                $candidates[] = $pathSeo[$field . '_' . $locale] ?? null;
                if ($defaultLocale !== $locale) {
                    $candidates[] = $pathSeo[$field . '_' . $defaultLocale] ?? null;
                }
                $candidates[] = $pathSeo[$field] ?? null;
            }
            $candidates[] = $siteInfo[$field . '_' . $locale] ?? null;
            if ($defaultLocale !== $locale) {
                $candidates[] = $siteInfo[$field . '_' . $defaultLocale] ?? null;
            }
            $candidates[] = $siteInfo[$field] ?? null;
            foreach ($siteFallback as $extra) {
                $candidates[] = $extra;
            }
            return $this->firstNonEmpty($candidates);
        };

        $metaTitleBase = $resolveLocaleField('meta_title', [$siteInfo['title_' . $locale] ?? null, $siteInfo['title'] ?? null]);
        $metaDescriptionBase = $resolveLocaleField('meta_description');
        $metaKeywordsBase = $resolveLocaleField('meta_keywords');

        $pageTitle = $this->firstNonEmpty([
            $pathSeo['page_title_' . $locale] ?? null,
            ($defaultLocale !== $locale ? ($pathSeo['page_title_' . $defaultLocale] ?? null) : null),
            $pathSeo['page_title'] ?? null,
            $metaTitleBase,
        ]);
        $pageDescription = $metaDescriptionBase;
        $pageKeywords = $metaKeywordsBase;

        $htmlLang = \LOCALES[$context->locale()] ?? $context->locale();
        $canonicalUrl = $context->canonicalUrl();
        $alternateUrls = [];
        $xDefaultUrl = null;

        if (\project_feature_enabled('seo.alternate_hreflang', \project_localization_enabled())) {
            foreach (\locales() as $locale) {
                $alternateUrls[$locale] = $context->baseUrl() . \build_localized_url($context->innerPath(), $locale);
            }

            $xDefaultUrl = $context->baseUrl() . \build_localized_url($context->innerPath(), \project_default_locale());
        }

        $ogImage = $this->resolveAbsoluteUrl(
            !empty($pathSeo['og_image']) ? $pathSeo['og_image'] : ($siteInfo['og_image'] ?? ''),
            $context->baseUrl(),
            !empty($siteInfo['meta_thumb'])
                ? (\BASE_PATH . '/uploads/meta/' . $siteInfo['meta_thumb'])
                : (\BASE_PATH . '/resource/images/main/hero_poster.png')
        );

        $metaImage = $this->resolveAbsoluteUrl(
            $siteInfo['meta_image'] ?? '',
            $context->baseUrl(),
            $ogImage
        );

        $faviconUrl = $this->resolveAbsoluteUrl(
            $siteInfo['favicon'] ?? '',
            $context->baseUrl(),
            !empty($siteInfo['meta_favicon_ico'])
                ? (\BASE_PATH . '/uploads/meta/' . $siteInfo['meta_favicon_ico'])
                : (\BASE_PATH . '/resource/images/favicon.png')
        );

        $googleVerifications = $siteInfo['google_site_verification'] ?? [];
        if (!is_array($googleVerifications)) {
            $googleVerifications = array_filter([(string) $googleVerifications]);
        }

        return new SeoMeta([
            'site_info' => $siteInfo,
            'html_lang' => $htmlLang,
            'canonical_url' => $canonicalUrl,
            'alternate_urls' => $alternateUrls,
            'x_default_url' => $xDefaultUrl,
            'page_title' => $pageTitle,
            'page_description' => $pageDescription,
            'page_keywords' => $pageKeywords,
            'robots' => $this->firstNonEmpty([
                $pathSeo['robots'] ?? null,
                $siteInfo['meta_robots'] ?? null,
                'index, follow',
            ]),
            'meta_image' => $metaImage,
            'og_title' => $this->firstNonEmpty([
                $siteInfo['og_title'] ?? null,
                $pageTitle,
            ]),
            'og_description' => $this->firstNonEmpty([
                $siteInfo['og_description'] ?? null,
                $pageDescription,
            ]),
            'og_image' => $ogImage,
            'og_locale' => $this->firstNonEmpty([
                $siteInfo['og_locale'] ?? null,
                $context->locale() === 'ko' ? 'ko_KR' : 'en_US',
            ]),
            'og_type' => $this->firstNonEmpty([
                $pathSeo['og_type'] ?? null,
                $siteInfo['og_type'] ?? null,
                'website',
            ]),
            'twitter_card' => $this->firstNonEmpty([
                $pathSeo['twitter_card'] ?? null,
                $siteInfo['twitter_card'] ?? null,
                'summary_large_image',
            ]),
            'twitter_title' => $this->firstNonEmpty([
                $siteInfo['twitter_title'] ?? null,
                $pageTitle,
            ]),
            'twitter_description' => $this->firstNonEmpty([
                $siteInfo['twitter_desc'] ?? null,
                $pageDescription,
            ]),
            'twitter_image' => $this->resolveAbsoluteUrl(
                $siteInfo['twitter_image'] ?? '',
                $context->baseUrl(),
                $ogImage
            ),
            'favicon_url' => $faviconUrl,
            'naver_verification' => $siteInfo['naver_site_verification'] ?? null,
            'google_verifications' => $googleVerifications,
        ]);
    }

    public function getSiteInfo(): array
    {
        if ($this->siteInfo !== null) {
            return $this->siteInfo;
        }

        try {
            $statement = DB::getInstance()->query('SELECT * FROM nb_siteinfo LIMIT 1');
            $row = $statement instanceof \PDOStatement ? $statement->fetch(PDO::FETCH_ASSOC) : false;
            $this->siteInfo = is_array($row) ? $row : [];
        } catch (\Throwable $e) {
            $this->siteInfo = [];
        }

        return $this->siteInfo;
    }

    protected function findPathSeo(string $path): ?array
    {
        try {
            $defaultAdminDir = defined('SITE_ADMIN_DIR') ? SITE_ADMIN_DIR : 'console';
            $adminModelBasePath = $GLOBALS['NO_ADMIN_PATH'] ?? (dirname(__DIR__, 2) . '/' . $defaultAdminDir);
            $seoModelPath = $adminModelBasePath . '/Model/SeoModel.php';

            if (!is_file($seoModelPath)) {
                return null;
            }

            require_once $seoModelPath;

            if (!class_exists('SeoModel')) {
                return null;
            }

            return \SeoModel::findByPath($path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function firstNonEmpty(array $values): string
    {
        foreach ($values as $value) {
            if (!is_string($value)) {
                $value = (string) $value;
            }

            if (trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }

    protected function resolveAbsoluteUrl(string $value, string $baseUrl, string $fallback): string
    {
        $candidate = trim($value);
        if ($candidate === '') {
            $candidate = $fallback;
        }

        if (strpos($candidate, 'http://') === 0 || strpos($candidate, 'https://') === 0) {
            return $candidate;
        }

        return $baseUrl . $candidate;
    }
}
