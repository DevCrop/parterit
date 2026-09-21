<?php

use Core\Application;

require_once dirname(__DIR__).'/config/setup.php';
require_once dirname(__DIR__).'/config/functions.php';
require_once dirname(__DIR__).'/config/helpers.php';

if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}


spl_autoload_register(function ($class) {
    $path = ROOT_PATH . '/src/' . str_replace('\\', '/', $class) . '.php';

    
    if (file_exists($path)) {
        require_once $path;
    }
});

require_once dirname(__DIR__).'/config/variables.php';
require_once dirname(__DIR__).'/config/configuration.php';

$app = new Application(ROOT_PATH);

[, , $innerRoutePath] = path_locale_segments($app->request()->path());
$segments = $innerRoutePath === '/' ? [] : array_values(array_filter(explode('/', trim($innerRoutePath, '/')), 'strlen'));
$firstAfterLocale = $segments[0] ?? null;
$isAdmin = (strtolower((string)$firstAfterLocale) === 'admin');

if (!$isAdmin) {
    include_once ROOT_PATH.'/routes/web.php';
    $app->syncLocaleFromRequest();
    include_once ROOT_PATH.'/bootstrap/menu.php';
}
