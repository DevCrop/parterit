<?php

namespace Seo;

class SeoContext
{
    protected string $scheme;
    protected string $host;
    protected string $requestUri;
    protected string $requestPath;
    protected string $innerPath;
    protected string $locale;
    protected string $baseUrl;

    public function __construct(
        string $scheme,
        string $host,
        string $requestUri,
        string $requestPath,
        string $innerPath,
        string $locale
    ) {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->requestUri = $requestUri;
        $this->requestPath = $requestPath;
        $this->innerPath = $innerPath;
        $this->locale = $locale;
        $this->baseUrl = $scheme . '://' . $host;
    }

    public static function fromGlobals(?string $locale = null): self
    {
        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ||
            (($_SERVER['SERVER_PORT'] ?? '') == 443)
        );

        $scheme = $isHttps ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $requestPath = (string) (parse_url($requestUri, PHP_URL_PATH) ?: '/');
        $innerPath = \strip_locale_prefix_from_path($requestPath);
        $resolvedLocale = $locale ?: (\function_exists('locale') ? \locale() : 'ko');

        return new self($scheme, $host, $requestUri, $requestPath, $innerPath, $resolvedLocale);
    }

    public function scheme(): string
    {
        return $this->scheme;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function requestUri(): string
    {
        return $this->requestUri;
    }

    public function requestPath(): string
    {
        return $this->requestPath;
    }

    public function innerPath(): string
    {
        return $this->innerPath;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function canonicalUrl(): string
    {
        return $this->baseUrl . \localized_path($this->innerPath);
    }
}
