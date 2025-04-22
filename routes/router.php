<?php
/* function getRoute($uri) {
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
        case '/404':
            return 'app/views/404.php';
        default:
            header("Location: /404");
            exit();
    }
} */
function getRoute($uri) {
    if($uri==="/home" || $uri==="/"){
        return 'app/views/home.php';
    }else if($uri==="/login"){
        return 'app/views/login.php';
    }else if($uri === "/register"){
        return 'app/views/register.php';
    }
    else if($uri === "/playlists"){
        return 'app/views/playlists.php';
    }
    else if($uri === "/premium"){
        return 'app/views/premium.php';
    }
    else if($uri === "/404"){
        return 'app/views/404.php';
    }else{
        header("Location: /404");
        exit();
    }
}
?>