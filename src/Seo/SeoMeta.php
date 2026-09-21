<?php

namespace Seo;

class SeoMeta
{
    protected array $siteInfo;
    protected string $htmlLang;
    protected string $canonicalUrl;
    protected array $alternateUrls;
    protected ?string $xDefaultUrl;
    protected string $pageTitle;
    protected string $pageDescription;
    protected string $pageKeywords;
    protected string $robots;
    protected string $metaImage;
    protected string $ogTitle;
    protected string $ogDescription;
    protected string $ogImage;
    protected string $ogLocale;
    protected string $ogType;
    protected string $twitterCard;
    protected string $twitterTitle;
    protected string $twitterDescription;
    protected string $twitterImage;
    protected string $faviconUrl;
    protected ?string $naverVerification;
    protected array $googleVerifications;

    public function __construct(array $data)
    {
        $this->siteInfo = $data['site_info'] ?? [];
        $this->htmlLang = (string) ($data['html_lang'] ?? 'ko');
        $this->canonicalUrl = (string) ($data['canonical_url'] ?? '');
        $this->alternateUrls = $data['alternate_urls'] ?? [];
        $this->xDefaultUrl = $data['x_default_url'] ?? null;
        $this->pageTitle = (string) ($data['page_title'] ?? '');
        $this->pageDescription = (string) ($data['page_description'] ?? '');
        $this->pageKeywords = (string) ($data['page_keywords'] ?? '');
        $this->robots = (string) ($data['robots'] ?? 'index, follow');
        $this->metaImage = (string) ($data['meta_image'] ?? '');
        $this->ogTitle = (string) ($data['og_title'] ?? '');
        $this->ogDescription = (string) ($data['og_description'] ?? '');
        $this->ogImage = (string) ($data['og_image'] ?? '');
        $this->ogLocale = (string) ($data['og_locale'] ?? 'ko_KR');
        $this->ogType = (string) ($data['og_type'] ?? 'website');
        $this->twitterCard = (string) ($data['twitter_card'] ?? 'summary');
        $this->twitterTitle = (string) ($data['twitter_title'] ?? '');
        $this->twitterDescription = (string) ($data['twitter_description'] ?? '');
        $this->twitterImage = (string) ($data['twitter_image'] ?? '');
        $this->faviconUrl = (string) ($data['favicon_url'] ?? '');
        $this->naverVerification = isset($data['naver_verification']) ? (string) $data['naver_verification'] : null;
        $this->googleVerifications = array_values(array_filter($data['google_verifications'] ?? []));
    }

    public function siteInfo(): array
    {
        return $this->siteInfo;
    }

    public function htmlLang(): string
    {
        return $this->htmlLang;
    }

    public function canonicalUrl(): string
    {
        return $this->canonicalUrl;
    }

    public function alternateUrls(): array
    {
        return $this->alternateUrls;
    }

    public function xDefaultUrl(): ?string
    {
        return $this->xDefaultUrl;
    }

    public function pageTitle(): string
    {
        return $this->pageTitle;
    }

    public function pageDescription(): string
    {
        return $this->pageDescription;
    }

    public function pageKeywords(): string
    {
        return $this->pageKeywords;
    }

    public function robots(): string
    {
        return $this->robots;
    }

    public function metaImage(): string
    {
        return $this->metaImage;
    }

    public function ogTitle(): string
    {
        return $this->ogTitle;
    }

    public function ogDescription(): string
    {
        return $this->ogDescription;
    }

    public function ogImage(): string
    {
        return $this->ogImage;
    }

    public function ogLocale(): string
    {
        return $this->ogLocale;
    }

    public function ogType(): string
    {
        return $this->ogType;
    }

    public function twitterCard(): string
    {
        return $this->twitterCard;
    }

    public function twitterTitle(): string
    {
        return $this->twitterTitle;
    }

    public function twitterDescription(): string
    {
        return $this->twitterDescription;
    }

    public function twitterImage(): string
    {
        return $this->twitterImage;
    }

    public function faviconUrl(): string
    {
        return $this->faviconUrl;
    }

    public function naverVerification(): ?string
    {
        return $this->naverVerification;
    }

    public function googleVerifications(): array
    {
        return $this->googleVerifications;
    }
}
