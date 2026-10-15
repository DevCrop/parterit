<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
if (in_array('--lint', $argv, true)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
        static fn($entry) => !$entry->isDir() || !in_array($entry->getFilename(), ['.git', 'node_modules', 'uploads', 'sources', 'handoff'], true)
    ));
    $result = ['php' => PHP_VERSION, 'files' => 0, 'issues' => []];
    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') continue;
        $output = [];
        exec(escapeshellarg(PHP_BINARY) . ' -d auto_prepend_file= -d error_reporting=32767 -d display_errors=1 -d log_errors=0 -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $status);
        $result['files']++;
        if ($status !== 0 || preg_match('/Deprecated:|Warning:|Fatal error:|Parse error:/', implode("\n", $output))) {
            $result['issues'][] = ['file' => substr($file->getPathname(), strlen($root) + 1), 'output' => $output];
        }
    }
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit($result['issues'] ? 1 : 0);
}

error_reporting(E_ALL);
set_error_handler(static function ($level, $message, $file, $line) {
    throw new ErrorException($message, 0, $level, $file, $line);
});
require $root . '/vendor/autoload.php';
require $root . '/inc/lib/helper/ImageHelper.php';
require $root . '/inc/lib/func.php';

$directory = sys_get_temp_dir() . '/php-compat-' . bin2hex(random_bytes(8));
mkdir($directory, 0700);
$checks = [];
$check = static function (string $name, bool $pass) use (&$checks): void {
    $checks[] = ['name' => $name, 'pass' => $pass];
};
try {
    $check('PHP >= 8.3', PHP_VERSION_ID >= 80300);
    foreach (['gd', 'mbstring', 'fileinfo', 'pdo_mysql', 'mysqli', 'zip', 'exif'] as $extension) {
        $check('extension ' . $extension, extension_loaded($extension));
    }
    $check('GD WebP support', !empty(gd_info()['WebP Support']));
    foreach ([['large', 2000, 1000], ['small', 120, 80]] as [$name, $width, $height]) {
        $source = $directory . '/' . $name . '.png';
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 40, 120, 240, 127));
        imagefilledrectangle($image, 5, 5, 50, 50, imagecolorallocatealpha($image, 40, 120, 240, 0));
        imagepng($image, $source);
        $hash = hash_file('sha256', $source);
        $webp = ImageHelper::generateWebpVariant($source);
        $size = $webp ? getimagesize($webp) : false;
        $expectedWidth = min(1600, $width);
        $check($name . ' webp dimensions', $size && $size[0] === $expectedWidth && $size[1] === (int) ($height * $expectedWidth / $width));
        $check($name . ' original unchanged', hash_file('sha256', $source) === $hash);
        if ($webp) {
            $decoded = imagecreatefromwebp($webp);
            $check($name . ' alpha retained', imagecolorsforindex($decoded, imagecolorat($decoded, 0, 0))['alpha'] === 127);
        }
        $check($name . ' paired deletion', imageDelete($source) && !is_file($source) && !is_file($directory . '/' . $name . '.webp'));
        unset($image, $decoded);
    }
    $source = $directory . '/photo.jpg';
    $image = imagecreatetruecolor(200, 100);
    imagejpeg($image, $source);
    $check('JPEG to WebP', ImageHelper::generateWebpVariant($source) === $directory . '/photo.webp');
    $check('missing source fallback', ImageHelper::generateWebpVariant($directory . '/missing.png') === null);
    $textFile = $directory . '/attachment.txt';
    file_put_contents($textFile, 'fixture');
    $check('non-image fallback', ImageHelper::generateWebpVariant($textFile) === null);
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->setFrom('qa@example.test');
    $mail->addAddress('recipient@example.test');
    $mail->Subject = 'Compatibility fixture';
    $mail->Body = 'MIME generation only; not sent.';
    $check('PHPMailer MIME', $mail->preSend() && str_contains($mail->getSentMIMEMessage(), $mail->Body));
    $check('password hashing', password_verify('fixture', password_hash('fixture', PASSWORD_DEFAULT)));
} catch (Throwable $error) {
    $checks[] = ['name' => 'unexpected error', 'pass' => false, 'error' => $error->getMessage()];
} finally {
    foreach (new DirectoryIterator($directory) as $entry) {
        if ($entry->isFile()) unlink($entry->getPathname());
    }
    rmdir($directory);
}
echo json_encode(['php' => PHP_VERSION, 'checks' => $checks], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit(count(array_filter($checks, static fn($check) => !$check['pass'])) ? 1 : 0);
