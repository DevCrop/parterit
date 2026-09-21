<?php

final class AssetHelper
{
    public static function projectAssetUrl(string $key, string $default = '', bool $cache = false): string
    {
        $path = ProjectHelper::config($key, $default);
        if (!is_string($path)) {
            $path = $default;
        }
        $path = trim($path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        return PathHelper::basePath($path, $cache);
    }

    public static function imagePath(string $path, bool $cache = false): string
    {
        return PathHelper::basePath('/resource/images/' . ltrim($path, '/'), $cache);
    }

    public static function lordIcon(string $key, ?string $primary = null, string $secondary = '#08a88a', bool $loop = false, bool $themeAware = true): string
    {
        if ($primary === null) {
            $primary = '#3549ff';
        }

        $option = $loop ? 'trigger="loop" delay="2000"' : 'trigger="loop"';
        $themeClass = $themeAware ? 'class="lord-icon-theme"' : '';
        $themeData = $themeAware ? 'data-lord-icon-key="' . $key . '"' : '';

        return '
            <lord-icon
                src="https://cdn.lordicon.com/' . $key . '.json"
                ' . $option . '
                colors="primary:' . $primary . ',secondary:' . $secondary . '"
                ' . $themeClass . '
                ' . $themeData . '
                style="width: 100%; height: 100%;"
            >
            </lord-icon>
        ';
    }

}
