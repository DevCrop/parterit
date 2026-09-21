<?php
/**
 * SUMMERNOTE 에디터에서 작성된 raw HTML을 브랜드 톤에 맞춰 출력한다.
 *
 * @var string $html  nb_board.contents 원문 (admin 인증 경계 안에서 작성된 신뢰 입력)
 */
$html = isset($html) ? (string) $html : '';
?>
<div class="no-prose">
    <?= $html ?>
</div>
