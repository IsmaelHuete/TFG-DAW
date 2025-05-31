<?php
    session_start();
    require_once '../config/Conexion_BBDD.php';
    require_once '../app/models/usuario.php';
    require_once '../app/models/normal.php';
    require_once '../app/models/artista.php';

    // Verificar si el usuario ya está logueado
    if (isset($_SESSION['email'])) {
        header("Location: /");
        exit;
    }
    //nstanciar los modelos para gestionar usuarios normales y artistas para poder utilizar sus metodos
    $usuarioModel = new Usuario($pdo);
    $normalModel = new Normal($pdo);
    $artistaModel = new Artista($pdo);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        // Validar que el email tenga un formato correcto en el metodo del login 
        //comprobamos la contraseña cifrada con la que se ha registrado para ver si coincide
        $usuario = $usuarioModel->login($email, $password); 
        
        if ($usuario) {
            // Si las credenciales son correctas, guarda los datos en la sesión
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['tipo'] = $usuarioModel->obtenerTipo($email);
            $_SESSION['plan'] = $usuario['plan']; 
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuarioModel->getNombreByEmail($email);
 

            header("Location: /");
            exit;
        } else {
            // Si las credenciales son incorrectas, muestra un mensaje de error
            $mensaje = "❌ Credenciales incorrectas.";
        }
    }

?>

<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/png" href="img/image-brand.png">
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/registro.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <a href="/" class="volver-home" style="position: absolute; top: 30px; left: 30px; text-decoration: none; z-index: 10;">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
            <defs>
                <linearGradient id="flecha-gradient" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="20%" stop-color="#481B9A"/>
                    <stop offset="100%" stop-color="#FF4EC4"/>
                </linearGradient>
            </defs>
            <path d="M15 19l-7-7 7-7" stroke="url(#flecha-gradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <main>
        <div class="registro">
            <img src="/img/image-brand.png">
            <h2>Inicia sesion</h2>
            <form action="login" method="POST">
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit" name="iniciarSesion">Iniciar sesion</button>
            </form>
            <span><a href="#">¿Olvidaste tu contraseña?</a></span>
                    <p class="mensaje-error"><?php if (isset($mensaje)): ?>
                        <?= htmlspecialchars($mensaje) ?>
                        <?php endif; ?>
                    </p>
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