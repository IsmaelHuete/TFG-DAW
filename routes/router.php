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
            default:
                header("Location: /404");
                exit();
        }
    } 
?>