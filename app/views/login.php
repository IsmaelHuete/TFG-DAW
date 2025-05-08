<?php
    session_start();
    require_once '../config/Conexion_BBDD.php';
    require_once '../app/models/usuario.php';
    require_once '../app/models/normal.php';
    require_once '../app/models/artista.php';

    $usuarioModel = new Usuario($pdo);
    $normalModel = new Normal($pdo);
    $artistaModel = new Artista($pdo);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $usuarioModel->login($nombre, $password);
        $_SESSION['email']=$email;
        $_SESSION['tipo']=$tipo;
        header("Location: /");
        exit;
    }
?>


<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/registro.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
    <main>
        <div class="registro">
            <img src="/img/image-brand.png">
            <h2>Inicia sesion</h2>
            <form action="login" method="POST">
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" name="iniciarSesion">Iniciar sesion</button>
            </form>
            <span> <a href="#">¿Olvidaste tu contraseña?</a></span>
            <p>No tienes una cuenta?</p>
            <a href="register">Registrate</a>
        </div>
    </main>
    <div class="difuminado"></div>
    <?php 
        include ("layouts/footer2.php");
    ?> 
    <script src="js/header.js"></script>
    <script src="js/home.js"></script>
    
</body>
</html>