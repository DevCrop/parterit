<?php
declare(strict_types=1);

/** Small raster CAPTCHA fallback; no GD, SVG text or client-provided answer. */
final class CaptchaHelper
{
    private const DIGITS = [
        [14, 17, 19, 21, 25, 17, 14],
        [4, 12, 4, 4, 4, 4, 14],
        [14, 17, 1, 2, 4, 8, 31],
        [30, 1, 1, 14, 1, 1, 30],
        [2, 6, 10, 18, 31, 2, 2],
        [31, 16, 16, 30, 1, 1, 30],
        [14, 16, 16, 30, 17, 17, 14],
        [31, 1, 2, 4, 8, 8, 8],
        [14, 17, 17, 14, 17, 17, 14],
        [14, 17, 17, 15, 1, 1, 14],
    ];

    public static function noCache(): void
    {
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');
        header('X-Content-Type-Options: nosniff');
    }

    public static function matches(mixed $expected, mixed $submitted): bool
    {
        return is_string($expected) && is_string($submitted)
            && preg_match('/^[0-9]{5}$/D', $expected) === 1
            && preg_match('/^[0-9]{5}$/D', $submitted) === 1
            && hash_equals($expected, $submitted);
    }

    public static function sendPng(int $width, array $background): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name('PARTNERITSESSID');
            session_set_cookie_params([
                'lifetime' => 0, 'path' => '/', 'domain' => '',
                'secure' => false, 'httponly' => true, 'samesite' => 'Lax',
            ]);
            if (!session_start()) {
                throw new RuntimeException('CAPTCHA session could not be started.');
            }
        }

        $digits = '';
        for ($i = 0; $i < 5; $i++) {
            $digits .= (string) random_int(0, 9);
        }
        $png = self::render($digits, $width, $background);
        $_SESSION['captcha_secure'] = $digits;
        session_write_close();
        self::noCache();
        header('Content-Type: image/png');
        echo $png;
    }

    public static function render(string $digits, int $width, array $background): string
    {
        if (preg_match('/^[0-9]{5}$/D', $digits) !== 1 || !in_array($width, [100, 120], true)
            || count($background) !== 3) {
            throw new InvalidArgumentException('Invalid CAPTCHA dimensions or code.');
        }
        foreach ($background as $channel) {
            if (!is_int($channel) || $channel < 0 || $channel > 255) {
                throw new InvalidArgumentException('Invalid CAPTCHA color.');
            }
        }
        $height = 30;
        $pixels = str_repeat(pack('C3', ...array_values($background)), $width * $height);
        // Low-contrast raster noise; the answer is never embedded as text metadata.
        for ($i = 0; $i < 35; $i++) {
            self::pixel($pixels, $width, random_int(0, $width - 1), random_int(0, 29), 75);
        }
        for ($i = 0; $i < 5; $i++) {
            $left = intdiv($width - 100, 2) + 5 + $i * 20 + random_int(-1, 1);
            $top = random_int(3, 11);
            foreach (self::DIGITS[(int) $digits[$i]] as $row => $bits) {
                for ($column = 0; $column < 5; $column++) {
                    if (($bits & (1 << (4 - $column))) === 0) continue;
                    for ($dy = 0; $dy < 2; $dy++) {
                        for ($dx = 0; $dx < 2; $dx++) {
                            self::pixel($pixels, $width, $left + $column * 2 + $dx, $top + $row * 2 + $dy, 255);
                        }
                    }
                }
            }
        }
        $scanlines = '';
        for ($y = 0; $y < $height; $y++) {
            $scanlines .= "\0" . substr($pixels, $y * $width * 3, $width * 3);
        }
        $compressed = function_exists('gzcompress') ? gzcompress($scanlines, 6) : false;
        if ($compressed === false) {
            // One final uncompressed DEFLATE block; these fixed images are < 64 KiB.
            $length = strlen($scanlines);
            $compressed = "\x78\x01\x01" . pack('v2', $length, 0xffff ^ $length)
                . $scanlines . hash('adler32', $scanlines, true);
        }
        return "\x89PNG\r\n\x1a\n"
            . self::chunk('IHDR', pack('NNC5', $width, $height, 8, 2, 0, 0, 0))
            . self::chunk('IDAT', $compressed) . self::chunk('IEND', '');
    }

    private static function pixel(string &$pixels, int $width, int $x, int $y, int $channel): void
    {
        $offset = ($y * $width + $x) * 3;
        $pixels[$offset] = $pixels[$offset + 1] = $pixels[$offset + 2] = chr($channel);
    }

    private static function chunk(string $type, string $data): string
    {
        return pack('N', strlen($data)) . $type . $data . hash('crc32b', $type . $data, true);
    }
}
