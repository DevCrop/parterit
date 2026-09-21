<?php
require_once path('views/components/partnerit-stories-data.php');

$story = partnerit_customer_story_by_id((int) ($storyNo ?? 0));
$storyTitle = $story['title'] ?? '고객사례를 찾을 수 없습니다';
?>
<?php section('seo_title', e($storyTitle) . ' | 고객사례 | 파트너잇') ?>
<?php section('seo_desc', $story ? mb_substr((string) $story['description'], 0, 150) : '요청한 고객사례를 찾을 수 없습니다.') ?>
<?php section('content') ?>
    <?= include_view('components.partnerit-story-detail', ['story' => $story]) ?>
    <?= include_view('components.partnerit-final-cta') ?>
<?php end_section() ?>
