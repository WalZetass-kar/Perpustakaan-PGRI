<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

$filePath = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        'ttf' => 'font/ttf',
        'ico' => 'image/x-icon',
    ];
    if (isset($mimes[$extension])) {
        header('Content-Type: ' . $mimes[$extension]);
    }
    readfile($filePath);
    return true;
}

require_once __DIR__ . '/public/index.php';
