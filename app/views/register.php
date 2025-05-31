<?php
session_start();

// Permite añadir dinámicamente campos para canciones en el formulario de álbum
require_once '../config/Conexion_BBDD.php';
require_once '../app/models/usuario.php';
require_once '../app/models/normal.php';
require_once '../app/models/artista.php';

// Verificar si el usuario ya está logueado
if (isset($_SESSION['email'])) {
    header("Location: /");
    exit;
}

// Instancia los modelos para gestionar usuarios normales y artistas
$usuarioModel = new Usuario($pdo);
$normalModel = new Normal($pdo);
$artistaModel = new Artista($pdo);

$error = '';

// Si el formulario se ha enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoge los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $f_nacimiento = $_POST['f_nacimiento'] ?? null;
    $tipo = $_POST['tipo'];

    // Verificar si ya existe el correo
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        // Si ya existe, muestra un mensaje de error
        $error = "❌ Ya existe una cuenta con ese correo.";
    } else {
        // Si no existe, registra el usuario en la tabla usuario
        $usuarioModel->registrar($email, $nombre, $f_nacimiento, $password);

        // Según el tipo, lo registra en la tabla correspondiente
        if ($tipo === 'normal') {
            $normalModel->registrar();
        } elseif ($tipo === 'artista') {
            $artistaModel->registrar();
        }

        // Inicia sesión automáticamente tras el registro
        $_SESSION['email'] = $email;
        $_SESSION['tipo'] = $tipo;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['id_usuario'] = $pdo->lastInsertId(); // Asumiendo que el ID es autoincremental
        header("Location: /");
        exit;
    }
}
?>


<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            <?php if (!empty($error)): ?>
                <p style="color: red; margin-top: 10px;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
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