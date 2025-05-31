<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: /login'); 
    exit;
}
require_once __DIR__ . '/../../config/Conexion_BBDD.php';
require_once __DIR__ . '../../models/usuario.php';

// Obtiene el email de la sesión y prepara la variable para el tipo de plan
$email = $_SESSION['email'] ?? null;
$tipo_plan = null;

// Si hay email, obtiene el tipo de plan del usuario (normal o premium)
if ($email) {
    $usuarioModel = new Usuario($pdo);
    $tipo_plan = $usuarioModel->getPlanByEmail($email);
}
?>

<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium</title>
    <link rel="icon" type="image/png" href="img/image-brand.png">
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/premiun.css">

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
        <h1>Elige tu plan</h1>
        <div class="plan">
            <img class="vendido" src="/img/plan/ChatGPT_Image_Apr_15__2025__10_12_12_PM-removebg-preview.png">
            <div class="basico">
                <h2>Premium Mensual</h2>
                <span>$9.99<span class="letra"> / mes</span></span>
                <ul>
                    <li>&#10003;  Reproduccion ilimitada</li>
                    <li>&#10003;  Sin anuncios</li>
                    <li>&#10003;  Calidad estandar</li>
                    <li>&#10003;  1 dispositivo</li>
                </ul>
                <form>
                    <?php if ($tipo_plan === 'premium'): ?>
                        <button type="button" disabled class="boton-premium">¡Ya eres premium!</button>
                    <?php else: ?>
                        <a href="/checkout?plan=mensual" class="boton-premium">Mensual</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="premiun">
                <h2>Premium Anual</h2>
                <span>$99.99 <span class="letra"> / año</span></span>
                <ul>
                    <li>&#10003;  Reproduccion ilimitada</li>
                    <li>&#10003;  Sin anuncios</li>
                    <li>&#10003;  Alta fidelidad</li>
                    <li>&#10003;  5 dispositivo</li>
                </ul>
                <form>
                    <!-- Si el usuario ya es premium, deshabilita el botón -->
                    <?php if ($tipo_plan === 'premium' ): ?>
                        <button type="button" disabled class="boton-premium">¡Ya eres premium!</button>
                        
                    <!-- Si no es premium, muestra el enlace para comprar el plan mensual -->
                    <?php else: ?>
                        <a href="/checkout?plan=anual" class="boton-premium">Anual</a>
                    <?php endif; ?>
                </form>
            </div>
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