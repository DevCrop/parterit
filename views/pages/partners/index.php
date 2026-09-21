<?php section('seo_title', '고객사례 | 파트너잇') ?>
<?php section('seo_desc', '파트너잇을 먼저 경험한 고객들의 지원사업 탐색과 AI 리포트 이용 후기를 만나보세요.') ?>
<?php section('content') ?>
    <div class="partnerit-stories">
        <?= include_view('components.partnerit-subhero', ['kind' => 'stories']) ?>
        <?= include_view('components.partnerit-stories-featured') ?>
        <?= include_view('components.partnerit-stories-gallery') ?>
    </div>
    <?= include_view('components.partnerit-partners') ?>
    <?= include_view('components.partnerit-final-cta') ?>
<?php end_section() ?>
