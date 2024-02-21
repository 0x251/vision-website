<?php
require_once __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);



$klein = new \Klein\Klein();



$klein->respond('GET', '/', function () {
    $indexFilePath = __DIR__ . '/index.html';
    $content = file_get_contents($indexFilePath);
    
    return $content;
});


$klein->respond('GET','/dash', function () {
    $indexFilePath = __DIR__ . '/dashboard/main.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    
    return $content;
});


$klein->respond('GET','/settings', function () {
    $indexFilePath = __DIR__ . '/dashboard/settings.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    
    return $content;
});


$klein->respond('GET','/download', function () {
    $indexFilePath = __DIR__ . '/dashboard/download.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    
    return $content;
});

$klein->respond('GET', '/dash/check', function () {

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');

    set_time_limit(0);

    ini_set('output_buffering', 'off');
    ini_set('zlib.output_compression', false);
    while (ob_get_level()) { ob_end_flush(); }
    ob_implicit_flush(true);

    $indexFilePath = __DIR__ . '/api/check-sub.php';

    ob_start();

    include $indexFilePath;

   
    while (ob_get_level() > 0) {
        ob_end_clean();
    }


    flush();
});


$klein->respond('POST', '/dash/download', function () {
    $indexFilePath = __DIR__ . '/api/download.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    return $content;
});


$klein->respond('POST', '/dash/reset-hwid', function () {
    $indexFilePath = __DIR__ . '/api/reset-hwid.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    return $content;
});

$klein->respond('POST', '/dash/profile-pic', function () {
    $indexFilePath = __DIR__ . '/api/profile-pic.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    return $content;
});


$klein->respond('GET', '/dash/profile', function () {
    $indexFilePath = __DIR__ . '/api/profile.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    return $content;
});


$klein->respond('GET', '/dash/logout', function () {
    $indexFilePath = __DIR__ . '/api/logout.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    return $content;
});


$klein->respond('POST', '/dash/redeem-key', function () {
    $indexFilePath = __DIR__ . '/api/redeem-key.php';
    ob_start();
    include $indexFilePath;
    $content = ob_get_clean();
    return $content;
});

$klein->respond('GET','/login', function () {
    $indexFilePath = __DIR__ . '/auth/login.php';
    $content = file_get_contents($indexFilePath);
    
    return $content;
});

$klein->respond('GET','/register', function () {
    $indexFilePath = __DIR__ . '/auth/register.php';
    $content = file_get_contents($indexFilePath);
    
    return $content;
});

$klein->onHttpError(function ($code, $router) {
    $response = $router->response();
    $indexFilePath = __DIR__ . '/index.html';
    switch ($code) {
        case 404:
            $response->body(file_get_contents($indexFilePath));
            break;
        case 405:
            $router->response()->body(
                'Seems like Visions servers are down! please wait or join our discord for support https://discord.gg/vision'
            );
            break;
        case 403:
            $router->response()->body(
                'Hey bitch i see you, lets not be a skid today!'
            );
            break;
    
        default:
            $router->response()->body(
                "Error: {$code}"
            );
    }
});


$klein->dispatch();