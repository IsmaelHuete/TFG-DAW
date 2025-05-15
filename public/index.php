<?php
include '../routes/router.php';

// ✅ Corregido: extrae solo el path sin parámetros
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Vista individual de artista
if ($requestUri === '/artista') {
    include __DIR__ . '/../app/views/artista.php';
    exit;
}
if ($requestUri === '/album') {
    include __DIR__ . '/../app/views/album.php';
    exit;
}

$route = getRoute($requestUri);
$routePath = __DIR__ . '/../' . $route;

if (file_exists($routePath)) {
    include $routePath;
} else {
    include __DIR__ . '/../app/views/404.php'; 
}
