<?php

include_once __DIR__.'/inc/lib/base.class.php';

$response = $app->handleRequest();
echo $response->send(); 
