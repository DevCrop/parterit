<?php

final class ProjectHelper
{
    public static function profile(): array
    {
        if (defined('PROJECT_PROFILE') && is_array(PROJECT_PROFILE)) {
            return PROJECT_PROFILE;
        }

        return [];
    }

    public static function config(string $key, $default = null)
    {
        $value = self::profile();
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public static function brandName(string $default = 'Site'): string
    {
        $name = self::config('branding.display_name', defined('SITE_NAME') ? SITE_NAME : $default);
        $name = is_string($name) ? trim($name) : '';

        return $name !== '' ? $name : $default;
    }

    public static function primaryCtaPath(): string
    {
        $path = self::config(
            'routes.primary_cta_path',
            defined('SITE_PRIMARY_CTA_PATH') ? SITE_PRIMARY_CTA_PATH : '/contact/free-asset-diagnosis'
        );
        $path = is_string($path) ? trim($path) : '';

        return $path !== '' ? '/' . ltrim($path, '/') : '/contact/free-asset-diagnosis';
    }

    public static function localizationEnabled(): bool
    {
        return (bool) self::config(
            'localization.enabled',
            defined('SITE_I18N_ENABLED') ? SITE_I18N_ENABLED : true
        );
    }

    public static function defaultLocale(): string
    {
        $locale = self::config(
            'localization.default_locale',
            defined('SITE_DEFAULT_LOCALE') ? SITE_DEFAULT_LOCALE : 'en'
        );
        $locale = is_string($locale) ? trim($locale) : '';

        return $locale !== '' ? $locale : 'en';
    }

    public static function supportedLocales(): array
    {
        $locales = self::config(
            'localization.supported_locales',
            defined('SITE_SUPPORTED_LOCALES') && is_array(SITE_SUPPORTED_LOCALES) ? SITE_SUPPORTED_LOCALES : []
        );

        if (!is_array($locales)) {
            $locales = [];
        }

        $locales = array_values(array_unique(array_filter(array_map('strval', $locales), 'strlen')));

        if ($locales === []) {
            $locales = [self::defaultLocale()];
        }

        if (!in_array(self::defaultLocale(), $locales, true)) {
            array_unshift($locales, self::defaultLocale());
        }

        return array_values(array_unique($locales));
    }

    public static function featureEnabled(string $key, bool $default = false): bool
    {
        return (bool) self::config('features.' . $key, $default);
    }
}
