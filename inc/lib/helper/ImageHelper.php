<?php

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

/**
 * 이미지 변환 유틸리티 — 업로드 시점에 .webp 파생본을 생성한다.
 *
 * - intervention/image v3 (PHP 8.3/8.4)
 * - GD/Imagick 미설치 환경에서는 graceful degrade(null 반환) → <picture> 폴백이 정상 작동
 */
final class ImageHelper
{
    /**
     * 원본 이미지 옆에 같은 basename + .webp 파생본을 생성한다.
     *
     * @param string $sourceFullPath 원본 절대경로 (예: /var/www/html/uploads/board/abc.jpg)
     * @param int    $maxWidth       가로 최대치 (이상이면 비율 유지 축소; 작은 이미지는 그대로)
     * @param int    $quality        WebP 품질 (1~100)
     * @return string|null           생성된 .webp 절대경로 / 실패·미지원 시 null
     */
    public static function generateWebpVariant(string $sourceFullPath, int $maxWidth = 1600, int $quality = 80): ?string
    {
        if (!is_file($sourceFullPath)) {
            return null;
        }

        $ext = strtolower(pathinfo($sourceFullPath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            return null;
        }

        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            return null;
        }

        if (!class_exists(ImageManager::class)) {
            return null;
        }

        try {
            $driver = extension_loaded('imagick') ? ImagickDriver::class : GdDriver::class;
            // Keep the existing asset orientation and never upscale small originals.
            $manager = new ImageManager($driver, autoOrientation: false);
            if (!$manager->driver()->supports('webp')) {
                return null;
            }
            $img = $manager->read($sourceFullPath)->scaleDown(width: max(1, $maxWidth));

            $dir  = dirname($sourceFullPath);
            $base = pathinfo($sourceFullPath, PATHINFO_FILENAME);
            $dest = $dir . DIRECTORY_SEPARATOR . $base . '.webp';

            $img->toWebp(quality: max(0, min(100, $quality)))->save($dest);
            unset($img, $manager);

            return is_file($dest) ? $dest : null;
        } catch (\Throwable $e) {
            error_log('[ImageHelper] webp generate fail: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 원본과 짝을 이루는 .webp 파일 경로를 삭제한다 (원본 삭제와 함께 호출).
     */
    public static function deleteWebpVariant(string $sourceFullPath): bool
    {
        $ext = strtolower(pathinfo($sourceFullPath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            return false;
        }

        $dir  = dirname($sourceFullPath);
        $base = pathinfo($sourceFullPath, PATHINFO_FILENAME);
        $webp = $dir . DIRECTORY_SEPARATOR . $base . '.webp';

        if (is_file($webp)) {
            return @unlink($webp);
        }
        return false;
    }

    /**
     * <picture> 태그를 출력한다 — webp 짝꿍이 있으면 <source>를 끼우고, 없으면 <img>만 반환.
     *
     * 입력 형태:
     *   - 'logo/client_logo_1.png'         → resource/images 기준 상대경로 (image_path와 동일)
     *   - '/resource/images/sub/x.jpg'     → 절대경로 (base_path)
     *   - 'https://cdn.example.com/x.jpg'  → 외부 URL (picture로 감싸지 않고 img만)
     *
     * @param array<string,string|bool|int> $attrs  alt/class/loading/decoding/fetchpriority 등
     */
    public static function picture(string $input, array $attrs = []): string
    {
        $isExternal = preg_match('#^https?://#i', $input) === 1;
        $isAbsolute = isset($input[0]) && $input[0] === '/';
        $rootPath   = defined('ROOT_PATH') ? (string) ROOT_PATH : '';

        // src URL 결정
        if ($isExternal) {
            $srcUrl  = $input;
            $diskJpg = '';
        } elseif ($isAbsolute) {
            $srcUrl  = PathHelper::basePath($input, true);
            $diskJpg = $rootPath !== '' ? $rootPath . '/' . ltrim($input, '/') : '';
        } else {
            $srcUrl  = AssetHelper::imagePath($input, true);
            $diskJpg = $rootPath !== '' ? $rootPath . '/resource/images/' . ltrim($input, '/') : '';
        }

        $ext     = strtolower(pathinfo($input, PATHINFO_EXTENSION));
        $canWebp = in_array($ext, ['jpg', 'jpeg', 'png'], true);

        $imgHtml = self::renderImgTag($srcUrl, $attrs);

        if (!$canWebp || $diskJpg === '' || !is_file($diskJpg)) {
            return $imgHtml;
        }

        $diskWebp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $diskJpg);
        if (!is_file($diskWebp)) {
            return $imgHtml;
        }

        // webp URL — 캐시버스터는 webp 자체 mtime을 따른다
        if ($isAbsolute) {
            $absWebpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $input);
            $webpUrl     = PathHelper::basePath($absWebpPath, true);
        } else {
            $relWebpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $input);
            $webpUrl     = AssetHelper::imagePath($relWebpPath, true);
        }

        return '<picture><source srcset="' . htmlspecialchars($webpUrl, ENT_QUOTES, 'UTF-8') . '" type="image/webp">' . $imgHtml . '</picture>';
    }

    /**
     * 이미 결정된 src URL · src_webp URL 두 값으로 <picture>를 직접 출력한다.
     * — DB·동적 변수에서 src가 오는 케이스용 (post-card-list, post-detail, hero poster …).
     * $srcWebp가 빈 문자열이면 plain <img>로 폴백.
     */
    public static function pictureFromUrl(string $srcUrl, string $srcWebp, array $attrs = []): string
    {
        $imgHtml = self::renderImgTag($srcUrl, $attrs);
        if ($srcWebp === '') {
            return $imgHtml;
        }
        return '<picture><source srcset="' . htmlspecialchars($srcWebp, ENT_QUOTES, 'UTF-8') . '" type="image/webp">' . $imgHtml . '</picture>';
    }

    /**
     * 동일 디스크 위치(상대 또는 절대 input)에 짝꿍 .webp 가 있으면 캐시버스터 포함 webp public URL을 반환한다.
     * 외부 호출자가 src/srcWebp를 별도 결정해야 할 때 사용 (e.g. 컨트롤러).
     */
    public static function webpUrlFor(string $input): string
    {
        if (preg_match('#^https?://#i', $input) === 1) {
            return '';
        }

        $ext = strtolower(pathinfo($input, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            return '';
        }

        $rootPath   = defined('ROOT_PATH') ? (string) ROOT_PATH : '';
        $isAbsolute = isset($input[0]) && $input[0] === '/';
        $diskJpg    = $isAbsolute
            ? ($rootPath !== '' ? $rootPath . '/' . ltrim($input, '/') : '')
            : ($rootPath !== '' ? $rootPath . '/resource/images/' . ltrim($input, '/') : '');

        if ($diskJpg === '' || !is_file($diskJpg)) {
            return '';
        }
        $diskWebp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $diskJpg);
        if (!is_file($diskWebp)) {
            return '';
        }

        $relWebp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $input);
        return $isAbsolute
            ? PathHelper::basePath($relWebp, true)
            : AssetHelper::imagePath($relWebp, true);
    }

    private static function renderImgTag(string $src, array $attrs): string
    {
        $parts = ['src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"'];
        foreach ($attrs as $k => $v) {
            if ($v === null || $v === false) {
                continue;
            }
            $key = (string) $k;
            if ($v === true) {
                $parts[] = $key;
                continue;
            }
            $parts[] = $key . '="' . htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8') . '"';
        }
        return '<img ' . implode(' ', $parts) . '>';
    }
}
