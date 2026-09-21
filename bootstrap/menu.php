<?php

use Menu\Menu;

$menu = new Menu('site');
app()->share('menu', $menu);

$about = $menu->add('서비스 소개', '/about', 'about-us', ['orderIndex' => 10]);
$menu->add('회사소개', '/about/company', 'company-intro', [
    'parent' => $about,
    'orderIndex' => 10,
]);

$business = $menu->add('주요기능', '/matching', 'business', ['orderIndex' => 20]);
$menu->add('공고매칭', '/matching', 'announcement-matching', [
    'parent' => $business,
    'orderIndex' => 10,
]);
$menu->add('AI 보고서', '/ai-report', 'ai-report', [
    'parent' => $business,
    'orderIndex' => 20,
]);
$menu->add('컨소시엄 매칭', '/consortium', 'consortium-matching', [
    'parent' => $business,
    'orderIndex' => 30,
]);

$menu->add('요금정책', '/pricing', 'pricing', ['orderIndex' => 30]);
$menu->add('고객사례', '/partners', 'partners', ['orderIndex' => 40]);
