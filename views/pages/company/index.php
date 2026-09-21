<?php section('seo_title', '회사소개 | 파트너잇') ?>
<?php section('seo_desc', '기업의 기회와 파트너를 연결하는 AI 기반 통합 성장 플랫폼 파트너잇을 소개합니다.') ?>
<?php section('content') ?>
<?= include_view('components.partnerit-company-hero') ?>
<?= include_view('components.partnerit-company-flow') ?>
<?= include_view('components.partnerit-company-vision') ?>
<?= include_view('components.partnerit-company-history') ?>
<?= include_view('components.partnerit-company-certificates') ?>
<?= include_view('components.partnerit-reviews') ?>
<?= include_view('components.partnerit-partners') ?>
<?= include_view('components.partnerit-pricing') ?>
<?= include_view('components.partnerit-final-cta') ?>
<?php end_section() ?>
