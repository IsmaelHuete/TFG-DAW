<?php
    //incluye el archivo de configuración de routes qu e contiene la función getRoute
    include '../routes/router.php';

    // extrae solo el path sin parámetros
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


    //llama a la función getRoute con la URI solicitada
    // y obtiene la ruta del archivo correspondiente por ejemplo,
    // si la URI es /home, devuelve app/views/home.php
    $route = getRoute($requestUri);
    // Construye la ruta completa al archivo
    $routePath = __DIR__ . '/../' . $route;


    //verifica si el archivo existe y lo incluye,
    //si no existe, incluye la página 404
    if (file_exists($routePath)) {
        include $routePath;
    } else {
        include __DIR__ . '/../app/views/404.php'; 
    }
