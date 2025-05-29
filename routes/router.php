<?php
    //esta función recibe una URI y devuelve la ruta del archivo correspondiente
    //si la URI no coincide con ninguna ruta definida, redirige a una página 404
    //si la URI es /, redirige a la página de inicio por ej
    function getRoute($uri) {
        switch ($uri) {
            case '/':
            case '/home':  
                return 'app/views/home.php'; 
            case '/login': 
                return 'app/views/login.php';
            case '/register':
                return 'app/views/register.php';
            case '/index':
                return 'app/views/index.php';
            case '/perfil':
                return 'app/views/perfil.php';
            case '/premium':
                return 'app/views/premium.php';
            case '/logout':
                return 'app/views/logout.php';
            case '/404':
                return 'app/views/404.php';
            case '/terms':
                return 'app/views/terminosServicio.php';
            case '/privacy':
                return 'app/views/politicaPrivacidad.php';
            case '/choices':
                return 'app/views/opcionesPrivacidad.php';
            case '/subircancion':
                return 'app/views/subirCancion.php';
            case '/checkout':
                return 'app/views/checkout.php';
            case '/pago_exito':
                return 'app/views/pago_exito.php';
            case '/estadisticas_artista':
                return 'app/views/estadisticas_artista.php';
            case '/premium_exito':
                return 'app/views/premium_exito.php';
            case '/premium_cancelado':
                return 'app/views/premium_cancelado.php';
            

            default:
                header("Location: /404");
                exit();
        }
    } 
?>