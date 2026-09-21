<?php

final class PathHelper
{
    /** @var array<string,string> */
    private static $assetVersionCache = [];

    public static function stripAppBaseFromPath(string $path): string
    {
        $path = $path === '' ? '/' : $path;
        $base = rtrim((string) (defined('BASE_PATH') ? BASE_PATH : ''), '/');
        if ($base === '') {
            return $path;
        }
        if ($path === $base || $path === $base . '/') {
            return '/';
        }
        if (strpos($path, $base . '/') === 0) {
            $rest = substr($path, strlen($base));

            return $rest === '' ? '/' : $rest;
        }

        return $path;
    }

    public static function localeSegments(string $path): array
    {
        $path = self::stripAppBaseFromPath($path);
        if ($path === '' || ($path[0] ?? '') !== '/') {
            $path = '/' . ltrim((string) $path, '/');
        }
        $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
        $first = $segments[0] ?? null;
        $localeCandidates = function_exists('project_supported_locales')
            ? project_supported_locales()
            : locales();
        $hadPrefix = $first !== null && in_array($first, $localeCandidates, true);
        $urlLocale = null;
        if ($hadPrefix) {
            $urlLocale = $first;
            array_shift($segments);
        }
        $stripped = '/' . implode('/', $segments);
        if ($stripped !== '/' && substr($stripped, -1) === '/') {
            $stripped = rtrim($stripped, '/');
        }
        if ($stripped === '') {
            $stripped = '/';
        }

        return [$hadPrefix, $urlLocale, $stripped];
    }

    public static function stripLocalePrefix(string $path): string
    {
        return self::localeSegments($path)[2];
    }

    public static function buildLocalizedUrl(string $pathAndQuery, ?string $locale = null): string
    {
        if (!function_exists('project_localization_enabled') || !project_localization_enabled()) {
            $query = '';
            $path = $pathAndQuery;
            if (($position = strpos($pathAndQuery, '?')) !== false) {
                $path = substr($pathAndQuery, 0, $position);
                $query = substr($pathAndQuery, $position);
            }
            $basePath = rtrim((string) (defined('BASE_PATH') ? BASE_PATH : ''), '/');

            return ($basePath === '' ? '' : $basePath) . '/' . ltrim($path, '/') . $query;
        }

        $locale = $locale ?? locale();
        if (!in_array($locale, locales(), true)) {
            $locale = function_exists('project_default_locale') ? project_default_locale() : DEFAULT_LOCALE;
        }
        $query = '';
        $path = $pathAndQuery;
        if (($position = strpos($pathAndQuery, '?')) !== false) {
            $path = substr($pathAndQuery, 0, $position);
            $query = substr($pathAndQuery, $position);
        }
        $path = '/' . ltrim($path, '/');
        $tail = ($path === '/') ? '/' . $locale : '/' . $locale . $path;
        $basePath = rtrim((string) (defined('BASE_PATH') ? BASE_PATH : ''), '/');

        return ($basePath === '' ? '' : $basePath) . $tail . $query;
    }

    public static function localizedPath(string $path): string
    {
        return self::buildLocalizedUrl('/' . ltrim($path, '/'));
    }

    public static function isLocalePrefixExemptPath(string $path): bool
    {
        $path = '/' . ltrim($path, '/');
        foreach (['/resource/', '/uploads/', '/dist/'] as $prefix) {
            if (strncmp($path, $prefix, strlen($prefix)) === 0) {
                return true;
            }
        }

        return in_array($path, ['/resource', '/uploads', '/dist'], true);
    }

    public static function isPublicStaticDiskPath(string $path): bool
    {
        return self::isLocalePrefixExemptPath($path);
    }

    public static function diskPath(string $path, bool $cache = false): string
    {
        $normalized = '/' . ltrim($path, '/');

        return ROOT_PATH . $normalized . self::buildAssetVersionSuffix($normalized, $cache);
    }

    public static function basePath(string $path, bool $cache = false): string
    {
        $normalized = '/' . ltrim($path, '/');

        return BASE_PATH . $normalized . self::buildAssetVersionSuffix($normalized, $cache);
    }

    private static function buildAssetVersionSuffix(string $path, bool $cache): string
    {
        if (!$cache) {
            return '';
        }

        $version = self::assetVersion($path);

        return $version !== '' ? '?v=' . $version : '';
    }

    private static function assetVersion(string $path): string
    {
        if (isset(self::$assetVersionCache[$path])) {
            return self::$assetVersionCache[$path];
        }

        $rootPath = defined('ROOT_PATH') ? (string) ROOT_PATH : '';
        $fullPath = $rootPath !== '' ? $rootPath . '/' . ltrim($path, '/') : '';

        if ($fullPath !== '' && is_file($fullPath)) {
            $modifiedAt = @filemtime($fullPath);
            self::$assetVersionCache[$path] = $modifiedAt !== false ? (string) $modifiedAt : '';

            return self::$assetVersionCache[$path];
        }

        self::$assetVersionCache[$path] = '';

        return self::$assetVersionCache[$path];
    }
}
