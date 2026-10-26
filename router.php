<?php

// Local router for PHP's built-in server. The production web server uses
// public/.htaccess for the same extensionless routes.
$requestPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$publicRoot = __DIR__ . '/public';

$routes = [
    '/' => 'index.php',
    '/downloads' => 'downloadspage.php',
    '/blog' => 'blogpage.php',
    '/benchmark' => 'benchmark.php',
    '/mission' => 'missionpage.php',
    '/about' => 'aboutpage.php',
    '/downloads/download' => 'downloads/download.php',
];

$legacyRoutes = [
    '/index.php' => '/',
    '/downloadspage.php' => '/downloads',
    '/blogpage.php' => '/blog',
    '/missionpage.php' => '/mission',
    '/aboutpage.php' => '/about',
    '/downloads/download.php' => '/downloads/download',
];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($legacyRoutes[$requestPath])) {
    header('Location: ' . $legacyRoutes[$requestPath], true, 301);
    exit;
}

if (is_file($publicRoot . $requestPath)) {
    return false;
}

if (isset($routes[$requestPath])) {
    require $publicRoot . '/' . $routes[$requestPath];
    return true;
}

http_response_code(404);
echo 'Not found';
