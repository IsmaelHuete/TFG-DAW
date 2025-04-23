<?php
    require_once '../config/Conexion_BBDD.php';
    require_once '../app/models/usuario.php';
    require_once '../app/models/normal.php';
    require_once '../app/models/artista.php';

    $usuarioModel = new Usuario($pdo);
    $normalModel = new Normal($pdo);
    $artistaModel = new Artista($pdo);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<script>alert('Llega aquí');</script>";

        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $f_nacimiento = $_POST['f_nacimiento'] ?? null;        
        $tipo = $_POST['tipo'];
 
        $usuarioModel->registrar($email, $nombre, $f_nacimiento, $password);
        if ($tipo === 'normal') {
            $normalModel->registrar();
        } elseif ($tipo === 'artista') {
            $artistaModel->registrar();
        } 
        header("Location: login");
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
            <h2>Crea tu cuenta</h2>
            <form action="register" method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <input type="date" name="f_nacimiento" placeholder="Fecha de nacimiento" required>
                <select name="tipo" required>
                    <option value="">Tipo de usuario</option>
                    <option value="normal">Normal</option>
                    <option value="artista">Artista</option>
                </select> 
                <button type="submit" name="registrarse">Registrarse</button>
            </form>
            <p>¿Ya tienes cuenta? <a href="login">Inicia sesion</a></p>
            
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