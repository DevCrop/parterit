<?php

if (!isset($NO_SITE_UNIQUE_KEY)) {
    $NO_SITE_UNIQUE_KEY = "DKHOLD";
}

/** @var \Routing\Router $router */
$router = app()->router();

$router->get('/', fn() => render('pages.index'))->name('home');
$router->get('/about', fn() => redirect('/about/company', 302))->name('about-us');
$router->get('/about/company', fn() => render('pages.company.index'))->name('company-intro');
$router->get('/business', fn() => redirect('/matching', 301));
$router->get('/business/announcement-matching', fn() => redirect('/matching', 301));
$router->get('/business/ai-report', fn() => redirect('/ai-report', 301));
$router->get('/business/consortium-matching', fn() => redirect('/consortium', 301));
$router->get('/matching', fn() => render('pages.business.index'))->name('business.announcement-matching');
$router->get('/ai-report', fn() => render('pages.business.index'))->name('business.ai-report');
$router->get('/consortium', fn() => render('pages.business.index'))->name('business.consortium-matching');
$router->get('/pricing', fn() => render('pages.business.index'))->name('pricing');
$router->get('/partners', fn() => render('pages.partners.index'))->name('partners');
$router->get('/partners/{no}', fn($request, $response, $no) => render('pages.partners.show', [
    'storyNo' => (int) $no,
]))->where('no', '[0-9]+')->name('partners.show');
$router->post('/brochure-requests', fn($request) => (new \Request\BrochureRequestService(\Database\DB::getInstance()))->submit($request))->name('brochure.requests');
$router->get('/brochure/download', fn($request) => \Request\BrochureRequestService::download($request))->name('brochure.download');
$router->get('/contact', function () {
    if (!project_feature_enabled('content.contact_page', false)) {
        return redirect('https://app.partnerit.kr/account/signin', 302);
    }

    return render('pages.contact.index');
})->name('contact');

$router->group(['prefix' => '/legal'], function ($r) {
    $r->get('/privacy', fn() => render('pages.legal.privacy'))->name('legal.privacy');
    $r->get('/cookies', fn() => render('pages.legal.cookies'))->name('legal.cookies');
    $r->get('/email-refusal', fn() => render('pages.legal.email'))->name('legal.email');
});

$router->get('/sitemap.xml', function () {
    return response(sitemap_generator()->generateXml(), 200, [
        'Content-Type' => 'application/xml; charset=utf-8',
    ]);
})->name('sitemap');

$router->get('/robots.txt', function () {
    return response(robots_generator()->build(), 200, [
        'Content-Type' => 'text/plain; charset=utf-8',
    ]);
})->name('robots');

$router->get('/{any}', function () {
    return redirect('/', 302);
})->where('any', '.+');
