<?php
    function getRoute($uri) {
        switch ($uri) {
            case '/':
            case '/home':  
                return 'app/views/home.php'; 
            case '/login': 
                return 'app/views/login.php';
            case '/register':
                return 'app/views/register.php';
            case '/playlists':
                return 'app/views/playlists.php';
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
            case '/miMusica':
                return 'app/views/miMusica.php';
            case '/uploadcanciones':
                return 'app/views/uploadCanciones.php';
            case '/subircancion':
                return 'app/views/subirCancion.php';
            case '/checkout':
                return 'app/views/checkout.php';
            case '/pago_exito':
                return 'app/views/pago_exito.php';
            case '/test':
                return 'public/ajax/test-platlist.php';
            case '/test2':
                return 'public/ajax/canciones_en_playlist.php';
            default:
                header("Location: /404");
                exit();
        }
    } 
?>