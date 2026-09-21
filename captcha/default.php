<?php

$image = imagecreatetruecolor(120, 30);
if (!$image) {
    http_response_code(500);
    exit('이미지를 생성할 수 없습니다.');
}

$background = imagecolorallocate($image, 11, 32, 63);
if ($background === false) {
    imagedestroy($image);
    http_response_code(500);
    exit('배경색을 설정할 수 없습니다.');
}

imagefill($image, 0, 0, $background);

$textColor = imagecolorallocate($image, 255, 255, 255);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('PARTNERITSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$digit = '';
for ($x = 15; $x <= 95; $x += 20) {
    $num = (string) random_int(0, 9);
    $digit .= $num;
    imagechar($image, random_int(3, 5), $x, random_int(2, 14), $num, $textColor);
}

$_SESSION['captcha_secure'] = $digit;

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
