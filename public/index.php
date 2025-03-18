<?php
include '../routes/router.php';

$requestUri = $_SERVER['REQUEST_URI'];
$route = getRoute($requestUri);
$routePath = __DIR__ . '/../' . $route;  

if (file_exists($routePath)) {
    include $routePath;
} else {
    include __DIR__ . '/../app/views/404.php'; 
}

?>
